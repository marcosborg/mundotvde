<?php

namespace App\Http\Requests;

use App\Models\WebsiteMessage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyWebsiteMessageRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('website_message_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:website_messages,id',
        ];
    }
}
