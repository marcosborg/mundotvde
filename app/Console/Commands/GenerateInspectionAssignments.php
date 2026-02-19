<?php

namespace App\Console\Commands;

use App\Jobs\SendInspectionReminderJob;
use App\Models\InspectionAssignment;
use App\Models\InspectionSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateInspectionAssignments extends Command
{
    protected $signature = 'inspections:generate-assignments';
    protected $description = 'Generate inspection assignments from active schedules and mark overdue ones';

    public function handle(): int
    {
        $now = Carbon::now();

        InspectionSchedule::with(['vehicle.driver', 'template'])
            ->where('is_active', true)
            ->chunkById(200, function ($schedules) use ($now) {
                foreach ($schedules as $schedule) {
                    if (!$schedule->vehicle || !$schedule->vehicle->driver || !$schedule->vehicle->driver->user_id) {
                        continue;
                    }

                    $last = InspectionAssignment::where('vehicle_id', $schedule->vehicle_id)
                        ->where('template_id', $schedule->template_id)
                        ->orderByDesc('period_end')
                        ->first();

                    $periodStart = $last ? Carbon::parse($last->period_end)->copy() : $now->copy()->startOfDay();
                    if ($periodStart->lt($now->copy()->subDays($schedule->frequency_days * 2))) {
                        $periodStart = $now->copy()->startOfDay();
                    }

                    $periodEnd = $periodStart->copy()->addDays((int) $schedule->frequency_days);
                    $dueAt = Carbon::parse($periodEnd->format('Y-m-d') . ' ' . $schedule->due_time);

                    $existingOpen = InspectionAssignment::where('vehicle_id', $schedule->vehicle_id)
                        ->where('template_id', $schedule->template_id)
                        ->whereIn('status', ['pending', 'in_progress', 'submitted', 'rejected', 'overdue'])
                        ->where('period_end', '>=', $periodStart)
                        ->exists();

                    if (!$existingOpen && $dueAt->lte($now->copy()->addDays(2))) {
                        $assignment = InspectionAssignment::create([
                            'vehicle_id' => $schedule->vehicle_id,
                            'template_id' => $schedule->template_id,
                            'assigned_user_id' => $schedule->vehicle->driver->user_id,
                            'period_start' => $periodStart,
                            'period_end' => $periodEnd,
                            'due_at' => $dueAt,
                            'grace_hours' => (int) $schedule->grace_hours,
                            'reminder_policy_json' => $schedule->reminder_policy_json,
                            'status' => 'pending',
                            'generated_by' => 'scheduler',
                        ]);

                        SendInspectionReminderJob::dispatch($assignment->id, 'created');
                    }
                }
            });

        InspectionAssignment::with('template')
            ->whereIn('status', ['pending', 'in_progress', 'rejected'])
            ->chunkById(200, function ($assignments) use ($now) {
                foreach ($assignments as $assignment) {
                    $graceHours = is_numeric($assignment->grace_hours) ? (int) $assignment->grace_hours : 24;
                    if ($now->gt(Carbon::parse($assignment->due_at)->addHours($graceHours))) {
                        $assignment->update(['status' => 'overdue']);
                        SendInspectionReminderJob::dispatch($assignment->id, 'overdue');
                    }

                    $policy = (array) ($assignment->reminder_policy_json ?? []);
                    $hoursBefore = array_filter((array) ($policy['hours_before'] ?? [24, 2]), fn($v) => is_numeric($v));
                    $hoursOverdue = array_filter((array) ($policy['overdue_hours'] ?? [24]), fn($v) => is_numeric($v));

                    foreach ($hoursBefore as $hours) {
                        $triggerAt = Carbon::parse($assignment->due_at)->subHours((int) $hours);
                        if ($now->gte($triggerAt) && $now->lt($triggerAt->copy()->addHour())) {
                            $cacheKey = sprintf('inspection:reminder:before:%d:%d', $assignment->id, (int) $hours);
                            if (Cache::add($cacheKey, true, now()->addHours(2))) {
                                SendInspectionReminderJob::dispatch($assignment->id, 'due');
                            }
                        }
                    }

                    if ($assignment->status === 'overdue') {
                        foreach ($hoursOverdue as $hours) {
                            $triggerAt = Carbon::parse($assignment->due_at)->addHours($graceHours + (int) $hours);
                            if ($now->gte($triggerAt) && $now->lt($triggerAt->copy()->addHour())) {
                                $cacheKey = sprintf('inspection:reminder:overdue:%d:%d', $assignment->id, (int) $hours);
                                if (Cache::add($cacheKey, true, now()->addHours(2))) {
                                    SendInspectionReminderJob::dispatch($assignment->id, 'overdue');
                                }
                            }
                        }
                    }
                }
            });

        $this->info('Inspection assignments generated/updated successfully.');

        return self::SUCCESS;
    }
}

