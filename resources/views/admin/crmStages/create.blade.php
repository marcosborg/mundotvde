@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.crmStage.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-stages.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                            <label class="required" for="category_id">{{ trans('cruds.crmStage.fields.category') }}</label>
                            <select class="form-control select2" name="category_id" id="category_id" required>
                                @foreach($categories as $id => $entry)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('category'))
                                <span class="help-block" role="alert">{{ $errors->first('category') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.category_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.crmStage.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('position') ? 'has-error' : '' }}">
                            <label for="position">{{ trans('cruds.crmStage.fields.position') }}</label>
                            <input class="form-control" type="number" name="position" id="position" value="{{ old('position', '') }}" step="1">
                            @if($errors->has('position'))
                                <span class="help-block" role="alert">{{ $errors->first('position') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.position_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('color') ? 'has-error' : '' }}">
                            <label for="color">{{ trans('cruds.crmStage.fields.color') }}</label>
                            <input class="form-control" type="text" name="color" id="color" value="{{ old('color', '') }}">
                            @if($errors->has('color'))
                                <span class="help-block" role="alert">{{ $errors->first('color') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.color_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('is_won') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="is_won" value="0">
                                <input type="checkbox" name="is_won" id="is_won" value="1" {{ old('is_won', 0) == 1 ? 'checked' : '' }}>
                                <label for="is_won" style="font-weight: 400">{{ trans('cruds.crmStage.fields.is_won') }}</label>
                            </div>
                            @if($errors->has('is_won'))
                                <span class="help-block" role="alert">{{ $errors->first('is_won') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.is_won_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('is_lost') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="is_lost" value="0">
                                <input type="checkbox" name="is_lost" id="is_lost" value="1" {{ old('is_lost', 0) == 1 ? 'checked' : '' }}>
                                <label for="is_lost" style="font-weight: 400">{{ trans('cruds.crmStage.fields.is_lost') }}</label>
                            </div>
                            @if($errors->has('is_lost'))
                                <span class="help-block" role="alert">{{ $errors->first('is_lost') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.is_lost_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('auto_assign_to_user') ? 'has-error' : '' }}">
                            <label for="auto_assign_to_user_id">{{ trans('cruds.crmStage.fields.auto_assign_to_user') }}</label>
                            <select class="form-control select2" name="auto_assign_to_user_id" id="auto_assign_to_user_id">
                                @foreach($auto_assign_to_users as $id => $entry)
                                    <option value="{{ $id }}" {{ old('auto_assign_to_user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('auto_assign_to_user'))
                                <span class="help-block" role="alert">{{ $errors->first('auto_assign_to_user') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStage.fields.auto_assign_to_user_helper') }}</span>
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