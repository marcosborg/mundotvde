<?php

namespace App\Http\Requests;

use App\Models\StandTvdePage;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStandTvdePageRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('stand_tvde_page_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:stand_tvde_pages,id',
        ];
    }
}