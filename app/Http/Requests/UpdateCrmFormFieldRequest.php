<?php

namespace App\Http\Requests;

use App\Models\CrmFormField;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmFormFieldRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_form_field_edit');
    }

    public function rules()
    {
        return [
            'form_id' => [
                'required',
                'integer',
            ],
            'label' => [
                'string',
                'required',
            ],
            'help_text' => [
                'string',
                'nullable',
            ],
            'placeholder' => [
                'string',
                'nullable',
            ],
            'default_value' => [
                'string',
                'nullable',
            ],
            'min_value' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'max_value' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
