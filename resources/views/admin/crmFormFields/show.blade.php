@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmFormField.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-form-fields.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.form') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->form->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.label') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->label }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.type') }}
                                    </th>
                                    <td>
                                        {{ App\Models\CrmFormField::TYPE_RADIO[$crmFormField->type] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.required') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmFormField->required ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.help_text') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->help_text }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.placeholder') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->placeholder }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.default_value') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->default_value }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.is_unique') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmFormField->is_unique ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.min_value') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->min_value }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.max_value') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->max_value }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.options_json') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->options_json }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormField.fields.position') }}
                                    </th>
                                    <td>
                                        {{ $crmFormField->position }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-form-fields.index') }}">
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