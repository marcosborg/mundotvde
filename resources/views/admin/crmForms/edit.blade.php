@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.crmForm.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-forms.update", [$crmForm->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                            <label for="category_id">{{ trans('cruds.crmForm.fields.category') }}</label>
                            <select class="form-control select2" name="category_id" id="category_id">
                                @foreach($categories as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('category_id') ? old('category_id') : $crmForm->category->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('category'))
                                <span class="help-block" role="alert">{{ $errors->first('category') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.category_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.crmForm.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $crmForm->name) }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('slug') ? 'has-error' : '' }}">
                            <label class="required" for="slug">{{ trans('cruds.crmForm.fields.slug') }}</label>
                            <input class="form-control" type="text" name="slug" id="slug" value="{{ old('slug', $crmForm->slug) }}" required>
                            @if($errors->has('slug'))
                                <span class="help-block" role="alert">{{ $errors->first('slug') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.slug_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmForm.fields.status') }}</label>
                            @foreach(App\Models\CrmForm::STATUS_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="status_{{ $key }}" name="status" value="{{ $key }}" {{ old('status', $crmForm->status) === (string) $key ? 'checked' : '' }}>
                                    <label for="status_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('confirmation_message') ? 'has-error' : '' }}">
                            <label for="confirmation_message">{{ trans('cruds.crmForm.fields.confirmation_message') }}</label>
                            <input class="form-control" type="text" name="confirmation_message" id="confirmation_message" value="{{ old('confirmation_message', $crmForm->confirmation_message) }}">
                            @if($errors->has('confirmation_message'))
                                <span class="help-block" role="alert">{{ $errors->first('confirmation_message') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.confirmation_message_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('redirect_url') ? 'has-error' : '' }}">
                            <label for="redirect_url">{{ trans('cruds.crmForm.fields.redirect_url') }}</label>
                            <input class="form-control" type="text" name="redirect_url" id="redirect_url" value="{{ old('redirect_url', $crmForm->redirect_url) }}">
                            @if($errors->has('redirect_url'))
                                <span class="help-block" role="alert">{{ $errors->first('redirect_url') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.redirect_url_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('notify_emails') ? 'has-error' : '' }}">
                            <label for="notify_emails">{{ trans('cruds.crmForm.fields.notify_emails') }}</label>
                            <input class="form-control" type="text" name="notify_emails" id="notify_emails" value="{{ old('notify_emails', $crmForm->notify_emails) }}">
                            @if($errors->has('notify_emails'))
                                <span class="help-block" role="alert">{{ $errors->first('notify_emails') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.notify_emails_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('create_card_on_submit') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="create_card_on_submit" value="0">
                                <input type="checkbox" name="create_card_on_submit" id="create_card_on_submit" value="1" {{ $crmForm->create_card_on_submit || old('create_card_on_submit', 0) === 1 ? 'checked' : '' }}>
                                <label for="create_card_on_submit" style="font-weight: 400">{{ trans('cruds.crmForm.fields.create_card_on_submit') }}</label>
                            </div>
                            @if($errors->has('create_card_on_submit'))
                                <span class="help-block" role="alert">{{ $errors->first('create_card_on_submit') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmForm.fields.create_card_on_submit_helper') }}</span>
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