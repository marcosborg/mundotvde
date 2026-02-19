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
        <div class="panel-heading">Criar inspeção manual</div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.inspections.assignments.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Viatura</label>
                        <select class="form-control" name="vehicle_id" required>
                            <option value="">Selecionar</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} - {{ $vehicle->driver->name ?? 'Sem motorista' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Template</label>
                        <select class="form-control" name="template_id" required>
                            <option value="">Selecionar</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Prazo</label>
                        <input class="form-control" type="datetime-local" name="due_at" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Tolerância (h)</label>
                        <input class="form-control" type="number" name="grace_hours" value="24" min="0" max="168">
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Criar inspeção</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Inspeções</div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Viatura</th>
                        <th>Motorista</th>
                        <th>Template</th>
                        <th>Prazo</th>
                        <th>Estado</th>
                        <th>Origem</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>
                            <td>{{ $assignment->vehicle->license_plate ?? '-' }}</td>
                            <td>{{ $assignment->vehicle->driver->name ?? '-' }}</td>
                            <td>{{ $assignment->template->name ?? '-' }}</td>
                            <td>{{ $assignment->due_at }}</td>
                            <td><span class="label label-default">{{ $assignment->status }}</span></td>
                            <td>{{ $assignment->generated_by }}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.inspections.show', $assignment->id) }}">Detalhe</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $assignments->links() }}
        </div>
    </div>
</div>
@endsection

