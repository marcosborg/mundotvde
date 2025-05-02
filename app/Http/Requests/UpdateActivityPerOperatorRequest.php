<?php

namespace App\Http\Requests;

use App\Models\ActivityPerOperator;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateActivityPerOperatorRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_per_operator_edit');
    }

    public function rules()
    {
        return [
            'activity_launch_id' => [
                'required',
                'integer',
            ],
            'tvde_operator_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
