<?php

namespace App\Http\Requests;

use App\Models\CrmCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_category_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
                'unique:crm_categories,name,' . request()->route('crm_category')->id,
            ],
            'slug' => [
                'string',
                'required',
                'unique:crm_categories,slug,' . request()->route('crm_category')->id,
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
                'unique:crm_categories,position,' . request()->route('crm_category')->id,
            ],
        ];
    }
}
