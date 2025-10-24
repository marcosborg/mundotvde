@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.docCompany.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.doc-companies.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.nipc') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->nipc }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.address') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->address }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.location') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->location }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.zip') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->zip }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.docCompany.fields.country') }}
                                    </th>
                                    <td>
                                        {{ $docCompany->country }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.doc-companies.index') }}">
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