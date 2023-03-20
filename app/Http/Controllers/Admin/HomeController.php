<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLaunch;
use App\Models\Driver;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index()
    {

        $driver = Driver::where('user_id', auth()->user()->id)->first();

        abort_if(!$driver, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityLaunches = ActivityLaunch::where([
            'driver_id' => $driver->id,
            'paid' => 1
        ])
            ->with([
                'week',
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

        return view('home')->with([
            'activityLaunches' => $activityLaunches
        ]);
    }
}