<?php

namespace App\Http\Requests;

use App\Models\StandTvdePage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreStandTvdePageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stand_tvde_page_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
        ];
    }
}