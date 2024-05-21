<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyTvdeActivityRequest;
use App\Http\Requests\StoreTvdeActivityRequest;
use App\Http\Requests\UpdateTvdeActivityRequest;
use App\Models\TvdeActivity;
use App\Models\TvdeWeek;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TvdeActivityController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('tvde_activity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = TvdeActivity::with(['tvde_week'])->select(sprintf('%s.*', (new TvdeActivity)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'tvde_activity_show';
                $editGate      = 'tvde_activity_edit';
                $deleteGate    = 'tvde_activity_delete';
                $crudRoutePart = 'tvde-activities';

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
            $table->addColumn('tvde_week_start_date', function ($row) {
                return $row->tvde_week ? $row->tvde_week->start_date : '';
            });

            $table->editColumn('driver_code', function ($row) {
                return $row->driver_code ? $row->driver_code : '';
            });
            $table->editColumn('earnings_one', function ($row) {
                return $row->earnings_one ? $row->earnings_one : '';
            });
            $table->editColumn('earnings_two', function ($row) {
                return $row->earnings_two ? $row->earnings_two : '';
            });
            $table->editColumn('earnings_three', function ($row) {
                return $row->earnings_three ? $row->earnings_three : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'tvde_week']);

            return $table->make(true);
        }

        return view('admin.tvdeActivities.index');
    }

    public function create()
    {
        abort_if(Gate::denies('tvde_activity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tvde_weeks = TvdeWeek::pluck('start_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.tvdeActivities.create', compact('tvde_weeks'));
    }

    public function store(StoreTvdeActivityRequest $request)
    {
        $tvdeActivity = TvdeActivity::create($request->all());

        return redirect()->route('admin.tvde-activities.index');
    }

    public function edit(TvdeActivity $tvdeActivity)
    {
        abort_if(Gate::denies('tvde_activity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tvde_weeks = TvdeWeek::pluck('start_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tvdeActivity->load('tvde_week');

        return view('admin.tvdeActivities.edit', compact('tvdeActivity', 'tvde_weeks'));
    }

    public function update(UpdateTvdeActivityRequest $request, TvdeActivity $tvdeActivity)
    {
        $tvdeActivity->update($request->all());

        return redirect()->route('admin.tvde-activities.index');
    }

    public function show(TvdeActivity $tvdeActivity)
    {
        abort_if(Gate::denies('tvde_activity_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tvdeActivity->load('tvde_week');

        return view('admin.tvdeActivities.show', compact('tvdeActivity'));
    }

    public function destroy(TvdeActivity $tvdeActivity)
    {
        abort_if(Gate::denies('tvde_activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tvdeActivity->delete();

        return back();
    }

    public function massDestroy(MassDestroyTvdeActivityRequest $request)
    {
        $tvdeActivities = TvdeActivity::find(request('ids'));

        foreach ($tvdeActivities as $tvdeActivity) {
            $tvdeActivity->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}