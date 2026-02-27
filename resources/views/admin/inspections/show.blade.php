@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Detalhe inspeção #{{ $inspection->id }}</div>
                <div class="panel-body">
                    <p><strong>Tipo:</strong> {{ ucfirst($inspection->type) }}</p>
                    <p><strong>Viatura:</strong> {{ $inspection->vehicle->license_plate ?? '-' }}</p>
                    <p><strong>Condutor:</strong> {{ $inspection->driver->name ?? '-' }}</p>
                    <p><strong>Estado:</strong> {{ $inspection->status }}</p>
                    <p><strong>Local:</strong> {{ $inspection->location_text }} ({{ $inspection->location_lat }}, {{ $inspection->location_lng }})</p>

                    @if($inspection->report)
                    <p><strong>PDF:</strong> <a href="{{ asset('storage/' . $inspection->report->pdf_path) }}" target="_blank">Abrir relatório</a></p>
                    <p><strong>Hash:</strong> <code>{{ $inspection->report->pdf_hash }}</code></p>
                    @endif

                    <p>
                        <a class="btn btn-info" href="{{ route('admin.inspections.edit', $inspection->id) }}">Voltar ao wizard</a>
                        <a class="btn btn-default" href="{{ route('admin.inspections.index') }}">Voltar à lista</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
