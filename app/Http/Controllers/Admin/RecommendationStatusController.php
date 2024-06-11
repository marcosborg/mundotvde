<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRecommendationStatusRequest;
use App\Http\Requests\StoreRecommendationStatusRequest;
use App\Http\Requests\UpdateRecommendationStatusRequest;
use App\Models\RecommendationStatus;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecommendationStatusController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('recommendation_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recommendationStatuses = RecommendationStatus::all();

        return view('admin.recommendationStatuses.index', compact('recommendationStatuses'));
    }

    public function create()
    {
        abort_if(Gate::denies('recommendation_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.recommendationStatuses.create');
    }

    public function store(StoreRecommendationStatusRequest $request)
    {
        $recommendationStatus = RecommendationStatus::create($request->all());

        return redirect()->route('admin.recommendation-statuses.index');
    }

    public function edit(RecommendationStatus $recommendationStatus)
    {
        abort_if(Gate::denies('recommendation_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.recommendationStatuses.edit', compact('recommendationStatus'));
    }

    public function update(UpdateRecommendationStatusRequest $request, RecommendationStatus $recommendationStatus)
    {
        $recommendationStatus->update($request->all());

        return redirect()->route('admin.recommendation-statuses.index');
    }

    public function show(RecommendationStatus $recommendationStatus)
    {
        abort_if(Gate::denies('recommendation_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.recommendationStatuses.show', compact('recommendationStatus'));
    }

    public function destroy(RecommendationStatus $recommendationStatus)
    {
        abort_if(Gate::denies('recommendation_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $recommendationStatus->delete();

        return back();
    }

    public function massDestroy(MassDestroyRecommendationStatusRequest $request)
    {
        $recommendationStatuses = RecommendationStatus::find(request('ids'));

        foreach ($recommendationStatuses as $recommendationStatus) {
            $recommendationStatus->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
