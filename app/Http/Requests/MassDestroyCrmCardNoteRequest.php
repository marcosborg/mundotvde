<?php

namespace App\Http\Requests;

use App\Models\CrmCardNote;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmCardNoteRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_card_note_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_card_notes,id',
        ];
    }
}
