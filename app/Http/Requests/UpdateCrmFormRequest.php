<?php

namespace App\Http\Requests;

use App\Models\CrmForm;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmFormRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_form_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:crm_forms,name,' . request()->route('crm_form')->id,
            ],
            'slug' => [
                'string',
                'required',
                'unique:crm_forms,slug,' . request()->route('crm_form')->id,
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
