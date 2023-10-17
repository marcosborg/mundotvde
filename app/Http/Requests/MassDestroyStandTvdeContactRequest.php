<?php

namespace App\Http\Requests;

use App\Models\StandTvdeContact;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStandTvdeContactRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('stand_tvde_contact_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:stand_tvde_contacts,id',
        ];
    }
}