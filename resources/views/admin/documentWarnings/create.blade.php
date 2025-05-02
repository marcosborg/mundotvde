@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.documentWarning.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.document-warnings.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('citizen_card') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="citizen_card" value="0">
                                <input type="checkbox" name="citizen_card" id="citizen_card" value="1" {{ old('citizen_card', 0) == 1 ? 'checked' : '' }}>
                                <label for="citizen_card" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.citizen_card') }}</label>
                            </div>
                            @if($errors->has('citizen_card'))
                                <span class="help-block" role="alert">{{ $errors->first('citizen_card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.citizen_card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('tvde_driver_certificate') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="tvde_driver_certificate" value="0">
                                <input type="checkbox" name="tvde_driver_certificate" id="tvde_driver_certificate" value="1" {{ old('tvde_driver_certificate', 0) == 1 ? 'checked' : '' }}>
                                <label for="tvde_driver_certificate" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.tvde_driver_certificate') }}</label>
                            </div>
                            @if($errors->has('tvde_driver_certificate'))
                                <span class="help-block" role="alert">{{ $errors->first('tvde_driver_certificate') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.tvde_driver_certificate_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('criminal_record') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="criminal_record" value="0">
                                <input type="checkbox" name="criminal_record" id="criminal_record" value="1" {{ old('criminal_record', 0) == 1 ? 'checked' : '' }}>
                                <label for="criminal_record" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.criminal_record') }}</label>
                            </div>
                            @if($errors->has('criminal_record'))
                                <span class="help-block" role="alert">{{ $errors->first('criminal_record') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.criminal_record_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('profile_picture') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="profile_picture" value="0">
                                <input type="checkbox" name="profile_picture" id="profile_picture" value="1" {{ old('profile_picture', 0) == 1 ? 'checked' : '' }}>
                                <label for="profile_picture" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.profile_picture') }}</label>
                            </div>
                            @if($errors->has('profile_picture'))
                                <span class="help-block" role="alert">{{ $errors->first('profile_picture') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.profile_picture_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('driving_license') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="driving_license" value="0">
                                <input type="checkbox" name="driving_license" id="driving_license" value="1" {{ old('driving_license', 0) == 1 ? 'checked' : '' }}>
                                <label for="driving_license" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.driving_license') }}</label>
                            </div>
                            @if($errors->has('driving_license'))
                                <span class="help-block" role="alert">{{ $errors->first('driving_license') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.driving_license_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('iban') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="iban" value="0">
                                <input type="checkbox" name="iban" id="iban" value="1" {{ old('iban', 0) == 1 ? 'checked' : '' }}>
                                <label for="iban" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.iban') }}</label>
                            </div>
                            @if($errors->has('iban'))
                                <span class="help-block" role="alert">{{ $errors->first('iban') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.iban_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('dua_vehicle') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="dua_vehicle" value="0">
                                <input type="checkbox" name="dua_vehicle" id="dua_vehicle" value="1" {{ old('dua_vehicle', 0) == 1 ? 'checked' : '' }}>
                                <label for="dua_vehicle" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.dua_vehicle') }}</label>
                            </div>
                            @if($errors->has('dua_vehicle'))
                                <span class="help-block" role="alert">{{ $errors->first('dua_vehicle') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.dua_vehicle_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('car_insurance') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="car_insurance" value="0">
                                <input type="checkbox" name="car_insurance" id="car_insurance" value="1" {{ old('car_insurance', 0) == 1 ? 'checked' : '' }}>
                                <label for="car_insurance" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.car_insurance') }}</label>
                            </div>
                            @if($errors->has('car_insurance'))
                                <span class="help-block" role="alert">{{ $errors->first('car_insurance') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.car_insurance_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('ipo_vehicle') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="ipo_vehicle" value="0">
                                <input type="checkbox" name="ipo_vehicle" id="ipo_vehicle" value="1" {{ old('ipo_vehicle', 0) == 1 ? 'checked' : '' }}>
                                <label for="ipo_vehicle" style="font-weight: 400">{{ trans('cruds.documentWarning.fields.ipo_vehicle') }}</label>
                            </div>
                            @if($errors->has('ipo_vehicle'))
                                <span class="help-block" role="alert">{{ $errors->first('ipo_vehicle') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentWarning.fields.ipo_vehicle_helper') }}</span>
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