@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.crmFormField.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-form-fields.update", [$crmFormField->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('form') ? 'has-error' : '' }}">
                            <label class="required" for="form_id">{{ trans('cruds.crmFormField.fields.form') }}</label>
                            <select class="form-control select2" name="form_id" id="form_id" required>
                                @foreach($forms as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('form_id') ? old('form_id') : $crmFormField->form->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('form'))
                                <span class="help-block" role="alert">{{ $errors->first('form') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.form_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('label') ? 'has-error' : '' }}">
                            <label class="required" for="label">{{ trans('cruds.crmFormField.fields.label') }}</label>
                            <input class="form-control" type="text" name="label" id="label" value="{{ old('label', $crmFormField->label) }}" required>
                            @if($errors->has('label'))
                                <span class="help-block" role="alert">{{ $errors->first('label') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.label_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmFormField.fields.type') }}</label>
                            @foreach(App\Models\CrmFormField::TYPE_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="type_{{ $key }}" name="type" value="{{ $key }}" {{ old('type', $crmFormField->type) === (string) $key ? 'checked' : '' }}>
                                    <label for="type_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('type'))
                                <span class="help-block" role="alert">{{ $errors->first('type') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.type_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('required') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="required" value="0">
                                <input type="checkbox" name="required" id="required" value="1" {{ $crmFormField->required || old('required', 0) === 1 ? 'checked' : '' }}>
                                <label for="required" style="font-weight: 400">{{ trans('cruds.crmFormField.fields.required') }}</label>
                            </div>
                            @if($errors->has('required'))
                                <span class="help-block" role="alert">{{ $errors->first('required') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.required_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('help_text') ? 'has-error' : '' }}">
                            <label for="help_text">{{ trans('cruds.crmFormField.fields.help_text') }}</label>
                            <input class="form-control" type="text" name="help_text" id="help_text" value="{{ old('help_text', $crmFormField->help_text) }}">
                            @if($errors->has('help_text'))
                                <span class="help-block" role="alert">{{ $errors->first('help_text') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.help_text_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('placeholder') ? 'has-error' : '' }}">
                            <label for="placeholder">{{ trans('cruds.crmFormField.fields.placeholder') }}</label>
                            <input class="form-control" type="text" name="placeholder" id="placeholder" value="{{ old('placeholder', $crmFormField->placeholder) }}">
                            @if($errors->has('placeholder'))
                                <span class="help-block" role="alert">{{ $errors->first('placeholder') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.placeholder_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('default_value') ? 'has-error' : '' }}">
                            <label for="default_value">{{ trans('cruds.crmFormField.fields.default_value') }}</label>
                            <input class="form-control" type="text" name="default_value" id="default_value" value="{{ old('default_value', $crmFormField->default_value) }}">
                            @if($errors->has('default_value'))
                                <span class="help-block" role="alert">{{ $errors->first('default_value') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.default_value_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('is_unique') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="is_unique" value="0">
                                <input type="checkbox" name="is_unique" id="is_unique" value="1" {{ $crmFormField->is_unique || old('is_unique', 0) === 1 ? 'checked' : '' }}>
                                <label for="is_unique" style="font-weight: 400">{{ trans('cruds.crmFormField.fields.is_unique') }}</label>
                            </div>
                            @if($errors->has('is_unique'))
                                <span class="help-block" role="alert">{{ $errors->first('is_unique') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.is_unique_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('min_value') ? 'has-error' : '' }}">
                            <label for="min_value">{{ trans('cruds.crmFormField.fields.min_value') }}</label>
                            <input class="form-control" type="number" name="min_value" id="min_value" value="{{ old('min_value', $crmFormField->min_value) }}" step="1">
                            @if($errors->has('min_value'))
                                <span class="help-block" role="alert">{{ $errors->first('min_value') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.min_value_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('max_value') ? 'has-error' : '' }}">
                            <label for="max_value">{{ trans('cruds.crmFormField.fields.max_value') }}</label>
                            <input class="form-control" type="number" name="max_value" id="max_value" value="{{ old('max_value', $crmFormField->max_value) }}" step="1">
                            @if($errors->has('max_value'))
                                <span class="help-block" role="alert">{{ $errors->first('max_value') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.max_value_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('options_json') ? 'has-error' : '' }}">
                            <label for="options_json">{{ trans('cruds.crmFormField.fields.options_json') }}</label>
                            <textarea class="form-control" name="options_json" id="options_json">{{ old('options_json', $crmFormField->options_json) }}</textarea>
                            @if($errors->has('options_json'))
                                <span class="help-block" role="alert">{{ $errors->first('options_json') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.options_json_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('position') ? 'has-error' : '' }}">
                            <label for="position">{{ trans('cruds.crmFormField.fields.position') }}</label>
                            <input class="form-control" type="number" name="position" id="position" value="{{ old('position', $crmFormField->position) }}" step="1">
                            @if($errors->has('position'))
                                <span class="help-block" role="alert">{{ $errors->first('position') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmFormField.fields.position_helper') }}</span>
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