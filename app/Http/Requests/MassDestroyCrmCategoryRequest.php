<?php

namespace App\Http\Requests;

use App\Models\CrmCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCrmCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('crm_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:crm_categories,id',
        ];
    }
}
