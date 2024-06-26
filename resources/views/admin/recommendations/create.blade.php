@extends('layouts.admin')
@section('content')
<div class="content">

  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          {{ trans('global.create') }} {{ trans('cruds.recommendation.title_singular') }}
        </div>
        <div class="panel-body">
          <form method="POST" action="{{ route("admin.recommendations.store") }}" enctype="multipart/form-data">
            @csrf
            @if (auth()->user()->roles()->where('title', 'Admin')->exists())
            <div class="form-group {{ $errors->has('driver') ? 'has-error' : '' }}">
              <label class="required" for="driver_id">{{ trans('cruds.recommendation.fields.driver') }}</label>
              <select class="form-control select2" name="driver_id" id="driver_id" required>
                @foreach($drivers as $id => $entry)
                <option value="{{ $id }}" {{ old('driver_id')==$id ? 'selected' : '' }}>{{ $entry }}</option>
                @endforeach
              </select>
              @if($errors->has('driver'))
              <span class="help-block" role="alert">{{ $errors->first('driver') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.driver_helper') }}</span>
            </div>
            @else
            <input type="hidden" name="driver_id" id="driver_id" value="{{ $driver_id }}">
            @endif
            @if (auth()->user()->roles()->where('title', 'Admin')->exists())
            <div class="form-group {{ $errors->has('recommendation_status') ? 'has-error' : '' }}">
              <label class="required" for="recommendation_status_id">{{
                trans('cruds.recommendation.fields.recommendation_status') }}</label>
              <select class="form-control select2" name="recommendation_status_id" id="recommendation_status_id"
                required>
                @foreach($recommendation_statuses as $id => $entry)
                <option value="{{ $id }}" {{ old('recommendation_status_id')==$id ? 'selected' : '' }}>{{ $entry }}
                </option>
                @endforeach
              </select>
              @if($errors->has('recommendation_status'))
              <span class="help-block" role="alert">{{ $errors->first('recommendation_status') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.recommendation_status_helper') }}</span>
            </div>
            @else
            <input type="hidden" name="recommendation_status_id" id="recommendation_status_id" value="1">
            @endif
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
              <label class="required" for="name">{{ trans('cruds.recommendation.fields.name') }}</label>
              <input class="form-control" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
              @if($errors->has('name'))
              <span class="help-block" role="alert">{{ $errors->first('name') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.name_helper') }}</span>
            </div>
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
              <label class="required" for="email">{{ trans('cruds.recommendation.fields.email') }}</label>
              <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" required>
              @if($errors->has('email'))
              <span class="help-block" role="alert">{{ $errors->first('email') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.email_helper') }}</span>
            </div>
            <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
              <label class="required" for="phone">{{ trans('cruds.recommendation.fields.phone') }}</label>
              <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', '') }}" required>
              @if($errors->has('phone'))
              <span class="help-block" role="alert">{{ $errors->first('phone') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.phone_helper') }}</span>
            </div>
            <div class="form-group {{ $errors->has('city') ? 'has-error' : '' }}">
              <label class="required" for="city">{{ trans('cruds.recommendation.fields.city') }}</label>
              <input class="form-control" type="text" name="city" id="city" value="{{ old('city', '') }}" required>
              @if($errors->has('city'))
              <span class="help-block" role="alert">{{ $errors->first('city') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.city_helper') }}</span>
            </div>
            <div class="form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
              <label for="comments">{{ trans('cruds.recommendation.fields.comments') }}</label>
              <textarea class="form-control ckeditor" name="comments" id="comments">{!! old('comments') !!}</textarea>
              @if($errors->has('comments'))
              <span class="help-block" role="alert">{{ $errors->first('comments') }}</span>
              @endif
              <span class="help-block">{{ trans('cruds.recommendation.fields.comments_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.recommendations.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $recommendation->id ?? 0 }}');
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