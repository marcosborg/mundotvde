<?php

namespace App\Http\Requests;

use App\Models\ActivityLaunch;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyActivityLaunchRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('activity_launch_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:activity_launches,id',
        ];
    }
}
