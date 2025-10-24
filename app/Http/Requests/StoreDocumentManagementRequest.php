<?php

namespace App\Http\Requests;

use App\Models\DocumentManagement;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDocumentManagementRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_management_create');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'doc_company_id' => [
                'required',
                'integer',
            ],
            'signatures.*' => [
                'integer',
            ],
            'signatures' => [
                'array',
            ],
        ];
    }
}
