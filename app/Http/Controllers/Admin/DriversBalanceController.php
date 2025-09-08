<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriversBalanceController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('drivers_balance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::with([
            'activity_launches.activityPerOperators'
        ])
            ->whereHas('activity_launches', function ($query) {
                $query->where('paid', 0);
            })
            ->get();

        foreach ($drivers as $driver) {
            $balance = [];
            foreach ($driver->activity_launches as $activity_launch) {
                $sub = [
                    $activity_launch->rent,
                    $activity_launch->management,
                    $activity_launch->insurance,
                    $activity_launch->fuel,
                    $activity_launch->garage,
                    $activity_launch->management_fee,
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
                    $balance[] = $result;
                }
            }
            $balance = array_sum($balance);
            $driver->balance = $balance;
        }

        return view('admin.driversBalances.index')->with([
            'drivers' => $drivers,
        ]);
    }

}