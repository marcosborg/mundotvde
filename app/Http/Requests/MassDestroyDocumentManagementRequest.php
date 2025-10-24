<?php

namespace App\Http\Requests;

use App\Models\DocumentManagement;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDocumentManagementRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('document_management_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:document_managements,id',
        ];
    }
}
