<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CrmForm;

class RenderCrmForms
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Ignora backoffice
        if ($request->is('admin/*')) {
            return $response;
        }

        // Apenas HTML
        $ct = (string) $response->headers->get('Content-Type', '');
        if ($ct && stripos($ct, 'text/html') === false) {
            return $response;
        }

        $html = $response->getContent();
        if (!is_string($html) || $html === '') {
            return $response;
        }

        /**
         * Tokens suportados:
         *  - [form:slug]
         *  - [form="slug"] / [form:'slug'] / [form:"slug"]
         *  - render:crm-form slug="slug"
         *  - [form-fields:slug]
         *  - [form-fields="slug"] / [form-fields:'slug'] / [form-fields:"slug"]
         *  - render:crm-form-fields slug="slug"
         *
         * Usamos ~ como delimitador para evitar conflitos com '/' em comentários (modo 'x').
         */
        $pattern = '~
            \[form:(?P<slug1>[a-z0-9\-_]+)\] |
            \[form=(?:"|\')(?P<slug2>[a-z0-9\-_]+)(?:"|\')\] |
            render:crm-form\s+slug=(?:"|\')(?P<slug3>[a-z0-9\-_]+)(?:"|\') |
            \[form-fields:(?P<slug4>[a-z0-9\-_]+)\] |
            render:crm-form-fields\s+slug=(?:"|\')(?P<slug5>[a-z0-9\-_]+)(?:"|\') |
            \[form:(?:"|\')(?P<slug6>[a-z0-9\-_]+)(?:"|\')\] |
            \[form-fields:(?:"|\')(?P<slug7>[a-z0-9\-_]+)(?:"|\')\]
        ~ix';

        $html = preg_replace_callback($pattern, function ($m) {
            $slug = $m['slug1'] ?? $m['slug2'] ?? $m['slug3'] ?? $m['slug4'] ?? $m['slug5'] ?? $m['slug6'] ?? $m['slug7'] ?? null;
            if (!$slug) {
                return $this->dbg('CRM-FORM: slug não capturado');
            }
            // É fields-only quando veio de form-fields (slug4/slug5/slug7)
            $fieldsOnly = isset($m['slug4']) || isset($m['slug5']) || isset($m['slug7']);
            return $this->renderForm($slug, $fieldsOnly);
        }, $html);

        $response->setContent($html);
        return $response;
    }

    private function renderForm(string $slug, bool $fieldsOnly = false): string
    {
        $form = CrmForm::with(['fields' => fn($q) => $q->orderBy('position')->orderBy('id')])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$form) {
            return $this->dbg("CRM-FORM: '{$slug}' não encontrado ou não published");
        }

        try {
            $view = $fieldsOnly ? 'website.forms.fields' : 'website.forms.render';
            return view($view, ['form' => $form])->render();
        } catch (\Throwable $e) {
            return $this->dbg("CRM-FORM: '{$slug}' erro a renderizar view: " . $e->getMessage());
        }
    }

    private function dbg(string $msg): string
    {
        return "<!-- {$msg} -->";
    }
}
