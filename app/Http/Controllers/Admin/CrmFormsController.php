<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmFormRequest;
use App\Http\Requests\StoreCrmFormRequest;
use App\Http\Requests\UpdateCrmFormRequest;
use App\Models\CrmCategory;
use App\Models\CrmForm;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmFormsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_form_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmForm::with(['category'])->select(sprintf('%s.*', (new CrmForm)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_form_show';
                $editGate      = 'crm_form_edit';
                $deleteGate    = 'crm_form_delete';
                $crudRoutePart = 'crm-forms';

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
            $table->editColumn('slug', function ($row) {
                return $row->slug ? $row->slug : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? CrmForm::STATUS_RADIO[$row->status] : '';
            });
            $table->editColumn('confirmation_message', function ($row) {
                return $row->confirmation_message ? $row->confirmation_message : '';
            });
            $table->editColumn('redirect_url', function ($row) {
                return $row->redirect_url ? $row->redirect_url : '';
            });
            $table->editColumn('notify_emails', function ($row) {
                return $row->notify_emails ? $row->notify_emails : '';
            });
            $table->editColumn('create_card_on_submit', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->create_card_on_submit ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'category', 'create_card_on_submit']);

            return $table->make(true);
        }

        return view('admin.crmForms.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_form_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmForms.create', compact('categories'));
    }

    public function store(StoreCrmFormRequest $request)
    {
        $crmForm = CrmForm::create($request->all());

        return redirect()->route('admin.crm-forms.index');
    }

    public function edit(CrmForm $crmForm)
    {
        abort_if(Gate::denies('crm_form_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = CrmCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmForm->load('category');

        return view('admin.crmForms.edit', compact('categories', 'crmForm'));
    }

    public function update(UpdateCrmFormRequest $request, CrmForm $crmForm)
    {
        $crmForm->update($request->all());

        return redirect()->route('admin.crm-forms.index');
    }

    public function show(CrmForm $crmForm)
    {
        abort_if(Gate::denies('crm_form_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmForm->load('category');

        return view('admin.crmForms.show', compact('crmForm'));
    }

    public function destroy(CrmForm $crmForm)
    {
        abort_if(Gate::denies('crm_form_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmForm->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmFormRequest $request)
    {
        $crmForms = CrmForm::find(request('ids'));

        foreach ($crmForms as $crmForm) {
            $crmForm->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
