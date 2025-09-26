@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.crmCard.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-cards.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                            <label class="required" for="category_id">{{ trans('cruds.crmCard.fields.category') }}</label>
                            <select class="form-control select2" name="category_id" id="category_id" required>
                                @foreach($categories as $id => $entry)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('category'))
                                <span class="help-block" role="alert">{{ $errors->first('category') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.category_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('stage') ? 'has-error' : '' }}">
                            <label class="required" for="stage_id">{{ trans('cruds.crmCard.fields.stage') }}</label>
                            <select class="form-control select2" name="stage_id" id="stage_id" required>
                                @foreach($stages as $id => $entry)
                                    <option value="{{ $id }}" {{ old('stage_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('stage'))
                                <span class="help-block" role="alert">{{ $errors->first('stage') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.stage_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label class="required" for="title">{{ trans('cruds.crmCard.fields.title') }}</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                            @if($errors->has('title'))
                                <span class="help-block" role="alert">{{ $errors->first('title') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.title_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('form') ? 'has-error' : '' }}">
                            <label for="form_id">{{ trans('cruds.crmCard.fields.form') }}</label>
                            <select class="form-control select2" name="form_id" id="form_id">
                                @foreach($forms as $id => $entry)
                                    <option value="{{ $id }}" {{ old('form_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('form'))
                                <span class="help-block" role="alert">{{ $errors->first('form') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.form_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmCard.fields.source') }}</label>
                            @foreach(App\Models\CrmCard::SOURCE_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="source_{{ $key }}" name="source" value="{{ $key }}" {{ old('source', 'form') === (string) $key ? 'checked' : '' }}>
                                    <label for="source_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('source'))
                                <span class="help-block" role="alert">{{ $errors->first('source') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.source_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('priority') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmCard.fields.priority') }}</label>
                            @foreach(App\Models\CrmCard::PRIORITY_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="priority_{{ $key }}" name="priority" value="{{ $key }}" {{ old('priority', 'medium') === (string) $key ? 'checked' : '' }}>
                                    <label for="priority_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('priority'))
                                <span class="help-block" role="alert">{{ $errors->first('priority') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.priority_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmCard.fields.status') }}</label>
                            @foreach(App\Models\CrmCard::STATUS_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="status_{{ $key }}" name="status" value="{{ $key }}" {{ old('status', 'open') === (string) $key ? 'checked' : '' }}>
                                    <label for="status_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('lost_reason') ? 'has-error' : '' }}">
                            <label for="lost_reason">{{ trans('cruds.crmCard.fields.lost_reason') }}</label>
                            <input class="form-control" type="text" name="lost_reason" id="lost_reason" value="{{ old('lost_reason', '') }}">
                            @if($errors->has('lost_reason'))
                                <span class="help-block" role="alert">{{ $errors->first('lost_reason') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.lost_reason_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('won_at') ? 'has-error' : '' }}">
                            <label for="won_at">{{ trans('cruds.crmCard.fields.won_at') }}</label>
                            <input class="form-control datetime" type="text" name="won_at" id="won_at" value="{{ old('won_at') }}">
                            @if($errors->has('won_at'))
                                <span class="help-block" role="alert">{{ $errors->first('won_at') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.won_at_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('closed_at') ? 'has-error' : '' }}">
                            <label for="closed_at">{{ trans('cruds.crmCard.fields.closed_at') }}</label>
                            <input class="form-control datetime" type="text" name="closed_at" id="closed_at" value="{{ old('closed_at') }}">
                            @if($errors->has('closed_at'))
                                <span class="help-block" role="alert">{{ $errors->first('closed_at') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.closed_at_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('due_at') ? 'has-error' : '' }}">
                            <label for="due_at">{{ trans('cruds.crmCard.fields.due_at') }}</label>
                            <input class="form-control datetime" type="text" name="due_at" id="due_at" value="{{ old('due_at') }}">
                            @if($errors->has('due_at'))
                                <span class="help-block" role="alert">{{ $errors->first('due_at') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.due_at_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('assigned_to') ? 'has-error' : '' }}">
                            <label for="assigned_to_id">{{ trans('cruds.crmCard.fields.assigned_to') }}</label>
                            <select class="form-control select2" name="assigned_to_id" id="assigned_to_id">
                                @foreach($assigned_tos as $id => $entry)
                                    <option value="{{ $id }}" {{ old('assigned_to_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('assigned_to'))
                                <span class="help-block" role="alert">{{ $errors->first('assigned_to') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.assigned_to_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('created_by') ? 'has-error' : '' }}">
                            <label for="created_by_id">{{ trans('cruds.crmCard.fields.created_by') }}</label>
                            <select class="form-control select2" name="created_by_id" id="created_by_id">
                                @foreach($created_bies as $id => $entry)
                                    <option value="{{ $id }}" {{ old('created_by_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('created_by'))
                                <span class="help-block" role="alert">{{ $errors->first('created_by') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.created_by_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('position') ? 'has-error' : '' }}">
                            <label for="position">{{ trans('cruds.crmCard.fields.position') }}</label>
                            <input class="form-control" type="number" name="position" id="position" value="{{ old('position', '') }}" step="1">
                            @if($errors->has('position'))
                                <span class="help-block" role="alert">{{ $errors->first('position') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.position_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('fields_snapshot_json') ? 'has-error' : '' }}">
                            <label for="fields_snapshot_json">{{ trans('cruds.crmCard.fields.fields_snapshot_json') }}</label>
                            <textarea class="form-control" name="fields_snapshot_json" id="fields_snapshot_json">{{ old('fields_snapshot_json') }}</textarea>
                            @if($errors->has('fields_snapshot_json'))
                                <span class="help-block" role="alert">{{ $errors->first('fields_snapshot_json') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.fields_snapshot_json_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('crm_card_attachments') ? 'has-error' : '' }}">
                            <label for="crm_card_attachments">{{ trans('cruds.crmCard.fields.crm_card_attachments') }}</label>
                            <div class="needsclick dropzone" id="crm_card_attachments-dropzone">
                            </div>
                            @if($errors->has('crm_card_attachments'))
                                <span class="help-block" role="alert">{{ $errors->first('crm_card_attachments') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmCard.fields.crm_card_attachments_helper') }}</span>
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

@section('scripts')
<script>
    var uploadedCrmCardAttachmentsMap = {}
Dropzone.options.crmCardAttachmentsDropzone = {
    url: '{{ route('admin.crm-cards.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="crm_card_attachments[]" value="' + response.name + '">')
      uploadedCrmCardAttachmentsMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedCrmCardAttachmentsMap[file.name]
      }
      $('form').find('input[name="crm_card_attachments[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($crmCard) && $crmCard->crm_card_attachments)
          var files =
            {!! json_encode($crmCard->crm_card_attachments) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="crm_card_attachments[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection