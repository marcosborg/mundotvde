@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.crmCardNote.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-card-notes.update", [$crmCardNote->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('card') ? 'has-error' : '' }}">
                            <label class="required" for="card_id">{{ trans('cruds.crmCardNote.fields.card') }}</label>
                            <select class="form-control select2" name="card_id" id="card_id" required>
                                @foreach($cards as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('card_id') ? old('card_id') : $crmCardNote->card->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('card'))
                                <span class="help-block" role="alert">{{ $errors->first('card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardNote.fields.card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                            <label for="user_id">{{ trans('cruds.crmCardNote.fields.user') }}</label>
                            <select class="form-control select2" name="user_id" id="user_id">
                                @foreach($users as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $crmCardNote->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user'))
                                <span class="help-block" role="alert">{{ $errors->first('user') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardNote.fields.user_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                            <label for="content">{{ trans('cruds.crmCardNote.fields.content') }}</label>
                            <textarea class="form-control" name="content" id="content">{{ old('content', $crmCardNote->content) }}</textarea>
                            @if($errors->has('content'))
                                <span class="help-block" role="alert">{{ $errors->first('content') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardNote.fields.content_helper') }}</span>
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