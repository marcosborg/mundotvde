@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.activityPerOperator.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.activity-per-operators.update", [$activityPerOperator->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('activity_launch') ? 'has-error' : '' }}">
                            <label class="required" for="activity_launch_id">{{ trans('cruds.activityPerOperator.fields.activity_launch') }}</label>
                            <select class="form-control select2" name="activity_launch_id" id="activity_launch_id" required>
                                @foreach($activity_launches as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('activity_launch_id') ? old('activity_launch_id') : $activityPerOperator->activity_launch->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('activity_launch'))
                                <span class="help-block" role="alert">{{ $errors->first('activity_launch') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityPerOperator.fields.activity_launch_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('gross') ? 'has-error' : '' }}">
                            <label for="gross">{{ trans('cruds.activityPerOperator.fields.gross') }}</label>
                            <input class="form-control" type="number" name="gross" id="gross" value="{{ old('gross', $activityPerOperator->gross) }}" step="0.01">
                            @if($errors->has('gross'))
                                <span class="help-block" role="alert">{{ $errors->first('gross') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityPerOperator.fields.gross_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('net') ? 'has-error' : '' }}">
                            <label for="net">{{ trans('cruds.activityPerOperator.fields.net') }}</label>
                            <input class="form-control" type="number" name="net" id="net" value="{{ old('net', $activityPerOperator->net) }}" step="0.01">
                            @if($errors->has('net'))
                                <span class="help-block" role="alert">{{ $errors->first('net') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityPerOperator.fields.net_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('taxes') ? 'has-error' : '' }}">
                            <label for="taxes">{{ trans('cruds.activityPerOperator.fields.taxes') }}</label>
                            <input class="form-control" type="number" name="taxes" id="taxes" value="{{ old('taxes', $activityPerOperator->taxes) }}" step="0.01">
                            @if($errors->has('taxes'))
                                <span class="help-block" role="alert">{{ $errors->first('taxes') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityPerOperator.fields.taxes_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('tvde_operator') ? 'has-error' : '' }}">
                            <label class="required" for="tvde_operator_id">{{ trans('cruds.activityPerOperator.fields.tvde_operator') }}</label>
                            <select class="form-control select2" name="tvde_operator_id" id="tvde_operator_id" required>
                                @foreach($tvde_operators as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('tvde_operator_id') ? old('tvde_operator_id') : $activityPerOperator->tvde_operator->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('tvde_operator'))
                                <span class="help-block" role="alert">{{ $errors->first('tvde_operator') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.activityPerOperator.fields.tvde_operator_helper') }}</span>
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