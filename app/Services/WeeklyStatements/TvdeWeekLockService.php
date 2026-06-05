<?php

namespace App\Services\WeeklyStatements;

use App\Models\TvdeWeek;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TvdeWeekLockService
{
    private WeeklyStatementSnapshotService $snapshotService;

    public function __construct(WeeklyStatementSnapshotService $snapshotService)
    {
        $this->snapshotService = $snapshotService;
    }

    public function close(TvdeWeek $week, User $user): TvdeWeek
    {
        return DB::transaction(function () use ($week, $user) {
            $week = TvdeWeek::whereKey($week->id)->lockForUpdate()->firstOrFail();

            if ($week->isClosed()) {
                throw ValidationException::withMessages([
                    'week' => 'Esta semana ja esta fechada.',
                ]);
            }

            $activityLaunches = $week->activityLaunches()
                ->with([
                    'driver.operation',
                    'driver.card',
                    'week.tvde_month.year',
                    'activityPerOperators.tvde_operator',
                ])
                ->get();

            if ($activityLaunches->isEmpty()) {
                throw ValidationException::withMessages([
                    'week' => 'Nao e possivel fechar uma semana sem atividades lancadas.',
                ]);
            }

            foreach ($activityLaunches as $activityLaunch) {
                $this->snapshotService->createSnapshot($activityLaunch, $user);
            }

            $week->update([
                'status' => 'closed',
                'locked_at' => now(),
                'locked_by' => $user->id,
                'reopened_at' => null,
                'reopened_by' => null,
            ]);

            return $week;
        });
    }

    public function reopen(TvdeWeek $week, User $user): TvdeWeek
    {
        return DB::transaction(function () use ($week, $user) {
            $week = TvdeWeek::whereKey($week->id)->lockForUpdate()->firstOrFail();

            if ($week->isOpen()) {
                throw ValidationException::withMessages([
                    'week' => 'Esta semana ja esta aberta.',
                ]);
            }

            $week->update([
                'status' => 'open',
                'reopened_at' => now(),
                'reopened_by' => $user->id,
            ]);

            return $week;
        });
    }
}
