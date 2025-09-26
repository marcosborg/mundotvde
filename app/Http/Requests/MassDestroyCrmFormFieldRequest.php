<?php

namespace App\Http\Requests;

use App\Models\CrmFormField;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmFormFieldRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_form_field_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_form_fields,id',
        ];
    }
}
