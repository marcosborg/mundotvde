@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.documentGenerated.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.document-generateds.update", [$documentGenerated->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('document_management') ? 'has-error' : '' }}">
                            <label class="required" for="document_management_id">{{ trans('cruds.documentGenerated.fields.document_management') }}</label>
                            <select class="form-control select2" name="document_management_id" id="document_management_id" required>
                                @foreach($document_managements as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('document_management_id') ? old('document_management_id') : $documentGenerated->document_management->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('document_management'))
                                <span class="help-block" role="alert">{{ $errors->first('document_management') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentGenerated.fields.document_management_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('driver') ? 'has-error' : '' }}">
                            <label class="required" for="driver_id">{{ trans('cruds.documentGenerated.fields.driver') }}</label>
                            <select class="form-control select2" name="driver_id" id="driver_id" required>
                                @foreach($drivers as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $documentGenerated->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('driver'))
                                <span class="help-block" role="alert">{{ $errors->first('driver') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentGenerated.fields.driver_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                            <label for="date">{{ trans('cruds.documentGenerated.fields.date') }}</label>
                            <input class="form-control date" type="text" name="date" id="date" value="{{ old('date', $documentGenerated->date) }}">
                            @if($errors->has('date'))
                                <span class="help-block" role="alert">{{ $errors->first('date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentGenerated.fields.date_helper') }}</span>
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