@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.crmCategory.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-categories.update", [$crmCategory->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.crmCategory.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $crmCategory->name) }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCategory.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('slug') ? 'has-error' : '' }}">
                            <label class="required" for="slug">{{ trans('cruds.crmCategory.fields.slug') }}</label>
                            <input class="form-control" type="text" name="slug" id="slug" value="{{ old('slug', $crmCategory->slug) }}" required>
                            @if($errors->has('slug'))
                                <span class="help-block" role="alert">{{ $errors->first('slug') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCategory.fields.slug_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('color') ? 'has-error' : '' }}">
                            <label for="color">{{ trans('cruds.crmCategory.fields.color') }}</label>
                            <input class="form-control" type="text" name="color" id="color" value="{{ old('color', $crmCategory->color) }}">
                            @if($errors->has('color'))
                                <span class="help-block" role="alert">{{ $errors->first('color') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCategory.fields.color_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('position') ? 'has-error' : '' }}">
                            <label class="required" for="position">{{ trans('cruds.crmCategory.fields.position') }}</label>
                            <input class="form-control" type="number" name="position" id="position" value="{{ old('position', $crmCategory->position) }}" step="1" required>
                            @if($errors->has('position'))
                                <span class="help-block" role="alert">{{ $errors->first('position') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCategory.fields.position_helper') }}</span>
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