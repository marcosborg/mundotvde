<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLaunch;
use App\Notifications\ActivityLaunchesSend;
use App\Notifications\PaidReceipt;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Driver;

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

        $activityLaunches = ActivityLaunch::with([
            'week',
            'driver',
            'activityPerOperators.tvde_operator',
        ])
            ->get();

        foreach ($activityLaunches as $activityLaunch) {
            $sub = [
                $activityLaunch->rent,
                $activityLaunch->management,
                $activityLaunch->insurance,
                $activityLaunch->fuel,
                $activityLaunch->garage,
                $activityLaunch->tolls,
                $activityLaunch->others,
            ];
            $sub = array_sum($sub);
            $sum = [];
            foreach ($activityLaunch->activityPerOperators as $activityPerOperator) {
                $sum[] = $activityPerOperator->net - $activityPerOperator->taxes;
            }
            $sum = array_sum($sum);
            $total = $sum - $sub + $activityLaunch->refund;
            $activityLaunch->total = $total;
        }

        $send = $activityLaunches->where('send', 1);
        $notSend = $activityLaunches->where('send', 0);

        return view('partials.paymentsToDrivers')->with([
            'send' => $send,
            'notSend' => $notSend
        ]);
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
        $activityLaunch = ActivityLaunch::with([
            'driver.user'
        ])->where('id', $request->id)->first();
        $activityLaunch->paid = 1;
        $activityLaunch->save();

        //SEND EMAIL
        $activityLaunch->driver->user->notify(new PaidReceipt());
    }
}
