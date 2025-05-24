@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ trans('cruds.billingAnalysi.title') }}
                </div>
                <div class="panel-body">
                    <ul class="nav nav-pills">
                        @foreach ($tvde_years as $year)
                        <li role="presentation" {{ $tvde_year_id == $year->id ? 'class=active' : '' }}><a href="{{ route('admin.billing-analysis.index', ['tvde_year_id' => $year->id]) }}">{{ $year->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @if (!$tvde_year_id)
            <div class="alert alert-info">
                Seecione um ano para análise.
            </div>
            @else
            <div class="form-group">
                <label for="driverSearch">Pesquisar Motorista:</label>
                <input type="text" id="driverSearch" class="form-control" placeholder="Escreve o nome do motorista...">
            </div>
            @if ($tvde_year_id)
            <div class="mb-3">
                <a href="/admin/billing-analysis/export/{{ $tvde_year_id }}" class="btn btn-success">
                    Exportar para Excel
                </a>
            </div>
            @endif

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Motorista</th>
                        <th>Total TGA (€)</th>
                        <th>Total OC (€)</th>
                        <th>Distribuição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($drivers as $driver)
                    @php
                    $totalTGA = $driver->receipts->where('company', 'TGA')->sum('value');
                    $totalOC = $driver->receipts->where('company', 'OC')->sum('value');
                    $total = $totalTGA + $totalOC;

                    $tgaPercent = $total > 0 ? round(($totalTGA / $total) * 100) : 0;
                    $ocPercent = 100 - $tgaPercent;
                    @endphp
                    <tr class="driver-row">
                        <td>{{ $driver->name }}</td>
                        <td>{{ number_format($totalTGA, 2) }}</td>
                        <td>{{ number_format($totalOC, 2) }}</td>
                        <td>
                            <div style="display: flex; height: 25px; width: 100%; background: #eee; border-radius: 5px; overflow: hidden; font-size: 12px;">
                                <div style="width: {{ $ocPercent }}%; background-color: #a8e6cf; display: flex; align-items: center; justify-content: center; position: relative;">
                                    @if ($ocPercent > 5)
                                    <span>OC <span style="font-size: 10px;">{{ $ocPercent }}%</span></span>
                                    @endif
                                </div>
                                <div style="width: {{ $tgaPercent }}%; background-color: #d0e7ff; display: flex; align-items: center; justify-content: center; position: relative;">
                                    @if ($tgaPercent > 5)
                                    <span>TGA <span style="font-size: 10px;">{{ $tgaPercent }}%</span></span>
                                    @endif
                                </div>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            @endif
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.getElementById('driverSearch').addEventListener('input', function() {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll('.driver-row');

        rows.forEach(row => {
            let name = row.querySelector('td').textContent.toLowerCase();
            if (name.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

</script>

@endsection
