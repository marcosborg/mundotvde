<?php

namespace App\Http\Requests;

use App\Models\CrmCard;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmCardRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_card_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_cards,id',
        ];
    }
}
