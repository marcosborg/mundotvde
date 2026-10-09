<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Driver, User, DriverMessageTemplate, DriverContractVersion};
use App\Services\DriverOnboarding;
use App\Http\Middleware\RequireDriverContract;
use Illuminate\Support\Facades\{DB, Schema, Gate, Mail};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
class DriverOnboardingTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        config(['driver_onboarding.enabled' => true]);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->softDeletes();
            $t->timestamps();
        });
        Schema::create('drivers', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            foreach (['name', 'email', 'phone', 'driver_vat', 'address', 'zip', 'city', 'citizen_card', 'driver_license', 'drivers_certificate', 'license_plate', 'brand', 'model'] as $c) {
                $t->string($c)->nullable();
            }
            $t->softDeletes();
            $t->timestamps();
        });
        (require base_path('database/migrations/2026_10_09_120000_create_driver_onboarding_tables.php'))->up();
        DB::table('users')->insert(['id' => 1, 'name' => 'Teste', 'email' => 'teste@example.test']);
        DB::table('users')->insert(['id' => 2, 'name' => 'Outro', 'email' => 'outro@example.test']);
        Driver::create(['user_id' => 1, 'name' => 'Condutor Teste', 'license_plate' => 'AA-00-AA', 'brand' => 'Marca', 'model' => 'Modelo']);
        DriverMessageTemplate::where('key', 'own_vehicle')->update(['active' => true, 'body' => 'Olá {nome}. Viatura: {marca} {modelo}, {matricula}.']);
    }
    private function generate()
    {
        return app(DriverOnboarding::class)->generate(Driver::first(), 'own_vehicle', 99);
    }
    public function test_draft_contract_cannot_be_generated() : void
    {
        $this->expectException(ValidationException::class);
        app(DriverOnboarding::class)->generate(Driver::first(), 'company_rental', 99);
    }
    public function test_contract_is_immutable_and_only_explicit_generation_replaces_it() : void
    {
        $contract = $this->generate();
        Driver::first()->update(['license_plate' => 'BB-00-BB']);
        DriverMessageTemplate::where('key', 'own_vehicle')->update(['body' => 'Novo {matricula}']);
        $this->assertStringContainsString('AA-00-AA', $contract->fresh()->body);
        $this->assertSame(1, DriverContractVersion::count());
        app(DriverOnboarding::class)->accept($contract, User::find(1), $contract->content_hash, '127.0.0.1', 'test');
        $new = $this->generate();
        $this->assertSame(2, $new->version);
        $this->assertNull($new->accepted_at);
        $this->assertNotNull($contract->fresh()->accepted_at);
        $this->assertNotNull($contract->fresh()->superseded_at);
        $this->assertSame('Novo BB-00-BB', $new->body);
        $this->assertSame($new->id, DriverContractVersion::pendingFor(User::find(1))->id);
    }
    public function test_other_user_cannot_accept_contract() : void
    {
        $contract = $this->generate();
        try {
            app(DriverOnboarding::class)->accept($contract, User::find(2), $contract->content_hash, '127.0.0.1', 'test');
            $this->fail('Should reject');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertNull($contract->fresh()->accepted_at);
    }
    public function test_stale_contract_cannot_be_accepted() : void
    {
        $contract = $this->generate();
        $this->generate();
        try {
            app(DriverOnboarding::class)->accept($contract, User::find(1), $contract->content_hash, '127.0.0.1', 'test');
            $this->fail('Should reject');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }
    }
    public function test_wrong_content_hash_is_rejected_and_acceptance_is_idempotent() : void
    {
        $contract = $this->generate();
        $service = app(DriverOnboarding::class);
        try {
            $service->accept($contract, User::find(1), str_repeat('0', 64), '127.0.0.1', 'test');
            $this->fail('Should reject');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }
        $service->accept($contract, User::find(1), $contract->content_hash, '127.0.0.1', 'original');
        $service->accept($contract, User::find(1), $contract->content_hash, '127.0.0.2', 'retry');
        $this->assertSame('127.0.0.1', $contract->fresh()->accepted_ip);
        $this->assertNull(DriverContractVersion::pendingFor(User::find(1)));
    }
    public function test_pending_contract_blocks_web_and_api_until_accepted() : void
    {
        $contract = $this->generate();
        $middleware = new RequireDriverContract();
        $web = Request::create('/admin');
        $web->setUserResolver(fn() => User::find(1));
        $this->assertSame(route('driver-contract.show'), $middleware->handle($web, fn() => response('ok'))->getTargetUrl());
        $api = Request::create('/api/app/reports');
        $api->setUserResolver(fn() => User::find(1));
        $blocked = $middleware->handle($api, fn() => response('ok'));
        $this->assertSame(423, $blocked->status());
        $this->assertSame('CONTRACT_ACCEPTANCE_REQUIRED', $blocked->getData(true)['code']);
        app(DriverOnboarding::class)->accept($contract, User::find(1), $contract->content_hash, '127.0.0.1', 'test');
        $this->assertSame('ok', $middleware->handle($api, fn() => response('ok'))->getContent());
    }
    public function test_missing_placeholder_data_leaves_previous_contract_current() : void
    {
        $contract = $this->generate();
        DriverMessageTemplate::where('key', 'own_vehicle')->update(['body' => 'NIF {nif}']);
        try {
            $this->generate();
            $this->fail('Should reject');
        } catch (ValidationException $e) {
        }
        $this->assertNull($contract->fresh()->superseded_at);
        $this->assertSame(1, DriverContractVersion::count());
    }
    public function test_welcome_email_uses_account_and_template_and_records_delivery() : void
    {
        Mail::fake();
        Gate::before(fn() => true);
        $this->actingAs(User::find(2));
        $request = Request::create('/', 'POST', ['confirm' => 1]);
        $request->setUserResolver(fn() => User::find(2));
        (new \App\Http\Controllers\Admin\DriverOnboardingController())->welcome($request, Driver::first(), app(DriverOnboarding::class));
        $this->assertDatabaseHas('driver_welcome_deliveries', ['recipient' => 'teste@example.test', 'sent_by' => 2]);
        $this->assertStringContainsString('Condutor Teste', DB::table('driver_welcome_deliveries')->value('body'));
    }
    private function httpMiddleware() : void
    {
        $this->withoutMiddleware([\App\Http\Middleware\AuthGates::class, \App\Http\Middleware\VerificationMiddleware::class, \App\Http\Middleware\RenderCrmForms::class]);
    }
    public function test_http_acceptance_requires_explicit_checkbox_and_then_unlocks() : void
    {
        $this->httpMiddleware();
        $contract = $this->generate();
        $this->actingAs(User::find(1));
        $this->get('/admin')->assertRedirect(route('driver-contract.show'));
        $this->get('/driver-contract')->assertOk()->assertSee('Li e concordo')->assertSee('AA-00-AA');
        $this->postJson('/driver-contract/accept', ['contract_id' => $contract->id, 'content_hash' => $contract->content_hash])->assertUnprocessable();
        $this->assertNull($contract->fresh()->accepted_at);
        $this->post('/driver-contract/accept', ['contract_id' => $contract->id, 'content_hash' => $contract->content_hash, 'agree' => 1])->assertRedirect(route('admin.home'));
        $this->assertNotNull($contract->fresh()->accepted_at);
        $this->get('/driver-contract/pdf')->assertOk()->assertHeader('content-type', 'application/pdf');
    }
    public function test_api_exposes_pending_contract_and_accepts_with_token() : void
    {
        $this->httpMiddleware();
        $contract = $this->generate();
        \Laravel\Sanctum\Sanctum::actingAs(User::find(1));
        $this->getJson('/api/app/reports')->assertStatus(423)->assertJson(['code' => 'CONTRACT_ACCEPTANCE_REQUIRED']);
        $this->getJson('/api/app/contract')->assertOk()->assertJson(['id' => $contract->id, 'content_hash' => $contract->content_hash]);
        $this->postJson('/api/app/contract/accept', ['contract_id' => $contract->id, 'content_hash' => $contract->content_hash, 'agree' => true])->assertOk()->assertJson(['accepted' => true]);
        $this->getJson('/api/app/contracts')->assertOk()->assertJson(['version' => 1, 'requires_authentication' => true]);
    }
    public function test_driver_cannot_generate_contracts_or_edit_templates() : void
    {
        $this->httpMiddleware();
        $this->actingAs(User::find(1));
        Gate::before(fn() => false);
        $this->post('/admin/drivers/1/generate-contract', ['type' => 'own_vehicle'])->assertForbidden();
        $this->put('/admin/driver-templates/1', ['title' => 'Changed', 'body' => 'Changed', 'active' => 1])->assertForbidden();
        $this->post('/admin/drivers/1/welcome', ['confirm' => 1])->assertForbidden();
    }

    public function test_disabled_rollout_never_blocks_existing_users_or_exposes_new_routes(): void
    {
        $this->httpMiddleware();
        $this->generate();
        config(['driver_onboarding.enabled' => false]);
        $this->actingAs(User::find(1));
        $this->assertNull(DriverContractVersion::pendingFor(User::find(1)));
        $request = Request::create('/api/app/reports');
        $request->setUserResolver(fn () => User::find(1));
        $this->assertSame('ok', (new RequireDriverContract)->handle($request, fn () => response('ok'))->getContent());
        $this->get('/driver-contract')->assertNotFound();
        $this->get('/admin/driver-templates')->assertNotFound();
    }
}
