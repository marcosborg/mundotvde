@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.recommendation.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.recommendations.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.driver') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->driver->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.recommendation_status') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->recommendation_status->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.email') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.phone') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->phone }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.city') }}
                                    </th>
                                    <td>
                                        {{ $recommendation->city }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.recommendation.fields.comments') }}
                                    </th>
                                    <td>
                                        {!! $recommendation->comments !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.recommendations.index') }}">
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