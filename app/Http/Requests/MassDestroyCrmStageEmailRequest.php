<?php

namespace App\Http\Requests;

use App\Models\CrmStageEmail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmStageEmailRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_stage_email_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_stage_emails,id',
        ];
    }
}
