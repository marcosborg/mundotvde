<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 28mm 18mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.5; color: #111; }
        h1   { font-size: 14px; margin: 0 0 10px; font-weight: 700; text-transform: uppercase; text-align: center; }
        .content { margin-top: 6px; text-align: left; } /* sem justificação */

        /* Assinaturas usando tabela para dompdf */
        .signatures { margin-top: 28px; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 10px 0 10px; }
        .sig-img { height: 70px; margin-bottom: 6px; }
        .sig-line { border-top: 1px solid #333; height: 1px; margin: 6px auto 4px; width: 80%; }
        .sig-title { font-size: 12px; }
        .sig-extra { font-size: 10px; color: #444; margin-top: 4px; }
        .meta { font-size: 10px; color: $666; margin-top: 18px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    <div class="content">{!! $body_html !!}</div>

    @php
        $sigs = $signatureImages ?? [];
        $rows = array_chunk($sigs, 2); // 2 por linha
    @endphp

    @if(count($sigs))
        <div class="signatures">
            <table class="sig-table">
                @foreach($rows as $pair)
                    <tr>
                        @foreach($pair as $sig)
                            <td>
                                @if(!empty($sig['uri']))
                                    <img class="sig-img" src="{{ $sig['uri'] }}" alt="assinatura">
                                @else
                                    <div style="height:70px;"></div>
                                @endif

                                <div class="sig-line"></div>
                                <div class="sig-title">{{ $sig['title'] }}</div>

                                @if(!empty($sig['extra']))
                                    <div class="sig-extra">{{ $sig['extra'] }}</div>
                                @endif
                            </td>
                        @endforeach
                        @if(count($pair) === 1)
                            <td></td> {{-- célula vazia para manter 2 colunas quando par for ímpar --}}
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="meta">
        Documento gerado em {{ now()->format('d/m/Y H:i') }} — #{{ $generated->id }}
    </div>
</body>
</html>
