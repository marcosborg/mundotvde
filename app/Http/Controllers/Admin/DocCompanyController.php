<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDocCompanyRequest;
use App\Http\Requests\StoreDocCompanyRequest;
use App\Http\Requests\UpdateDocCompanyRequest;
use App\Models\DocCompany;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DocCompanyController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('doc_company_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $docCompanies = DocCompany::all();

        return view('admin.docCompanies.index', compact('docCompanies'));
    }

    public function create()
    {
        abort_if(Gate::denies('doc_company_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.docCompanies.create');
    }

    public function store(StoreDocCompanyRequest $request)
    {
        $docCompany = DocCompany::create($request->all());

        return redirect()->route('admin.doc-companies.index');
    }

    public function edit(DocCompany $docCompany)
    {
        abort_if(Gate::denies('doc_company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.docCompanies.edit', compact('docCompany'));
    }

    public function update(UpdateDocCompanyRequest $request, DocCompany $docCompany)
    {
        $docCompany->update($request->all());

        return redirect()->route('admin.doc-companies.index');
    }

    public function show(DocCompany $docCompany)
    {
        abort_if(Gate::denies('doc_company_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.docCompanies.show', compact('docCompany'));
    }

    public function destroy(DocCompany $docCompany)
    {
        abort_if(Gate::denies('doc_company_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $docCompany->delete();

        return back();
    }

    public function massDestroy(MassDestroyDocCompanyRequest $request)
    {
        $docCompanies = DocCompany::find(request('ids'));

        foreach ($docCompanies as $docCompany) {
            $docCompany->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
