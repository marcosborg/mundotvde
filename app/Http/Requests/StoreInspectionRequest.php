<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('inspection_create');
    }

    public function rules()
    {
        return [
            'type' => ['required', 'in:initial,handover,routine,return'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicle_items,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'location_lat' => ['nullable', 'numeric'],
            'location_lng' => ['nullable', 'numeric'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'location_accuracy' => ['nullable', 'numeric'],
            'location_timezone' => ['nullable', 'string', 'max:60'],
        ];
    }
}
