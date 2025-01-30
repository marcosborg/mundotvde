<?php

namespace App\Http\Requests;

use App\Models\TimeLog;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTimeLogRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('time_log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:time_logs,id',
        ];
    }
}
