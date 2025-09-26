<?php

namespace App\Http\Requests;

use App\Models\CrmCardNote;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmCardNoteRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_card_note_edit');
    }

    public function rules()
    {
        return [
            'card_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
