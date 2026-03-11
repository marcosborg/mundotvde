@extends('layouts.admin')
@section('content')
<div class="content">
  @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

  <div class="row" style="margin-bottom:12px;">
    <div class="col-md-3"><div class="small-box bg-blue"><div class="inner"><h3>{{ $summary['total'] }}</h3><p>Total agendamentos</p></div><div class="icon"><i class="fa fa-calendar"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-green"><div class="inner"><h3>{{ $summary['active'] }}</h3><p>Ativos</p></div><div class="icon"><i class="fa fa-check"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-yellow"><div class="inner"><h3>{{ $summary['due_now'] }}</h3><p>Para gerar agora</p></div><div class="icon"><i class="fa fa-clock-o"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-gray"><div class="inner"><h3>{{ $summary['inactive'] }}</h3><p>Inativos</p></div><div class="icon"><i class="fa fa-pause"></i></div></div></div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">Agendamentos de Rotina</div>
    <div class="panel-body">
      <div class="alert alert-info" style="margin-bottom:16px;">
        <strong>Regras de execucao das rotinas programadas</strong><br>
        1) Na app do motorista so aparecem inspecoes de <strong>Rotina</strong> geradas por agendamento.<br>
        2) A rotina programada so arranca na janela operacional <strong>Entrega -> Rotina -> Recolha</strong>.<br>
        3) Fora desta janela, o sistema ignora o agendamento (skip) e nao cria inspeção.<br>
        4) Se a rotina nao estiver programada, nao deve ser executada na app.
      </div>

      <p>
        @can('inspection_edit')
        <a class="btn btn-success" href="{{ route('admin.inspection-schedules.create') }}">Novo agendamento</a>
        @endcan
        <a class="btn btn-default" href="{{ route('admin.inspections.index') }}">Ir para inspeções</a>
      </p>

      <form method="GET" class="row" style="margin-bottom:16px;">
        <div class="col-md-3">
          <label>Viatura</label>
          <select name="vehicle_id" class="form-control select2">
            <option value="">Todas</option>
            @foreach($vehicles as $v)
            <option value="{{ $v->id }}" {{ (string)request('vehicle_id') === (string)$v->id ? 'selected' : '' }}>{{ $v->license_plate }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label>Motorista</label>
          <select name="driver_id" class="form-control select2">
            <option value="">Todos</option>
            @foreach($drivers as $d)
            <option value="{{ $d->id }}" {{ (string)request('driver_id') === (string)$d->id ? 'selected' : '' }}>{{ $d->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label>Estado</label>
          <select name="is_active" class="form-control">
            <option value="">Todos</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativo</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativo</option>
          </select>
        </div>
        <div class="col-md-2">
          <label>Próxima execução de</label>
          <input type="date" name="next_run_from" class="form-control" value="{{ request('next_run_from') }}">
        </div>
        <div class="col-md-2">
          <label>até</label>
          <input type="date" name="next_run_to" class="form-control" value="{{ request('next_run_to') }}">
        </div>
        <div class="col-md-12" style="margin-top:10px;">
          <button class="btn btn-primary" type="submit">Filtrar</button>
          <a class="btn btn-default" href="{{ route('admin.inspection-schedules.index') }}">Limpar</a>
        </div>
      </form>

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
          @forelse($schedules as $s)
          <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->vehicle->license_plate ?? '-' }}</td>
            <td>{{ $s->driver->name ?? '-' }}</td>
            <td>{{ $s->frequency_days }}</td>
            <td>{{ optional($s->next_run_at)->format('Y-m-d H:i') ?? '-' }}</td>
            <td>{!! $s->is_active ? '<span class="label label-success">Sim</span>' : '<span class="label label-default">Não</span>' !!}</td>
            <td>
              <a class="btn btn-xs btn-primary" href="{{ route('admin.inspection-schedules.show', $s->id) }}">Ver</a>
              <a class="btn btn-xs btn-info" href="{{ route('admin.inspection-schedules.edit', $s->id) }}">Editar</a>
              @if($s->is_active)
              <form method="POST" action="{{ route('admin.inspection-schedules.run-now', $s->id) }}" style="display:inline">@csrf<button class="btn btn-xs btn-warning" type="submit" onclick="return confirm('Gerar inspeção de rotina agora?');">Gerar agora</button></form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center">Sem resultados.</td></tr>
          @endforelse
        </tbody>
      </table>
      {{ $schedules->links() }}
    </div>
  </div>
</div>
@endsection
