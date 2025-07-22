@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.activityLaunch.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.activity-launches.update", [$activityLaunch->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('driver') ? 'has-error' : '' }}">
                            <label class="required" for="driver_id">{{ trans('cruds.activityLaunch.fields.driver') }}</label>
                            <select class="form-control select2" name="driver_id" id="driver_id" required>
                                @foreach($drivers as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $activityLaunch->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('driver'))
                                <span class="help-block" role="alert">{{ $errors->first('driver') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.driver_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('week') ? 'has-error' : '' }}">
                            <label class="required" for="week_id">{{ trans('cruds.activityLaunch.fields.week') }}</label>
                            <select class="form-control select2" name="week_id" id="week_id" required>
                                @foreach($weeks as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('week_id') ? old('week_id') : $activityLaunch->week->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('week'))
                                <span class="help-block" role="alert">{{ $errors->first('week') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.week_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('rent') ? 'has-error' : '' }}">
                            <label class="required" for="rent">{{ trans('cruds.activityLaunch.fields.rent') }}</label>
                            <input class="form-control" type="number" name="rent" id="rent" value="{{ old('rent', $activityLaunch->rent) }}" step="0.01" required>
                            @if($errors->has('rent'))
                                <span class="help-block" role="alert">{{ $errors->first('rent') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.rent_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('management') ? 'has-error' : '' }}">
                            <label class="required" for="management">{{ trans('cruds.activityLaunch.fields.management') }}</label>
                            <input class="form-control" type="number" name="management" id="management" value="{{ old('management', $activityLaunch->management) }}" step="0.01" required>
                            @if($errors->has('management'))
                                <span class="help-block" role="alert">{{ $errors->first('management') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.management_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('insurance') ? 'has-error' : '' }}">
                            <label class="required" for="insurance">{{ trans('cruds.activityLaunch.fields.insurance') }}</label>
                            <input class="form-control" type="number" name="insurance" id="insurance" value="{{ old('insurance', $activityLaunch->insurance) }}" step="0.01" required>
                            @if($errors->has('insurance'))
                                <span class="help-block" role="alert">{{ $errors->first('insurance') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.insurance_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('fuel') ? 'has-error' : '' }}">
                            <label class="required" for="fuel">{{ trans('cruds.activityLaunch.fields.fuel') }}</label>
                            <input class="form-control" type="number" name="fuel" id="fuel" value="{{ old('fuel', $activityLaunch->fuel) }}" step="0.01" required>
                            @if($errors->has('fuel'))
                                <span class="help-block" role="alert">{{ $errors->first('fuel') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.fuel_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('tolls') ? 'has-error' : '' }}">
                            <label class="required" for="tolls">{{ trans('cruds.activityLaunch.fields.tolls') }}</label>
                            <input class="form-control" type="number" name="tolls" id="tolls" value="{{ old('tolls', $activityLaunch->tolls) }}" step="0.01" required>
                            @if($errors->has('tolls'))
                                <span class="help-block" role="alert">{{ $errors->first('tolls') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.tolls_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('garage') ? 'has-error' : '' }}">
                            <label class="required" for="garage">{{ trans('cruds.activityLaunch.fields.garage') }}</label>
                            <input class="form-control" type="number" name="garage" id="garage" value="{{ old('garage', $activityLaunch->garage) }}" step="0.01" required>
                            @if($errors->has('garage'))
                                <span class="help-block" role="alert">{{ $errors->first('garage') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.garage_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('others') ? 'has-error' : '' }}">
                            <label class="required" for="others">{{ trans('cruds.activityLaunch.fields.others') }}</label>
                            <input class="form-control" type="number" name="others" id="others" value="{{ old('others', $activityLaunch->others) }}" step="0.01" required>
                            @if($errors->has('others'))
                                <span class="help-block" role="alert">{{ $errors->first('others') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.others_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('refund') ? 'has-error' : '' }}">
                            <label class="required" for="refund">{{ trans('cruds.activityLaunch.fields.refund') }}</label>
                            <input class="form-control" type="number" name="refund" id="refund" value="{{ old('refund', $activityLaunch->refund) }}" step="0.01" required>
                            @if($errors->has('refund'))
                                <span class="help-block" role="alert">{{ $errors->first('refund') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.refund_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('initial_kilometers') ? 'has-error' : '' }}">
                            <label for="initial_kilometers">{{ trans('cruds.activityLaunch.fields.initial_kilometers') }}</label>
                            <input class="form-control" type="number" name="initial_kilometers" id="initial_kilometers" value="{{ old('initial_kilometers', $activityLaunch->initial_kilometers) }}" step="1">
                            @if($errors->has('initial_kilometers'))
                                <span class="help-block" role="alert">{{ $errors->first('initial_kilometers') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.initial_kilometers_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('final_kilometers') ? 'has-error' : '' }}">
                            <label for="final_kilometers">{{ trans('cruds.activityLaunch.fields.final_kilometers') }}</label>
                            <input class="form-control" type="number" name="final_kilometers" id="final_kilometers" value="{{ old('final_kilometers', $activityLaunch->final_kilometers) }}" step="1">
                            @if($errors->has('final_kilometers'))
                                <span class="help-block" role="alert">{{ $errors->first('final_kilometers') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.final_kilometers_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('send') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="send" value="0">
                                <input type="checkbox" name="send" id="send" value="1" {{ $activityLaunch->send || old('send', 0) === 1 ? 'checked' : '' }}>
                                <label for="send" style="font-weight: 400">{{ trans('cruds.activityLaunch.fields.send') }}</label>
                            </div>
                            @if($errors->has('send'))
                                <span class="help-block" role="alert">{{ $errors->first('send') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.send_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="paid" value="0">
                                <input type="checkbox" name="paid" id="paid" value="1" {{ $activityLaunch->paid || old('paid', 0) === 1 ? 'checked' : '' }}>
                                <label for="paid" style="font-weight: 400">{{ trans('cruds.activityLaunch.fields.paid') }}</label>
                            </div>
                            @if($errors->has('paid'))
                                <span class="help-block" role="alert">{{ $errors->first('paid') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityLaunch.fields.paid_helper') }}</span>
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