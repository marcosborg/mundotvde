<?php

namespace App\Http\Requests;

use App\Models\CrmCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCrmCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_category_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:crm_categories',
            ],
            'slug' => [
                'string',
                'required',
                'unique:crm_categories',
            ],
            'color' => [
                'string',
                'nullable',
            ],
            'position' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'unique:crm_categories,position',
            ],
        ];
    }
}
