<?php

namespace App\Http\Requests;

use App\Models\DocumentGenerated;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDocumentGeneratedRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('document_generated_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:document_generateds,id',
        ];
    }
}
