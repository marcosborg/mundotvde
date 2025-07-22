@extends('layouts.admin')

@section('content')
<div class="content">
    <div class="panel panel-default">
        <div class="panel-heading">
            Todas as atividades dos motoristas TVDE
        </div>
        <div class="panel-body">

            {{-- Botões --}}
            <div class="mb-3">
                <button id="select-all" class="btn btn-primary btn-sm">Selecionar Todos</button>
                <button id="deselect-all" class="btn btn-warning btn-sm">Desselecionar Todos</button>
                <button id="validate-selection" class="btn btn-success btn-sm">
                    <span id="validate-text">Validar Selecionados</span>
                    <span id="validate-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>

            <form id="drivers-form">
                <input type="hidden" name="week_id" id="week_id" value="{{ $tvde_week_id }}">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2"><input type="checkbox" disabled></th>
                            <th rowspan="2">Motorista</th>
                            <th colspan="3" class="text-center">Uber</th>
                            <th colspan="3" class="text-center">Bolt</th>
                        </tr>
                        <tr>
                            <th>Bruto</th>
                            <th>Líquido</th>
                            <th>Impostos</th>
                            <th>Bruto</th>
                            <th>Líquido</th>
                            <th>Impostos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = [
                                'uber' => ['gross' => 0, 'net' => 0, 'taxes' => 0],
                                'bolt' => ['gross' => 0, 'net' => 0, 'taxes' => 0],
                            ];
                        @endphp

                        @foreach ($drivers as $driver)
                            @php
                                $hasActivity = \App\Models\ActivityLaunch::where('driver_id', $driver->id)
                                    ->where('week_id', $tvde_week_id)
                                    ->exists();

                                // Somar totais
                                $total['uber']['gross'] += $driver->results['uber_activities']['earnings_one'];
                                $total['uber']['net'] += $driver->results['uber_activities']['earnings_two'];
                                $total['uber']['taxes'] += $driver->results['uber_activities']['earnings_three'];

                                $total['bolt']['gross'] += $driver->results['bolt_activities']['earnings_one'];
                                $total['bolt']['net'] += $driver->results['bolt_activities']['earnings_two'];
                                $total['bolt']['taxes'] += $driver->results['bolt_activities']['earnings_three'];
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox"
                                        name="drivers[]"
                                        value="{{ $driver->id }}"
                                        class="driver-checkbox"
                                        {{ $hasActivity ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    {{ $driver->name }}
                                    @if ($hasActivity)
                                        <span class="badge bg-secondary">Atividade criada</span>
                                    @endif
                                </td>

                                {{-- Uber --}}
                                <td>{{ number_format($driver->results['uber_activities']['earnings_one'], 2, ',', '.') }} €</td>
                                <td>{{ number_format($driver->results['uber_activities']['earnings_two'], 2, ',', '.') }} €</td>
                                <td>{{ number_format($driver->results['uber_activities']['earnings_three'], 2, ',', '.') }} €</td>

                                {{-- Bolt --}}
                                <td>{{ number_format($driver->results['bolt_activities']['earnings_one'], 2, ',', '.') }} €</td>
                                <td>{{ number_format($driver->results['bolt_activities']['earnings_two'], 2, ',', '.') }} €</td>
                                <td>{{ number_format($driver->results['bolt_activities']['earnings_three'], 2, ',', '.') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">Totais:</td>
                            <td>{{ number_format($total['uber']['gross'], 2, ',', '.') }} €</td>
                            <td>{{ number_format($total['uber']['net'], 2, ',', '.') }} €</td>
                            <td>{{ number_format($total['uber']['taxes'], 2, ',', '.') }} €</td>
                            <td>{{ number_format($total['bolt']['gross'], 2, ',', '.') }} €</td>
                            <td>{{ number_format($total['bolt']['net'], 2, ',', '.') }} €</td>
                            <td>{{ number_format($total['bolt']['taxes'], 2, ',', '.') }} €</td>
                        </tr>
                    </tfoot>
                </table>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('select-all').addEventListener('click', function () {
        document.querySelectorAll('.driver-checkbox:not(:disabled)').forEach(cb => cb.checked = true);
    });

    document.getElementById('deselect-all').addEventListener('click', function () {
        document.querySelectorAll('.driver-checkbox:not(:disabled)').forEach(cb => cb.checked = false);
    });

    document.getElementById('validate-selection').addEventListener('click', function () {
        const selected = Array.from(document.querySelectorAll('.driver-checkbox:checked'))
            .map(cb => cb.value);

        if (selected.length === 0) {
            alert('Nenhum motorista selecionado!');
            return;
        }

        // Mostrar spinner
        document.getElementById('validate-text').classList.add('d-none');
        document.getElementById('validate-spinner').classList.remove('d-none');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.create.selected.driver.activity') }}";

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        const weekInput = document.createElement('input');
        weekInput.type = 'hidden';
        weekInput.name = 'week_id';
        weekInput.value = '{{ $tvde_week_id }}';
        form.appendChild(weekInput);

        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'driver_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });
</script>
@endsection
