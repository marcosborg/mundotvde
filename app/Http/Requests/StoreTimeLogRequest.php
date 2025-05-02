<?php

namespace App\Http\Requests;

use App\Models\TimeLog;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTimeLogRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('time_log_create');
    }

    public function rules()
    {
        return [
            'driver_id' => [
                'required',
                'integer',
            ],
            'status' => [
                'required',
            ],
        ];
    }
}
