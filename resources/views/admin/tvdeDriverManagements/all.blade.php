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
                            <th rowspan="2" class="text-center" style="width:42px;">
                                <input type="checkbox" disabled>
                            </th>
                            <th rowspan="2">Motorista</th>
                            <th rowspan="2" class="text-center" style="width:140px;">Gestão</th>
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
                                // atividade existente (se houver) para esta semana/motorista
                                $existingActivity = \App\Models\ActivityLaunch::where('driver_id', $driver->id)
                                    ->where('week_id', $tvde_week_id)
                                    ->first();

                                $hasActivity = (bool) $existingActivity;
                                // PASSA A VALER O CAMPO 'management'
                                $existingManagement = $existingActivity?->management ?? 0;

                                // Somatórios
                                $total['uber']['gross'] += $driver->results['uber_activities']['earnings_one'];
                                $total['uber']['net']   += $driver->results['uber_activities']['earnings_two'];
                                $total['uber']['taxes'] += $driver->results['uber_activities']['earnings_three'];

                                $total['bolt']['gross'] += $driver->results['bolt_activities']['earnings_one'];
                                $total['bolt']['net']   += $driver->results['bolt_activities']['earnings_two'];
                                $total['bolt']['taxes'] += $driver->results['bolt_activities']['earnings_three'];
                            @endphp

                            <tr>
                                {{-- 1ª coluna: seleção do motorista --}}
                                <td class="text-center">
                                    <input type="checkbox"
                                           name="drivers[]"
                                           value="{{ $driver->id }}"
                                           class="driver-checkbox"
                                           {{ $hasActivity ? 'disabled' : '' }}>
                                </td>

                                {{-- Nome --}}
                                <td>
                                    {{ $driver->name }}
                                    @if ($hasActivity)
                                        <span class="badge bg-secondary">Atividade criada</span>
                                    @endif
                                </td>

                                {{-- Gestão (25€) --}}
                                <td class="text-center">
                                    <input type="checkbox"
                                           class="mgmt-checkbox"
                                           data-driver-id="{{ $driver->id }}"
                                           {{ $hasActivity ? 'disabled' : '' }}
                                           {{ (!$hasActivity || $existingManagement > 0) ? 'checked' : '' }}>
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
                            <td colspan="3" class="text-end">Totais:</td>
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
/** Selecionar / deselecionar motoristas (só os não desativados) */
document.getElementById('select-all').addEventListener('click', function () {
    document.querySelectorAll('.driver-checkbox:not(:disabled)').forEach(cb => cb.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function () {
    document.querySelectorAll('.driver-checkbox:not(:disabled)').forEach(cb => cb.checked = false);
});

/** Validar seleção -> submeter form com drivers + flags de gestão */
document.getElementById('validate-selection').addEventListener('click', function () {
    const selected = Array.from(document.querySelectorAll('.driver-checkbox:checked')).map(cb => cb.value);

    if (selected.length === 0) {
        alert('Nenhum motorista selecionado!');
        return;
    }

    // Mostrar spinner
    document.getElementById('validate-text').classList.add('d-none');
    document.getElementById('validate-spinner').classList.remove('d-none');

    // Construir form POST dinâmico
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('admin.create.selected.driver.activity') }}";

    // CSRF
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '{{ csrf_token() }}';
    form.appendChild(token);

    // Semana
    const weekInput = document.createElement('input');
    weekInput.type = 'hidden';
    weekInput.name = 'week_id';
    weekInput.value = '{{ $tvde_week_id }}';
    form.appendChild(weekInput);

    // Mapear gestão por motorista (checked => 1; else 0)
    const mgmtByDriver = {};
    document.querySelectorAll('.mgmt-checkbox').forEach(chk => {
        const driverId = chk.getAttribute('data-driver-id');
        mgmtByDriver[driverId] = chk.checked ? 1 : 0;
    });

    // Anexar motoristas selecionados e respetiva flag de gestão
    selected.forEach(id => {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'driver_ids[]';
        idInput.value = id;
        form.appendChild(idInput);

        const mgmtInput = document.createElement('input');
        mgmtInput.type = 'hidden';
        // AGORA ENVIA PARA 'management[...]'
        mgmtInput.name = `management[${id}]`;
        mgmtInput.value = mgmtByDriver[id] ? 1 : 0;
        form.appendChild(mgmtInput);
    });

    document.body.appendChild(form);
    form.submit();
});
</script>
@endsection
