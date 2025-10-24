<?php

namespace App\Http\Requests;

use App\Models\DocumentManagement;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDocumentManagementRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_management_edit');
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
