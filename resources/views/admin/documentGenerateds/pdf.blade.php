<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 15mm 18mm 15mm 18mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.5; color: #111; }
        /* Título: maiúsculas, menor e centrado */
        h1   { font-size: 14px; margin: 0 0 10px; font-weight: 700; text-transform: uppercase; text-align: center; }
        /* Corpo sem justificação */
        .content { margin-top: 6px; text-align: left; }
        /* Assinaturas */
        .signatures { margin-top: 30px; }
        .sig-grid { display: flex; flex-wrap: wrap; gap: 18px; }
        .sig-cell { flex: 1 1 100%; text-align: center; }
        .sig-grid.two-cols .sig-cell { flex: 0 0 calc(50% - 9px); }
        .sig-img { height: 70px; margin-bottom: 8px; }
        .sig-title { font-size: 12px; border-top: 1px solid #333; padding-top: 6px; display: inline-block; min-width: 70%; }
        .meta { font-size: 10px; color: #666; margin-top: 18px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    <div class="content">{!! $body_html !!}</div>

    @php $sigCount = count($signatureImages ?? []); @endphp
    @if($sigCount)
        <div class="signatures">
            <div class="sig-grid {{ $sigCount >= 2 ? 'two-cols' : '' }}">
                @foreach($signatureImages as $sig)
                    <div class="sig-cell">
                        @if(!empty($sig['uri']))
                            <img class="sig-img" src="{{ $sig['uri'] }}" alt="assinatura">
                        @else
                            <div style="height:70px;"></div>
                        @endif
                        <div class="sig-title">{{ $sig['title'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="meta">
        Documento gerado em {{ now()->format('d/m/Y H:i') }} — #{{ $generated->id }}
    </div>
</body>
</html>
