<?php

namespace App\Http\Requests;

use App\Models\DocumentGenerated;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDocumentGeneratedRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_generated_create');
    }

    public function rules()
    {
        return [
            'document_management_id' => [
                'required',
                'integer',
            ],
            'driver_id' => [
                'required',
                'integer',
            ],
            'date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
        ];
    }
}
