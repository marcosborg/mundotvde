<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLaunch;
use App\Notifications\ActivityLaunchesPaid;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $activityLaunches = ActivityLaunch::limit(200)
            ->with([
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

        $paid = $activityLaunches->where('paid', 1);
        $notPaid = $activityLaunches->where('paid', 0);

        return view('partials.paymentsToDrivers')->with([
            'paid' => $paid,
            'notPaid' => $notPaid
        ]);
    }

    public function confirmPay(Request $request)
    {

        $activityLaunches = json_decode($request->activityLaunches);
        $activityLaunchesByDriver = collect();
        //Atualizar ActivityLaunches
        foreach ($activityLaunches as $activity_launch_id) {
            $activityLaunch = ActivityLaunch::where('id', $activity_launch_id)
                ->with([
                    'driver.user',
                    'activityPerOperators',
                    'week',
                ])
                ->first();
            $activityLaunch->paid = true;
            $activityLaunch->save();
            $activityLaunchesByDriver->add(collect([
                'driver_id' => $activityLaunch->driver->id,
                'user' => $activityLaunch->driver->user,
                'activityLaunch' => $activityLaunch,
            ]));
        }
        //Identificar Drivers
        $activityLaunchesByDriver = $activityLaunchesByDriver->groupBy('driver_id');
        foreach ($activityLaunchesByDriver as $activityLaunches) {
            $data = [];
            //Fazer calculos
            foreach ($activityLaunches as $value) {
                $sub = [
                    $value['activityLaunch']['rent'],
                    $value['activityLaunch']['management'],
                    $value['activityLaunch']['insurance'],
                    $value['activityLaunch']['fuel'],
                    $value['activityLaunch']['tolls'],
                    $value['activityLaunch']['others'],
                ];
                $sub = array_sum($sub);
                $sum = [];
                foreach ($value['activityLaunch']['activityPerOperators'] as $v) {
                    $sum[] = $v['net'] - $v['taxes'];
                }
                $sum = array_sum($sum);
                $total = $sum - $sub + $value['activityLaunch']['refund'];
                $data[] = [
                    'number' => $value['activityLaunch']['week']['number'],
                    'start_date' => Carbon::parse($value['activityLaunch']['week']['start_date'])->format('d-m-Y'),
                    'end_date' => Carbon::parse($value['activityLaunch']['week']['end_date'])->format('d-m-Y'),
                    'sum' => $sum,
                    'sub' => $sub,
                    'total' => $total,
                    'refund' => floatval($value['activityLaunch']['refund']),
                ];
            }
            //Enviar email
            $user = $activityLaunches[0]['user'];
            $user->notify(new ActivityLaunchesPaid($data));
        }

    }


}