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
use App\Models\CrmFormField;
use Illuminate\Support\Str;

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

    public function saveBuilder(\Illuminate\Http\Request $request, \App\Models\CrmForm $crm_form)
    {
        \Gate::authorize('crm_form_edit');

        $data = $request->validate([
            'name'                   => ['required', 'string', 'max:190'],
            'slug'                   => ['required', 'string', 'max:190', 'unique:crm_forms,slug,' . $crm_form->id],
            'status'                 => ['required', 'in:draft,published,archived'],
            'confirmation_message'   => ['nullable', 'string', 'max:255'],
            'redirect_url'           => ['nullable', 'string', 'max:255'],
            'notify_emails'          => ['nullable', 'string', 'max:500'],
            'create_card_on_submit'  => ['nullable', 'boolean'],
            'category_id'            => ['nullable', 'integer', 'exists:crm_categories,id'],
        ]);

        $crm_form->fill($data);
        $crm_form->create_card_on_submit = (bool)($data['create_card_on_submit'] ?? false);
        $crm_form->save();

        return back()->with('status', 'Formulário guardado.');
    }

    // CRUD simplificado dos campos (AJAX)
    public function fieldsStore(\Illuminate\Http\Request $request, \App\Models\CrmForm $crm_form)
    {
        \Gate::authorize('crm_form_edit');

        $data = $request->validate([
            'label'         => ['required', 'string', 'max:190'],
            'type'          => ['required', 'in:text,textarea,number,checkbox,select'],
            'required'      => ['nullable', 'boolean'],
            'help_text'     => ['nullable', 'string', 'max:255'],
            'placeholder'   => ['nullable', 'string', 'max:190'],
            'default_value' => ['nullable', 'string'],
            'is_unique'     => ['nullable', 'boolean'],
            'min_value'     => ['nullable', 'numeric'],
            'max_value'     => ['nullable', 'numeric'],
            'options_json'  => ['nullable', 'string'], // JSON para select
        ]);

        $pos = (int) $crm_form->fields()->max('position') + 10;

        $field = $crm_form->fields()->create(array_merge($data, [
            'required' => (bool)($data['required'] ?? false),
            'is_unique' => (bool)($data['is_unique'] ?? false),
            'position' => $pos,
        ]));

        return response()->json(['ok' => true, 'field' => $field]);
    }

    public function fieldsUpdate(\Illuminate\Http\Request $request, \App\Models\CrmFormField $field)
    {
        \Gate::authorize('crm_form_edit');

        $data = $request->validate([
            'label'         => ['sometimes', 'required', 'string', 'max:190'],
            'type'          => ['sometimes', 'required', 'in:text,textarea,number,checkbox,select'],
            'required'      => ['nullable', 'boolean'],
            'help_text'     => ['nullable', 'string', 'max:255'],
            'placeholder'   => ['nullable', 'string', 'max:190'],
            'default_value' => ['nullable', 'string'],
            'is_unique'     => ['nullable', 'boolean'],
            'min_value'     => ['nullable', 'numeric'],
            'max_value'     => ['nullable', 'numeric'],
            'options_json'  => ['nullable', 'string'],
        ]);

        $data['required']  = (bool)($data['required'] ?? $field->required);
        $data['is_unique'] = (bool)($data['is_unique'] ?? $field->is_unique);

        $field->update($data);

        return response()->json(['ok' => true, 'field' => $field->fresh()]);
    }

    public function fieldsDestroy(\App\Models\CrmFormField $field, Request $request)
    {
        \Gate::authorize('crm_form_field_delete');

        $field->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true], 200);
        }

        return back()->with('status', 'Campo apagado');
    }

    public function fieldsReorder(\Illuminate\Http\Request $request, \App\Models\CrmForm $crm_form)
    {
        \Gate::authorize('crm_form_edit');
        $ids = $request->validate(['ids' => 'required|array'])['ids'];
        $pos = 10;
        foreach ($ids as $id) {
            $crm_form->fields()->where('id', $id)->update(['position' => $pos]);
            $pos += 10;
        }
        return response()->json(['ok' => true]);
    }

    public function builderIndex()
    {
        Gate::authorize('crm_form_access');

        $forms = CrmForm::withCount(['fields', 'submissions'])
            ->orderByDesc('updated_at')
            ->get();

        $categories = CrmCategory::orderBy('position')->orderBy('id')->get(['id', 'name']);

        return view('admin.crmForms.builder-index', compact('forms', 'categories'));
    }

    public function builderQuickStore(Request $request)
    {
        Gate::authorize('crm_form_create');

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:crm_categories,id'],
        ]);

        $form = new CrmForm();
        $form->category_id = $data['category_id'] ?? null;
        $form->name        = $data['name'];
        $form->slug        = Str::slug($data['name']);
        // garante unicidade simples
        $base = $form->slug;
        $i = 2;
        while (CrmForm::where('slug', $form->slug)->exists()) {
            $form->slug = $base . '-' . $i++;
        }
        $form->status = 'draft';
        $form->save();

        return redirect()->route('admin.crm-forms.builder', $form);
    }

    public function builder(CrmForm $crm_form)
    {
        Gate::authorize('crm_form_access');

        $crm_form->load([
            'category',
            'fields' => fn($q) => $q->orderBy('position')->orderBy('id')
        ]);

        $categories = CrmCategory::orderBy('position')->orderBy('id')->get(['id', 'name']);
        $fieldTypes = array_keys(CrmFormField::TYPE_RADIO); // ['text','textarea','number','checkbox','select']

        return view('admin.crmForms.builder', compact('crm_form', 'categories', 'fieldTypes'));
    }
}
