@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.standTvdeContact.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.stand-tvde-contacts.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.email') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.phone') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->phone }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.car') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->car }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.subject') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->subject }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.standTvdeContact.fields.message') }}
                                    </th>
                                    <td>
                                        {{ $standTvdeContact->message }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.stand-tvde-contacts.index') }}">
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