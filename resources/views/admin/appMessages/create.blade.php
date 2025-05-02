@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.appMessage.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.app-messages.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                            <label class="required" for="user_id">{{ trans('cruds.appMessage.fields.user') }}</label>
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                @foreach($users as $id => $entry)
                                    <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user'))
                                <span class="help-block" role="alert">{{ $errors->first('user') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.appMessage.fields.user_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('messages') ? 'has-error' : '' }}">
                            <label for="messages">{{ trans('cruds.appMessage.fields.messages') }}</label>
                            <textarea class="form-control" name="messages" id="messages">{{ old('messages') }}</textarea>
                            @if($errors->has('messages'))
                                <span class="help-block" role="alert">{{ $errors->first('messages') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.appMessage.fields.messages_helper') }}</span>
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