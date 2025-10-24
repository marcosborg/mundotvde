@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.driver.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.drivers.update", [$driver->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                            <label for="user_id">{{ trans('cruds.driver.fields.user') }}</label>
                            <select class="form-control select2" name="user_id" id="user_id">
                                @foreach($users as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $driver->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user'))
                                <span class="help-block" role="alert">{{ $errors->first('user') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.user_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                            <label class="required" for="code">{{ trans('cruds.driver.fields.code') }}</label>
                            <input class="form-control" type="text" name="code" id="code" value="{{ old('code', $driver->code) }}" required>
                            @if($errors->has('code'))
                                <span class="help-block" role="alert">{{ $errors->first('code') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.code_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label class="required" for="name">{{ trans('cruds.driver.fields.name') }}</label>
                            <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $driver->name) }}" required>
                            @if($errors->has('name'))
                                <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('tvde_operators') ? 'has-error' : '' }}">
                            <label for="tvde_operators">{{ trans('cruds.driver.fields.tvde_operator') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2" name="tvde_operators[]" id="tvde_operators" multiple>
                                @foreach($tvde_operators as $id => $tvde_operator)
                                    <option value="{{ $id }}" {{ (in_array($id, old('tvde_operators', [])) || $driver->tvde_operators->contains($id)) ? 'selected' : '' }}>{{ $tvde_operator }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('tvde_operators'))
                                <span class="help-block" role="alert">{{ $errors->first('tvde_operators') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.tvde_operator_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('card') ? 'has-error' : '' }}">
                            <label for="card_id">{{ trans('cruds.driver.fields.card') }}</label>
                            <select class="form-control select2" name="card_id" id="card_id">
                                @foreach($cards as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('card_id') ? old('card_id') : $driver->card->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('card'))
                                <span class="help-block" role="alert">{{ $errors->first('card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('operation') ? 'has-error' : '' }}">
                            <label for="operation_id">{{ trans('cruds.driver.fields.operation') }}</label>
                            <select class="form-control select2" name="operation_id" id="operation_id">
                                @foreach($operations as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('operation_id') ? old('operation_id') : $driver->operation->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('operation'))
                                <span class="help-block" role="alert">{{ $errors->first('operation') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.operation_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('local') ? 'has-error' : '' }}">
                            <label for="local_id">{{ trans('cruds.driver.fields.local') }}</label>
                            <select class="form-control select2" name="local_id" id="local_id">
                                @foreach($locals as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('local_id') ? old('local_id') : $driver->local->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('local'))
                                <span class="help-block" role="alert">{{ $errors->first('local') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.local_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('start_date') ? 'has-error' : '' }}">
                            <label for="start_date">{{ trans('cruds.driver.fields.start_date') }}</label>
                            <input class="form-control date" type="text" name="start_date" id="start_date" value="{{ old('start_date', $driver->start_date) }}">
                            @if($errors->has('start_date'))
                                <span class="help-block" role="alert">{{ $errors->first('start_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.start_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('end_date') ? 'has-error' : '' }}">
                            <label for="end_date">{{ trans('cruds.driver.fields.end_date') }}</label>
                            <input class="form-control date" type="text" name="end_date" id="end_date" value="{{ old('end_date', $driver->end_date) }}">
                            @if($errors->has('end_date'))
                                <span class="help-block" role="alert">{{ $errors->first('end_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.end_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('reason') ? 'has-error' : '' }}">
                            <label for="reason">{{ trans('cruds.driver.fields.reason') }}</label>
                            <input class="form-control" type="text" name="reason" id="reason" value="{{ old('reason', $driver->reason) }}">
                            @if($errors->has('reason'))
                                <span class="help-block" role="alert">{{ $errors->first('reason') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.reason_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <label for="phone">{{ trans('cruds.driver.fields.phone') }}</label>
                            <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $driver->phone) }}">
                            @if($errors->has('phone'))
                                <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.phone_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('payment_vat') ? 'has-error' : '' }}">
                            <label for="payment_vat">{{ trans('cruds.driver.fields.payment_vat') }}</label>
                            <input class="form-control" type="text" name="payment_vat" id="payment_vat" value="{{ old('payment_vat', $driver->payment_vat) }}">
                            @if($errors->has('payment_vat'))
                                <span class="help-block" role="alert">{{ $errors->first('payment_vat') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.payment_vat_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('citizen_card') ? 'has-error' : '' }}">
                            <label for="citizen_card">{{ trans('cruds.driver.fields.citizen_card') }}</label>
                            <input class="form-control" type="text" name="citizen_card" id="citizen_card" value="{{ old('citizen_card', $driver->citizen_card) }}">
                            @if($errors->has('citizen_card'))
                                <span class="help-block" role="alert">{{ $errors->first('citizen_card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.citizen_card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('citizen_card_expiry_date') ? 'has-error' : '' }}">
                            <label for="citizen_card_expiry_date">{{ trans('cruds.driver.fields.citizen_card_expiry_date') }}</label>
                            <input class="form-control date" type="text" name="citizen_card_expiry_date" id="citizen_card_expiry_date" value="{{ old('citizen_card_expiry_date', $driver->citizen_card_expiry_date) }}">
                            @if($errors->has('citizen_card_expiry_date'))
                                <span class="help-block" role="alert">{{ $errors->first('citizen_card_expiry_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.citizen_card_expiry_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('birth_date') ? 'has-error' : '' }}">
                            <label for="birth_date">{{ trans('cruds.driver.fields.birth_date') }}</label>
                            <input class="form-control date" type="text" name="birth_date" id="birth_date" value="{{ old('birth_date', $driver->birth_date) }}">
                            @if($errors->has('birth_date'))
                                <span class="help-block" role="alert">{{ $errors->first('birth_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.birth_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('drivers_certificate') ? 'has-error' : '' }}">
                            <label for="drivers_certificate">{{ trans('cruds.driver.fields.drivers_certificate') }}</label>
                            <input class="form-control" type="text" name="drivers_certificate" id="drivers_certificate" value="{{ old('drivers_certificate', $driver->drivers_certificate) }}">
                            @if($errors->has('drivers_certificate'))
                                <span class="help-block" role="alert">{{ $errors->first('drivers_certificate') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.drivers_certificate_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('drivers_certificate_expiration_date') ? 'has-error' : '' }}">
                            <label for="drivers_certificate_expiration_date">{{ trans('cruds.driver.fields.drivers_certificate_expiration_date') }}</label>
                            <input class="form-control date" type="text" name="drivers_certificate_expiration_date" id="drivers_certificate_expiration_date" value="{{ old('drivers_certificate_expiration_date', $driver->drivers_certificate_expiration_date) }}">
                            @if($errors->has('drivers_certificate_expiration_date'))
                                <span class="help-block" role="alert">{{ $errors->first('drivers_certificate_expiration_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.drivers_certificate_expiration_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('cruds.driver.fields.email') }}</label>
                            <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $driver->email) }}">
                            @if($errors->has('email'))
                                <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('iban') ? 'has-error' : '' }}">
                            <label for="iban">{{ trans('cruds.driver.fields.iban') }}</label>
                            <input class="form-control" type="text" name="iban" id="iban" value="{{ old('iban', $driver->iban) }}">
                            @if($errors->has('iban'))
                                <span class="help-block" role="alert">{{ $errors->first('iban') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.iban_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="address">{{ trans('cruds.driver.fields.address') }}</label>
                            <input class="form-control" type="text" name="address" id="address" value="{{ old('address', $driver->address) }}">
                            @if($errors->has('address'))
                                <span class="help-block" role="alert">{{ $errors->first('address') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.address_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('zip') ? 'has-error' : '' }}">
                            <label for="zip">{{ trans('cruds.driver.fields.zip') }}</label>
                            <input class="form-control" type="text" name="zip" id="zip" value="{{ old('zip', $driver->zip) }}">
                            @if($errors->has('zip'))
                                <span class="help-block" role="alert">{{ $errors->first('zip') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.zip_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('city') ? 'has-error' : '' }}">
                            <label for="city">{{ trans('cruds.driver.fields.city') }}</label>
                            <input class="form-control" type="text" name="city" id="city" value="{{ old('city', $driver->city) }}">
                            @if($errors->has('city'))
                                <span class="help-block" role="alert">{{ $errors->first('city') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.city_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('state') ? 'has-error' : '' }}">
                            <label class="required" for="state_id">{{ trans('cruds.driver.fields.state') }}</label>
                            <select class="form-control select2" name="state_id" id="state_id" required>
                                @foreach($states as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('state_id') ? old('state_id') : $driver->state->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('state'))
                                <span class="help-block" role="alert">{{ $errors->first('state') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.state_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('driver_license') ? 'has-error' : '' }}">
                            <label for="driver_license">{{ trans('cruds.driver.fields.driver_license') }}</label>
                            <input class="form-control" type="text" name="driver_license" id="driver_license" value="{{ old('driver_license', $driver->driver_license) }}">
                            @if($errors->has('driver_license'))
                                <span class="help-block" role="alert">{{ $errors->first('driver_license') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.driver_license_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('driver_license_expiration_date') ? 'has-error' : '' }}">
                            <label for="driver_license_expiration_date">{{ trans('cruds.driver.fields.driver_license_expiration_date') }}</label>
                            <input class="form-control date" type="text" name="driver_license_expiration_date" id="driver_license_expiration_date" value="{{ old('driver_license_expiration_date', $driver->driver_license_expiration_date) }}">
                            @if($errors->has('driver_license_expiration_date'))
                                <span class="help-block" role="alert">{{ $errors->first('driver_license_expiration_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.driver_license_expiration_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('driver_vat') ? 'has-error' : '' }}">
                            <label for="driver_vat">{{ trans('cruds.driver.fields.driver_vat') }}</label>
                            <input class="form-control" type="text" name="driver_vat" id="driver_vat" value="{{ old('driver_vat', $driver->driver_vat) }}">
                            @if($errors->has('driver_vat'))
                                <span class="help-block" role="alert">{{ $errors->first('driver_vat') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.driver_vat_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('uber_uuid') ? 'has-error' : '' }}">
                            <label for="uber_uuid">{{ trans('cruds.driver.fields.uber_uuid') }}</label>
                            <input class="form-control" type="text" name="uber_uuid" id="uber_uuid" value="{{ old('uber_uuid', $driver->uber_uuid) }}">
                            @if($errors->has('uber_uuid'))
                                <span class="help-block" role="alert">{{ $errors->first('uber_uuid') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.uber_uuid_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('bolt_name') ? 'has-error' : '' }}">
                            <label for="bolt_name">{{ trans('cruds.driver.fields.bolt_name') }}</label>
                            <input class="form-control" type="text" name="bolt_name" id="bolt_name" value="{{ old('bolt_name', $driver->bolt_name) }}">
                            @if($errors->has('bolt_name'))
                                <span class="help-block" role="alert">{{ $errors->first('bolt_name') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.bolt_name_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('license_plate') ? 'has-error' : '' }}">
                            <label for="license_plate">{{ trans('cruds.driver.fields.license_plate') }}</label>
                            <input class="form-control" type="text" name="license_plate" id="license_plate" value="{{ old('license_plate', $driver->license_plate) }}">
                            @if($errors->has('license_plate'))
                                <span class="help-block" role="alert">{{ $errors->first('license_plate') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.license_plate_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('vehicle_date') ? 'has-error' : '' }}">
                            <label for="vehicle_date">{{ trans('cruds.driver.fields.vehicle_date') }}</label>
                            <input class="form-control date" type="text" name="vehicle_date" id="vehicle_date" value="{{ old('vehicle_date', $driver->vehicle_date) }}">
                            @if($errors->has('vehicle_date'))
                                <span class="help-block" role="alert">{{ $errors->first('vehicle_date') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.vehicle_date_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('brand') ? 'has-error' : '' }}">
                            <label for="brand">{{ trans('cruds.driver.fields.brand') }}</label>
                            <input class="form-control" type="text" name="brand" id="brand" value="{{ old('brand', $driver->brand) }}">
                            @if($errors->has('brand'))
                                <span class="help-block" role="alert">{{ $errors->first('brand') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.brand_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('model') ? 'has-error' : '' }}">
                            <label for="model">{{ trans('cruds.driver.fields.model') }}</label>
                            <input class="form-control" type="text" name="model" id="model" value="{{ old('model', $driver->model) }}">
                            @if($errors->has('model'))
                                <span class="help-block" role="alert">{{ $errors->first('model') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.model_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label for="notes">{{ trans('cruds.driver.fields.notes') }}</label>
                            <textarea class="form-control" name="notes" id="notes">{{ old('notes', $driver->notes) }}</textarea>
                            @if($errors->has('notes'))
                                <span class="help-block" role="alert">{{ $errors->first('notes') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.driver.fields.notes_helper') }}</span>
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