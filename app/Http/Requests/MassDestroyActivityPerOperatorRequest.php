<?php

namespace App\Http\Requests;

use App\Models\ActivityPerOperator;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyActivityPerOperatorRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('activity_per_operator_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:activity_per_operators,id',
        ];
    }
}
