<?php
// app/Services/DocumentRenderService.php
namespace App\Services;

use App\Models\DocumentGenerated;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DocumentRenderService
{
    public static function renderBody(DocumentGenerated $dg): array
    {
        $docMgmt = $dg->document_management()->with(['doc_company', 'signatures.media'])->firstOrFail();
        $driver  = $dg->driver;
        $company = $docMgmt->doc_company;

        $replacements = self::buildReplacements($docMgmt, $driver, $company);

        $text = (string) $docMgmt->text;
        $body = strtr($text, $replacements);
        $body = preg_replace('/\[[a-z_]+\]/i', '', $body);
        $body = nl2br($body);

        return [
            'title'        => e($docMgmt->title ?? 'Documento'),
            'body_html'    => $body,
            'signatures'   => $docMgmt->signatures,
            'replacements' => $replacements, // <- novo
        ];
    }

    /** Renderiza qualquer texto com as mesmas tags. */
    public static function renderText(array $replacements, string $text, bool $nl2br = true): string
    {
        $out = strtr($text, $replacements);
        $out = preg_replace('/\[[a-z_]+\]/i', '', $out);
        return $nl2br ? nl2br($out) : $out;
    }

    public static function buildReplacements($docMgmt, $driver, $company): array
    {
        $r = [];

        if ($driver) {
            foreach ($driver->getAttributes() as $k => $v) {
                $r['[driver_' . $k . ']'] = self::formatValue($k, $v);
            }
        }
        if ($company) {
            foreach ($company->getAttributes() as $k => $v) {
                $r['[company_' . $k . ']'] = self::formatValue($k, $v);
            }
        }
        foreach ($docMgmt->getAttributes() as $k => $v) {
            $r['[doc_' . $k . ']'] = self::formatValue($k, $v);
        }

        // Extras úteis
        $r['[date]']     = now()->format(config('panel.date_format', 'd/m/Y'));
        $r['[now]']      = now()->format('d/m/Y H:i');
        $r['[doc_id]']   = (string)($docMgmt->id ?? '');

        return $r;
    }

    protected static function formatValue(string $key, $value): string
    {
        if (blank($value)) return '';
        $dateish = ['date','birth','dob','expiry','valid','valid_until','citizen_card_expiry_date','drivers_certificate_expiry','vehicle_date','created_at','updated_at'];
        if (Str::endsWith($key, $dateish) || Str::contains($key, $dateish)) {
            try { return Carbon::parse($value)->format(config('panel.date_format', 'd/m/Y')); } catch (\Throwable $e) {}
        }
        return (string)$value;
    }

    public static function imageToDataUri(?string $path): ?string
    {
        if (!$path || !is_file($path)) return null;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = @file_get_contents($path);
        if ($data === false) return null;
        return 'data:image/' . strtolower($type) . ';base64,' . base64_encode($data);
    }
}
