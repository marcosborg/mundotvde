@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.whatsappMessage.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.whatsapp-messages.update", [$whatsappMessage->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                            <label class="required" for="user">{{ trans('cruds.whatsappMessage.fields.user') }}</label>
                            <input class="form-control" type="text" name="user" id="user" value="{{ old('user', $whatsappMessage->user) }}" required>
                            @if($errors->has('user'))
                                <span class="help-block" role="alert">{{ $errors->first('user') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.whatsappMessage.fields.user_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('messages') ? 'has-error' : '' }}">
                            <label for="messages">{{ trans('cruds.whatsappMessage.fields.messages') }}</label>
                            <textarea class="form-control" name="messages" id="messages">{{ old('messages', $whatsappMessage->messages) }}</textarea>
                            @if($errors->has('messages'))
                                <span class="help-block" role="alert">{{ $errors->first('messages') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.whatsappMessage.fields.messages_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection