<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTimeLogRequest;
use App\Http\Requests\StoreTimeLogRequest;
use App\Http\Requests\UpdateTimeLogRequest;
use App\Models\Driver;
use App\Models\TimeLog;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TimeLogsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('time_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = TimeLog::with(['driver'])->select(sprintf('%s.*', (new TimeLog)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'time_log_show';
                $editGate      = 'time_log_edit';
                $deleteGate    = 'time_log_delete';
                $crudRoutePart = 'time-logs';

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
            $table->addColumn('driver_name', function ($row) {
                return $row->driver ? $row->driver->name : '';
            });

            $table->editColumn('driver.name', function ($row) {
                return $row->driver ? (is_string($row->driver) ? $row->driver : $row->driver->name) : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? TimeLog::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'driver']);

            return $table->make(true);
        }

        return view('admin.timeLogs.index');
    }

    public function create()
    {
        abort_if(Gate::denies('time_log_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.timeLogs.create', compact('drivers'));
    }

    public function store(StoreTimeLogRequest $request)
    {
        $timeLog = TimeLog::create($request->all());

        return redirect()->route('admin.time-logs.index');
    }

    public function edit(TimeLog $timeLog)
    {
        abort_if(Gate::denies('time_log_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $timeLog->load('driver');

        return view('admin.timeLogs.edit', compact('drivers', 'timeLog'));
    }

    public function update(UpdateTimeLogRequest $request, TimeLog $timeLog)
    {
        $timeLog->update($request->all());

        return redirect()->route('admin.time-logs.index');
    }

    public function show(TimeLog $timeLog)
    {
        abort_if(Gate::denies('time_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $timeLog->load('driver');

        return view('admin.timeLogs.show', compact('timeLog'));
    }

    public function destroy(TimeLog $timeLog)
    {
        abort_if(Gate::denies('time_log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $timeLog->delete();

        return back();
    }

    public function massDestroy(MassDestroyTimeLogRequest $request)
    {
        $timeLogs = TimeLog::find(request('ids'));

        foreach ($timeLogs as $timeLog) {
            $timeLog->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
