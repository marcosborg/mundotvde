<div class="row">
  <div class="col-md-6 form-group">
    <label class="required">Viatura</label>
    <select name="vehicle_id" class="form-control select2" required>
      @foreach($vehicles as $v)
      <option value="{{ $v->id }}" {{ (string)old('vehicle_id', $inspectionSchedule->vehicle_id ?? '') === (string)$v->id ? 'selected' : '' }}>
        {{ $v->license_plate }}
      </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6 form-group">
    <label>Motorista</label>
    <select name="driver_id" class="form-control select2">
      @foreach($drivers as $id => $name)
      <option value="{{ $id }}" {{ (string)old('driver_id', $inspectionSchedule->driver_id ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
      @endforeach
    </select>
  </div>
</div>
<div class="row">
  <div class="col-md-3 form-group">
    <label class="required">Frequência (dias)</label>
    <input class="form-control" type="number" name="frequency_days" min="1" max="365" value="{{ old('frequency_days', $inspectionSchedule->frequency_days ?? 7) }}" required>
  </div>
  <div class="col-md-3 form-group">
    <label>Início</label>
    <input class="form-control" type="datetime-local" name="start_at" value="{{ old('start_at', isset($inspectionSchedule->start_at) && $inspectionSchedule->start_at ? $inspectionSchedule->start_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
  </div>
  <div class="col-md-3 form-group">
    <label>Próxima execução</label>
    <input class="form-control" type="datetime-local" name="next_run_at" value="{{ old('next_run_at', isset($inspectionSchedule->next_run_at) && $inspectionSchedule->next_run_at ? $inspectionSchedule->next_run_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
  </div>
  <div class="col-md-3 form-group">
    <label>Ativo</label>
    <select class="form-control" name="is_active">
      <option value="1" {{ (string)old('is_active', isset($inspectionSchedule) ? (int)$inspectionSchedule->is_active : 1) === '1' ? 'selected' : '' }}>Sim</option>
      <option value="0" {{ (string)old('is_active', isset($inspectionSchedule) ? (int)$inspectionSchedule->is_active : 1) === '0' ? 'selected' : '' }}>Não</option>
    </select>
  </div>
</div>
<div class="form-group">
  <label>Notas</label>
  <textarea class="form-control" name="notes">{{ old('notes', $inspectionSchedule->notes ?? '') }}</textarea>
</div>

@php
  $selectedConfig = [
    'documents' => old('routine_config.documents', $routineConfig['documents'] ?? []),
    'operational_checks' => old('routine_config.operational_checks', $routineConfig['operational_checks'] ?? []),
    'accessories' => old('routine_config.accessories', $routineConfig['accessories'] ?? []),
    'exterior_slots' => old('routine_config.exterior_slots', $routineConfig['exterior_slots'] ?? []),
    'interior_slots' => old('routine_config.interior_slots', $routineConfig['interior_slots'] ?? []),
  ];
@endphp

<div class="panel panel-info">
  <div class="panel-heading">
    <strong>Configuração da rotina na app do motorista</strong>
    <br>
    <small>Tudo começa selecionado. Retire só o que não interessa para este agendamento.</small>
  </div>
  <div class="panel-body">
    @foreach([
      'documents' => 'Etapa 3 - Documentação',
      'operational_checks' => 'Etapa 4 - Estado operacional',
      'accessories' => 'Etapa 5 - Acessórios e extras',
      'exterior_slots' => 'Etapa 6 - Fotografias exteriores',
      'interior_slots' => 'Etapa 7 - Fotografias interiores',
    ] as $sectionKey => $sectionTitle)
      <div class="routine-section panel panel-default">
        <div class="panel-heading clearfix">
          <span class="pull-left"><strong>{{ $sectionTitle }}</strong></span>
          <div class="btn-group btn-group-xs pull-right" role="group">
            <button type="button" class="btn btn-success js-section-toggle" data-target="{{ $sectionKey }}" data-check="1">Selecionar todos</button>
            <button type="button" class="btn btn-default js-section-toggle" data-target="{{ $sectionKey }}" data-check="0">Remover todos</button>
          </div>
        </div>
        <div class="panel-body">
          <div class="row">
            @foreach(($routineOptions[$sectionKey] ?? []) as $itemKey => $label)
              <div class="col-sm-6 col-md-4">
                <label class="checkbox-inline">
                  <input
                    type="checkbox"
                    class="js-section-input"
                    data-section="{{ $sectionKey }}"
                    name="routine_config[{{ $sectionKey }}][]"
                    value="{{ $itemKey }}"
                    {{ in_array($itemKey, $selectedConfig[$sectionKey] ?? [], true) ? 'checked' : '' }}
                  >
                  {{ $label }}
                </label>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('click', function (event) {
    var target = event.target;
    if (!target.classList.contains('js-section-toggle')) {
      return;
    }

    var section = target.getAttribute('data-target');
    var shouldCheck = target.getAttribute('data-check') === '1';
    var inputs = document.querySelectorAll('.js-section-input[data-section="' + section + '"]');
    inputs.forEach(function (input) {
      input.checked = shouldCheck;
    });
  });
</script>
@endpush

