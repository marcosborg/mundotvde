@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.activityLaunch.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.activity-launches.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.driver') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->driver->code ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.week') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->week->start_date ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.rent') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->rent }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.management') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->management }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.insurance') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->insurance }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.fuel') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->fuel }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.tolls') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->tolls }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.others') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->others }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.refund') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->refund }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.initial_kilometers') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->initial_kilometers }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.final_kilometers') }}
                                    </th>
                                    <td>
                                        {{ $activityLaunch->final_kilometers }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.send') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $activityLaunch->send ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityLaunch.fields.paid') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $activityLaunch->paid ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.activity-launches.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.relatedData') }}
                </div>
                <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
                    <li role="presentation">
                        <a href="#activity_launch_activity_per_operators" aria-controls="activity_launch_activity_per_operators" role="tab" data-toggle="tab">
                            {{ trans('cruds.activityPerOperator.title') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" role="tabpanel" id="activity_launch_activity_per_operators">
                        @includeIf('admin.activityLaunches.relationships.activityLaunchActivityPerOperators', ['activityPerOperators' => $activityLaunch->activityLaunchActivityPerOperators])
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection