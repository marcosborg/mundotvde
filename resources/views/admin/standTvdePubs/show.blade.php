@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.standTvdePub.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.stand-tvde-pubs.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $standTvdePub->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $standTvdePub->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.image') }}
                                    </th>
                                    <td>
                                        @if($standTvdePub->image)
                                            <a href="{{ $standTvdePub->image->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $standTvdePub->image->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePub.fields.text') }}
                                    </th>
                                    <td>
                                        {!! $standTvdePub->text !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.stand-tvde-pubs.index') }}">
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