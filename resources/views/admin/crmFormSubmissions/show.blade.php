@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmFormSubmission.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-form-submissions.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.form') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->form->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.category') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->category->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.submitted_at') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->submitted_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.user_agent') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->user_agent }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.referer') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->referer }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.utm_json') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->utm_json }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.data_json') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->data_json }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmFormSubmission.fields.created_card') }}
                                    </th>
                                    <td>
                                        {{ $crmFormSubmission->created_card->title ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-form-submissions.index') }}">
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