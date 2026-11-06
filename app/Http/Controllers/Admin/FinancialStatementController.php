<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLaunch;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WeeklyStatements\WeeklyStatementSnapshotService;

class FinancialStatementController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('financial_statement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver = Driver::where('user_id', auth()->user()->id)->first();

        abort_if(!$driver, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityLaunches = ActivityLaunch::where([
            'driver_id' => $driver->id,
            'send' => 1
        ])
            ->with([
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->orderBy('id', 'desc')
            ->get();

        foreach ($activityLaunches as $activityLaunch) {
            $sub = [
                $activityLaunch->rent,
                $activityLaunch->management,
                $activityLaunch->insurance,
                $activityLaunch->fuel,
                $activityLaunch->tolls,
                $activityLaunch->others
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $activityLaunch->total = $sum - $sub + $activityLaunch->refund;
            $activityLaunch->sum = $sum;
            $activityLaunch->sub = $sub;
        }

        return view('admin.financialStatements.index')->with([
            'activityLaunches' => $activityLaunches
        ]);
    }

    public function pdf(Request $request)
    {
        abort_if(Gate::denies('financial_statement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $driver = Driver::where('user_id', auth()->user()->id)->first()->load('activity_launches');

        abort_if(!$driver, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityLaunch = ActivityLaunch::where([
            'id' => $request->id,
        ])
            ->with([
                'driver.operation',
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->first();

        abort_if(!$activityLaunch, Response::HTTP_NOT_FOUND, '404 Not Found');

        if ($archivedPdf = $this->archivedPdfResponse($activityLaunch, (bool) $request->stream)) {
            return $archivedPdf;
        }

        $driver_id = $activityLaunch->driver_id;

        $driver = Driver::find($driver_id)->load('activity_launches');

        $sub = [
            $activityLaunch->rent,
            $activityLaunch->management,
            $activityLaunch->insurance,
            $activityLaunch->fuel,
            $activityLaunch->tolls,
            $activityLaunch->garage,
            $activityLaunch->management_fee,
            $activityLaunch->others
        ];
        $sub = array_sum($sub);
        $net = [];
        $taxes = [];
        $totals = [];
        foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
            $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            $net[] = $activityPerOperator->net;
            $taxes[] = $activityPerOperator->taxes;
        }
        $sum = array_sum($sum);
        $net = array_sum($net);
        $taxes = array_sum($taxes);
        $activityLaunch->total = $sum - $sub + $activityLaunch->refund;
        $activityLaunch->sum = $sum;
        $activityLaunch->sub = $sub;
        $activityLaunch->net = $net;
        $activityLaunch->taxes = $taxes;

        $openActivityLaunches = ActivityLaunch::where('driver_id', $driver_id)
            ->openStatements()
            ->with([
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($openActivityLaunches as $openActivityLaunch) {
            $sub = [
                $openActivityLaunch->rent,
                $openActivityLaunch->management,
                $openActivityLaunch->insurance,
                $openActivityLaunch->fuel,
                $openActivityLaunch->tolls,
                $openActivityLaunch->garage,
                $openActivityLaunch->management_fee,
                $openActivityLaunch->others
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($openActivityLaunch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $openActivityLaunch->total = $sum - $sub + $openActivityLaunch->refund;
            $openActivityLaunch->sum = $sum;
            $openActivityLaunch->sub = $sub;
        }

        $balance = $openActivityLaunches->sum('total');

        $pdf = Pdf::loadView('admin.financialStatements.pdf', [
            'activityLaunch' => $activityLaunch,
            'openActivityLaunches' => $openActivityLaunches,
            'balance' => $balance,
        ])->setOption([
            'isRemoteEnabled' => true,
        ]);

        if ($request->stream) {
            return $pdf->stream();
        } else {
            return $pdf->download($activityLaunch->created_at . '.pdf');
        }
        
    }

    private function archivedPdfResponse(ActivityLaunch $activityLaunch, bool $stream)
    {
        if (!$activityLaunch->week || !$activityLaunch->week->isClosed()) {
            return null;
        }

        $snapshot = $activityLaunch->statementSnapshots()
            ->orderByDesc('version')
            ->first();

        if (!$snapshot || !$snapshot->data_json) {
            return null;
        }

        $statement = app(WeeklyStatementSnapshotService::class)
            ->refreshOpenMovements($snapshot->data_json, $activityLaunch->driver_id);
        $pdf = Pdf::loadView('admin.financialStatements.snapshot_pdf', [
            'statement' => $statement,
        ])->setOption([
            'isRemoteEnabled' => true,
        ]);
        $fileName = sprintf('extrato_%d_v%d.pdf', $activityLaunch->id, $snapshot->version);

        return $stream
            ? $pdf->stream($fileName)
            : $pdf->download($fileName);
    }

}
