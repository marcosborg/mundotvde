@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.websiteMessage.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.website-messages.update", [$websiteMessage->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('cruds.websiteMessage.fields.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $websiteMessage->email) }}">
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.websiteMessage.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('messages') ? 'has-error' : '' }}">
                            <label for="messages">{{ trans('cruds.websiteMessage.fields.messages') }}</label>
                            <textarea class="form-control" name="messages" id="messages">{{ old('messages', $websiteMessage->messages) }}</textarea>
                            @if($errors->has('messages'))
                                <span class="help-block" role="alert">{{ $errors->first('messages') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.websiteMessage.fields.messages_helper') }}</span>
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