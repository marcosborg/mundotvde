@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.crmCardActivity.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-card-activities.update", [$crmCardActivity->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('card') ? 'has-error' : '' }}">
                            <label class="required" for="card_id">{{ trans('cruds.crmCardActivity.fields.card') }}</label>
                            <select class="form-control select2" name="card_id" id="card_id" required>
                                @foreach($cards as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('card_id') ? old('card_id') : $crmCardActivity->card->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('card'))
                                <span class="help-block" role="alert">{{ $errors->first('card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardActivity.fields.card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmCardActivity.fields.type') }}</label>
                            @foreach(App\Models\CrmCardActivity::TYPE_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="type_{{ $key }}" name="type" value="{{ $key }}" {{ old('type', $crmCardActivity->type) === (string) $key ? 'checked' : '' }}>
                                    <label for="type_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('type'))
                                <span class="help-block" role="alert">{{ $errors->first('type') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardActivity.fields.type_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('meta_json') ? 'has-error' : '' }}">
                            <label for="meta_json">{{ trans('cruds.crmCardActivity.fields.meta_json') }}</label>
                            <textarea class="form-control" name="meta_json" id="meta_json">{{ old('meta_json', $crmCardActivity->meta_json) }}</textarea>
                            @if($errors->has('meta_json'))
                                <span class="help-block" role="alert">{{ $errors->first('meta_json') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardActivity.fields.meta_json_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('created_by') ? 'has-error' : '' }}">
                            <label for="created_by_id">{{ trans('cruds.crmCardActivity.fields.created_by') }}</label>
                            <select class="form-control select2" name="created_by_id" id="created_by_id">
                                @foreach($created_bies as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('created_by_id') ? old('created_by_id') : $crmCardActivity->created_by->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('created_by'))
                                <span class="help-block" role="alert">{{ $errors->first('created_by') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCardActivity.fields.created_by_helper') }}</span>
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