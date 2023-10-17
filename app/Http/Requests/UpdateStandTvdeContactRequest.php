<?php

namespace App\Http\Requests;

use App\Models\StandTvdeContact;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStandTvdeContactRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('stand_tvde_contact_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
            ],
            'phone' => [
                'string',
                'required',
            ],
            'car' => [
                'string',
                'required',
            ],
            'subject' => [
                'string',
                'required',
            ],
        ];
    }
}