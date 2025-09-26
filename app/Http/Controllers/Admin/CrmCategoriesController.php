<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCrmCategoryRequest;
use App\Http\Requests\StoreCrmCategoryRequest;
use App\Http\Requests\UpdateCrmCategoryRequest;
use App\Models\CrmCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CrmCategoriesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('crm_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CrmCategory::query()->select(sprintf('%s.*', (new CrmCategory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'crm_category_show';
                $editGate      = 'crm_category_edit';
                $deleteGate    = 'crm_category_delete';
                $crudRoutePart = 'crm-categories';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('slug', function ($row) {
                return $row->slug ? $row->slug : '';
            });
            $table->editColumn('color', function ($row) {
                return $row->color ? $row->color : '';
            });
            $table->editColumn('position', function ($row) {
                return $row->position ? $row->position : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.crmCategories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('crm_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.crmCategories.create');
    }

    public function store(StoreCrmCategoryRequest $request)
    {
        $crmCategory = CrmCategory::create($request->all());

        return redirect()->route('admin.crm-categories.index');
    }

    public function edit(CrmCategory $crmCategory)
    {
        abort_if(Gate::denies('crm_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.crmCategories.edit', compact('crmCategory'));
    }

    public function update(UpdateCrmCategoryRequest $request, CrmCategory $crmCategory)
    {
        $crmCategory->update($request->all());

        return redirect()->route('admin.crm-categories.index');
    }

    public function show(CrmCategory $crmCategory)
    {
        abort_if(Gate::denies('crm_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.crmCategories.show', compact('crmCategory'));
    }

    public function destroy(CrmCategory $crmCategory)
    {
        abort_if(Gate::denies('crm_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $crmCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyCrmCategoryRequest $request)
    {
        $crmCategories = CrmCategory::find(request('ids'));

        foreach ($crmCategories as $crmCategory) {
            $crmCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
