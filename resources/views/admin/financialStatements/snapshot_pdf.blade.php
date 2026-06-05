<!doctype html>
<html lang="pt">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Extrato</title>
    <style>
        html { font-family: sans-serif; font-size: 11px; }
        table { border-collapse: collapse; }
        th, td { padding: 8px; }
        @page { margin-top: 40px; margin-bottom: 0; margin-left: 40px; margin-right: 40px; }
        body { margin: 0; }
        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 50px; line-height: 35px; }
    </style>
</head>

<body>
    @php
        $weekStart = !empty($statement['week']['start_date']) ? \Carbon\Carbon::parse($statement['week']['start_date']) : null;
        $weekEnd = !empty($statement['week']['end_date']) ? \Carbon\Carbon::parse($statement['week']['end_date']) : null;
        $expenses = $statement['amounts']['expenses'] ?? [];
        $vehicle = $statement['vehicle'] ?? [];
        $initialKm = (int) ($vehicle['initial_kilometers'] ?? 0);
        $finalKm = (int) ($vehicle['final_kilometers'] ?? 0);
    @endphp
    <table width="100%">
        <tbody>
            <tr>
                <td width="50%" style="vertical-align: top; padding-bottom: 10px;">
                    <h3>Extrato de servicos prestados: <span style="font-weight: lighter">{{ $statement['driver']['operation'] ?? '' }}</span><br>
                        Motorista: <span style="font-weight: lighter">{{ $statement['driver']['name'] ?? '' }}</span></h3>
                    <p><strong>NIF :</strong> {{ $statement['driver']['payment_vat'] ?? '' }} <strong><br>
                            IBAN :</strong> {{ $statement['driver']['iban'] ?? '' }}
                    </p>
                    <strong>Periodo de {{ $weekStart ? $weekStart->isoFormat('D [de] MMMM [de] YYYY') : '' }} a {{ $weekEnd ? $weekEnd->isoFormat('D [de] MMMM [de] YYYY') : '' }}</strong>
                </td>
                <td width="50%" style="text-align: right; vertical-align: top;">
                    <img src="{{ $statement['company']['logo_url'] ?? 'https://mundotvde.pt/assets/website/img/logo.png' }}" width="250">
                    <p>{{ $statement['company']['address'] ?? 'Praceta da Tabaqueira 2A 1950-256 Lisboa' }}<br>
                        {{ $statement['company']['email'] ?? 'geral@mundotvde.pt' }} - {{ $statement['company']['website'] ?? 'www.mundotvde.pt' }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="100%">
        <thead>
            <tr style="background: #eeeeee;border-bottom: solid 1px #cccccc;">
                <th width="50%" style="text-align: left;">Resultado do periodo</th>
                <th width="50%"></th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Total de recebimentos liquido</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($statement['amounts']['net'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Total de impostos a descontar (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($statement['amounts']['taxes'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Aluguer (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['rent'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Gestao (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['management'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Seguro (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['insurance'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Combustivel (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['fuel'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Portagens (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['tolls'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Oficina (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['garage'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Caucao (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['management_fee'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Debitos (-)</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($expenses['others'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td width="50%" style="text-align: left;">Creditos</td>
                <td width="50%" style="text-align: right;">EUR {{ number_format($statement['amounts']['refund'] ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <th width="50%" style="text-align: left;">Resultado da Semana</th>
                <th width="50%" style="text-align: right;">EUR {{ number_format($statement['amounts']['total'] ?? 0, 2, '.', '') }}</th>
            </tr>
            <tr style="background: #eeeeee;">
                <th width="50%" style="text-align: left;">Saldo acumulado</th>
                <th width="50%" style="text-align: right;">EUR {{ number_format($statement['amounts']['balance'] ?? 0, 2, '.', '') }}</th>
            </tr>
        </tbody>
    </table>
    <table width="100%" style="margin-top: 30px;">
        <tbody>
            <tr>
                @foreach (($statement['amounts']['operators'] ?? []) as $key => $operator)
                <td style="padding: 0; border-left: solid {{ $key === 0 ? '0' : '5' }}px transparent;">
                    <table width="100%" style="background: #eeeeee; border: solid 1px #cccccc;">
                        <thead>
                            <tr style="border-bottom: solid 1px #cccccc;">
                                <th colspan="2">Rendimentos {{ $operator['name'] ?? '' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><th>Bruto</th><td>EUR {{ number_format($operator['gross'] ?? 0, 2, '.', '') }}</td></tr>
                            <tr><th>Liquido</th><td>EUR {{ number_format($operator['net'] ?? 0, 2, '.', '') }}</td></tr>
                            <tr><th>Impostos</th><td>EUR {{ number_format($operator['taxes'] ?? 0, 2, '.', '') }}</td></tr>
                        </tbody>
                    </table>
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <table width="100%" style="background: #cccccc; margin-top: 30px;">
        <thead>
            <tr style="border-bottom: solid 1px #aaaaaa;"><th colspan="9" style="text-align: left;">Viaturas</th></tr>
            <tr>
                <th>Matricula</th><th>Marca</th><th>Modelo</th><th>Data de inicio</th><th>Kms inicio</th><th>Data de fim</th><th>Kms fim</th><th>Total Kms</th><th>N.o de dias</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center;">
                <td>{{ $vehicle['license_plate'] ?? '' }}</td>
                <td>{{ $vehicle['brand'] ?? '' }}</td>
                <td>{{ $vehicle['model'] ?? '' }}</td>
                <td>{{ $weekStart ? $weekStart->format('d-m-Y') : '' }}</td>
                <td>{{ $vehicle['initial_kilometers'] ?? '' }}</td>
                <td>{{ $weekEnd ? $weekEnd->format('d-m-Y') : '' }}</td>
                <td>{{ $vehicle['final_kilometers'] ?? '' }}</td>
                <td>{{ $finalKm - $initialKm }}</td>
                <td>{{ $weekStart && $weekEnd ? $weekStart->diffInDays($weekEnd) + 1 : '' }}</td>
            </tr>
        </tbody>
    </table>
    <table width="100%" style="margin-top: 30px; background: #cccccc;">
        <thead>
            <tr><th colspan="4" style="text-align: left; border-bottom: solid 1px #aaaaaa;">Movimentos dos ultimos 60 dias</th></tr>
            <tr><th>Data</th><th>Estado do movimento</th><th>Valor</th><th>Saldo</th></tr>
        </thead>
        <tbody>
            @foreach (($statement['amounts']['movements_60_days'] ?? []) as $movement)
            <tr style="text-align: center;">
                <td>{{ !empty($movement['date']) ? \Carbon\Carbon::parse($movement['date'])->format('d-m-Y') : '' }}</td>
                <td>{{ $movement['status'] ?? '' }}</td>
                <td>EUR {{ number_format($movement['total'] ?? 0, 2, '.', '') }}</td>
                <td>EUR {{ number_format($movement['balance'] ?? 0, 2, '.', '') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>Mundo TVDE &copy; <?php echo date("Y");?></footer>
</body>

</html>
