<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInspectionScheduleRequest extends FormRequest
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
            'routine_config' => ['nullable', 'array'],
            'routine_config.documents' => ['nullable', 'array'],
            'routine_config.documents.*' => ['string'],
            'routine_config.operational_checks' => ['nullable', 'array'],
            'routine_config.operational_checks.*' => ['string'],
            'routine_config.accessories' => ['nullable', 'array'],
            'routine_config.accessories.*' => ['string'],
            'routine_config.exterior_slots' => ['nullable', 'array'],
            'routine_config.exterior_slots.*' => ['string'],
            'routine_config.interior_slots' => ['nullable', 'array'],
            'routine_config.interior_slots.*' => ['string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
