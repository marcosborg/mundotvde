<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLaunch;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $driver = Driver::where('user_id', auth()->user()->id)->first();

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

        $driver_id = $activityLaunch->driver_id;

        $sub = [
            $activityLaunch->rent,
            $activityLaunch->management,
            $activityLaunch->insurance,
            $activityLaunch->fuel,
            $activityLaunch->tolls,
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

        //LAST 60 DAYS
        $activityLaunches60 = ActivityLaunch::where([
            'driver_id' => $driver_id,
            'send' => 1
        ])
            ->whereDate('created_at', '>=', now()->subDays(60)->format('Y-m-d H:i:s'))
            ->with([
                'week.tvde_month.year',
                'activityPerOperators.tvde_operator',
            ])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($activityLaunches60 as $activityLaunch60) {
            $sub = [
                $activityLaunch60->rent,
                $activityLaunch60->management,
                $activityLaunch60->insurance,
                $activityLaunch60->fuel,
                $activityLaunch60->tolls,
                $activityLaunch60->others
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($activityLaunch60->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $activityLaunch60->total = $sum - $sub + $activityLaunch60->refund;
            $activityLaunch60->sum = $sum;
            $activityLaunch60->sub = $sub;
        }

        $pdf = Pdf::loadView('admin.financialStatements.pdf', [
            'activityLaunch' => $activityLaunch,
            'activityLaunches60' => $activityLaunches60,
        ])->setOption([
            'isRemoteEnabled' => true,
        ]);

        if ($request->stream) {
            return $pdf->stream();
        } else {
            return $pdf->download($activityLaunch->created_at . '.pdf');
        }
        
    }

}