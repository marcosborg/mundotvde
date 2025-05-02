<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyActivityPerOperatorRequest;
use App\Http\Requests\StoreActivityPerOperatorRequest;
use App\Http\Requests\UpdateActivityPerOperatorRequest;
use App\Models\ActivityLaunch;
use App\Models\ActivityPerOperator;
use App\Models\TvdeOperator;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ActivityPerOperatorController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('activity_per_operator_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ActivityPerOperator::with(['activity_launch', 'tvde_operator'])->select(sprintf('%s.*', (new ActivityPerOperator)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'activity_per_operator_show';
                $editGate      = 'activity_per_operator_edit';
                $deleteGate    = 'activity_per_operator_delete';
                $crudRoutePart = 'activity-per-operators';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('activity_launch_rent', function ($row) {
                return $row->activity_launch ? $row->activity_launch->rent : '';
            });

            $table->editColumn('activity_launch.management', function ($row) {
                return $row->activity_launch ? (is_string($row->activity_launch) ? $row->activity_launch : $row->activity_launch->management) : '';
            });
            $table->editColumn('activity_launch.insurance', function ($row) {
                return $row->activity_launch ? (is_string($row->activity_launch) ? $row->activity_launch : $row->activity_launch->insurance) : '';
            });
            $table->editColumn('activity_launch.fuel', function ($row) {
                return $row->activity_launch ? (is_string($row->activity_launch) ? $row->activity_launch : $row->activity_launch->fuel) : '';
            });
            $table->editColumn('activity_launch.tolls', function ($row) {
                return $row->activity_launch ? (is_string($row->activity_launch) ? $row->activity_launch : $row->activity_launch->tolls) : '';
            });
            $table->editColumn('activity_launch.otthers', function ($row) {
                return $row->activity_launch ? (is_string($row->activity_launch) ? $row->activity_launch : $row->activity_launch->otthers) : '';
            });
            $table->editColumn('gross', function ($row) {
                return $row->gross ? $row->gross : '';
            });
            $table->editColumn('net', function ($row) {
                return $row->net ? $row->net : '';
            });
            $table->editColumn('taxes', function ($row) {
                return $row->taxes ? $row->taxes : '';
            });
            $table->addColumn('tvde_operator_name', function ($row) {
                return $row->tvde_operator ? $row->tvde_operator->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'activity_launch', 'tvde_operator']);

            return $table->make(true);
        }

        return view('admin.activityPerOperators.index');
    }

    public function create()
    {
        abort_if(Gate::denies('activity_per_operator_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity_launches = ActivityLaunch::pluck('rent', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tvde_operators = TvdeOperator::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.activityPerOperators.create', compact('activity_launches', 'tvde_operators'));
    }

    public function store(StoreActivityPerOperatorRequest $request)
    {
        $activityPerOperator = ActivityPerOperator::create($request->all());

        return redirect()->route('admin.activity-per-operators.index');
    }

    public function edit(ActivityPerOperator $activityPerOperator)
    {
        abort_if(Gate::denies('activity_per_operator_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity_launches = ActivityLaunch::pluck('rent', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tvde_operators = TvdeOperator::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $activityPerOperator->load('activity_launch', 'tvde_operator');

        return view('admin.activityPerOperators.edit', compact('activityPerOperator', 'activity_launches', 'tvde_operators'));
    }

    public function update(UpdateActivityPerOperatorRequest $request, ActivityPerOperator $activityPerOperator)
    {
        $activityPerOperator->update($request->all());

        return redirect()->route('admin.activity-per-operators.index');
    }

    public function show(ActivityPerOperator $activityPerOperator)
    {
        abort_if(Gate::denies('activity_per_operator_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityPerOperator->load('activity_launch', 'tvde_operator');

        return view('admin.activityPerOperators.show', compact('activityPerOperator'));
    }

    public function destroy(ActivityPerOperator $activityPerOperator)
    {
        abort_if(Gate::denies('activity_per_operator_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activityPerOperator->delete();

        return back();
    }

    public function massDestroy(MassDestroyActivityPerOperatorRequest $request)
    {
        $activityPerOperators = ActivityPerOperator::find(request('ids'));

        foreach ($activityPerOperators as $activityPerOperator) {
            $activityPerOperator->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
