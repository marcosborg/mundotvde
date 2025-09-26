<?php

namespace App\Http\Requests;

use App\Models\CrmEmailsQueue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmEmailsQueueRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_emails_queue_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_emails_queues,id',
        ];
    }
}
