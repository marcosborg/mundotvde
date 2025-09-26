@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.crmStageEmail.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-stage-emails.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('stage') ? 'has-error' : '' }}">
                            <label class="required" for="stage_id">{{ trans('cruds.crmStageEmail.fields.stage') }}</label>
                            <select class="form-control select2" name="stage_id" id="stage_id" required>
                                @foreach($stages as $id => $entry)
                                    <option value="{{ $id }}" {{ old('stage_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('stage'))
                                <span class="help-block" role="alert">{{ $errors->first('stage') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.stage_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('to_emails') ? 'has-error' : '' }}">
                            <label for="to_emails">{{ trans('cruds.crmStageEmail.fields.to_emails') }}</label>
                            <textarea class="form-control" name="to_emails" id="to_emails">{{ old('to_emails') }}</textarea>
                            @if($errors->has('to_emails'))
                                <span class="help-block" role="alert">{{ $errors->first('to_emails') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.to_emails_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('bcc_emails') ? 'has-error' : '' }}">
                            <label for="bcc_emails">{{ trans('cruds.crmStageEmail.fields.bcc_emails') }}</label>
                            <textarea class="form-control" name="bcc_emails" id="bcc_emails">{{ old('bcc_emails') }}</textarea>
                            @if($errors->has('bcc_emails'))
                                <span class="help-block" role="alert">{{ $errors->first('bcc_emails') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.bcc_emails_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                            <label class="required" for="subject">{{ trans('cruds.crmStageEmail.fields.subject') }}</label>
                            <input class="form-control" type="text" name="subject" id="subject" value="{{ old('subject', '') }}" required>
                            @if($errors->has('subject'))
                                <span class="help-block" role="alert">{{ $errors->first('subject') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.subject_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('body_template') ? 'has-error' : '' }}">
                            <label for="body_template">{{ trans('cruds.crmStageEmail.fields.body_template') }}</label>
                            <textarea class="form-control ckeditor" name="body_template" id="body_template">{!! old('body_template') !!}</textarea>
                            @if($errors->has('body_template'))
                                <span class="help-block" role="alert">{{ $errors->first('body_template') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.body_template_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('send_on_enter') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="send_on_enter" value="0">
                                <input type="checkbox" name="send_on_enter" id="send_on_enter" value="1" {{ old('send_on_enter', 0) == 1 || old('send_on_enter') === null ? 'checked' : '' }}>
                                <label for="send_on_enter" style="font-weight: 400">{{ trans('cruds.crmStageEmail.fields.send_on_enter') }}</label>
                            </div>
                            @if($errors->has('send_on_enter'))
                                <span class="help-block" role="alert">{{ $errors->first('send_on_enter') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.send_on_enter_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('send_on_exit') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="send_on_exit" value="0">
                                <input type="checkbox" name="send_on_exit" id="send_on_exit" value="1" {{ old('send_on_exit', 0) == 1 ? 'checked' : '' }}>
                                <label for="send_on_exit" style="font-weight: 400">{{ trans('cruds.crmStageEmail.fields.send_on_exit') }}</label>
                            </div>
                            @if($errors->has('send_on_exit'))
                                <span class="help-block" role="alert">{{ $errors->first('send_on_exit') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.send_on_exit_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('delay_minutes') ? 'has-error' : '' }}">
                            <label for="delay_minutes">{{ trans('cruds.crmStageEmail.fields.delay_minutes') }}</label>
                            <input class="form-control" type="number" name="delay_minutes" id="delay_minutes" value="{{ old('delay_minutes', '') }}" step="1">
                            @if($errors->has('delay_minutes'))
                                <span class="help-block" role="alert">{{ $errors->first('delay_minutes') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.delay_minutes_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('is_active') ? 'has-error' : '' }}">
                            <div>
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 0) == 1 || old('is_active') === null ? 'checked' : '' }}>
                                <label for="is_active" style="font-weight: 400">{{ trans('cruds.crmStageEmail.fields.is_active') }}</label>
                            </div>
                            @if($errors->has('is_active'))
                                <span class="help-block" role="alert">{{ $errors->first('is_active') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmStageEmail.fields.is_active_helper') }}</span>
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
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.crm-stage-emails.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $crmStageEmail->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

@endsection