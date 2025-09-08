<?php

namespace App\Http\Requests;

use App\Models\ActivityLaunch;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreActivityLaunchRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_launch_create');
    }

    public function rules()
    {
        return [
            'driver_id' => [
                'required',
                'integer',
            ],
            'week_id' => [
                'required',
                'integer',
            ],
            'rent' => [
                'required',
            ],
            'management' => [
                'required',
            ],
            'insurance' => [
                'required',
            ],
            'fuel' => [
                'required',
            ],
            'tolls' => [
                'required',
            ],
            'garage' => [
                'required',
            ],
            'others' => [
                'required',
            ],
            'refund' => [
                'required',
            ],
            'management_fee' => [
                'required',
            ],
            'initial_kilometers' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'final_kilometers' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
