<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmCardActivityRequest;
use App\Http\Requests\StoreCrmCardActivityRequest;
use App\Http\Requests\UpdateCrmCardActivityRequest;
use App\Models\CrmCard;
use App\Models\CrmCardActivity;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmCardActivitiesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_card_activity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmCardActivity::with(['card', 'created_by'])->select(sprintf('%s.*', (new CrmCardActivity)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_card_activity_show';
                $editGate      = 'crm_card_activity_edit';
                $deleteGate    = 'crm_card_activity_delete';
                $crudRoutePart = 'crm-card-activities';

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
            $table->addColumn('card_title', function ($row) {
                return $row->card ? $row->card->title : '';
            });

            $table->editColumn('type', function ($row) {
                return $row->type ? CrmCardActivity::TYPE_RADIO[$row->type] : '';
            });
            $table->editColumn('meta_json', function ($row) {
                return $row->meta_json ? $row->meta_json : '';
            });
            $table->addColumn('created_by_name', function ($row) {
                return $row->created_by ? $row->created_by->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'card', 'created_by']);

            return $table->make(true);
        }

        return view('admin.crmCardActivities.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_card_activity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmCardActivities.create', compact('cards', 'created_bies'));
    }

    public function store(StoreCrmCardActivityRequest $request)
    {
        $crmCardActivity = CrmCardActivity::create($request->all());

        return redirect()->route('admin.crm-card-activities.index');
    }

    public function edit(CrmCardActivity $crmCardActivity)
    {
        abort_if(Gate::denies('crm_card_activity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cards = CrmCard::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $created_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmCardActivity->load('card', 'created_by');

        return view('admin.crmCardActivities.edit', compact('cards', 'created_bies', 'crmCardActivity'));
    }

    public function update(UpdateCrmCardActivityRequest $request, CrmCardActivity $crmCardActivity)
    {
        $crmCardActivity->update($request->all());

        return redirect()->route('admin.crm-card-activities.index');
    }

    public function show(CrmCardActivity $crmCardActivity)
    {
        abort_if(Gate::denies('crm_card_activity_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCardActivity->load('card', 'created_by');

        return view('admin.crmCardActivities.show', compact('crmCardActivity'));
    }

    public function destroy(CrmCardActivity $crmCardActivity)
    {
        abort_if(Gate::denies('crm_card_activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCardActivity->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmCardActivityRequest $request)
    {
        $crmCardActivities = CrmCardActivity::find(request('ids'));

        foreach ($crmCardActivities as $crmCardActivity) {
            $crmCardActivity->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
