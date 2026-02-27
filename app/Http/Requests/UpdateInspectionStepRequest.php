<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInspectionStepRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('inspection_edit');
    }

    public function rules()
    {
        return [
            'step' => ['required', 'integer', 'min:1', 'max:10'],
            'action' => ['nullable', 'in:save,complete'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],

            'checklist' => ['nullable', 'array'],
            'checklist.*' => ['nullable', 'array'],
            'checklist_photos' => ['nullable', 'array'],
            'checklist_photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],

            'exterior_photos' => ['nullable', 'array'],
            'exterior_photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'interior_photos' => ['nullable', 'array'],
            'interior_photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],

            'location' => ['nullable', 'string', 'max:30'],
            'part' => ['nullable', 'string', 'max:120'],
            'part_section' => ['nullable', 'string', 'max:120'],
            'damage_type' => ['nullable', 'string', 'max:40'],
            'damage_notes' => ['nullable', 'string'],
            'damage_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],

            'extra_observations' => ['nullable', 'string'],
            'extra_photos' => ['nullable', 'array'],
            'extra_photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],

            'inspector_name' => ['nullable', 'string', 'max:255'],
            'driver_signature_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
