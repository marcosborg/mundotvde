<?php

namespace App\Services\Inspections;

use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\InspectionStepState;
use App\Models\User;
use Illuminate\Support\Carbon;

class InspectionRoutineSchedulerService
{
    public function __construct(private InspectionSequenceService $sequence)
    {
    }

    public function run(bool $dryRun = false, ?InspectionSchedule $singleSchedule = null): array
    {
        $query = InspectionSchedule::query()->where('is_active', true);

        if ($singleSchedule) {
            $query->where('id', $singleSchedule->id);
        } else {
            $query->where(function ($q) {
                $q->whereNull('next_run_at')->orWhere('next_run_at', '<=', now());
            });
        }

        $schedules = $query->with(['vehicle', 'driver'])->get();

        $created = 0;
        $skipped = 0;
        $messages = [];

        foreach ($schedules as $schedule) {
            try {
                $skipReason = $this->canGenerateForSchedule($schedule);
                if ($skipReason) {
                    $skipped++;
                    $messages[] = "schedule {$schedule->id}: {$skipReason}";
                    continue;
                }

                $previous = $this->sequence->validateCreationSequence('routine', (int) $schedule->vehicle_id);

                if (!$dryRun) {
                    $creator = $this->resolveCreator($schedule);
                    $inspection = Inspection::create([
                        'type' => 'routine',
                        'vehicle_id' => $schedule->vehicle_id,
                        'driver_id' => $schedule->driver_id,
                        'created_by_user_id' => $creator->id,
                        'responsible_user_id' => $creator->id,
                        'status' => 'in_progress',
                        'current_step' => 1,
                        'previous_inspection_id' => $previous?->id,
                        'started_at' => now(),
                    ]);

                    for ($step = 1; $step <= 10; $step++) {
                        InspectionStepState::create([
                            'inspection_id' => $inspection->id,
                            'step' => $step,
                        ]);
                    }

                    $this->sequence->cloneOpenDamages($inspection);

                    $schedule->update([
                        'last_run_at' => now(),
                        'next_run_at' => $this->nextRunAt($schedule),
                    ]);
                }

                $created++;
                $messages[] = "schedule {$schedule->id}: routine generated";
            } catch (\Throwable $e) {
                $skipped++;
                $messages[] = "schedule {$schedule->id}: " . $e->getMessage();
            }
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
            'messages' => $messages,
        ];
    }

    private function canGenerateForSchedule(InspectionSchedule $schedule): ?string
    {
        if (!$schedule->vehicle_id) {
            return 'vehicle missing';
        }

        $hasOpenRoutine = Inspection::query()
            ->where('vehicle_id', $schedule->vehicle_id)
            ->where('type', 'routine')
            ->whereIn('status', ['draft', 'in_progress', 'ready_to_sign'])
            ->exists();

        if ($hasOpenRoutine) {
            return 'existing open routine';
        }

        return null;
    }

    private function nextRunAt(InspectionSchedule $schedule): Carbon
    {
        $base = $schedule->next_run_at ?: now();
        return Carbon::parse($base)->addDays((int) $schedule->frequency_days);
    }

    private function resolveCreator(InspectionSchedule $schedule): User
    {
        if ($schedule->created_by_user_id) {
            $user = User::find($schedule->created_by_user_id);
            if ($user) {
                return $user;
            }
        }

        $fallback = User::query()->orderBy('id')->first();
        if (!$fallback) {
            throw new \RuntimeException('No users available to assign created_by_user_id');
        }

        return $fallback;
    }
}
