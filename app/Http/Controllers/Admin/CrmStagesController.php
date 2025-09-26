<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmStageRequest;
use App\Http\Requests\StoreCrmStageRequest;
use App\Http\Requests\UpdateCrmStageRequest;
use App\Models\CrmCategory;
use App\Models\CrmStage;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmStagesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_stage_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmStage::with(['category', 'auto_assign_to_user'])->select(sprintf('%s.*', (new CrmStage)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_stage_show';
                $editGate      = 'crm_stage_edit';
                $deleteGate    = 'crm_stage_delete';
                $crudRoutePart = 'crm-stages';

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
            $table->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '';
            });

            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('position', function ($row) {
                return $row->position ? $row->position : '';
            });
            $table->editColumn('color', function ($row) {
                return $row->color ? $row->color : '';
            });
            $table->editColumn('is_won', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_won ? 'checked' : null) . '>';
            });
            $table->editColumn('is_lost', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_lost ? 'checked' : null) . '>';
            });
            $table->addColumn('auto_assign_to_user_name', function ($row) {
                return $row->auto_assign_to_user ? $row->auto_assign_to_user->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'category', 'is_won', 'is_lost', 'auto_assign_to_user']);

            return $table->make(true);
        }

        return view('admin.crmStages.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_stage_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $auto_assign_to_users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmStages.create', compact('auto_assign_to_users', 'categories'));
    }

    public function store(StoreCrmStageRequest $request)
    {
        $crmStage = CrmStage::create($request->all());

        return redirect()->route('admin.crm-stages.index');
    }

    public function edit(CrmStage $crmStage)
    {
        abort_if(Gate::denies('crm_stage_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $auto_assign_to_users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmStage->load('category', 'auto_assign_to_user');

        return view('admin.crmStages.edit', compact('auto_assign_to_users', 'categories', 'crmStage'));
    }

    public function update(UpdateCrmStageRequest $request, CrmStage $crmStage)
    {
        $crmStage->update($request->all());

        return redirect()->route('admin.crm-stages.index');
    }

    public function show(CrmStage $crmStage)
    {
        abort_if(Gate::denies('crm_stage_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmStage->load('category', 'auto_assign_to_user');

        return view('admin.crmStages.show', compact('crmStage'));
    }

    public function destroy(CrmStage $crmStage)
    {
        abort_if(Gate::denies('crm_stage_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmStage->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmStageRequest $request)
    {
        $crmStages = CrmStage::find(request('ids'));

        foreach ($crmStages as $crmStage) {
            $crmStage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
