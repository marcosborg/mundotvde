<?php

namespace App\Http\Requests;

use App\Models\DocCompany;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDocCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('doc_company_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'nipc' => [
                'string',
                'nullable',
            ],
            'license_number' => [
                'string',
                'nullable',
            ],
            'address' => [
                'string',
                'nullable',
            ],
            'location' => [
                'string',
                'nullable',
            ],
            'zip' => [
                'string',
                'nullable',
            ],
            'country' => [
                'string',
                'nullable',
            ],
        ];
    }
}
