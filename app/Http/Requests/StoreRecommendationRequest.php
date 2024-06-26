<?php

namespace App\Http\Requests;

use App\Models\Recommendation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRecommendationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('recommendation_create');
    }

    public function rules()
    {
        return [
            'driver_id' => [
                'required',
                'integer',
            ],
            'recommendation_status_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
            ],
            'phone' => [
                'string',
                'required',
            ],
            'city' => [
                'string',
                'required',
            ],
        ];
    }
}
