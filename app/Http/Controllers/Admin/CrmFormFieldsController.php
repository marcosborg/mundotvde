<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmFormFieldRequest;
use App\Http\Requests\StoreCrmFormFieldRequest;
use App\Http\Requests\UpdateCrmFormFieldRequest;
use App\Models\CrmForm;
use App\Models\CrmFormField;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmFormFieldsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_form_field_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmFormField::with(['form'])->select(sprintf('%s.*', (new CrmFormField)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_form_field_show';
                $editGate      = 'crm_form_field_edit';
                $deleteGate    = 'crm_form_field_delete';
                $crudRoutePart = 'crm-form-fields';

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
            $table->addColumn('form_name', function ($row) {
                return $row->form ? $row->form->name : '';
            });

            $table->editColumn('label', function ($row) {
                return $row->label ? $row->label : '';
            });
            $table->editColumn('type', function ($row) {
                return $row->type ? CrmFormField::TYPE_RADIO[$row->type] : '';
            });
            $table->editColumn('required', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->required ? 'checked' : null) . '>';
            });
            $table->editColumn('help_text', function ($row) {
                return $row->help_text ? $row->help_text : '';
            });
            $table->editColumn('placeholder', function ($row) {
                return $row->placeholder ? $row->placeholder : '';
            });
            $table->editColumn('default_value', function ($row) {
                return $row->default_value ? $row->default_value : '';
            });
            $table->editColumn('is_unique', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->is_unique ? 'checked' : null) . '>';
            });
            $table->editColumn('min_value', function ($row) {
                return $row->min_value ? $row->min_value : '';
            });
            $table->editColumn('max_value', function ($row) {
                return $row->max_value ? $row->max_value : '';
            });
            $table->editColumn('options_json', function ($row) {
                return $row->options_json ? $row->options_json : '';
            });
            $table->editColumn('position', function ($row) {
                return $row->position ? $row->position : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'form', 'required', 'is_unique']);

            return $table->make(true);
        }

        return view('admin.crmFormFields.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_form_field_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.crmFormFields.create', compact('forms'));
    }

    public function store(StoreCrmFormFieldRequest $request)
    {
        $crmFormField = CrmFormField::create($request->all());

        return redirect()->route('admin.crm-form-fields.index');
    }

    public function edit(CrmFormField $crmFormField)
    {
        abort_if(Gate::denies('crm_form_field_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $forms = CrmForm::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $crmFormField->load('form');

        return view('admin.crmFormFields.edit', compact('crmFormField', 'forms'));
    }

    public function update(UpdateCrmFormFieldRequest $request, CrmFormField $crmFormField)
    {
        $crmFormField->update($request->all());

        return redirect()->route('admin.crm-form-fields.index');
    }

    public function show(CrmFormField $crmFormField)
    {
        abort_if(Gate::denies('crm_form_field_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmFormField->load('form');

        return view('admin.crmFormFields.show', compact('crmFormField'));
    }

    public function destroy(CrmFormField $crmFormField)
    {
        abort_if(Gate::denies('crm_form_field_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmFormField->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmFormFieldRequest $request)
    {
        $crmFormFields = CrmFormField::find(request('ids'));

        foreach ($crmFormFields as $crmFormField) {
            $crmFormField->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
