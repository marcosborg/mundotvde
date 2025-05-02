<?php

namespace App\Http\Requests;

use App\Models\DocumentWarning;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDocumentWarningRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_warning_create');
    }

    public function rules()
    {
        return [];
    }
}
