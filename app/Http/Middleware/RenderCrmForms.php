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

        // Ignora backoffice e respostas que não são HTML
        if ($request->is('admin/*')) {
            return $response;
        }
        $ct = $response->headers->get('Content-Type', '');
        if ($ct && stripos($ct, 'text/html') === false) {
            return $response;
        }

        $html = $response->getContent();

        // [form:slug] | [form="slug"] | render:crm-form slug="slug"
        $pattern = '/
            \[form:(?P<slug1>[a-z0-9\-_]+)\]                                     |
            \[form=(?:"|\')(?P<slug2>[a-z0-9\-_]+)(?:"|\')\]                      |
            render:crm-form\s+slug=(?:"|\')(?P<slug3>[a-z0-9\-_]+)(?:"|\')
        /ix';

        $html = preg_replace_callback($pattern, function ($m) {
            $slug = $m['slug1'] ?? $m['slug2'] ?? $m['slug3'] ?? null;
            if (!$slug) {
                return $this->dbg('CRM-FORM: slug não capturado');
            }
            return $this->renderForm($slug);
        }, $html);

        $response->setContent($html);
        return $response;
    }

    private function renderForm(string $slug): string
    {
        $form = CrmForm::with(['fields' => fn($q) => $q->orderBy('position')])
            ->where('slug', $slug)
            ->where('status', 'published')   // só publica
            ->first();

        if (!$form) {
            return $this->dbg("CRM-FORM: '{$slug}' não encontrado ou não published");
        }

        try {
            return view('website.forms.render', ['form' => $form])->render();
        } catch (\Throwable $e) {
            return $this->dbg("CRM-FORM: '{$slug}' erro a renderizar view: ".$e->getMessage());
        }
    }

    private function dbg(string $msg): string
    {
        // Mostra sempre, para poderes ver no View Source
        return "<!-- {$msg} -->";
    }
}
