<?php

namespace App\Http\Requests;

use App\Models\WhatsappMessage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateWhatsappMessageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('whatsapp_message_edit');
    }

    public function rules()
    {
        return [
            'user' => [
                'string',
                'required',
            ],
        ];
    }
}
