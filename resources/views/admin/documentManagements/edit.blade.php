@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('global.edit') }} {{ trans('cruds.documentManagement.title_singular') }}
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route("admin.document-managements.update", [$documentManagement->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <label class="required" for="title">{{ trans('cruds.documentManagement.fields.title') }}</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title', $documentManagement->title) }}" required>
                            @if($errors->has('title'))
                                <span class="help-block" role="alert">{{ $errors->first('title') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentManagement.fields.title_helper') }}</span>
                        </div>
                        <div class="form-group {{ $errors->has('doc_company') ? 'has-error' : '' }}">
                            <label class="required" for="doc_company_id">{{ trans('cruds.documentManagement.fields.doc_company') }}</label>
                            <select class="form-control select2" name="doc_company_id" id="doc_company_id" required>
                                @foreach($doc_companies as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('doc_company_id') ? old('doc_company_id') : $documentManagement->doc_company->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('doc_company'))
                                <span class="help-block" role="alert">{{ $errors->first('doc_company') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.documentManagement.fields.doc_company_helper') }}</span>
                        </div>
                        <!-- Botão para abrir o modal -->
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tagsModal">
                            <i class="fa fa-tags"></i> Ver tags disponíveis
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="tagsModal" tabindex="-1" role="dialog" aria-labelledby="tagsModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="tagsModalLabel"><i class="fa fa-tags"></i> Tags disponíveis para utilização</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                
                                <input type="text" id="tagSearch" class="form-control mb-3" placeholder="Pesquisar tag ou descrição...">

                                <div id="tagsList">
                                  <!-- DRIVER -->
                                  <h5 class="mt-3"><i class="fa fa-user text-primary"></i> Motorista (Driver)</h5>
                                  <table class="table table-sm table-bordered tags-table">
                                    <tbody>
                                      @foreach([
                                        'driver_id' => 'ID interno do motorista',
                                        'driver_name' => 'Nome completo',
                                        'driver_email' => 'Email',
                                        'driver_phone' => 'Telefone',
                                        'driver_city' => 'Cidade',
                                        'driver_address' => 'Morada',
                                        'driver_zip' => 'Código postal',
                                        'driver_country' => 'País',
                                        'driver_nif' => 'NIF',
                                        'driver_citizen_card' => 'Nº do Cartão de Cidadão',
                                        'driver_citizen_card_expiry_date' => 'Validade do Cartão de Cidadão',
                                        'driver_birth_date' => 'Data de nascimento',
                                        'driver_nationality' => 'Nacionalidade',
                                        'driver_driver_certificate' => 'Nº do Certificado TVDE',
                                        'driver_driver_certificate_expiry' => 'Validade do Certificado TVDE',
                                        'driver_license_number' => 'Nº da Carta de Condução',
                                        'driver_license_expiry' => 'Validade da Carta de Condução',
                                        'driver_license_plate' => 'Matrícula do veículo',
                                        'driver_brand' => 'Marca do veículo',
                                        'driver_model' => 'Modelo do veículo',
                                        'driver_vehicle_date' => 'Data da 1ª matrícula',
                                        'driver_iban' => 'IBAN',
                                        'driver_niss' => 'NISS'
                                      ] as $tag => $desc)
                                      <tr>
                                        <td><code>[{{ $tag }}]</code></td>
                                        <td>{{ $desc }}</td>
                                        <td class="text-right"><button type="button" class="btn btn-xs btn-outline-primary copy-tag" data-tag="[{{ $tag }}]">Copiar</button></td>
                                      </tr>
                                      @endforeach
                                    </tbody>
                                  </table>

                                  <!-- OWNER -->
                                  <h5 class="mt-4"><i class="fa fa-id-badge text-success"></i> Proprietário (Owner)</h5>
                                  <p class="text-muted small">Mesmas tags do motorista, mas com prefixo <code>[owner_]</code></p>

                                  <!-- COMPANY -->
                                  <h5 class="mt-4"><i class="fa fa-building text-warning"></i> Empresa (Doc Company)</h5>
                                  <table class="table table-sm table-bordered tags-table">
                                    <tbody>
                                      @foreach([
                                        'company_id' => 'ID interno da empresa',
                                        'company_name' => 'Nome da empresa',
                                        'company_nipc' => 'NIPC',
                                        'company_address' => 'Morada',
                                        'company_zip' => 'Código postal',
                                        'company_city' => 'Cidade',
                                        'company_country' => 'País',
                                        'company_phone' => 'Telefone',
                                        'company_email' => 'Email',
                                        'company_license_number' => 'Nº de licença TVDE',
                                        'company_iban' => 'IBAN'
                                      ] as $tag => $desc)
                                      <tr>
                                        <td><code>[{{ $tag }}]</code></td>
                                        <td>{{ $desc }}</td>
                                        <td class="text-right"><button type="button" class="btn btn-xs btn-outline-primary copy-tag" data-tag="[{{ $tag }}]">Copiar</button></td>
                                      </tr>
                                      @endforeach
                                    </tbody>
                                  </table>

                                  <!-- DOCUMENT -->
                                  <h5 class="mt-4"><i class="fa fa-file-text text-secondary"></i> Documento (Document Generated)</h5>
                                  <table class="table table-sm table-bordered tags-table">
                                    <tbody>
                                      @foreach([
                                        'date' => 'Data do documento (campo date)',
                                        'doc_id' => 'ID do modelo de documento (Document Management)',
                                        'generated_id' => 'ID do documento gerado',
                                        'now' => 'Data e hora atuais'
                                      ] as $tag => $desc)
                                      <tr>
                                        <td><code>[{{ $tag }}]</code></td>
                                        <td>{{ $desc }}</td>
                                        <td class="text-right"><button type="button" class="btn btn-xs btn-outline-primary copy-tag" data-tag="[{{ $tag }}]">Copiar</button></td>
                                      </tr>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div>

                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group {{ $errors->has('text') ? 'has-error' : '' }}">
                            <label for="text">{{ trans('cruds.documentManagement.fields.text') }}</label>
                            <textarea class="form-control ckeditor" name="text" id="text">{!! old('text', $documentManagement->text) !!}</textarea>
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
                                    <option value="{{ $id }}" {{ (in_array($id, old('signatures', [])) || $documentManagement->signatures->contains($id)) ? 'selected' : '' }}>{{ $signature }}</option>
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
<script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Copiar tag
                            document.querySelectorAll('.copy-tag').forEach(btn => {
                                btn.addEventListener('click', function () {
                                    const tag = this.dataset.tag;
                                    navigator.clipboard.writeText(tag).then(() => {
                                        this.textContent = 'Copiado!';
                                        this.classList.remove('btn-outline-primary');
                                        this.classList.add('btn-success');
                                        setTimeout(() => {
                                            this.textContent = 'Copiar';
                                            this.classList.remove('btn-success');
                                            this.classList.add('btn-outline-primary');
                                        }, 1200);
                                    });
                                });
                            });

                            // Pesquisa
                            const searchInput = document.getElementById('tagSearch');
                            const rows = document.querySelectorAll('#tagsList table tbody tr');
                            searchInput.addEventListener('keyup', function () {
                                const term = this.value.toLowerCase();
                                rows.forEach(row => {
                                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                                });
                            });
                        });
                        </script>
@endsection