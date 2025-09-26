@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.crmEmailsQueue.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.crm-emails-queues.update", [$crmEmailsQueue->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('stage_email') ? 'has-error' : '' }}">
                            <label class="required" for="stage_email_id">{{ trans('cruds.crmEmailsQueue.fields.stage_email') }}</label>
                            <select class="form-control select2" name="stage_email_id" id="stage_email_id" required>
                                @foreach($stage_emails as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('stage_email_id') ? old('stage_email_id') : $crmEmailsQueue->stage_email->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('stage_email'))
                                <span class="help-block" role="alert">{{ $errors->first('stage_email') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.stage_email_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('card') ? 'has-error' : '' }}">
                            <label for="card_id">{{ trans('cruds.crmEmailsQueue.fields.card') }}</label>
                            <select class="form-control select2" name="card_id" id="card_id">
                                @foreach($cards as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('card_id') ? old('card_id') : $crmEmailsQueue->card->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('card'))
                                <span class="help-block" role="alert">{{ $errors->first('card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('to') ? 'has-error' : '' }}">
                            <label class="required" for="to">{{ trans('cruds.crmEmailsQueue.fields.to') }}</label>
                            <input class="form-control" type="text" name="to" id="to" value="{{ old('to', $crmEmailsQueue->to) }}" required>
                            @if($errors->has('to'))
                                <span class="help-block" role="alert">{{ $errors->first('to') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.to_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('cc') ? 'has-error' : '' }}">
                            <label for="cc">{{ trans('cruds.crmEmailsQueue.fields.cc') }}</label>
                            <input class="form-control" type="text" name="cc" id="cc" value="{{ old('cc', $crmEmailsQueue->cc) }}">
                            @if($errors->has('cc'))
                                <span class="help-block" role="alert">{{ $errors->first('cc') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.cc_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                            <label class="required" for="subject">{{ trans('cruds.crmEmailsQueue.fields.subject') }}</label>
                            <input class="form-control" type="text" name="subject" id="subject" value="{{ old('subject', $crmEmailsQueue->subject) }}" required>
                            @if($errors->has('subject'))
                                <span class="help-block" role="alert">{{ $errors->first('subject') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.subject_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('body_html') ? 'has-error' : '' }}">
                            <label for="body_html">{{ trans('cruds.crmEmailsQueue.fields.body_html') }}</label>
                            <textarea class="form-control ckeditor" name="body_html" id="body_html">{!! old('body_html', $crmEmailsQueue->body_html) !!}</textarea>
                            @if($errors->has('body_html'))
                                <span class="help-block" role="alert">{{ $errors->first('body_html') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.body_html_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label>{{ trans('cruds.crmEmailsQueue.fields.status') }}</label>
                            @foreach(App\Models\CrmEmailsQueue::STATUS_RADIO as $key => $label)
                                <div>
                                    <input type="radio" id="status_{{ $key }}" name="status" value="{{ $key }}" {{ old('status', $crmEmailsQueue->status) === (string) $key ? 'checked' : '' }}>
                                    <label for="status_{{ $key }}" style="font-weight: 400">{{ $label }}</label>
                                </div>
                            @endforeach
                            @if($errors->has('status'))
                                <span class="help-block" role="alert">{{ $errors->first('status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.status_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('error') ? 'has-error' : '' }}">
                            <label for="error">{{ trans('cruds.crmEmailsQueue.fields.error') }}</label>
                            <input class="form-control" type="text" name="error" id="error" value="{{ old('error', $crmEmailsQueue->error) }}">
                            @if($errors->has('error'))
                                <span class="help-block" role="alert">{{ $errors->first('error') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.error_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('scheduled_at') ? 'has-error' : '' }}">
                            <label for="scheduled_at">{{ trans('cruds.crmEmailsQueue.fields.scheduled_at') }}</label>
                            <input class="form-control datetime" type="text" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at', $crmEmailsQueue->scheduled_at) }}">
                            @if($errors->has('scheduled_at'))
                                <span class="help-block" role="alert">{{ $errors->first('scheduled_at') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.scheduled_at_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('sent_at') ? 'has-error' : '' }}">
                            <label for="sent_at">{{ trans('cruds.crmEmailsQueue.fields.sent_at') }}</label>
                            <input class="form-control datetime" type="text" name="sent_at" id="sent_at" value="{{ old('sent_at', $crmEmailsQueue->sent_at) }}">
                            @if($errors->has('sent_at'))
                                <span class="help-block" role="alert">{{ $errors->first('sent_at') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.crmEmailsQueue.fields.sent_at_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.crm-emails-queues.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $crmEmailsQueue->id ?? 0 }}');
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