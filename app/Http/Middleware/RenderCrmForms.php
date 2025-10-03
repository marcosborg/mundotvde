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

        if (
            $request->is('admin/*') ||
            stripos($response->headers->get('Content-Type', ''), 'text/html') === false
        ) {
            return $response;
        }

        $content = $response->getContent();

        // [form:slug]
        $content = preg_replace_callback('/\[form:([a-z0-9\-\_]+)\]/i', function ($m) {
            $slug = $m[1];
            $form = CrmForm::with(['fields' => function($q){ $q->orderBy('position'); }])
                ->where('slug', $slug)
                ->where('status', 'published')
                ->first();

            if (!$form) return ''; // silencioso

            return view('website.forms.render', compact('form'))->render();
        }, $content);

        $response->setContent($content);
        return $response;
    }
}
