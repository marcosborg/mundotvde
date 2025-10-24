<?php

namespace App\Http\Requests;

use App\Models\Signature;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSignatureRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('signature_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
        ];
    }
}
