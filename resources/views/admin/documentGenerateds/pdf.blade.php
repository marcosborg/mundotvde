<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 15mm 18mm; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #111;
        }
        /* Título: maiúsculas, menor e centrado */
        h1 {
            font-size: 14px;
            margin: 0 0 10px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }
        /* Corpo sem justificação */
        .content {
            margin-top: 6px;
            text-align: left;
        }

        /* Assinaturas com tabela (compatível com dompdf) */
        .signatures   { margin-top: 28px; }
        .sig-table    { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .sig-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 10px; }

        /* Bloco superior com altura fixa para alinhar a linha em ambas as colunas */
        .sig-top   { height: 76px; } /* 70px de imagem + 6px de margem inferior visual */
        .sig-img   { max-height: 70px; display: block; margin: 0 auto 6px auto; }

        .sig-line  { border-top: 1px solid #333; height: 1px; margin: 6px auto 4px; width: 80%; }
        .sig-title { font-size: 12px; }
        .sig-extra { font-size: 10px; color: #444; margin-top: 4px; }

        .meta { font-size: 10px; color: #666; margin-top: 18px; }
    </style>
</head>
<body>

    {{-- Corpo já com substituições realizadas pelo serviço --}}
    <div class="content">{!! $body_html !!}</div>

    @php
        $sigs = $signatureImages ?? [];
        $rows = array_chunk($sigs, 2); // 2 assinaturas por linha
    @endphp

    @if(!empty($signatureImages) && count($signatureImages) > 0)
        <div class="signatures" style="margin-top: 30px;">
            @if(count($signatureImages) === 1)
                @php $sig = $signatureImages[0]; @endphp
                <div style="text-align:center; display:inline-block; width:100%;">
                    <div style="height:76px;">
                        @if(!empty($sig['uri']))
                            <img src="{{ $sig['uri'] }}" style="max-height:70px; margin-bottom:6px;" alt="assinatura">
                        @endif
                    </div>
                    <div style="border-top:1px solid #333; width:40%; margin:6px auto 4px;"></div>
                    <div style="font-size:12px;">{{ $sig['title'] ?? 'Assinatura' }}</div>
                    @if(!empty($sig['extra_html']))
                        <div style="font-size:10px; color:#444; margin-top:4px;">{!! $sig['extra_html'] !!}</div>
                    @endif
                </div>
            @elseif(count($signatureImages) >= 2)
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        @foreach($signatureImages as $sig)
                            <td style="width:50%; text-align:center; vertical-align:top; padding:0 10px;">
                                <div style="height:76px;">
                                    @if(!empty($sig['uri']))
                                        <img src="{{ $sig['uri'] }}" style="max-height:70px; margin-bottom:6px;" alt="assinatura">
                                    @endif
                                </div>
                                <div style="border-top:1px solid #333; width:80%; margin:6px auto 4px;"></div>
                                <div style="font-size:12px;">{{ $sig['title'] ?? 'Assinatura' }}</div>
                                @if(!empty($sig['extra_html']))
                                    <div style="font-size:10px; color:#444; margin-top:4px;">{!! $sig['extra_html'] !!}</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            @endif
        </div>
    @endif

</body>
</html>
