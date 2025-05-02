<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDocumentWarningRequest;
use App\Http\Requests\StoreDocumentWarningRequest;
use App\Http\Requests\UpdateDocumentWarningRequest;
use App\Models\DocumentWarning;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentWarningController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('document_warning_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentWarnings = DocumentWarning::all();

        return view('admin.documentWarnings.index', compact('documentWarnings'));
    }

    public function create()
    {
        abort_if(Gate::denies('document_warning_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.documentWarnings.create');
    }

    public function store(StoreDocumentWarningRequest $request)
    {
        $documentWarning = DocumentWarning::create($request->all());

        return redirect()->route('admin.document-warnings.index');
    }

    public function edit(DocumentWarning $documentWarning)
    {
        abort_if(Gate::denies('document_warning_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.documentWarnings.edit', compact('documentWarning'));
    }

    public function update(UpdateDocumentWarningRequest $request, DocumentWarning $documentWarning)
    {
        $documentWarning->update($request->all());

        return redirect('/admin/document-warnings/1/edit')->with('message', 'Atualizado.');
    }

    public function show(DocumentWarning $documentWarning)
    {
        abort_if(Gate::denies('document_warning_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.documentWarnings.show', compact('documentWarning'));
    }

    public function destroy(DocumentWarning $documentWarning)
    {
        abort_if(Gate::denies('document_warning_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentWarning->delete();

        return back();
    }

    public function massDestroy(MassDestroyDocumentWarningRequest $request)
    {
        $documentWarnings = DocumentWarning::find(request('ids'));

        foreach ($documentWarnings as $documentWarning) {
            $documentWarning->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
