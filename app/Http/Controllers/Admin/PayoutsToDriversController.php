<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLaunch;
use App\Notifications\ActivityLaunchesSend;
use App\Notifications\PaidReceipt;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Driver;
use Yajra\DataTables\Facades\DataTables;

class PayoutsToDriversController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('payouts_to_driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.payoutsToDrivers.index');
    }

    public function ajax()
    {
        abort_if(Gate::denies('payouts_to_driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $notSend = ActivityLaunch::with([
            'week',
            'driver.operation',
            'activityPerOperators',
        ])
            ->where('send', 0)
            ->get();

        foreach ($notSend as $activityLaunch) {
            $activityLaunch->total = $this->calculateTotal($activityLaunch);
        }

        return view('partials.paymentsToDrivers')->with([
            'notSend' => $notSend
        ]);
    }

    public function send(Request $request)
    {
        abort_if(Gate::denies('payouts_to_driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ActivityLaunch::query()
            ->where('activity_launches.send', 1)
            ->leftJoin('tvde_weeks', 'activity_launches.week_id', '=', 'tvde_weeks.id')
            ->leftJoin('drivers', 'activity_launches.driver_id', '=', 'drivers.id')
            ->leftJoin('operations', 'drivers.operation_id', '=', 'operations.id')
            ->with(['activityPerOperators'])
            ->select([
                'activity_launches.*',
                'tvde_weeks.start_date as week_start_date',
                'tvde_weeks.end_date as week_end_date',
                'tvde_weeks.number as week_number',
                'drivers.name as driver_name',
                'drivers.code as driver_code',
                'operations.name as driver_operation',
                'drivers.license_plate as driver_license_plate',
                'drivers.email as driver_email',
            ]);

        $table = DataTables::of($query);

        $table->editColumn('id', function ($row) {
            return $row->id ?: '';
        });

        $table->editColumn('driver_name', function ($row) {
            return $row->driver_name ?: '';
        });

        $table->editColumn('driver_code', function ($row) {
            return $row->driver_code ?: '';
        });

        $table->editColumn('driver_operation', function ($row) {
            return $row->driver_operation ?: '';
        });

        $table->editColumn('driver_license_plate', function ($row) {
            return $row->driver_license_plate ?: '';
        });

        $table->editColumn('driver_email', function ($row) {
            return $row->driver_email ?: '';
        });

        $table->editColumn('week_number', function ($row) {
            return $row->week_number ?: '';
        });

        $table->editColumn('week_start_date', function ($row) {
            return $row->week_start_date ?: '';
        });

        $table->editColumn('week_end_date', function ($row) {
            return $row->week_end_date ?: '';
        });

        $table->addColumn('total', function ($row) {
            return number_format($this->calculateTotal($row), 2, ',', '.');
        });

        if (Gate::allows('pay_payout_access')) {
            $table->addColumn('pay_action', function ($row) {
                if ($row->paid) {
                    return '';
                }

                return '<button class="btn btn-success btn-sm" id="pay-' . $row->id . '" onclick="pay(' . $row->id . ')" type="button">Pagar</button>';
            });
        }

        $table->addColumn('statement', function ($row) {
            return '<a href="/admin/financial-statements/pdf/' . $row->id . '" class="btn btn-success btn-sm">Extrato</a>';
        });

        $rawColumns = ['statement'];

        if (Gate::allows('pay_payout_access')) {
            $rawColumns[] = 'pay_action';
        }

        $table->rawColumns($rawColumns);

        return $table->make(true);
    }

    public function confirmSend(Request $request)
    {

        $activityLaunches = json_decode($request->activityLaunches);
        foreach ($activityLaunches as $activity_launch_id) {
            $this->data($activity_launch_id);
        }
    }

    private function data($activity_launch_id)
    {
        $activityLaunche = ActivityLaunch::find($activity_launch_id)
            ->load([
                'week',
                'activityPerOperators.tvde_operator',
                'driver.user',
            ]);

        $activityLaunche->send = true;
        $activityLaunche->save();

        $sub = [
            $activityLaunche->rent,
            $activityLaunche->management,
            $activityLaunche->insurance,
            $activityLaunche->fuel,
            $activityLaunche->tolls,
            $activityLaunche->garage,
            $activityLaunche->management_fee,
            $activityLaunche->others,
        ];
        $taxes = [];
        $sub = array_sum($sub);
        $sum = [];
        $sum_net = [];
        $taxes = [];
        foreach ($activityLaunche->activityPerOperators as $activityPerOperator) {
            $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            $sum_net[] = $activityPerOperator->net;
            $taxes[] = $activityPerOperator->taxes;
        }
        $sum = array_sum($sum);
        $activityLaunche->balance = $this->getBalance($activityLaunche->driver_id);
        $activityLaunche->total = $sum - $sub + $activityLaunche->refund;
        $activityLaunche->sum = $sum;
        $activityLaunche->sub = $sub;
        $activityLaunche->sum_net = array_sum($sum_net);
        $activityLaunche->total_after_refund = $activityLaunche->sum_net + $activityLaunche->refund;
        $activityLaunche->taxes = array_sum($taxes);
        $activityLaunche->total_descount_after_taxes = $activityLaunche->sub + $activityLaunche->taxes;

        $activityLaunche->driver->user->notify(new ActivityLaunchesSend($activityLaunche));
    }

    private function getBalance($driver_id)
    {

        $driver = Driver::find($driver_id)
            ->load([
                'activity_launches.activityPerOperators'
            ]);

        $balance = 0;

        foreach ($driver->activity_launches as $activity_launch) {
            $sub = [
                $activity_launch->rent,
                $activity_launch->management,
                $activity_launch->insurance,
                $activity_launch->fuel,
                $activity_launch->tolls,
                $activity_launch->garage,
                $activity_launch->management_fee,
                $activity_launch->others
            ];
            $sub = array_sum($sub);

            $sum = [];
            foreach ($activity_launch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);

            $result = $sum - $sub + $activity_launch->refund;

            if (!$activity_launch->paid) {
                $balance += $result;
            }
        }

        return $balance;
    }

    public function pay(Request $request)
    {
        abort_if(Gate::denies('pay_payout_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityLaunch = ActivityLaunch::with([
            'driver.user'
        ])->where('id', $request->id)->first();
        $activityLaunch->paid = 1;
        $activityLaunch->save();

        //SEND EMAIL
        $activityLaunch->driver->user->notify(new PaidReceipt());
    }

    private function calculateTotal(ActivityLaunch $activityLaunch): float
    {
        $sub = [
            $activityLaunch->rent,
            $activityLaunch->management,
            $activityLaunch->insurance,
            $activityLaunch->fuel,
            $activityLaunch->garage,
            $activityLaunch->management_fee,
            $activityLaunch->tolls,
            $activityLaunch->others,
        ];

        $sub = array_sum(array_map(function ($value) {
            return $value ?: 0;
        }, $sub));

        $sum = $activityLaunch->activityPerOperators->sum(function ($activityPerOperator) {
            return ($activityPerOperator->net ?: 0) - ($activityPerOperator->taxes ?: 0);
        });

        return $sum - $sub + ($activityLaunch->refund ?: 0);
    }
}
