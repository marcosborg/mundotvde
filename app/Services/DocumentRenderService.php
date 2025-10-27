<?php
// app/Services/DocumentRenderService.php

namespace App\Services;

use App\Models\DocumentGenerated;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DocumentRenderService
{
    /**
     * Renderiza o corpo do documento com substituição de tags.
     */
    public static function renderBody(DocumentGenerated $dg): array
    {
        $docMgmt = $dg->document_management()->with(['doc_company', 'signatures.media'])->firstOrFail();
        $driver  = $dg->driver;
        $company = $docMgmt->doc_company;

        // Mapa de substituições (inclui [date] do DocumentGenerated)
        $replacements = self::buildReplacements($dg, $docMgmt, $driver, $company);

        // Texto do DocumentManagement com tags substituídas
        $text = (string) $docMgmt->text;
        $body = self::replaceTags($text, $replacements, true);

        return [
            'title'        => e($docMgmt->title ?? 'Documento'),
            'body_html'    => $body,
            'signatures'   => $docMgmt->signatures,
            'replacements' => $replacements,
        ];
    }

    /**
     * Renderiza qualquer texto (ex.: other_fields das assinaturas) com as mesmas tags.
     */
    public static function renderText(array $replacements, string $text, bool $nl2br = true): string
    {
        return self::replaceTags($text, $replacements, $nl2br);
    }

    /**
     * Constrói o mapa base de substituições.
     * Suporta chaves tipo driver_nome, company_nipc, doc_title, date, now, etc.
     */
    public static function buildReplacements(
        DocumentGenerated $dg,
        $docMgmt,
        $driver,
        $company
    ): array {
        $r = [];

        // DRIVER
        if ($driver) {
            foreach ($driver->getAttributes() as $k => $v) {
                $norm = Str::lower($k); // ex.: driver_driver_certificate
                // evita dupla prefixação
                $key  = Str::startsWith($norm, 'driver_') ? $norm : 'driver_' . $norm;
                $r[$key] = self::formatValue($k, $v);

                // alias: driver_driver_* -> driver_*
                if (Str::startsWith($key, 'driver_driver_')) {
                    $alias = 'driver_' . Str::replaceFirst('driver_', '', $key); // remove um 'driver_'
                    $r[$alias] = $r[$key];
                }
            }
        }

        // OWNER (também é Driver)
        if (method_exists($dg, 'owner') && $dg->owner) {
            $owner = $dg->owner;
            foreach ($owner->getAttributes() as $k => $v) {
                $norm = Str::lower($k); // ex.: owner_driver_certificate OU driver_driver_certificate
                // se já vier com owner_, mantém; se vier com driver_ (porque reaproveitaram campos), prefixa com owner_
                if (Str::startsWith($norm, 'owner_')) {
                    $key = $norm;
                } elseif (Str::startsWith($norm, 'driver_')) {
                    $key = 'owner_' . Str::replaceFirst('driver_', '', $norm);
                } else {
                    $key = 'owner_' . $norm;
                }
                $r[$key] = self::formatValue($k, $v);

                // alias: owner_driver_* -> owner_*
                if (Str::startsWith($key, 'owner_driver_')) {
                    $alias = 'owner_' . Str::replaceFirst('owner_driver_', '', $key);
                    $r[$alias] = $r[$key];
                }
            }
        }

        // COMPANY
        if ($company) {
            foreach ($company->getAttributes() as $k => $v) {
                $norm = Str::lower($k);
                $key  = Str::startsWith($norm, 'company_') ? $norm : 'company_' . $norm;
                $r[$key] = self::formatValue($k, $v);
            }
        }

        // DOC MANAGEMENT
        if ($docMgmt) {
            foreach ($docMgmt->getAttributes() as $k => $v) {
                $norm = Str::lower($k);
                $key  = Str::startsWith($norm, 'doc_') ? $norm : 'doc_' . $norm;
                $r[$key] = self::formatValue($k, $v);
            }
        }

        // [date] -> data do DocumentGenerated (raw)
        try {
            $rawDate = $dg->getRawOriginal('date');
            $r['date'] = $rawDate
                ? \Carbon\Carbon::parse($rawDate)->format(config('panel.date_format', 'd/m/Y'))
                : '';
        } catch (\Throwable $e) {
            $r['date'] = '';
        }

        // Extras
        $r['now']          = now()->format('d/m/Y H:i');
        $r['doc_id']       = (string)($docMgmt->id ?? '');
        $r['generated_id'] = (string)($dg->id ?? '');

        return $r;
    }

    /**
     * Substitui tags dentro de um texto.
     * Aceita: [driver_name], [Driver Name], [company_nipc], [Date], etc. (case-insensitive).
     * Se a tag não tiver prefixo (ex.: [name]) tenta driver_name, company_name, doc_name por esta ordem.
     */
    protected static function replaceTags(string $text, array $replacements, bool $nl2br): string
    {
        $callback = function ($m) use ($replacements) {
            // conteúdo dentro de []
            $raw = $m[1];

            // normaliza entidades HTML e espaços
            $norm = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $norm = preg_replace('/\s+/u', '_', trim($norm)); // "Driver Name" -> "Driver_Name"
            $key  = Str::lower($norm);                        // case-insensitive

            // 1) tentativa direta (ex.: driver_name, company_nipc, date, now)
            if (array_key_exists($key, $replacements)) {
                return (string) $replacements[$key];
            }

            // 2) se não tiver prefixo, tenta driver_, company_, doc_ por esta ordem
            if (!Str::startsWith($key, ['driver_', 'company_', 'doc_'])) {
                foreach (['driver_', 'company_', 'doc_'] as $prefix) {
                    $try = $prefix . $key;
                    if (array_key_exists($try, $replacements)) {
                        return (string) $replacements[$try];
                    }
                }
            }

            // 3) não encontrado -> vazio (em alternativa, devolve $m[0] para debugging)
            return '';
        };

        // Aceita letras, acentos, underscores e espaços dentro dos colchetes
        $out = preg_replace_callback('/\[([\p{L}\p{M}\s_]+)\]/u', $callback, $text);

        // Remove quaisquer tags não resolvidas que tenham ficado
        $out = preg_replace('/\[[^\]]+\]/', '', $out);

        return $nl2br ? nl2br($out) : $out;
    }

    /**
     * Formata valores "com cara de data" segundo o config.
     */
    protected static function formatValue(string $key, $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $dateish = [
            'date',
            'birth',
            'dob',
            'expiry',
            'valid',
            'valid_until',
            'citizen_card_expiry_date',
            'drivers_certificate_expiry',
            'vehicle_date',
            'created_at',
            'updated_at'
        ];

        if (Str::endsWith($key, $dateish) || Str::contains($key, $dateish)) {
            try {
                return Carbon::parse($value)->format(config('panel.date_format', 'd/m/Y'));
            } catch (\Throwable $e) {
                // se não for uma data válida, devolve tal como está
            }
        }

        return (string) $value;
    }

    /**
     * Converte imagem local para data URI (compatível com dompdf).
     */
    public static function imageToDataUri(?string $path): ?string
    {
        if (!$path || !is_file($path)) {
            return null;
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = @file_get_contents($path);
        if ($data === false) {
            return null;
        }
        return 'data:image/' . strtolower($type) . ';base64,' . base64_encode($data);
    }
}
