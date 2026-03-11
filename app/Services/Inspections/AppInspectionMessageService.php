<?php

namespace App\Services\Inspections;

use App\Models\AppMessage;
use App\Models\Driver;
use App\Models\Inspection;
use App\Models\InspectionSchedule;

class AppInspectionMessageService
{
    public function notifyRoutineGenerated(InspectionSchedule $schedule, Inspection $inspection): void
    {
        $targetUserId = $this->resolveTargetUserId($schedule, $inspection);
        if (!$targetUserId) {
            return;
        }

        $row = AppMessage::firstOrNew(['user_id' => $targetUserId]);
        $messages = $this->decodeMessages($row->messages);

        $messages[] = [
            'role' => 'assistant',
            'type' => 'inspection_routine',
            'title' => 'Nova inspeção de rotina',
            'content' => 'Foi gerada a inspeção #' . $inspection->id . ' para a viatura ' . ($schedule->vehicle->license_plate ?? '-') . '.',
            'inspection_id' => $inspection->id,
            'vehicle_id' => $inspection->vehicle_id,
            'created_at' => now()->toIso8601String(),
        ];

        $row->messages = json_encode(array_slice($messages, -50), JSON_UNESCAPED_UNICODE);
        $row->save();
    }

    private function resolveTargetUserId(InspectionSchedule $schedule, Inspection $inspection): ?int
    {
        if ($inspection->driver_id) {
            $driver = Driver::query()->find($inspection->driver_id);
            if ($driver?->user_id) {
                return (int) $driver->user_id;
            }
        }

        if ($schedule->driver?->user_id) {
            return (int) $schedule->driver->user_id;
        }

        if ($schedule->vehicle?->driver?->user_id) {
            return (int) $schedule->vehicle->driver->user_id;
        }

        return null;
    }

    private function decodeMessages($raw): array
    {
        if (!is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
