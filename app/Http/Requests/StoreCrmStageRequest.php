<?php

namespace App\Http\Requests;

use App\Models\CrmStage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCrmStageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_stage_create');
    }

    public function rules()
    {
        return [
            'category_id' => [
                'required',
                'integer',
            ],
            'name' => [
                'string',
                'required',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'color' => [
                'string',
                'nullable',
            ],
        ];
    }
}
