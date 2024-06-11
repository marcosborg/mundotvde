<?php

namespace App\Http\Requests;

use App\Models\DocumentWarning;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDocumentWarningRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('document_warning_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:document_warnings,id',
        ];
    }
}
