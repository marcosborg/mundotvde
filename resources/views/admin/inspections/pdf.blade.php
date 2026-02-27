<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body{font-family: DejaVu Sans, sans-serif; font-size:12px}
        h1,h2{margin:0 0 8px}
        .section{margin:10px 0;padding:8px;border:1px solid #ddd}
        table{width:100%;border-collapse:collapse}
        td,th{border:1px solid #ddd;padding:4px}
    </style>
</head>
<body>
    <h1>Relatório de Inspeção #{{ $inspection->id }}</h1>
    <div class="section">
        <strong>Tipo:</strong> {{ ucfirst($inspection->type) }}<br>
        <strong>Data/Hora:</strong> {{ optional($inspection->completed_at)->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s') }}<br>
        <strong>Viatura:</strong> {{ $inspection->vehicle->license_plate ?? '-' }} - {{ $inspection->vehicle->vehicle_brand->name ?? '' }} {{ $inspection->vehicle->vehicle_model->name ?? '' }}<br>
        <strong>Condutor:</strong> {{ $inspection->driver->name ?? '-' }}<br>
        <strong>Local:</strong> {{ $inspection->location_text ?? '-' }} ({{ $inspection->location_lat }}, {{ $inspection->location_lng }})
    </div>

    <div class="section">
        <h2>Fotos</h2>
        <table>
            <thead><tr><th>Categoria</th><th>Slot</th><th>Ficheiro</th></tr></thead>
            <tbody>
                @foreach($inspection->photos as $photo)
                <tr><td>{{ $photo->category }}</td><td>{{ $photo->slot }}</td><td>{{ $photo->path }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Danos</h2>
        <table>
            <thead><tr><th>Scope</th><th>Local</th><th>Peça</th><th>Tipo</th><th>Estado</th></tr></thead>
            <tbody>
                @foreach($inspection->damages as $damage)
                <tr>
                    <td>{{ $damage->scope }}</td>
                    <td>{{ $damage->location }}</td>
                    <td>{{ $damage->part }}</td>
                    <td>{{ $damage->damage_type }}</td>
                    <td>{{ $damage->is_resolved ? 'Resolvido' : 'Ativo' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Assinaturas</h2>
        @foreach($inspection->signatures as $signature)
            <p>{{ strtoupper($signature->role) }} - {{ $signature->signed_by_name }} - {{ $signature->signed_at }}</p>
        @endforeach
    </div>

    <div class="section">
        <h2>Pendências</h2>
        @if(!empty($missingItems))
            <ul>
                @foreach($missingItems as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <p>Sem pendências.</p>
        @endif
    </div>
</body>
</html>
