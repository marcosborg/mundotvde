@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.vehicleEvent.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.vehicle-events.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.vehicleEvent.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.vehicleEvent.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <label for="description">{{ trans('cruds.vehicleEvent.fields.description') }}</label>
                            <textarea class="form-control" name="description" id="description">{{ old('description') }}</textarea>
                            @if($errors->has('description'))
                                <span class="help-block" role="alert">{{ $errors->first('description') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.vehicleEvent.fields.description_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('vehicle_event_type') ? 'has-error' : '' }}">
                            <label class="required" for="vehicle_event_type_id">{{ trans('cruds.vehicleEvent.fields.vehicle_event_type') }}</label>
                            <select class="form-control select2" name="vehicle_event_type_id" id="vehicle_event_type_id" required>
                                @foreach($vehicle_event_types as $id => $entry)
                                    <option value="{{ $id }}" {{ old('vehicle_event_type_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('vehicle_event_type'))
                                <span class="help-block" role="alert">{{ $errors->first('vehicle_event_type') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.vehicleEvent.fields.vehicle_event_type_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('vehicle_event_warning_time') ? 'has-error' : '' }}">
                            <label for="vehicle_event_warning_time_id">{{ trans('cruds.vehicleEvent.fields.vehicle_event_warning_time') }}</label>
                            <select class="form-control select2" name="vehicle_event_warning_time_id" id="vehicle_event_warning_time_id">
                                @foreach($vehicle_event_warning_times as $id => $entry)
                                    <option value="{{ $id }}" {{ old('vehicle_event_warning_time_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('vehicle_event_warning_time'))
                                <span class="help-block" role="alert">{{ $errors->first('vehicle_event_warning_time') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.vehicleEvent.fields.vehicle_event_warning_time_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                            <label class="required" for="date">{{ trans('cruds.vehicleEvent.fields.date') }}</label>
                            <input class="form-control datetime" type="text" name="date" id="date" value="{{ old('date') }}" required>
                            @if($errors->has('date'))
                                <span class="help-block" role="alert">{{ $errors->first('date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.vehicleEvent.fields.date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('vehicle_items') ? 'has-error' : '' }}">
                            <label for="vehicle_items">{{ trans('cruds.vehicleEvent.fields.vehicle_items') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2" name="vehicle_items[]" id="vehicle_items" multiple>
                                @foreach($vehicle_items as $id => $vehicle_item)
                                    <option value="{{ $id }}" {{ in_array($id, old('vehicle_items', [])) ? 'selected' : '' }}>{{ $vehicle_item }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('vehicle_items'))
                                <span class="help-block" role="alert">{{ $errors->first('vehicle_items') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.vehicleEvent.fields.vehicle_items_helper') }}</span>
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