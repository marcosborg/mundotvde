@extends('layouts.admin')
@section('content')
<div class="content">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          Inspecao #{{ $inspection->id }}
          <span class="label label-info" style="margin-left:8px;">{{ strtoupper($inspection->status) }}</span>
          <span class="label label-default" style="margin-left:4px;">Etapa atual: {{ $inspection->current_step }}</span>
        </div>
        <div class="panel-body">
          @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif

          <div class="alert alert-info">
            <strong>Viatura:</strong> {{ $inspection->vehicle->license_plate ?? '-' }} |
            <strong>Marca/Modelo:</strong> {{ $inspection->vehicle->vehicle_brand->name ?? '-' }} {{ $inspection->vehicle->vehicle_model->name ?? '' }} |
            <strong>Ano:</strong> {{ $inspection->vehicle->year ?? '-' }} |
            <strong>Tipo:</strong> {{ ucfirst($inspection->type) }}
          </div>

          <div class="row" style="margin-bottom:12px;">
            @foreach($steps as $stepNumber => $stepLabel)
              <div class="col-md-3" style="margin-bottom:8px;">
                <span class="label {{ $stepNumber < $inspection->current_step ? 'label-success' : ($stepNumber == $inspection->current_step ? 'label-primary' : 'label-default') }}" style="display:block;padding:8px;white-space:normal;">
                  {{ $stepNumber }}. {{ $stepLabel }}
                </span>
              </div>
            @endforeach
          </div>

          @php($activeStep = $inspection->report ? 10 : (int) $inspection->current_step)
          <div class="alert alert-warning">
            Esta visivel apenas a etapa ativa: <strong>{{ $activeStep }}. {{ $steps[$activeStep] ?? '' }}</strong>.
          </div>
          @if($activeStep > 1 && !$inspection->locked_at)
          <form method="POST" action="{{ route('admin.inspections.back-step', $inspection->id) }}" style="margin-bottom:12px;">
            @csrf
            <button class="btn btn-default" type="submit">Voltar para etapa {{ $activeStep - 1 }}</button>
          </form>
          @endif

          @if($activeStep === 1)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 1. Identificacao da viatura</div>
            <div class="panel-body">
              <p><strong>Matricula:</strong> {{ $inspection->vehicle->license_plate ?? '-' }}</p>
              <p><strong>Local:</strong> {{ $inspection->location_text ?? '-' }} ({{ $inspection->location_lat ?? '-' }}, {{ $inspection->location_lng ?? '-' }})</p>
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}">
                @csrf
                <input type="hidden" name="step" value="1">
                <input type="hidden" name="action" value="complete">
                <button class="btn btn-primary" type="submit">Concluir etapa 1</button>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 2)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 2. Identificacao do condutor</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}">
                @csrf
                <input type="hidden" name="step" value="2">
                <div class="form-group">
                  <label class="required">Condutor</label>
                  <select name="driver_id" class="form-control select2" required>
                    @foreach($drivers as $id => $name)
                      <option value="{{ $id }}" {{ (string)$inspection->driver_id === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                  </select>
                </div>
                <button class="btn btn-default" type="submit" name="action" value="save">Guardar</button>
                <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa 2</button>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 3)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 3. Documentacao, estado operacional e acessorios</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}">
                @csrf
                <input type="hidden" name="step" value="3">
                <div class="row">
                  <div class="col-md-4">
                    <h5>Documentacao</h5>
                    @foreach([
                      'dua' => 'Documento Unico Automovel (DUA)',
                      'insurance' => 'Seguro',
                      'inspection_periodic' => 'Inspecao periodica',
                      'tvde_stickers' => 'Disticos TVDE',
                      'no_smoking_sticker' => 'Autocolante de proibicao de fumar'
                    ] as $key => $label)
                      <div style="margin-bottom:10px;">
                        <label>
                          <input type="checkbox" name="checklist[documents][{{ $key }}]" value="1" {{ !empty($checklist['documents'][$key]) ? 'checked' : '' }}>
                          {{ $label }}
                        </label>
                        <input type="file" class="form-control" name="checklist_photos[{{ $key }}]" style="margin-top:4px;">
                        @if(!empty($checklistPhotoBySlot[$key]))
                          <small class="text-success">Foto atual: <a target="_blank" href="{{ asset('storage/' . $checklistPhotoBySlot[$key]->path) }}">ver</a></small>
                        @else
                          <small class="text-danger">Sem foto</small>
                        @endif
                      </div>
                    @endforeach
                  </div>
                  <div class="col-md-4">
                    <h5>Operacional</h5>
                    @foreach(['lights' => 'Luzes', 'brakes' => 'Travoes', 'wipers' => 'Escovas', 'tires' => 'Pneus'] as $key => $label)
                      <label><input type="checkbox" name="checklist[operational][{{ $key }}]" value="1" {{ !empty($checklist['operational'][$key]) ? 'checked' : '' }}> {{ $label }}</label><br>
                    @endforeach
                  </div>
                  <div class="col-md-4">
                    <h5>Acessorios</h5>
                    @foreach(['triangle' => 'Triangulo', 'vest' => 'Colete', 'extinguisher' => 'Extintor', 'spare' => 'Pneu suplente'] as $key => $label)
                      <label><input type="checkbox" name="checklist[accessories][{{ $key }}]" value="1" {{ !empty($checklist['accessories'][$key]) ? 'checked' : '' }}> {{ $label }}</label><br>
                    @endforeach
                  </div>
                </div>
                <button class="btn btn-default" type="submit" name="action" value="save">Guardar</button>
                <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa 3</button>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 4)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 4. Fotografias exteriores (obrigatorias)</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="step" value="4">
                <div class="row">
                  @foreach($requiredExterior as $slot)
                    <div class="col-md-3" style="margin-bottom:12px;">
                      <div class="well" style="margin-bottom:6px;">
                        <strong>{{ $slotLabels['exterior'][$slot] ?? $slot }}</strong><br>
                        <small class="text-muted">{{ $slot }}</small><br>
                        @if(!empty($exteriorBySlot[$slot]))
                          <span class="label label-success">OK</span>
                          <a href="{{ asset('storage/' . $exteriorBySlot[$slot]->path) }}" target="_blank">ver</a>
                        @else
                          <span class="label label-danger">Em falta</span>
                        @endif
                      </div>
                      <input type="file" class="form-control" name="exterior_photos[{{ $slot }}]">
                    </div>
                  @endforeach
                </div>
                <button class="btn btn-default" type="submit" name="action" value="save">Guardar uploads</button>
                <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa 4</button>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 5)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 5. Fotografias interiores (obrigatorias)</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="step" value="5">
                <div class="row">
                  @foreach($requiredInterior as $slot)
                    <div class="col-md-3" style="margin-bottom:12px;">
                      <div class="well" style="margin-bottom:6px;">
                        <strong>{{ $slotLabels['interior'][$slot] ?? $slot }}</strong><br>
                        <small class="text-muted">{{ $slot }}</small><br>
                        @if(!empty($interiorBySlot[$slot]))
                          <span class="label label-success">OK</span>
                          <a href="{{ asset('storage/' . $interiorBySlot[$slot]->path) }}" target="_blank">ver</a>
                        @else
                          <span class="label label-danger">Em falta</span>
                        @endif
                      </div>
                      <input type="file" class="form-control" name="interior_photos[{{ $slot }}]">
                    </div>
                  @endforeach
                </div>
                <button class="btn btn-default" type="submit" name="action" value="save">Guardar uploads</button>
                <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa 5</button>
              </form>
            </div>
          </div>
          @endif

          @if(in_array($activeStep, [6, 7], true))
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa {{ $activeStep }}. Danos {{ $activeStep === 6 ? 'exteriores' : 'interiores' }}</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="step" value="{{ $activeStep }}">
                <div class="row">
                  <div class="col-md-2"><label>Local</label><select class="form-control" name="location"><option value="">Selecione</option>@foreach($damageLocations as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                  <div class="col-md-2"><label>Peca</label><input class="form-control" name="part"></div>
                  <div class="col-md-2"><label>Parte</label><input class="form-control" name="part_section"></div>
                  <div class="col-md-2"><label>Tipo</label><select class="form-control" name="damage_type"><option value="">Selecione</option>@foreach($damageTypes as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                  <div class="col-md-4"><label>Foto*</label><input type="file" class="form-control" name="damage_photo"></div>
                </div>
                <div class="form-group" style="margin-top:8px;">
                  <label>Observacoes</label>
                  <textarea class="form-control" name="damage_notes"></textarea>
                </div>
                <button class="btn btn-default" type="submit" name="action" value="save">Guardar dano</button>
                <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa {{ $activeStep }}</button>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 8)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 8. Extras e observacoes</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="step" value="8">
                <div class="form-group">
                  <label>Observacoes adicionais</label>
                  <textarea class="form-control" name="extra_observations">{{ $inspection->extra_observations }}</textarea>
                </div>
                <div class="form-group">
                  <label>Fotos extra (opcional)</label>
                  <input class="form-control" type="file" name="extra_photos[]" multiple>
                </div>
                <button class="btn btn-default" type="submit" name="action" value="save">Guardar</button>
                <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa 8</button>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 9)
          <div class="panel panel-primary">
            <div class="panel-heading">Etapa 9. Assinaturas digitais</div>
            <div class="panel-body">
              <form method="POST" action="{{ route('admin.inspections.update-step', $inspection->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="step" value="9">
                <div class="row">
                  <div class="col-md-6">
                    <label>Nome de quem faz a vistoria (responsavel)</label>
                    <input class="form-control" name="inspector_name" value="{{ $signatureNames['responsible'] ?? '' }}">
                  </div>
                  <div class="col-md-6">
                    <label>Nome do condutor</label>
                    <input class="form-control" name="driver_signature_name" value="{{ $signatureNames['driver'] ?? '' }}">
                  </div>
                </div>
                <div style="margin-top:10px;">
                  <button class="btn btn-default" type="submit" name="action" value="save">Guardar assinatura</button>
                  <button class="btn btn-primary" type="submit" name="action" value="complete">Concluir etapa 9</button>
                </div>
              </form>
            </div>
          </div>
          @endif

          @if($activeStep === 10)
          <div class="panel panel-success">
            <div class="panel-heading">Etapa 10. Fecho e PDF imutavel</div>
            <div class="panel-body">
              <h4>Relatorio de pendencias</h4>
              @if(!empty($missingItems))
                <p>Itens nao preenchidos:</p>
                <div style="margin-bottom:12px;">
                  @foreach($missingItems as $m)
                    <span class="label label-danger" style="display:inline-block;margin:2px 4px 2px 0;">{{ $m['group'] }}: {{ $m['item'] }}</span>
                  @endforeach
                </div>
              @else
                <span class="label label-success">Sem pendencias</span>
              @endif

              <hr>
              @if($inspection->report)
                <p><strong>Relatorio ja gerado:</strong> <a target="_blank" href="{{ asset('storage/' . $inspection->report->pdf_path) }}">Abrir PDF</a></p>
                <p><strong>Hash:</strong> <code>{{ $inspection->report->pdf_hash }}</code></p>
              @else
                <form method="POST" action="{{ route('admin.inspections.close', $inspection->id) }}">
                  @csrf
                  <button class="btn btn-success" type="submit">Fechar inspecao e gerar PDF</button>
                </form>
              @endif
            </div>
          </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
