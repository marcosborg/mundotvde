<?php

namespace App\Http\Requests;

use App\Models\DocCompany;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDocCompanyRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('doc_company_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:doc_companies,id',
        ];
    }
}
