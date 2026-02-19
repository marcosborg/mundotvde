@extends('layouts.admin')
@section('content')
<div class="content">
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">Inspeção #{{ $assignment->id }}</div>
        <div class="panel-body">
            <p><strong>Viatura:</strong> {{ $assignment->vehicle->license_plate ?? '-' }}</p>
            <p><strong>Motorista:</strong> {{ $assignment->vehicle->driver->name ?? '-' }}</p>
            <p><strong>Template:</strong> {{ $assignment->template->name ?? '-' }}</p>
            <p><strong>Estado:</strong> {{ $assignment->status }}</p>
            <p><strong>Prazo:</strong> {{ $assignment->due_at }}</p>
            <a class="btn btn-default" href="{{ route('admin.inspections.evidence.zip', $assignment->id) }}">Download evidência (ZIP)</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Revisão</div>
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.inspections.review', $assignment->id) }}">
                @csrf
                <div class="form-group">
                    <label>Ação</label>
                    <select name="action" class="form-control" required>
                        <option value="approve">Aprovar</option>
                        <option value="reject">Rejeitar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notas</label>
                    <textarea class="form-control" name="notes" rows="3"></textarea>
                </div>
                <button class="btn btn-primary">Guardar revisão</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Fotos</div>
        <div class="panel-body">
            @if($assignment->submission && $assignment->submission->photos->count())
                <div class="row">
                    @foreach($assignment->submission->photos as $photo)
                        <div class="col-md-3" style="margin-bottom:15px">
                            <div class="thumbnail">
                                <div class="caption">
                                    <strong>{{ $photo->angle }}</strong><br>
                                    <a class="btn btn-xs btn-default" href="{{ route('admin.inspections.photo.download', $photo->id) }}">Download</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>Sem fotos submetidas.</p>
            @endif
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Defeitos</div>
        <div class="panel-body">
            @if($assignment->submission && $assignment->submission->defects->count())
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Severidade</th>
                            <th>Estado</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($assignment->submission->defects as $defect)
                        <tr>
                            <td>{{ $defect->title }}</td>
                            <td>{{ $defect->severity }}</td>
                            <td>{{ $defect->status }}</td>
                            <td>{{ $defect->description }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>Sem defeitos reportados.</p>
            @endif
        </div>
    </div>
</div>
@endsection

