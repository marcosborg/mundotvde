@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.bot.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.bots.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.bot.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.bot.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('instructions') ? 'has-error' : '' }}">
                            <label class="required" for="instructions">{{ trans('cruds.bot.fields.instructions') }}</label>
                            <textarea class="form-control" name="instructions" id="instructions" required>{{ old('instructions') }}</textarea>
                            @if($errors->has('instructions'))
                                <span class="help-block" role="alert">{{ $errors->first('instructions') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.bot.fields.instructions_helper') }}</span>
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