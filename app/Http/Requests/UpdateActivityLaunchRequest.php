<?php

namespace App\Http\Requests;

use App\Models\ActivityLaunch;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateActivityLaunchRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('activity_launch_edit');
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
            'others' => [
                'required',
            ],
            'refund' => [
                'required',
            ],
        ];
    }
}
