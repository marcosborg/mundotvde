@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.activityPerOperator.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.activity-per-operators.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityPerOperator.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $activityPerOperator->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityPerOperator.fields.activity_launch') }}
                                    </th>
                                    <td>
                                        {{ $activityPerOperator->activity_launch->rent ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityPerOperator.fields.gross') }}
                                    </th>
                                    <td>
                                        {{ $activityPerOperator->gross }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityPerOperator.fields.net') }}
                                    </th>
                                    <td>
                                        {{ $activityPerOperator->net }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityPerOperator.fields.taxes') }}
                                    </th>
                                    <td>
                                        {{ $activityPerOperator->taxes }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.activityPerOperator.fields.tvde_operator') }}
                                    </th>
                                    <td>
                                        {{ $activityPerOperator->tvde_operator->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.activity-per-operators.index') }}">
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