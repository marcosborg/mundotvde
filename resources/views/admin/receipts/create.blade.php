@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.create') }} {{ trans('cruds.receipt.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.receipts.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group {{ $errors->has('reference') ? 'has-error' : '' }}">
                            <label class="required" for="reference">{{ trans('cruds.receipt.fields.reference') }}</label>
                            <input class="form-control" type="text" name="reference" id="reference" value="{{ old('reference', '') }}" required>
                            @if($errors->has('reference'))
                                <span class="help-block" role="alert">{{ $errors->first('reference') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.receipt.fields.reference_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('activity_launch') ? 'has-error' : '' }}">
                            <label class="required" for="activity_launch_id">{{ trans('cruds.receipt.fields.activity_launch') }}</label>
                            <select class="form-control select2" name="activity_launch_id" id="activity_launch_id" required>
                                @foreach($activity_launches as $id => $entry)
                                    <option value="{{ $id }}" {{ old('activity_launch_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('activity_launch'))
                                <span class="help-block" role="alert">{{ $errors->first('activity_launch') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.receipt.fields.activity_launch_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('receipt') ? 'has-error' : '' }}">
                            <label class="required" for="receipt">{{ trans('cruds.receipt.fields.receipt') }}</label>
                            <div class="needsclick dropzone" id="receipt-dropzone">
                            </div>
                            @if($errors->has('receipt'))
                                <span class="help-block" role="alert">{{ $errors->first('receipt') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.receipt.fields.receipt_helper') }}</span>
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
    var uploadedReceiptMap = {}
Dropzone.options.receiptDropzone = {
    url: '{{ route('admin.receipts.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="receipt[]" value="' + response.name + '">')
      uploadedReceiptMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedReceiptMap[file.name]
      }
      $('form').find('input[name="receipt[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($receipt) && $receipt->receipt)
          var files =
            {!! json_encode($receipt->receipt) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="receipt[]" value="' + file.file_name + '">')
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