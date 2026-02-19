@extends('layouts.admin')
@section('content')
<div class="content">
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">Novo plano periódico</div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.inspections.schedules.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Viatura</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">Selecionar</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} - {{ $vehicle->driver->name ?? 'Sem motorista' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Template</label>
                        <select name="template_id" class="form-control" required>
                            <option value="">Selecionar</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Frequência (dias)</label>
                        <input type="number" name="frequency_days" class="form-control" value="7" min="1" max="365" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Hora limite</label>
                        <input type="time" name="due_time" class="form-control" value="09:00" required>
                    </div>
                    <div class="col-md-1 form-group">
                        <label>Tolerância (h)</label>
                        <input type="number" name="grace_hours" class="form-control" value="24" min="0" max="168" required>
                    </div>
                    <div class="col-md-1 form-group">
                        <label>Ativo</label>
                        <input type="checkbox" name="is_active" value="1" class="form-control" checked>
                    </div>
                </div>
                <div class="form-group">
                    <label>Reminder policy JSON (opcional)</label>
                    <textarea class="form-control" rows="3" name="reminder_policy_json" placeholder='{"hours_before": [24,2], "overdue_hours": [24]}'></textarea>
                </div>
                <button class="btn btn-success" type="submit">Criar plano</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Planos configurados</div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Viatura</th>
                        <th>Template</th>
                        <th>Freq.</th>
                        <th>Hora</th>
                        <th>Grace</th>
                        <th>Ativo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->vehicle->license_plate ?? '-' }}</td>
                            <td>{{ $schedule->template->name ?? '-' }}</td>
                            <td>{{ $schedule->frequency_days }} dias</td>
                            <td>{{ $schedule->due_time }}</td>
                            <td>{{ $schedule->grace_hours }}h</td>
                            <td>{{ $schedule->is_active ? 'Sim' : 'Não' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.inspections.schedules.destroy', $schedule->id) }}" onsubmit="return confirm('Remover plano?');" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-danger">Apagar</button>
                                </form>
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

