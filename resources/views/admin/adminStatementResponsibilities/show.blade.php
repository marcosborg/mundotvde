<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrato de Presta«ı«úo de Servi«ıos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        html {
            font-family: sans-serif;
            font-size: 14px;
            text-align: justify;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
        }

        @page {
            margin-top: 80px;
            margin-bottom: 0;
            margin-left: 100px;
            margin-right: 80px;
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
    <h4 class="text-center">DECLARA«Œ«üO DE UTILIZA«Œ«üO E TERMO DE RESPONSABILIDADE DE<br>UTILIZA«Œ«üO DE VIATURA</h4>
    <br>
    <br>
    @php
        $driver = $adminStatementResponsibility->driver ?? null;
        $contractNumber = $adminStatementResponsibility->contract_number ?? 'ó';
        $startYear = $driver && $driver->start_date ? \Carbon\Carbon::parse($driver->start_date)->year : 'ó';
    @endphp
    <p class="text-center"><strong>(Contrato de Presta«ı«úo de Servi«ıos n.∂ß {{ $contractNumber }} / {{ $startYear }})</strong></p>
    <br>
    <p>Opini«úo e Consenso Unipessoal Lda, com sede Largo do Rossio, n∂ß16 Loja A, 3515-138 Viseu, NIF: 515544930, neste
        ato representada pelo seu Gerente com poderes para o ato, Sr. Orlando Rodrigo Castro Saraiva, declara para os
        devidos efeitos que autoriza, {{ $driver->name ?? 'ó' }},
        com morada em {{ $driver->address ?? 'ó' }}, {{ $driver->zip ?? 'ó' }},
        {{ $driver->city ?? 'ó' }}, Portugal, com NIF {{ $driver->driver_vat ?? 'ó' }}, a conduzir e utilizar a viatura
        {{ $driver->brand ?? 'ó' }}, {{ $driver->model ?? 'ó' }}, {{
        $driver->license_plate ?? 'ó' }}, no «Ωmbito do contrato de presta«ı«úo de
        servi«ıos n.∂ß {{ optional($driver->admin_contract)->number ?? 'ó' }}/ 2023, assinado entre as
        partes.</p>
    <br>
    <p class="text-center"><strong>Clausula 1.∂¶</strong></p>
    <br>
    <p>A utiliza«ı«úo do ve«culo acima referido, destina-se «ßnica e exclusivamente para fins da atividade de TVDE no
        «Ωmbito da Lei 45/2018 de 10/08 e Declara«ı«úo de Retifica«ı«úo de 10/08, transferes e passeios tur«sticos em
        autom«¸vel conforme contrato de presta«ı«úo de servi«ıos n.{{
        optional($driver->admin_contract)->number ?? 'ó' }}/ 2023, assinado entre as partes bem como para
        seu uso pessoal quando n«úo estiver a ser utilizado no «Ωmbito da atividade profissional.</p>
    <br>
    <p class="text-center"><strong>Clausula 2.∂¶</strong></p>
    <br>
    <p>O condutor em cima identificada assume a total responsabilidade da viatura no que respeita a:</p>
    <ol>
        <li>Preju«zos que a referida viatura possa eventualmente sofrer ou provocar a terceiros durante a utiliza«ı«úo por
            esta.</li>
        <li>Pelas multas ou coimas que possam vir a ser aplicadas, na sequ«¶ncia da utiliza«ı«úo do ve«culo, por infra«ı«úo
            «ˇs disposi«ı«Êes do C«¸digo de Estrada ou «ˇ atividade.</li>
        <li>Cumprimento integral de todas as leis inerentes h«≠ atividade TVDE (EX: autocolantes TVDE, d«stico de n«úo
            fumador).</li>
    </ol>
    <br>
    <p class="text-center"><strong>Clausula 3.∂¶</strong></p>
    <br>
    <p>Por ser da responsabilidade do condutor todos os custos associados «ˇ utiliza«ı«úo e manuten«ı«úo do ve«culo, o mesmo
        n«úo integrar«≠ contrapartida financeira.</p>
    <br><br>
    @if ($adminStatementResponsibility->signed_at)
    <p>Assinado eletronicamente em, {{ $driver->city ?? 'ó' }}, {{
        $adminStatementResponsibility->signed_at ? \Carbon\Carbon::parse($adminStatementResponsibility->signed_at)->day : 'ó' }} de {{
        $adminStatementResponsibility->signed_at ? \Carbon\Carbon::parse($adminStatementResponsibility->signed_at)->formatLocalized('%B') : 'ó' }} de {{
        $adminStatementResponsibility->signed_at ? \Carbon\Carbon::parse($adminStatementResponsibility->signed_at)->year : 'ó' }}</p>
    <br>
    <table style="width: 100%">
        <thead>
            <tr>
                <th class="text-center">Empresa:</th>
                <th class="text-center">Condutor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">
                    <strong>Nome:</strong> Orlando Saraiva<br>
                    <strong>T«tulo: </strong> Gerente<br>
                    <strong><small><small>(Assinado eletronicamente)</small></small></strong>
                </td>
                <td class="text-center">
                    <strong>Nome:</strong> {{ $driver->name ?? 'ó' }}<br>
                    <strong>T«tulo: </strong> Condutor<br>
                    <strong><small><small>(Assinado eletronicamente)</small></small></strong>
                </td>
            </tr>
        </tbody>
    </table>
    @else
    <p style="text-align: left; font-size: 11px;"><strong>A DECLARA«Œ«üO AINDA N«üO FOI ASSINADA. O CONDUTOR DEVE ASSINAR
            EM "CONTRATOS/DECLARA«Œ«üO DE RESPONSABILIDADE".</strong></p>
    @endif
    <footer>
        Mundo TVDE ∂∏
        <?php echo date("Y");?>
    </footer>
</body>

</html>
