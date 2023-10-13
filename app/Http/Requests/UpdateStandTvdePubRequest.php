<?php

namespace App\Http\Requests;

use App\Models\StandTvdePub;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStandTvdePubRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stand_tvde_pub_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'nullable',
            ],
        ];
    }
}