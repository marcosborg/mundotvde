<?php

namespace App\Services;

use App\Models\DocumentGenerated;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DocumentRenderService
{
    /**
     * Renderiza o corpo substituindo tags [driver_*], [company_*] e [doc_*].
     * Aceita também tags do tipo [driver_nome_do_campo_em_driver] e [company_nome_do_campo_em_doc_company].
     */
    public static function renderBody(DocumentGenerated $dg): array
    {
        $docMgmt = $dg->document_management()->with(['doc_company', 'signatures.media'])->firstOrFail();
        $driver  = $dg->driver;
        $company = $docMgmt->doc_company;

        $text = (string) $docMgmt->text;

        // 1) Map automático de atributos (driver_*, company_*):
        $replacements = [];

        if ($driver) {
            foreach ($driver->getAttributes() as $k => $v) {
                $replacements['[driver_' . $k . ']'] = self::formatValue($k, $v);
            }
        }

        if ($company) {
            foreach ($company->getAttributes() as $k => $v) {
                $replacements['[company_' . $k . ']'] = self::formatValue($k, $v);
            }
        }

        // Opcional: tags do próprio document management (ex: [doc_title])
        foreach ($docMgmt->getAttributes() as $k => $v) {
            $replacements['[doc_' . $k . ']'] = self::formatValue($k, $v);
        }

        // 2) Substituições directas:
        $body = strtr($text, $replacements);

        // 3) Cleanup: remove quaisquer tags não resolvidas (ficam vazias)
        $body = preg_replace('/\[[a-z_]+\]/i', '', $body);

        // 4) Normalização mínima de quebras
        $body = nl2br($body);

        return [
            'title'        => e($docMgmt->title ?? 'Documento'),
            'body_html'    => $body,
            'signatures'   => $docMgmt->signatures, // coleção com media
        ];
    }

    /**
     * Formata valores especiais (datas, etc.)
     */
    protected static function formatValue(string $key, $value): string
    {
        if (blank($value)) {
            return '';
        }

        // Datas típicas
        $dateish = [
            'date', 'birth', 'dob', 'expiry', 'expired', 'valid', 'valid_until',
            'citizen_card_expiry_date', 'drivers_certificate_expiry', 'vehicle_date',
            'created_at','updated_at'
        ];

        if (Str::endsWith($key, $dateish) || Str::contains($key, $dateish)) {
            try {
                return Carbon::parse($value)->format(config('panel.date_format', 'd/m/Y'));
            } catch (\Throwable $e) {
                // não formatar se não for data válida
            }
        }

        return (string) $value;
    }

    /**
     * Converte um ficheiro de imagem (assinatura) em data URI base64 para dompdf.
     */
    public static function imageToDataUri(?string $path): ?string
    {
        if (!$path || !is_file($path)) {
            return null;
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = @file_get_contents($path);
        if ($data === false) return null;

        $mime = 'image/' . strtolower($type);
        // dompdf lida bem com png/jpg/svg. Se for webp, forçamos image/png após conversão simples (opcional).
        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }
}
