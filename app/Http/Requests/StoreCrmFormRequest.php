<?php

namespace App\Http\Requests;

use App\Models\CrmForm;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCrmFormRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_form_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:crm_forms',
            ],
            'slug' => [
                'string',
                'required',
                'unique:crm_forms',
            ],
            'confirmation_message' => [
                'string',
                'nullable',
            ],
            'redirect_url' => [
                'string',
                'nullable',
            ],
            'notify_emails' => [
                'string',
                'nullable',
            ],
        ];
    }
}
