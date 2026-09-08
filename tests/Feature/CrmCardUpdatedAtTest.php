<?php

namespace Tests\Feature;

use App\Events\CardStageChanged;
use App\Http\Controllers\Admin\CrmCardsController;
use App\Http\Controllers\Admin\CrmKanbanController;
use App\Models\CrmCard;
use App\Models\CrmCardNote;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CrmCardUpdatedAtTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Gate::before(fn ($user = null) => true);
        Event::fake([CardStageChanged::class]);
        Schema::create('crm_cards', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('stage_id');
            $t->string('title');
            $t->string('priority')->default('medium');
            $t->string('status')->default('open');
            $t->integer('position')->default(1000);
            $t->text('fields_snapshot_json')->nullable();
            foreach (['due_at', 'won_at', 'closed_at'] as $column) $t->dateTime($column)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('crm_stages', function (Blueprint $t) {
            $t->id();
            $t->boolean('is_won')->default(false);
            $t->boolean('is_lost')->default(false);
            $t->softDeletes();
        });
        Schema::create('crm_card_notes', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('card_id'); $t->unsignedBigInteger('user_id')->nullable();
            $t->text('content'); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('crm_card_activities', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('card_id'); $t->string('type');
            $t->text('meta_json'); $t->unsignedBigInteger('created_by_id')->nullable();
            $t->timestamps(); $t->softDeletes();
        });
        DB::table('crm_stages')->insert([['id' => 1], ['id' => 2]]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function card(): CrmCard
    {
        Carbon::setTestNow('2026-09-08 10:00:00');
        $card = CrmCard::create(['title' => 'Original', 'stage_id' => 1]);
        Event::fake([CardStageChanged::class]);
        Carbon::setTestNow('2026-09-08 11:00:00');
        return $card;
    }

    public function test_note_creation_updates_parent_and_response_without_changing_stage(): void
    {
        $card = $this->card();
        $response = (new CrmCardsController)->quickAddNote(Request::create('/', 'POST', ['content' => 'Follow up']), $card);
        $this->assertSame('2026-09-08 11:00:00', $card->fresh()->updated_at->format('Y-m-d H:i:s'));
        $this->assertSame('08/09/2026 11:00', $response->getData(true)['card']['updated_at_html']);
        $this->assertEquals(1, $card->fresh()->stage_id);
        Event::assertNotDispatched(CardStageChanged::class);
        Carbon::setTestNow('2026-09-08 12:00:00');
        CrmCardNote::first()->update(['content' => 'Edited']);
        $this->assertSame('12:00', $card->fresh()->updated_at->format('H:i'));
    }

    public function test_edit_updates_timestamp_and_response_but_read_does_not(): void
    {
        $card = $this->card();
        $this->assertSame('10:00', $card->fresh()->updated_at->format('H:i'));
        $response = (new CrmCardsController)->quickUpdate(Request::create('/', 'PATCH', ['title' => 'Edited', 'stage_id' => 1]), $card);
        $this->assertSame('11:00', $card->fresh()->updated_at->format('H:i'));
        $this->assertSame('08/09/2026 11:00', $response->getData(true)['card']['updated_at_html']);
        Event::assertNotDispatched(CardStageChanged::class);
    }

    public function test_move_still_updates_timestamp_and_dispatches_stage_event(): void
    {
        $card = $this->card();
        $response = (new CrmKanbanController)->move(Request::create('/', 'PATCH', ['stage_id' => 2]), $card);
        $this->assertSame('11:00', $card->fresh()->updated_at->format('H:i'));
        $this->assertSame('08/09/2026 11:00', $response->getData(true)['card']['updated_at_html']);
        Event::assertDispatched(CardStageChanged::class, fn ($event) => $event->fromStageId === 1 && $event->toStageId === 2);
    }
}
