<?php

namespace App\Http\Requests;

use App\Models\RecommendationStatus;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyRecommendationStatusRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('recommendation_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:recommendation_statuses,id',
        ];
    }
}
