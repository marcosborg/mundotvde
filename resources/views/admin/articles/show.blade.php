@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.article.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.articles.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.article.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $article->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.article.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $article->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.article.fields.resume') }}
                                    </th>
                                    <td>
                                        {{ $article->resume }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.article.fields.text') }}
                                    </th>
                                    <td>
                                        {!! $article->text !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.article.fields.photo') }}
                                    </th>
                                    <td>
                                        @if($article->photo)
                                            <a href="{{ $article->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                                <img src="{{ $article->photo->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.article.fields.active') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled" {{ $article->active ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.articles.index') }}">
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