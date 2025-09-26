@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.crmFormSubmission.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-form-submissions.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('form') ? 'has-error' : '' }}">
                            <label class="required" for="form_id">{{ trans('cruds.crmFormSubmission.fields.form') }}</label>
                            <select class="form-control select2" name="form_id" id="form_id" required>
                                @foreach($forms as $id => $entry)
                                    <option value="{{ $id }}" {{ old('form_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('form'))
                                <span class="help-block" role="alert">{{ $errors->first('form') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.form_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                            <label class="required" for="category_id">{{ trans('cruds.crmFormSubmission.fields.category') }}</label>
                            <select class="form-control select2" name="category_id" id="category_id" required>
                                @foreach($categories as $id => $entry)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('category'))
                                <span class="help-block" role="alert">{{ $errors->first('category') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.category_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('submitted_at') ? 'has-error' : '' }}">
                            <label for="submitted_at">{{ trans('cruds.crmFormSubmission.fields.submitted_at') }}</label>
                            <input class="form-control datetime" type="text" name="submitted_at" id="submitted_at" value="{{ old('submitted_at') }}">
                            @if($errors->has('submitted_at'))
                                <span class="help-block" role="alert">{{ $errors->first('submitted_at') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.submitted_at_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('user_agent') ? 'has-error' : '' }}">
                            <label for="user_agent">{{ trans('cruds.crmFormSubmission.fields.user_agent') }}</label>
                            <input class="form-control" type="text" name="user_agent" id="user_agent" value="{{ old('user_agent', '') }}">
                            @if($errors->has('user_agent'))
                                <span class="help-block" role="alert">{{ $errors->first('user_agent') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.user_agent_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('referer') ? 'has-error' : '' }}">
                            <label for="referer">{{ trans('cruds.crmFormSubmission.fields.referer') }}</label>
                            <input class="form-control" type="text" name="referer" id="referer" value="{{ old('referer', '') }}">
                            @if($errors->has('referer'))
                                <span class="help-block" role="alert">{{ $errors->first('referer') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.referer_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('utm_json') ? 'has-error' : '' }}">
                            <label for="utm_json">{{ trans('cruds.crmFormSubmission.fields.utm_json') }}</label>
                            <textarea class="form-control" name="utm_json" id="utm_json">{{ old('utm_json') }}</textarea>
                            @if($errors->has('utm_json'))
                                <span class="help-block" role="alert">{{ $errors->first('utm_json') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.utm_json_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('data_json') ? 'has-error' : '' }}">
                            <label for="data_json">{{ trans('cruds.crmFormSubmission.fields.data_json') }}</label>
                            <textarea class="form-control" name="data_json" id="data_json">{{ old('data_json') }}</textarea>
                            @if($errors->has('data_json'))
                                <span class="help-block" role="alert">{{ $errors->first('data_json') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.data_json_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('created_card') ? 'has-error' : '' }}">
                            <label for="created_card_id">{{ trans('cruds.crmFormSubmission.fields.created_card') }}</label>
                            <select class="form-control select2" name="created_card_id" id="created_card_id">
                                @foreach($created_cards as $id => $entry)
                                    <option value="{{ $id }}" {{ old('created_card_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('created_card'))
                                <span class="help-block" role="alert">{{ $errors->first('created_card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormSubmission.fields.created_card_helper') }}</span>
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