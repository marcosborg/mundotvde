<?php

namespace App\Http\Requests;

use App\Models\CrmFormSubmission;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmFormSubmissionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_form_submission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_form_submissions,id',
        ];
    }
}
