<?php

namespace App\Http\Requests;

use App\Models\WebsiteMessage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateWebsiteMessageRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('website_message_edit');
    }

    public function rules()
    {
        return [];
    }
}
