<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\DriverInspectionController;
use App\Models\Driver;
use App\Models\InspectionAssignment;
use App\Models\InspectionPhoto;
use App\Models\InspectionTemplate;
use App\Models\InspectionSubmission;
use App\Models\User;
use App\Models\VehicleItem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\CreatesInspectionTestSchema;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class DriverInspectionSecurityTest extends TestCase
{
    use CreatesInspectionTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootInspectionTestDatabase();
    }

    public function test_driver_cannot_start_other_driver_assignment(): void
    {
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Owner', 'email' => 'owner@example.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Intruder', 'email' => 'intruder@example.com', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $owner = User::findOrFail(1);
        $intruder = User::findOrFail(2);

        $driver = Driver::create(['user_id' => $owner->id, 'name' => 'Owner Driver']);
        $vehicle = VehicleItem::create(['driver_id' => $driver->id, 'license_plate' => '11-AA-11']);
        $template = InspectionTemplate::create([
            'name' => 'Template',
            'performer_type' => 'driver',
            'required_photo_angles_json' => ['front', 'rear', 'left', 'right'],
            'is_active' => true,
        ]);

        $assignment = InspectionAssignment::create([
            'vehicle_id' => $vehicle->id,
            'template_id' => $template->id,
            'assigned_user_id' => $owner->id,
            'period_start' => now()->subDay(),
            'period_end' => now(),
            'due_at' => now()->addDay(),
            'grace_hours' => 24,
            'status' => 'pending',
            'generated_by' => 'manual',
        ]);

        $request = Request::create('/api/driver/inspections/' . $assignment->id . '/start', 'POST');
        $request->setUserResolver(fn() => $intruder);

        $controller = app(DriverInspectionController::class);

        try {
            $controller->start($request, $assignment);
            $this->fail('Expected forbidden exception.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_upload_validation_rejects_invalid_file_type(): void
    {
        DB::table('users')->insert([
            'id' => 10,
            'name' => 'Driver',
            'email' => 'driver10@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::findOrFail(10);
        $driver = Driver::create(['user_id' => $user->id, 'name' => 'Driver']);
        $vehicle = VehicleItem::create(['driver_id' => $driver->id, 'license_plate' => '22-AA-22']);
        $template = InspectionTemplate::create([
            'name' => 'Template',
            'performer_type' => 'driver',
            'required_photo_angles_json' => ['front', 'rear', 'left', 'right'],
            'is_active' => true,
        ]);

        $assignment = InspectionAssignment::create([
            'vehicle_id' => $vehicle->id,
            'template_id' => $template->id,
            'assigned_user_id' => $user->id,
            'period_start' => now()->subDay(),
            'period_end' => now(),
            'due_at' => now()->addDay(),
            'grace_hours' => 24,
            'status' => 'pending',
            'generated_by' => 'manual',
        ]);

        InspectionSubmission::create([
            'assignment_id' => $assignment->id,
            'started_at' => now(),
            'created_by_user_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->create('invalid.pdf', 50, 'application/pdf');
        $request = Request::create('/api/driver/inspections/' . $assignment->id . '/photo', 'POST', [
            'angle' => 'front',
            'captured_at' => Carbon::now()->toDateTimeString(),
        ], [], ['file' => $file]);
        $request->setUserResolver(fn() => $user);

        $controller = app(DriverInspectionController::class);

        $this->expectException(ValidationException::class);
        $controller->photo($request, $assignment);
    }

    public function test_submit_requires_all_mandatory_angles(): void
    {
        DB::table('users')->insert([
            'id' => 20,
            'name' => 'Driver',
            'email' => 'driver20@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::findOrFail(20);
        $driver = Driver::create(['user_id' => $user->id, 'name' => 'Driver']);
        $vehicle = VehicleItem::create(['driver_id' => $driver->id, 'license_plate' => '33-AA-33']);
        $template = InspectionTemplate::create([
            'name' => 'Template',
            'performer_type' => 'driver',
            'required_photo_angles_json' => ['front', 'rear', 'left', 'right'],
            'is_active' => true,
        ]);

        $assignment = InspectionAssignment::create([
            'vehicle_id' => $vehicle->id,
            'template_id' => $template->id,
            'assigned_user_id' => $user->id,
            'period_start' => now()->subDay(),
            'period_end' => now(),
            'due_at' => now()->addDay(),
            'grace_hours' => 24,
            'status' => 'in_progress',
            'generated_by' => 'manual',
        ]);

        $submission = InspectionSubmission::create([
            'assignment_id' => $assignment->id,
            'started_at' => now(),
            'created_by_user_id' => $user->id,
        ]);

        InspectionPhoto::create([
            'submission_id' => $submission->id,
            'angle' => 'front',
            'file_disk' => 'local',
            'file_path' => 'test/front.jpg',
            'mime' => 'image/jpeg',
            'size' => 1024,
            'sha256' => str_repeat('a', 64),
            'uploaded_at' => now(),
        ]);

        $request = Request::create('/api/driver/inspections/' . $assignment->id . '/submit', 'POST', [
            'summary_notes' => 'Teste',
            'defects' => [],
        ]);
        $request->setUserResolver(fn() => $user);

        $controller = app(DriverInspectionController::class);
        $response = $controller->submit($request, $assignment);
        $payload = $response->getData(true);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('inspection_missing_required_angles', $payload['code']);
        $this->assertContains('rear', $payload['missing_angles']);
    }
}
