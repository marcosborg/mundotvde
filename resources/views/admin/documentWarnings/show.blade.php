@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.documentWarning.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.document-warnings.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $documentWarning->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.citizen_card') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->citizen_card ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.tvde_driver_certificate') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->tvde_driver_certificate ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.criminal_record') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->criminal_record ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.profile_picture') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->profile_picture ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.driving_license') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->driving_license ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.iban') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->iban ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.dua_vehicle') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->dua_vehicle ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.car_insurance') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->car_insurance ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentWarning.fields.ipo_vehicle') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $documentWarning->ipo_vehicle ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.document-warnings.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection