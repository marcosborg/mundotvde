<?php

namespace App\Http\Requests;

use App\Models\Receipt;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateReceiptRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('receipt_edit');
    }

    public function rules()
    {
        return [
            'reference' => [
                'string',
                'required',
            ],
            'activity_launch_id' => [
                'required',
                'integer',
            ],
            'receipt' => [
                'array',
                'required',
            ],
            'receipt.*' => [
                'required',
            ],
        ];
    }
}
