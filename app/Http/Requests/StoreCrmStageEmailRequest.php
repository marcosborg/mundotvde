<?php

namespace App\Http\Requests;

use App\Models\CrmStageEmail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCrmStageEmailRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_stage_email_create');
    }

    public function rules()
    {
        return [
            'stage_id' => [
                'required',
                'integer',
            ],
            'subject' => [
                'string',
                'required',
            ],
            'delay_minutes' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
