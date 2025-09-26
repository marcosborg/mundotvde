<?php

namespace App\Http\Requests;

use App\Models\CrmCardActivity;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmCardActivityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_card_activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_card_activities,id',
        ];
    }
}
