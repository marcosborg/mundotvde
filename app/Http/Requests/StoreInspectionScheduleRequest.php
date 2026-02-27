<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('inspection_edit');
    }

    public function rules()
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicle_items,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'frequency_days' => ['required', 'integer', 'min:1', 'max:365'],
            'start_at' => ['nullable', 'date'],
            'next_run_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
