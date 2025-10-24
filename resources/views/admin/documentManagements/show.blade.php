@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.documentManagement.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.document-managements.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $documentManagement->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $documentManagement->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.doc_company') }}
                                    </th>
                                    <td>
                                        {{ $documentManagement->doc_company->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.text') }}
                                    </th>
                                    <td>
                                        {!! $documentManagement->text !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.documentManagement.fields.signature') }}
                                    </th>
                                    <td>
                                        @foreach($documentManagement->signatures as $key => $signature)
                                            <span class="label label-info">{{ $signature->title }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.document-managements.index') }}">
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