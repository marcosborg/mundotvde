<?php

namespace App\Services\WeeklyStatements;

use App\Models\ActivityLaunch;
use App\Models\Driver;
use App\Models\User;
use App\Models\VehicleItem;
use App\Models\WeeklyStatementSnapshot;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class WeeklyStatementSnapshotService
{
    public function createSnapshot(ActivityLaunch $activityLaunch, User $user): WeeklyStatementSnapshot
    {
        $activityLaunch->loadMissing([
            'driver.operation',
            'driver.card',
            'week.tvde_month.year',
            'activityPerOperators.tvde_operator',
        ]);

        $version = ((int) WeeklyStatementSnapshot::where('activity_launch_id', $activityLaunch->id)->max('version')) + 1;
        $data = $this->buildData($activityLaunch);

        $pdf = Pdf::loadView('admin.financialStatements.snapshot_pdf', [
            'statement' => $data,
        ])->setOption([
            'isRemoteEnabled' => true,
        ]);

        $path = sprintf(
            'weekly-statements/week_%d/activity_launch_%d_v%d.pdf',
            $activityLaunch->week_id,
            $activityLaunch->id,
            $version
        );

        $content = $pdf->output();
        Storage::disk('public')->put($path, $content);

        return WeeklyStatementSnapshot::create([
            'tvde_week_id' => $activityLaunch->week_id,
            'activity_launch_id' => $activityLaunch->id,
            'driver_id' => $activityLaunch->driver_id,
            'vehicle_id' => $this->findVehicleId($activityLaunch->driver),
            'vehicle_plate' => $data['vehicle']['license_plate'] ?? null,
            'driver_name' => $data['driver']['name'] ?? null,
            'data_json' => $data,
            'pdf_path' => $path,
            'pdf_hash' => hash('sha256', $content),
            'version' => $version,
            'locked_at' => now(),
            'locked_by' => $user->id,
        ]);
    }

    public function buildData(ActivityLaunch $activityLaunch): array
    {
        $driver = $activityLaunch->driver;
        $week = $activityLaunch->week;
        $operators = $activityLaunch->activityPerOperators;

        $operatorRows = $operators->map(function ($operator) {
            return [
                'id' => $operator->id,
                'tvde_operator_id' => $operator->tvde_operator_id,
                'name' => optional($operator->tvde_operator)->name,
                'gross' => (float) $operator->gross,
                'net' => (float) $operator->net,
                'taxes' => (float) $operator->taxes,
                'net_after_taxes' => (float) $operator->net - (float) $operator->taxes,
            ];
        })->values();

        $net = (float) $operatorRows->sum('net');
        $taxes = (float) $operatorRows->sum('taxes');
        $incomeAfterTaxes = (float) $operatorRows->sum('net_after_taxes');
        $expenses = $this->expenses($activityLaunch);
        $sub = (float) array_sum($expenses);
        $total = $incomeAfterTaxes - $sub + (float) $activityLaunch->refund;

        $movements = $this->buildLast60Days($activityLaunch->driver_id);
        $balance = $this->buildOpenBalance($activityLaunch->driver_id);

        return [
            'activity_launch' => [
                'id' => $activityLaunch->id,
                'created_at' => optional($activityLaunch->created_at)->format('Y-m-d H:i:s'),
                'send' => (bool) $activityLaunch->send,
                'paid' => (bool) $activityLaunch->paid,
                'initial_kilometers' => $activityLaunch->initial_kilometers,
                'final_kilometers' => $activityLaunch->final_kilometers,
            ],
            'week' => [
                'id' => $week->id,
                'number' => $week->number,
                'start_date' => $this->dateValue($week->getRawOriginal('start_date') ?: $week->start_date),
                'end_date' => $this->dateValue($week->getRawOriginal('end_date') ?: $week->end_date),
                'month' => optional($week->tvde_month)->name,
                'year' => optional(optional($week->tvde_month)->year)->name,
            ],
            'driver' => [
                'id' => optional($driver)->id,
                'name' => optional($driver)->name,
                'code' => optional($driver)->code,
                'payment_vat' => optional($driver)->payment_vat,
                'iban' => optional($driver)->iban,
                'operation' => optional(optional($driver)->operation)->name,
                'card' => optional(optional($driver)->card)->code,
            ],
            'vehicle' => [
                'license_plate' => optional($driver)->license_plate,
                'brand' => optional($driver)->brand,
                'model' => optional($driver)->model,
                'start_date' => $this->dateValue($week->getRawOriginal('start_date') ?: $week->start_date),
                'end_date' => $this->dateValue($week->getRawOriginal('end_date') ?: $week->end_date),
                'initial_kilometers' => $activityLaunch->initial_kilometers,
                'final_kilometers' => $activityLaunch->final_kilometers,
                'total_kilometers' => ((int) $activityLaunch->final_kilometers) - ((int) $activityLaunch->initial_kilometers),
            ],
            'amounts' => [
                'operators' => $operatorRows->all(),
                'net' => $net,
                'taxes' => $taxes,
                'income_after_taxes' => $incomeAfterTaxes,
                'expenses' => $expenses,
                'sub' => $sub,
                'refund' => (float) $activityLaunch->refund,
                'total' => $total,
                'balance' => $balance,
                'movements_60_days' => $movements,
            ],
            'company' => [
                'name' => 'Mundo TVDE',
                'address' => 'Praceta da Tabaqueira 2A 1950-256 Lisboa',
                'email' => 'geral@mundotvde.pt',
                'website' => 'www.mundotvde.pt',
                'logo_url' => 'https://mundotvde.pt/assets/website/img/logo.png',
            ],
            'snapshot' => [
                'generated_at' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    private function expenses(ActivityLaunch $activityLaunch): array
    {
        return [
            'rent' => (float) $activityLaunch->rent,
            'management' => (float) $activityLaunch->management,
            'insurance' => (float) $activityLaunch->insurance,
            'fuel' => (float) $activityLaunch->fuel,
            'tolls' => (float) $activityLaunch->tolls,
            'garage' => (float) $activityLaunch->garage,
            'management_fee' => (float) $activityLaunch->management_fee,
            'others' => (float) $activityLaunch->others,
        ];
    }

    private function buildLast60Days(?int $driverId): array
    {
        if (!$driverId) {
            return [];
        }

        $budget = 0;

        return ActivityLaunch::where('driver_id', $driverId)
            ->where('send', 1)
            ->whereDate('created_at', '>=', now()->subDays(60)->format('Y-m-d H:i:s'))
            ->with('activityPerOperators.tvde_operator')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function (ActivityLaunch $launch) use (&$budget) {
                $total = $this->calculateTotal($launch);
                if (!$launch->paid) {
                    $budget += $total;
                }

                return [
                    'id' => $launch->id,
                    'date' => optional($launch->created_at)->format('Y-m-d'),
                    'paid' => (bool) $launch->paid,
                    'status' => $launch->paid ? 'Pago' : 'Em saldo',
                    'total' => $total,
                    'balance' => $budget,
                ];
            })
            ->values()
            ->all();
    }

    private function buildOpenBalance(?int $driverId): float
    {
        if (!$driverId) {
            return 0;
        }

        return (float) ActivityLaunch::where('driver_id', $driverId)
            ->with('activityPerOperators')
            ->get()
            ->sum(function (ActivityLaunch $launch) {
                return $launch->paid ? 0 : $this->calculateTotal($launch);
            });
    }

    private function calculateTotal(ActivityLaunch $launch): float
    {
        $income = $launch->activityPerOperators->sum(function ($operator) {
            return (float) $operator->net - (float) $operator->taxes;
        });

        return $income - array_sum($this->expenses($launch)) + (float) $launch->refund;
    }

    private function findVehicleId(?Driver $driver): ?int
    {
        if (!$driver || !$driver->license_plate) {
            return null;
        }

        return optional(VehicleItem::where('license_plate', $driver->license_plate)->first())->id;
    }

    private function dateValue($value): ?string
    {
        return $value ? (string) $value : null;
    }
}
