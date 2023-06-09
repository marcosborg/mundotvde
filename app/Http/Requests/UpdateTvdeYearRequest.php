<?php

namespace App\Http\Requests;

use App\Models\TvdeYear;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTvdeYearRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('tvde_year_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
