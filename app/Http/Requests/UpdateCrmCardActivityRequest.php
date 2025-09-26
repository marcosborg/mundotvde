<?php

namespace App\Http\Requests;

use App\Models\CrmCardActivity;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmCardActivityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_card_activity_edit');
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
