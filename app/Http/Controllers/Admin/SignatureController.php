<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroySignatureRequest;
use App\Http\Requests\StoreSignatureRequest;
use App\Http\Requests\UpdateSignatureRequest;
use App\Models\Signature;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class SignatureController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('signature_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $signatures = Signature::with(['media'])->get();

        return view('admin.signatures.index', compact('signatures'));
    }

    public function create()
    {
        abort_if(Gate::denies('signature_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.signatures.create');
    }

    public function store(StoreSignatureRequest $request)
    {
        $signature = Signature::create($request->all());

        if ($request->input('signature', false)) {
            $signature->addMedia(storage_path('tmp/uploads/' . basename($request->input('signature'))))->toMediaCollection('signature');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $signature->id]);
        }

        return redirect()->route('admin.signatures.index');
    }

    public function edit(Signature $signature)
    {
        abort_if(Gate::denies('signature_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.signatures.edit', compact('signature'));
    }

    public function update(UpdateSignatureRequest $request, Signature $signature)
    {
        $signature->update($request->all());

        if ($request->input('signature', false)) {
            if (! $signature->signature || $request->input('signature') !== $signature->signature->file_name) {
                if ($signature->signature) {
                    $signature->signature->delete();
                }
                $signature->addMedia(storage_path('tmp/uploads/' . basename($request->input('signature'))))->toMediaCollection('signature');
            }
        } elseif ($signature->signature) {
            $signature->signature->delete();
        }

        return redirect()->route('admin.signatures.index');
    }

    public function show(Signature $signature)
    {
        abort_if(Gate::denies('signature_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.signatures.show', compact('signature'));
    }

    public function destroy(Signature $signature)
    {
        abort_if(Gate::denies('signature_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $signature->delete();

        return back();
    }

    public function massDestroy(MassDestroySignatureRequest $request)
    {
        $signatures = Signature::find(request('ids'));

        foreach ($signatures as $signature) {
            $signature->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('signature_create') && Gate::denies('signature_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Signature();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
