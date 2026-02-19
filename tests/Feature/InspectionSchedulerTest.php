<?php

namespace Tests\Feature;

use App\Console\Commands\GenerateInspectionAssignments;
use App\Models\Driver;
use App\Models\InspectionAssignment;
use App\Models\InspectionSchedule;
use App\Models\InspectionTemplate;
use App\Models\User;
use App\Models\VehicleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesInspectionTestSchema;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class InspectionSchedulerTest extends TestCase
{
    use CreatesInspectionTestSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootInspectionTestDatabase();
    }

    public function test_scheduler_generates_assignment_for_active_schedule(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Driver',
            'email' => 'driver@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = User::findOrFail(1);
        $driver = Driver::create(['user_id' => $user->id, 'name' => 'Driver 1']);
        $vehicle = VehicleItem::create(['driver_id' => $driver->id, 'license_plate' => '00-AA-00']);
        $template = InspectionTemplate::create([
            'name' => 'Template',
            'performer_type' => 'driver',
            'required_photo_angles_json' => ['front', 'rear', 'left', 'right'],
            'is_active' => true,
        ]);

        InspectionSchedule::create([
            'vehicle_id' => $vehicle->id,
            'template_id' => $template->id,
            'frequency_days' => 1,
            'due_time' => '09:00',
            'grace_hours' => 24,
            'is_active' => true,
        ]);

        Artisan::call('inspections:generate-assignments');

        $this->assertDatabaseCount('inspection_assignments', 1);
        $this->assertDatabaseHas('inspection_assignments', [
            'vehicle_id' => $vehicle->id,
            'template_id' => $template->id,
            'assigned_user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_scheduler_marks_assignment_as_overdue_after_grace(): void
    {
        $assignment = InspectionAssignment::create([
            'vehicle_id' => 1,
            'template_id' => 1,
            'assigned_user_id' => 1,
            'period_start' => Carbon::now()->subDays(3),
            'period_end' => Carbon::now()->subDays(2),
            'due_at' => Carbon::now()->subDays(2),
            'grace_hours' => 1,
            'status' => 'pending',
            'generated_by' => 'manual',
        ]);

        Artisan::call('inspections:generate-assignments');

        $this->assertDatabaseHas('inspection_assignments', [
            'id' => $assignment->id,
            'status' => 'overdue',
        ]);
    }
}
