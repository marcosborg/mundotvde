<?php

namespace App\Http\Requests;

use App\Models\CrmEmailsQueue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmEmailsQueueRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_emails_queue_edit');
    }

    public function rules()
    {
        return [
            'stage_email_id' => [
                'required',
                'integer',
            ],
            'to' => [
                'string',
                'required',
            ],
            'cc' => [
                'string',
                'nullable',
            ],
            'subject' => [
                'string',
                'required',
            ],
            'error' => [
                'string',
                'nullable',
            ],
            'scheduled_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'sent_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }
}
