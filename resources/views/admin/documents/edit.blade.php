@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.document.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.documents.update", [$document->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('driver') ? 'has-error' : '' }}">
                            <label class="required" for="driver_id">{{ trans('cruds.document.fields.driver') }}</label>
                            <select class="form-control select2" name="driver_id" id="driver_id" required>
                                @foreach($drivers as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('driver_id') ? old('driver_id') : $document->driver->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('driver'))
                                <span class="help-block" role="alert">{{ $errors->first('driver') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.driver_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('notify_driver') ? 'has-error' : '' }}">
                          <div>
                              <input type="hidden" name="notify_driver" value="0">
                              <input type="checkbox" name="notify_driver" id="notify_driver" value="1" {{ $document->notify_driver || old('notify_driver', 0) === 1 ? 'checked' : '' }}>
                              <label for="notify_driver" style="font-weight: 400">{{ trans('cruds.document.fields.notify_driver') }}</label>
                          </div>
                          @if($errors->has('notify_driver'))
                              <span class="help-block" role="alert">{{ $errors->first('notify_driver') }}</span>
                          @endif
                          <span class="help-block">{{ trans('cruds.document.fields.notify_driver_helper') }}</span>
                      </div>
                      <div class="form-group {{ $errors->has('notify_text') ? 'has-error' : '' }}">
                          <label for="notify_text">{{ trans('cruds.document.fields.notify_text') }}</label>
                          <input class="form-control" type="text" name="notify_text" id="notify_text" value="{{ old('notify_text', $document->notify_text) }}">
                          @if($errors->has('notify_text'))
                              <span class="help-block" role="alert">{{ $errors->first('notify_text') }}</span>
                          @endif
                          <span class="help-block">{{ trans('cruds.document.fields.notify_text_helper') }}</span>
                      </div>
                        <div class="form-group {{ $errors->has('citizen_card') ? 'has-error' : '' }}">
                            <label for="citizen_card">{{ trans('cruds.document.fields.citizen_card') }}</label>
                            <div class="needsclick dropzone" id="citizen_card-dropzone">
                            </div>
                            @if($errors->has('citizen_card'))
                                <span class="help-block" role="alert">{{ $errors->first('citizen_card') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.citizen_card_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('tvde_driver_certificate') ? 'has-error' : '' }}">
                            <label for="tvde_driver_certificate">{{ trans('cruds.document.fields.tvde_driver_certificate') }}</label>
                            <div class="needsclick dropzone" id="tvde_driver_certificate-dropzone">
                            </div>
                            @if($errors->has('tvde_driver_certificate'))
                                <span class="help-block" role="alert">{{ $errors->first('tvde_driver_certificate') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.tvde_driver_certificate_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('criminal_record') ? 'has-error' : '' }}">
                            <label for="criminal_record">{{ trans('cruds.document.fields.criminal_record') }}</label>
                            <div class="needsclick dropzone" id="criminal_record-dropzone">
                            </div>
                            @if($errors->has('criminal_record'))
                                <span class="help-block" role="alert">{{ $errors->first('criminal_record') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.criminal_record_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('profile_picture') ? 'has-error' : '' }}">
                            <label for="profile_picture">{{ trans('cruds.document.fields.profile_picture') }}</label>
                            <div class="needsclick dropzone" id="profile_picture-dropzone">
                            </div>
                            @if($errors->has('profile_picture'))
                                <span class="help-block" role="alert">{{ $errors->first('profile_picture') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.profile_picture_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('driving_license') ? 'has-error' : '' }}">
                            <label for="driving_license">{{ trans('cruds.document.fields.driving_license') }}</label>
                            <div class="needsclick dropzone" id="driving_license-dropzone">
                            </div>
                            @if($errors->has('driving_license'))
                                <span class="help-block" role="alert">{{ $errors->first('driving_license') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.driving_license_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('iban') ? 'has-error' : '' }}">
                            <label for="iban">{{ trans('cruds.document.fields.iban') }}</label>
                            <div class="needsclick dropzone" id="iban-dropzone">
                            </div>
                            @if($errors->has('iban'))
                                <span class="help-block" role="alert">{{ $errors->first('iban') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.iban_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="address">{{ trans('cruds.document.fields.address') }}</label>
                            <div class="needsclick dropzone" id="address-dropzone">
                            </div>
                            @if($errors->has('address'))
                                <span class="help-block" role="alert">{{ $errors->first('address') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.address_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('dua_vehicle') ? 'has-error' : '' }}">
                            <label for="dua_vehicle">{{ trans('cruds.document.fields.dua_vehicle') }}</label>
                            <div class="needsclick dropzone" id="dua_vehicle-dropzone">
                            </div>
                            @if($errors->has('dua_vehicle'))
                                <span class="help-block" role="alert">{{ $errors->first('dua_vehicle') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.dua_vehicle_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('car_insurance') ? 'has-error' : '' }}">
                            <label for="car_insurance">{{ trans('cruds.document.fields.car_insurance') }}</label>
                            <div class="needsclick dropzone" id="car_insurance-dropzone">
                            </div>
                            @if($errors->has('car_insurance'))
                                <span class="help-block" role="alert">{{ $errors->first('car_insurance') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.car_insurance_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('ipo_vehicle') ? 'has-error' : '' }}">
                            <label for="ipo_vehicle">{{ trans('cruds.document.fields.ipo_vehicle') }}</label>
                            <div class="needsclick dropzone" id="ipo_vehicle-dropzone">
                            </div>
                            @if($errors->has('ipo_vehicle'))
                                <span class="help-block" role="alert">{{ $errors->first('ipo_vehicle') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.ipo_vehicle_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('vehicle_inspection') ? 'has-error' : '' }}">
                            <label for="vehicle_inspection">{{ trans('cruds.document.fields.vehicle_inspection') }}</label>
                            <div class="needsclick dropzone" id="vehicle_inspection-dropzone">
                            </div>
                            @if($errors->has('vehicle_inspection'))
                                <span class="help-block" role="alert">{{ $errors->first('vehicle_inspection') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.vehicle_inspection_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label for="notes">{{ trans('cruds.document.fields.notes') }}</label>
                            <textarea class="form-control" name="notes" id="notes">{{ old('notes', $document->notes) }}</textarea>
                            @if($errors->has('notes'))
                                <span class="help-block" role="alert">{{ $errors->first('notes') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.document.fields.notes_helper') }}</span>
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
    var uploadedCitizenCardMap = {}
Dropzone.options.citizenCardDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="citizen_card[]" value="' + response.name + '">')
      uploadedCitizenCardMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedCitizenCardMap[file.name]
      }
      $('form').find('input[name="citizen_card[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->citizen_card)
          var files =
            {!! json_encode($document->citizen_card) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="citizen_card[]" value="' + file.file_name + '">')
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
<script>
    var uploadedTvdeDriverCertificateMap = {}
Dropzone.options.tvdeDriverCertificateDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="tvde_driver_certificate[]" value="' + response.name + '">')
      uploadedTvdeDriverCertificateMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedTvdeDriverCertificateMap[file.name]
      }
      $('form').find('input[name="tvde_driver_certificate[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->tvde_driver_certificate)
          var files =
            {!! json_encode($document->tvde_driver_certificate) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="tvde_driver_certificate[]" value="' + file.file_name + '">')
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
<script>
    var uploadedCriminalRecordMap = {}
Dropzone.options.criminalRecordDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="criminal_record[]" value="' + response.name + '">')
      uploadedCriminalRecordMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedCriminalRecordMap[file.name]
      }
      $('form').find('input[name="criminal_record[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->criminal_record)
          var files =
            {!! json_encode($document->criminal_record) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="criminal_record[]" value="' + file.file_name + '">')
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
<script>
    Dropzone.options.profilePictureDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="profile_picture"]').remove()
      $('form').append('<input type="hidden" name="profile_picture" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="profile_picture"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($document) && $document->profile_picture)
      var file = {!! json_encode($document->profile_picture) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="profile_picture" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
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
<script>
    var uploadedDrivingLicenseMap = {}
Dropzone.options.drivingLicenseDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="driving_license[]" value="' + response.name + '">')
      uploadedDrivingLicenseMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDrivingLicenseMap[file.name]
      }
      $('form').find('input[name="driving_license[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->driving_license)
          var files =
            {!! json_encode($document->driving_license) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="driving_license[]" value="' + file.file_name + '">')
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
<script>
    var uploadedIbanMap = {}
Dropzone.options.ibanDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="iban[]" value="' + response.name + '">')
      uploadedIbanMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedIbanMap[file.name]
      }
      $('form').find('input[name="iban[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->iban)
          var files =
            {!! json_encode($document->iban) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="iban[]" value="' + file.file_name + '">')
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
<script>
    var uploadedAddressMap = {}
Dropzone.options.addressDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="address[]" value="' + response.name + '">')
      uploadedAddressMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedAddressMap[file.name]
      }
      $('form').find('input[name="address[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->address)
          var files =
            {!! json_encode($document->address) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="address[]" value="' + file.file_name + '">')
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
<script>
    var uploadedDuaVehicleMap = {}
Dropzone.options.duaVehicleDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="dua_vehicle[]" value="' + response.name + '">')
      uploadedDuaVehicleMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDuaVehicleMap[file.name]
      }
      $('form').find('input[name="dua_vehicle[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->dua_vehicle)
          var files =
            {!! json_encode($document->dua_vehicle) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="dua_vehicle[]" value="' + file.file_name + '">')
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
<script>
    var uploadedCarInsuranceMap = {}
Dropzone.options.carInsuranceDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="car_insurance[]" value="' + response.name + '">')
      uploadedCarInsuranceMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedCarInsuranceMap[file.name]
      }
      $('form').find('input[name="car_insurance[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->car_insurance)
          var files =
            {!! json_encode($document->car_insurance) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="car_insurance[]" value="' + file.file_name + '">')
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
<script>
    var uploadedIpoVehicleMap = {}
Dropzone.options.ipoVehicleDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="ipo_vehicle[]" value="' + response.name + '">')
      uploadedIpoVehicleMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedIpoVehicleMap[file.name]
      }
      $('form').find('input[name="ipo_vehicle[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->ipo_vehicle)
          var files =
            {!! json_encode($document->ipo_vehicle) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="ipo_vehicle[]" value="' + file.file_name + '">')
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
<script>
    var uploadedVehicleInspectionMap = {}
Dropzone.options.vehicleInspectionDropzone = {
    url: '{{ route('admin.documents.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="vehicle_inspection[]" value="' + response.name + '">')
      uploadedVehicleInspectionMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedVehicleInspectionMap[file.name]
      }
      $('form').find('input[name="vehicle_inspection[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($document) && $document->vehicle_inspection)
          var files =
            {!! json_encode($document->vehicle_inspection) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="vehicle_inspection[]" value="' + file.file_name + '">')
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