<?php

namespace App\Http\Requests;

use App\Models\AppMessage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreAppMessageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('app_message_create');
    }

    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
