@extends('layouts.admin')
@section('content')
<div class="content">
  @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
  <div class="panel panel-default">
    <div class="panel-heading">Agendamentos de Rotina</div>
    <div class="panel-body">
      <p>
        <a class="btn btn-success" href="{{ route('admin.inspection-schedules.create') }}">Novo agendamento</a>
        <a class="btn btn-default" href="{{ route('admin.inspections.index') }}">Ir para inspeções</a>
      </p>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Viatura</th>
            <th>Motorista</th>
            <th>Frequência (dias)</th>
            <th>Próxima execução</th>
            <th>Ativo</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          @foreach($schedules as $s)
          <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->vehicle->license_plate ?? '-' }}</td>
            <td>{{ $s->driver->name ?? '-' }}</td>
            <td>{{ $s->frequency_days }}</td>
            <td>{{ $s->next_run_at }}</td>
            <td>{!! $s->is_active ? '<span class="label label-success">Sim</span>' : '<span class="label label-default">Não</span>' !!}</td>
            <td>
              <a class="btn btn-xs btn-primary" href="{{ route('admin.inspection-schedules.show', $s->id) }}">Ver</a>
              <a class="btn btn-xs btn-info" href="{{ route('admin.inspection-schedules.edit', $s->id) }}">Editar</a>
              <form method="POST" action="{{ route('admin.inspection-schedules.run-now', $s->id) }}" style="display:inline">@csrf<button class="btn btn-xs btn-warning" type="submit">Gerar agora</button></form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {{ $schedules->links() }}
    </div>
  </div>
</div>
@endsection
