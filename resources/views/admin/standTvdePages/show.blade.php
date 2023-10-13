@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.standTvdePage.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.stand-tvde-pages.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $standTvdePage->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $standTvdePage->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.image') }}
                                    </th>
                                    <td>
                                        @if($standTvdePage->image)
                                            <a href="{{ $standTvdePage->image->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $standTvdePage->image->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdePage.fields.text') }}
                                    </th>
                                    <td>
                                        {!! $standTvdePage->text !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.stand-tvde-pages.index') }}">
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