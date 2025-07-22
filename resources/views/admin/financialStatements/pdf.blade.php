<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Extrato</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
        }

        @page {
            margin-top: 40px;
            margin-bottom: 0;
            margin-left: 40px;
            margin-right: 40px;
        }

        body {
            margin: 0;
        }

        footer {
            position: fixed;
            bottom: -0px;
            left: 0px;
            right: 0px;
            height: 50px;
            line-height: 35px;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tbody>
            <tr>
                <td width="50%" style="vertical-align: top; padding-bottom: 10px;">
                    <h3>Extrato de serviços prestados: <span style="font-weight: lighter">{{
                            $activityLaunch->driver->operation->name }}</span><br>
                        Motorista: <span style="font-weight: lighter">{{ $activityLaunch->driver->name }}</span></h3>
                    <p><strong>NIF :</strong> {{ $activityLaunch->driver->payment_vat }} <strong><br>
                            IBAN :</strong> {{ $activityLaunch->driver->iban }}
                    </p>
                    <strong>Período de {{ \Carbon\Carbon::parse($activityLaunch->week->start_date)->isoFormat('D [de]
                        MMMM
                        [de] YYYY') }} a {{ \Carbon\Carbon::parse($activityLaunch->week->end_date)->isoFormat('D [de]
                        MMMM
                        [de] YYYY') }}</strong>
                </td>
                <td width="50%" style="text-align: right; vertical-align: top;">
                    <img src="https://mundotvde.pt/assets/website/img/logo.png" width="250">
                    <p>Praceta da Tabaqueira 2A 1950-256 Lisboa<br>
                        geral@mundotvde.pt - www.mundotvde.pt</p>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="100%">
        <thead>
            <tr style="background: #eeeeee;border-bottom: solid 1px #cccccc;">
                <th width="50%" style="text-align: left;">Resultado do período</th>
                <th width="50%"></th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Total de recebimentos liquido</td>
                <td widtd="50%" style="text-align: right;">€ {{ number_format($activityLaunch->net, '2', '.') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Total de impostos a descontar (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ number_format($activityLaunch->taxes, 2, '.') }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Aluguer (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->rent }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Gestão (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->management }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Seguro (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->insurance }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Combustivel (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->fuel }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Portagens (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->tolls }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Oficina (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->garage }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Débitos (-)</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->others }}</td>
            </tr>
            <tr style="border-bottom: solid 1px #cccccc;">
                <td widtd="50%" style="text-align: left;">Créditos</td>
                <td widtd="50%" style="text-align: right;">€ {{ $activityLaunch->refund }}</td>

            <tr style="background: #eeeeee;">
                <th width="50%" style="text-align: left;">Resultado da Semana</th>
                <th widtd="50%" style="text-align: right;">€ {{ number_format($activityLaunch->total, 2, '.', '') }}
                </th>
            </tr>
            <tr style="background: #eeeeee;">
                <th width="50%" style="text-align: left;">Saldo acumulado</th>
                <th widtd="50%" style="text-align: right;">€ {{ number_format($balance, 2, '.', '') }}
                </th>
            </tr>
        </tbody>
    </table>
    <table width="100%" style="margin-top: 30px;">
        <tbody>
            <tr>
                @php
                $count = 0;
                @endphp
                @foreach ($activityLaunch->activityPerOperators as $activityPerOperator)
                <td style="padding: 0; border-left: solid {{ $count++ == 0 ? '0' : '5' }}px transparent;">
                    <table width="100%" style="background: #eeeeee; border: solid 1px #cccccc;">
                        <thead>
                            <tr style="border-bottom: solid 1px #cccccc;">
                                <th colspan="2">Rendimentos {{ $activityPerOperator->tvde_operator->name }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Bruto</th>
                                <td>€ {{ $activityPerOperator->gross }}</td>
                            </tr>
                            <tr>
                                <th>Líquido</th>
                                <td>€ {{ $activityPerOperator->net }}</td>
                            </tr>
                            <tr>
                                <th>Impostos</th>
                                <td>€ {{ $activityPerOperator->taxes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <table width="100%" style="background: #cccccc; margin-top: 30px;">
        <thead>
            <tr style="border-bottom: solid 1px #aaaaaa;">
                <th colspan="9" style="text-align: left;">Viaturas</th>
            </tr>
            <tr>
                <th>Matrícula</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Data de início</th>
                <th>Kms início</th>
                <th>Data de fim</th>
                <th>Kms fim</th>
                <th>Total Kms</th>
                <th>N.º de dias</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center;">
                <td>{{ $activityLaunch->driver->license_plate }}</td>
                <td>{{ $activityLaunch->driver->brand }}</td>
                <td>{{ $activityLaunch->driver->model }}</td>
                <td>{{ \Carbon\Carbon::parse($activityLaunch->week->start_date)->format('d-m-Y') }}</td>
                <td>{{ $activityLaunch->initial_kilometers }}</td>
                <td>{{ \Carbon\Carbon::parse($activityLaunch->week->end_date)->format('d-m-Y') }}</td>
                <td>{{ $activityLaunch->final_kilometers }}</td>
                <td>{{ $activityLaunch->final_kilometers - $activityLaunch->initial_kilometers }}</td>
                <td>{{
                    \Carbon\Carbon::parse($activityLaunch->week->start_date)->diffInDays(\Carbon\Carbon::parse($activityLaunch->week->end_date))
                    + 1
                    }}</td>
            </tr>
        </tbody>
    </table>
    <table width="100%" style="margin-top: 30px; background: #cccccc;">
        <thead>
            <tr>
                <th colspan="4" style="text-align: left; border-bottom: solid 1px #aaaaaa;">Movimentos dos últimos 60
                    dias</th>
            </tr>
            <tr>
                <th>Data</th>
                <th>Estado do movimento</th>
                <th>Valor</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
            $budget = 0;
            @endphp
            @foreach ($activityLaunches60 as $activityLaunch60)
            <tr style="text-align: center;">
                <td>{{ \Carbon\Carbon::parse($activityLaunch60->created_at)->format('d-m-Y') }}</td>
                <td>
                    @if ($activityLaunch60->paid == 0)
                    @php
                    $budget += $activityLaunch60->total;
                    @endphp
                    Em saldo
                    @else
                    Pago
                    @endif
                </td>
                <td>€ {{ $activityLaunch60->total }}</td>
                <td>€ {{ $budget }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        Mundo TVDE ©
        <?php echo date("Y");?>
    </footer>

</body>

</html>
