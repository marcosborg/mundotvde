@extends('layouts.admin')
@section('content')
<div class="content">
  <div class="panel panel-default">
    <div class="panel-heading">Agendamento #{{ $inspectionSchedule->id }}</div>
    <div class="panel-body">
      <p><strong>Viatura:</strong> {{ $inspectionSchedule->vehicle->license_plate ?? '-' }}</p>
      <p><strong>Motorista:</strong> {{ $inspectionSchedule->driver->name ?? '-' }}</p>
      <p><strong>Frequência:</strong> {{ $inspectionSchedule->frequency_days }} dias</p>
      <p><strong>Próxima execução:</strong> {{ $inspectionSchedule->next_run_at }}</p>
      <p><strong>Última execução:</strong> {{ $inspectionSchedule->last_run_at }}</p>
      <p><strong>Ativo:</strong> {{ $inspectionSchedule->is_active ? 'Sim' : 'Não' }}</p>
      <p>
        <a class="btn btn-info" href="{{ route('admin.inspection-schedules.edit', $inspectionSchedule->id) }}">Editar</a>
        <a class="btn btn-default" href="{{ route('admin.inspection-schedules.index') }}">Voltar</a>
      </p>
    </div>
  </div>
</div>
@endsection
