@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmStageEmail.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-stage-emails.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmStageEmail->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.stage') }}
                                    </th>
                                    <td>
                                        {{ $crmStageEmail->stage->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.to_emails') }}
                                    </th>
                                    <td>
                                        {{ $crmStageEmail->to_emails }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.bcc_emails') }}
                                    </th>
                                    <td>
                                        {{ $crmStageEmail->bcc_emails }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.subject') }}
                                    </th>
                                    <td>
                                        {{ $crmStageEmail->subject }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.body_template') }}
                                    </th>
                                    <td>
                                        {!! $crmStageEmail->body_template !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.send_on_enter') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmStageEmail->send_on_enter ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.send_on_exit') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmStageEmail->send_on_exit ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.delay_minutes') }}
                                    </th>
                                    <td>
                                        {{ $crmStageEmail->delay_minutes }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStageEmail.fields.is_active') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmStageEmail->is_active ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-stage-emails.index') }}">
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