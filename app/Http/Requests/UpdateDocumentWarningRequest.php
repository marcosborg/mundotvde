<?php

namespace App\Http\Requests;

use App\Models\DocumentWarning;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDocumentWarningRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('document_warning_edit');
    }

    public function rules()
    {
        return [];
    }
}
