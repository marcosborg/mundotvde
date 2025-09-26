<?php

namespace App\Http\Requests;

use App\Models\CrmFormSubmission;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCrmFormSubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_form_submission_create');
    }

    public function rules()
    {
        return [
            'form_id' => [
                'required',
                'integer',
            ],
            'category_id' => [
                'required',
                'integer',
            ],
            'submitted_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'user_agent' => [
                'string',
                'nullable',
            ],
            'referer' => [
                'string',
                'nullable',
            ],
        ];
    }
}
