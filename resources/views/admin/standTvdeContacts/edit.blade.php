@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.standTvdeContact.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.stand-tvde-contacts.update", [$standTvdeContact->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.standTvdeContact.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $standTvdeContact->name) }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.standTvdeContact.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="required" for="email">{{ trans('cruds.standTvdeContact.fields.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $standTvdeContact->email) }}" required>
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.standTvdeContact.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <label class="required" for="phone">{{ trans('cruds.standTvdeContact.fields.phone') }}</label>
                            <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $standTvdeContact->phone) }}" required>
                            @if($errors->has('phone'))
                                <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.standTvdeContact.fields.phone_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('car') ? 'has-error' : '' }}">
                            <label class="required" for="car">{{ trans('cruds.standTvdeContact.fields.car') }}</label>
                            <input class="form-control" type="text" name="car" id="car" value="{{ old('car', $standTvdeContact->car) }}" required>
                            @if($errors->has('car'))
                                <span class="help-block" role="alert">{{ $errors->first('car') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.standTvdeContact.fields.car_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                            <label class="required" for="subject">{{ trans('cruds.standTvdeContact.fields.subject') }}</label>
                            <input class="form-control" type="text" name="subject" id="subject" value="{{ old('subject', $standTvdeContact->subject) }}" required>
                            @if($errors->has('subject'))
                                <span class="help-block" role="alert">{{ $errors->first('subject') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.standTvdeContact.fields.subject_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('message') ? 'has-error' : '' }}">
                            <label for="message">{{ trans('cruds.standTvdeContact.fields.message') }}</label>
                            <textarea class="form-control" name="message" id="message">{{ old('message', $standTvdeContact->message) }}</textarea>
                            @if($errors->has('message'))
                                <span class="help-block" role="alert">{{ $errors->first('message') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.standTvdeContact.fields.message_helper') }}</span>
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