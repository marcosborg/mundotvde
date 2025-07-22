<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLaunch;
use App\Models\ActivityPerOperator;
use App\Models\Driver;
use App\Models\TvdeYear;
use App\Models\TvdeActivity;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TvdeDriverManagementController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('tvde_driver_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.tvdeDriverManagements.index');
    }

    public function ajax()
    {
        $years = TvdeYear::with([
            'months.weeks.activityLaunches.driver',
            'months.weeks.activityLaunches.activityPerOperators.tvde_operator',
        ])
            ->orderBy('name')
            ->get();

        return view('partials.tvdeDriverManagement', compact('years'));
    }

    public function drivers()
    {
        return Driver::with([
            'card'
        ])->get();
    }

    public function operators(Request $request)
    {
        $driver = Driver::where([
            'id' => $request->driver_id
        ])
            ->with([
                'tvde_operators'
            ])
            ->first();

        return $driver;
    }

    public function activityLaunch(Request $request)
    {
        $activityLaunch = ActivityLaunch::where('id', $request->activity_launch_id)
            ->with([
                'driver.card',
                'activityPerOperators.tvde_operator',
            ])
            ->first();
        return $activityLaunch;
    }

    public function updateActivity(Request $request)
    {
        $activityLaunch = ActivityLaunch::find($request->activity_launch_id);
        $activityLaunch->rent = $request->rent ? $request->rent : 0;
        $activityLaunch->management = $request->management ? $request->management : 0;
        $activityLaunch->insurance = $request->insurance ? $request->insurance : 0;
        $activityLaunch->fuel = $request->fuel ? $request->fuel : 0;
        $activityLaunch->tolls = $request->tolls ? $request->tolls : 0;
        $activityLaunch->garage = $request->garage ? $request->garage : 0;
        $activityLaunch->others = $request->others ? $request->others : 0;
        $activityLaunch->refund = $request->refund ? $request->refund : 0;
        $activityLaunch->initial_kilometers = $request->initial_kilometers ? $request->initial_kilometers : null;
        $activityLaunch->final_kilometers = $request->final_kilometers ? $request->final_kilometers : null;
        $activityLaunch->save();
        foreach ($request->all() as $key => $value) {
            if (strstr($key, 'update')) {
                $key = explode('-', $key);
                $activityPerOperator = ActivityPerOperator::find($key[1]);
                if ($key[2] == 'net') {
                    $activityPerOperator->net = $value ? $value : 0;
                } elseif ($key[2] == 'gross') {
                    $activityPerOperator->gross = $value ? $value : 0;
                } else {
                    $activityPerOperator->taxes = $value ? $value : 0;
                }
                $activityPerOperator->save();
            }
        }
        return redirect()->back();
    }

    public function driver(Request $request)
    {

        $request->validate([
            'driver_id' => 'required',
        ]);

        $driver = Driver::where('id', $request->driver_id)
            ->with('tvde_operators')
            ->first()->load('card');

        $uber_activities = TvdeActivity::where([
            'tvde_week_id' => $request->week_id,
            'driver_code' => $driver->uber_uuid
        ])->get();

        $bolt_activities = TvdeActivity::where([
            'tvde_week_id' => $request->week_id,
            'driver_code' => $driver->bolt_name
        ])->get();

        return [
            'driver' => $driver,
            'week_id' => $request->week_id,
            'uber_activities' => [
                'earnings_one' => $uber_activities->sum('earnings_one'),
                'earnings_two' => $uber_activities->sum('earnings_two'),
                'earnings_three' => $uber_activities->sum('earnings_three'),
            ],
            'bolt_activities' => [
                'earnings_one' => $bolt_activities->sum('earnings_one'),
                'earnings_two' => $bolt_activities->sum('earnings_two'),
                'earnings_three' => $bolt_activities->sum('earnings_three'),
            ]
        ];
    }

    public function createActivity(Request $request)
    {

        $activityLaunch = new ActivityLaunch;
        $activityLaunch->driver_id = $request->driver_id;
        $activityLaunch->week_id = $request->week_id;
        $activityLaunch->rent = $request->rent;
        $activityLaunch->management = $request->management;
        $activityLaunch->insurance = $request->insurance;
        $activityLaunch->fuel = $request->fuel;
        $activityLaunch->tolls = $request->tolls;
        $activityLaunch->garage = $request->garage;
        $activityLaunch->others = $request->others;
        $activityLaunch->refund = $request->refund;
        $activityLaunch->initial_kilometers = $request->initial_kilometers ? $request->initial_kilometers : null;
        $activityLaunch->final_kilometers = $request->final_kilometers ? $request->final_kilometers : null;
        $activityLaunch->save();

        $operators = collect();

        foreach ($request->all() as $key => $value) {
            if (strstr($key, 'create')) {
                $key = explode('-', $key);
                $operators->add([
                    'operator_id' => $key[1],
                    'type' => $key[2],
                    'value' => $value
                ]);
            }
        }

        $operators = $operators->groupBy('operator_id');
        foreach ($operators as $key => $operator) {
            $activityPerOperator = new ActivityPerOperator;
            $activityPerOperator->activity_launch_id = $activityLaunch->id;
            $activityPerOperator->tvde_operator_id = $key;
            foreach ($operator as $op) {
                if ($op['type'] == 'net') {
                    $activityPerOperator->net = $op['value'];
                } elseif ($op['type'] == 'gross') {
                    $activityPerOperator->gross = $op['value'];
                } else {
                    $activityPerOperator->taxes = $op['value'];
                }
            }
            $activityPerOperator->save();
        }

        return redirect()->back();
    }

    public function deleteActivityLaunch(Request $request)
    {
        ActivityLaunch::find($request->activity_louch_id)->delete();
        ActivityPerOperator::where('activity_launch_id', $request->activity_launch_id)->delete();
    }

    public function launchAllActivities($tvde_week_id)
    {
        $drivers = Driver::with(['tvde_operators', 'card'])->get();

        $drivers = $drivers->map(function ($driver) use ($tvde_week_id) {
            $uber_activities = TvdeActivity::where([
                'tvde_week_id' => $tvde_week_id,
                'driver_code' => $driver->uber_uuid
            ])->get();

            $bolt_activities = TvdeActivity::where([
                'tvde_week_id' => $tvde_week_id,
                'driver_code' => $driver->bolt_name
            ])->get();

            $driver->results = [
                'week_id' => $tvde_week_id,
                'uber_activities' => [
                    'earnings_one' => $uber_activities->sum('earnings_one'),
                    'earnings_two' => $uber_activities->sum('earnings_two'),
                    'earnings_three' => $uber_activities->sum('earnings_three'),
                ],
                'bolt_activities' => [
                    'earnings_one' => $bolt_activities->sum('earnings_one'),
                    'earnings_two' => $bolt_activities->sum('earnings_two'),
                    'earnings_three' => $bolt_activities->sum('earnings_three'),
                ]
            ];

            return $driver;
        });

        // Filtra apenas os motoristas com atividade
        $drivers = $drivers->filter(function ($driver) {
            $results = $driver->results;

            $totalEarnings = $results['uber_activities']['earnings_one']
                + $results['uber_activities']['earnings_two']
                + $results['uber_activities']['earnings_three']
                + $results['bolt_activities']['earnings_one']
                + $results['bolt_activities']['earnings_two']
                + $results['bolt_activities']['earnings_three'];

            return $totalEarnings > 0;
        });

        return view('admin.tvdeDriverManagements.all', [
            'drivers' => $drivers,
            'tvde_week_id' => $tvde_week_id, // <== isto resolve o erro na view
        ]);
    }

    public function createSelectedDriverActivity(Request $request)
    {
        $weekId = $request->input('week_id');
        $driverIds = $request->input('driver_ids', []);

        foreach ($driverIds as $driverId) {
            // Ignorar se já existir ActivityLaunch para este motorista e semana
            if (ActivityLaunch::where('driver_id', $driverId)->where('week_id', $weekId)->exists()) {
                continue;
            }

            // Obter o motorista com os operadores
            $driver = Driver::with('tvde_operators')->find($driverId);

            // Obter atividades de Uber e Bolt para esta semana
            $uber_activities = TvdeActivity::where([
                'tvde_week_id' => $weekId,
                'driver_code' => $driver->uber_uuid
            ])->get();

            $bolt_activities = TvdeActivity::where([
                'tvde_week_id' => $weekId,
                'driver_code' => $driver->bolt_name
            ])->get();

            // Criar o registo base (ActivityLaunch)
            $activityLaunch = ActivityLaunch::create([
                'driver_id' => $driverId,
                'week_id' => $weekId,
                'rent' => 0,
                'management' => 0,
                'insurance' => 0,
                'fuel' => 0,
                'tolls' => 0,
                'garage' => 0,
                'others' => 0,
                'refund' => 0,
                'initial_kilometers' => null,
                'final_kilometers' => null,
            ]);

            // Criar registos por operador
            foreach ($driver->tvde_operators as $operator) {
                $name = strtolower($operator->name);

                if (str_contains($name, 'uber')) {
                    $gross = $uber_activities->sum('earnings_one');
                    $net = $uber_activities->sum('earnings_two');
                    $taxes = $uber_activities->sum('earnings_three');
                } elseif (str_contains($name, 'bolt')) {
                    $gross = $bolt_activities->sum('earnings_one');
                    $net = $bolt_activities->sum('earnings_two');
                    $taxes = $bolt_activities->sum('earnings_three');
                } else {
                    $gross = 0;
                    $net = 0;
                    $taxes = 0;
                }

                ActivityPerOperator::create([
                    'activity_launch_id' => $activityLaunch->id,
                    'tvde_operator_id' => $operator->id,
                    'gross' => $gross,
                    'net' => $net,
                    'taxes' => $taxes,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Atividades criadas com sucesso!');
    }
}
