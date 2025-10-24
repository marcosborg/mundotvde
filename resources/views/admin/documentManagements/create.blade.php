@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.documentManagement.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.document-managements.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label class="required" for="title">{{ trans('cruds.documentManagement.fields.title') }}</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                            @if($errors->has('title'))
                                <span class="help-block" role="alert">{{ $errors->first('title') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentManagement.fields.title_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('doc_company') ? 'has-error' : '' }}">
                            <label class="required" for="doc_company_id">{{ trans('cruds.documentManagement.fields.doc_company') }}</label>
                            <select class="form-control select2" name="doc_company_id" id="doc_company_id" required>
                                @foreach($doc_companies as $id => $entry)
                                    <option value="{{ $id }}" {{ old('doc_company_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('doc_company'))
                                <span class="help-block" role="alert">{{ $errors->first('doc_company') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentManagement.fields.doc_company_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('text') ? 'has-error' : '' }}">
                            <label for="text">{{ trans('cruds.documentManagement.fields.text') }}</label>
                            <textarea class="form-control ckeditor" name="text" id="text">{!! old('text') !!}</textarea>
                            @if($errors->has('text'))
                                <span class="help-block" role="alert">{{ $errors->first('text') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentManagement.fields.text_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('signatures') ? 'has-error' : '' }}">
                            <label for="signatures">{{ trans('cruds.documentManagement.fields.signature') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2" name="signatures[]" id="signatures" multiple>
                                @foreach($signatures as $id => $signature)
                                    <option value="{{ $id }}" {{ in_array($id, old('signatures', [])) ? 'selected' : '' }}>{{ $signature }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('signatures'))
                                <span class="help-block" role="alert">{{ $errors->first('signatures') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentManagement.fields.signature_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.document-managements.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $documentManagement->id ?? 0 }}');
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