<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyStandTvdePageRequest;
use App\Http\Requests\StoreStandTvdePageRequest;
use App\Http\Requests\UpdateStandTvdePageRequest;
use App\Models\StandTvdePage;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class StandTvdePageController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('stand_tvde_page_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $standTvdePages = StandTvdePage::with(['media'])->get();

        return view('admin.standTvdePages.index', compact('standTvdePages'));
    }

    public function create()
    {
        abort_if(Gate::denies('stand_tvde_page_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdePages.create');
    }

    public function store(StoreStandTvdePageRequest $request)
    {
        $standTvdePage = StandTvdePage::create($request->all());

        if ($request->input('image', false)) {
            $standTvdePage->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $standTvdePage->id]);
        }

        return redirect()->route('admin.stand-tvde-pages.index');
    }

    public function edit(StandTvdePage $standTvdePage)
    {
        abort_if(Gate::denies('stand_tvde_page_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdePages.edit', compact('standTvdePage'));
    }

    public function update(UpdateStandTvdePageRequest $request, StandTvdePage $standTvdePage)
    {
        $standTvdePage->update($request->all());

        if ($request->input('image', false)) {
            if (! $standTvdePage->image || $request->input('image') !== $standTvdePage->image->file_name) {
                if ($standTvdePage->image) {
                    $standTvdePage->image->delete();
                }
                $standTvdePage->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($standTvdePage->image) {
            $standTvdePage->image->delete();
        }

        return redirect()->route('admin.stand-tvde-pages.index');
    }

    public function show(StandTvdePage $standTvdePage)
    {
        abort_if(Gate::denies('stand_tvde_page_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.standTvdePages.show', compact('standTvdePage'));
    }

    public function destroy(StandTvdePage $standTvdePage)
    {
        abort_if(Gate::denies('stand_tvde_page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $standTvdePage->delete();

        return back();
    }

    public function massDestroy(MassDestroyStandTvdePageRequest $request)
    {
        $standTvdePages = StandTvdePage::find(request('ids'));

        foreach ($standTvdePages as $standTvdePage) {
            $standTvdePage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('stand_tvde_page_create') && Gate::denies('stand_tvde_page_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new StandTvdePage();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}