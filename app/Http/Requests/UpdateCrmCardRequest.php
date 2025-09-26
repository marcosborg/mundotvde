<?php

namespace App\Http\Requests;

use App\Models\CrmCard;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCrmCardRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('crm_card_edit');
    }

    public function rules()
    {
        return [
            'category_id' => [
                'required',
                'integer',
            ],
            'stage_id' => [
                'required',
                'integer',
            ],
            'title' => [
                'string',
                'required',
            ],
            'lost_reason' => [
                'string',
                'nullable',
            ],
            'won_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'closed_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'due_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'position' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'crm_card_attachments' => [
                'array',
            ],
        ];
    }
}
