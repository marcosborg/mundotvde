<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyRecommendationRequest;
use App\Http\Requests\StoreRecommendationRequest;
use App\Http\Requests\UpdateRecommendationRequest;
use App\Models\Driver;
use App\Models\Recommendation;
use App\Models\RecommendationStatus;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class RecommendationController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('recommendation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recommendations = Recommendation::with(['driver', 'recommendation_status'])->get();

        return view('admin.recommendations.index', compact('recommendations'));
    }

    public function create()
    {
        abort_if(Gate::denies('recommendation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recommendation_statuses = RecommendationStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.recommendations.create', compact('drivers', 'recommendation_statuses'));
    }

    public function store(StoreRecommendationRequest $request)
    {
        $recommendation = Recommendation::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $recommendation->id]);
        }

        return redirect()->route('admin.recommendations.index');
    }

    public function edit(Recommendation $recommendation)
    {
        abort_if(Gate::denies('recommendation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drivers = Driver::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recommendation_statuses = RecommendationStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $recommendation->load('driver', 'recommendation_status');

        return view('admin.recommendations.edit', compact('drivers', 'recommendation', 'recommendation_statuses'));
    }

    public function update(UpdateRecommendationRequest $request, Recommendation $recommendation)
    {
        $recommendation->update($request->all());

        return redirect()->route('admin.recommendations.index');
    }

    public function show(Recommendation $recommendation)
    {
        abort_if(Gate::denies('recommendation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recommendation->load('driver', 'recommendation_status');

        return view('admin.recommendations.show', compact('recommendation'));
    }

    public function destroy(Recommendation $recommendation)
    {
        abort_if(Gate::denies('recommendation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recommendation->delete();

        return back();
    }

    public function massDestroy(MassDestroyRecommendationRequest $request)
    {
        $recommendations = Recommendation::find(request('ids'));

        foreach ($recommendations as $recommendation) {
            $recommendation->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('recommendation_create') && Gate::denies('recommendation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Recommendation();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}