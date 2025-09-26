@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.crmStage.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-stages.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $crmStage->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.category') }}
                                    </th>
                                    <td>
                                        {{ $crmStage->category->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $crmStage->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.position') }}
                                    </th>
                                    <td>
                                        {{ $crmStage->position }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.color') }}
                                    </th>
                                    <td>
                                        {{ $crmStage->color }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.is_won') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmStage->is_won ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.is_lost') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $crmStage->is_lost ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.crmStage.fields.auto_assign_to_user') }}
                                    </th>
                                    <td>
                                        {{ $crmStage->auto_assign_to_user->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.crm-stages.index') }}">
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