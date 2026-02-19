@extends('layouts.admin')
@section('content')
<div class="content">
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Próximas</div>
                <div class="panel-body"><h3>{{ $stats['upcoming_due'] }}</h3></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Em atraso</div>
                <div class="panel-body"><h3>{{ $stats['overdue'] }}</h3></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Aguardar revisão</div>
                <div class="panel-body"><h3>{{ $stats['awaiting_review'] }}</h3></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Ações rápidas</div>
        <div class="panel-body">
            <a href="{{ route('admin.inspections.templates') }}" class="btn btn-primary">Templates</a>
            <a href="{{ route('admin.inspections.schedules') }}" class="btn btn-info">Planos</a>
            <a href="{{ route('admin.inspections.assignments') }}" class="btn btn-success">Inspeções</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Últimas inspeções</div>
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>
                            <td>{{ $assignment->vehicle->license_plate ?? '-' }}</td>
                            <td>{{ $assignment->vehicle->driver->name ?? '-' }}</td>
                            <td>{{ $assignment->template->name ?? '-' }}</td>
                            <td>{{ $assignment->due_at }}</td>
                            <td><span class="label label-default">{{ $assignment->status }}</span></td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.inspections.show', $assignment->id) }}">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

