<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyDocumentManagementRequest;
use App\Http\Requests\StoreDocumentManagementRequest;
use App\Http\Requests\UpdateDocumentManagementRequest;
use App\Models\DocCompany;
use App\Models\DocumentManagement;
use App\Models\Signature;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class DocumentManagementController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('document_management_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentManagements = DocumentManagement::with(['doc_company', 'signatures'])->get();

        return view('admin.documentManagements.index', compact('documentManagements'));
    }

    public function create()
    {
        abort_if(Gate::denies('document_management_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $doc_companies = DocCompany::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signatures = Signature::pluck('title', 'id');

        return view('admin.documentManagements.create', compact('doc_companies', 'signatures'));
    }

    public function store(StoreDocumentManagementRequest $request)
    {
        $documentManagement = DocumentManagement::create($request->all());
        $documentManagement->signatures()->sync($request->input('signatures', []));
        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $documentManagement->id]);
        }

        return redirect()->route('admin.document-managements.index');
    }

    public function edit(DocumentManagement $documentManagement)
    {
        abort_if(Gate::denies('document_management_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $doc_companies = DocCompany::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signatures = Signature::pluck('title', 'id');

        $documentManagement->load('doc_company', 'signatures');

        return view('admin.documentManagements.edit', compact('doc_companies', 'documentManagement', 'signatures'));
    }

    public function update(UpdateDocumentManagementRequest $request, DocumentManagement $documentManagement)
    {
        $documentManagement->update($request->all());
        $documentManagement->signatures()->sync($request->input('signatures', []));

        return redirect()->route('admin.document-managements.index');
    }

    public function show(DocumentManagement $documentManagement)
    {
        abort_if(Gate::denies('document_management_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentManagement->load('doc_company', 'signatures');

        return view('admin.documentManagements.show', compact('documentManagement'));
    }

    public function destroy(DocumentManagement $documentManagement)
    {
        abort_if(Gate::denies('document_management_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $documentManagement->delete();

        return back();
    }

    public function massDestroy(MassDestroyDocumentManagementRequest $request)
    {
        $documentManagements = DocumentManagement::find(request('ids'));

        foreach ($documentManagements as $documentManagement) {
            $documentManagement->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('document_management_create') && Gate::denies('document_management_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new DocumentManagement();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
