@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.docCompany.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.doc-companies.update", [$docCompany->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.docCompany.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $docCompany->name) }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.docCompany.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('nipc') ? 'has-error' : '' }}">
                            <label for="nipc">{{ trans('cruds.docCompany.fields.nipc') }}</label>
                            <input class="form-control" type="text" name="nipc" id="nipc" value="{{ old('nipc', $docCompany->nipc) }}">
                            @if($errors->has('nipc'))
                                <span class="help-block" role="alert">{{ $errors->first('nipc') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.docCompany.fields.nipc_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="address">{{ trans('cruds.docCompany.fields.address') }}</label>
                            <input class="form-control" type="text" name="address" id="address" value="{{ old('address', $docCompany->address) }}">
                            @if($errors->has('address'))
                                <span class="help-block" role="alert">{{ $errors->first('address') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.docCompany.fields.address_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('location') ? 'has-error' : '' }}">
                            <label for="location">{{ trans('cruds.docCompany.fields.location') }}</label>
                            <input class="form-control" type="text" name="location" id="location" value="{{ old('location', $docCompany->location) }}">
                            @if($errors->has('location'))
                                <span class="help-block" role="alert">{{ $errors->first('location') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.docCompany.fields.location_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('zip') ? 'has-error' : '' }}">
                            <label for="zip">{{ trans('cruds.docCompany.fields.zip') }}</label>
                            <input class="form-control" type="text" name="zip" id="zip" value="{{ old('zip', $docCompany->zip) }}">
                            @if($errors->has('zip'))
                                <span class="help-block" role="alert">{{ $errors->first('zip') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.docCompany.fields.zip_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('country') ? 'has-error' : '' }}">
                            <label for="country">{{ trans('cruds.docCompany.fields.country') }}</label>
                            <input class="form-control" type="text" name="country" id="country" value="{{ old('country', $docCompany->country) }}">
                            @if($errors->has('country'))
                                <span class="help-block" role="alert">{{ $errors->first('country') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.docCompany.fields.country_helper') }}</span>
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