<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyStandTvdePubRequest;
use App\Http\Requests\StoreStandTvdePubRequest;
use App\Http\Requests\UpdateStandTvdePubRequest;
use App\Models\StandTvdePub;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class StandTvdePubController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('stand_tvde_pub_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $standTvdePubs = StandTvdePub::with(['media'])->get();

        return view('admin.standTvdePubs.index', compact('standTvdePubs'));
    }

    public function create()
    {
        abort_if(Gate::denies('stand_tvde_pub_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdePubs.create');
    }

    public function store(StoreStandTvdePubRequest $request)
    {
        $standTvdePub = StandTvdePub::create($request->all());

        if ($request->input('image', false)) {
            $standTvdePub->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $standTvdePub->id]);
        }

        return redirect()->route('admin.stand-tvde-pubs.index');
    }

    public function edit(StandTvdePub $standTvdePub)
    {
        abort_if(Gate::denies('stand_tvde_pub_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdePubs.edit', compact('standTvdePub'));
    }

    public function update(UpdateStandTvdePubRequest $request, StandTvdePub $standTvdePub)
    {
        $standTvdePub->update($request->all());

        if ($request->input('image', false)) {
            if (! $standTvdePub->image || $request->input('image') !== $standTvdePub->image->file_name) {
                if ($standTvdePub->image) {
                    $standTvdePub->image->delete();
                }
                $standTvdePub->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($standTvdePub->image) {
            $standTvdePub->image->delete();
        }

        return redirect()->route('admin.stand-tvde-pubs.index');
    }

    public function show(StandTvdePub $standTvdePub)
    {
        abort_if(Gate::denies('stand_tvde_pub_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdePubs.show', compact('standTvdePub'));
    }

    public function destroy(StandTvdePub $standTvdePub)
    {
        abort_if(Gate::denies('stand_tvde_pub_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $standTvdePub->delete();

        return back();
    }

    public function massDestroy(MassDestroyStandTvdePubRequest $request)
    {
        $standTvdePubs = StandTvdePub::find(request('ids'));

        foreach ($standTvdePubs as $standTvdePub) {
            $standTvdePub->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('stand_tvde_pub_create') && Gate::denies('stand_tvde_pub_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new StandTvdePub();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}