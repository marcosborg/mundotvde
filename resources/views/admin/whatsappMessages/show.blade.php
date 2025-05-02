@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.show') }} {{ trans('cruds.whatsappMessage.title') }}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.whatsapp-messages.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.whatsappMessage.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $whatsappMessage->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.whatsappMessage.fields.user') }}
                                    </th>
                                    <td>
                                        {{ $whatsappMessage->user }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.whatsappMessage.fields.messages') }}
                                    </th>
                                    <td>
                                        {{ $whatsappMessage->messages }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.whatsapp-messages.index') }}">
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