<?php

namespace App\Http\Requests;

use App\Models\RecommendationStatus;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateRecommendationStatusRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('recommendation_status_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
