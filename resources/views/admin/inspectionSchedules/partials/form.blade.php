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
