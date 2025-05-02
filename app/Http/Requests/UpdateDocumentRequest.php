<?php

namespace App\Http\Requests;

use App\Models\Document;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_edit');
    }

    public function rules()
    {
        return [
            'driver_id' => [
                'required',
                'integer',
            ],
            'notify_text' => [
                'string',
                'nullable',
            ],
            'citizen_card' => [
                'array',
            ],
            'tvde_driver_certificate' => [
                'array',
            ],
            'criminal_record' => [
                'array',
            ],
            'driving_license' => [
                'array',
            ],
            'iban' => [
                'array',
            ],
            'address' => [
                'array',
            ],
            'dua_vehicle' => [
                'array',
            ],
            'car_insurance' => [
                'array',
            ],
            'ipo_vehicle' => [
                'array',
            ],
        ];
    }
}
