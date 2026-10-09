<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Driver, DriverMessageTemplate, DriverContractVersion};
use App\Services\DriverOnboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Gate, DB, Mail};
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
class DriverOnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(config('driver_onboarding.enabled'), 404);
            return $next($request);
        });
    }

    public function templates()
    {
        Gate::authorize('admin_contract_edit');
        $templates = DriverMessageTemplate::all();
        return view('admin.driverOnboarding.templates', compact('templates'));
    }
    public function saveTemplate(Request $request, DriverMessageTemplate $template)
    {
        Gate::authorize('admin_contract_edit');
        $data = $request->validate(['title' => 'required|string|max:255', 'body' => 'nullable|string|max:100000', 'active' => 'sometimes|boolean']);
        $data['active'] = $request->boolean('active');
        if ($data['active'] && !trim($data['body'] ?? '')) {
            return back()->withErrors(['body' => 'Introduza o texto antes de ativar.'])->withInput();
        }
        $template->update($data);
        return back()->with('message', 'Template guardado. Os contratos já gerados mantêm o texto original.');
    }
    public function generate(Request $request, Driver $driver, DriverOnboarding $service)
    {
        Gate::authorize('driver_edit');
        Gate::authorize('admin_contract_create');
        $data = $request->validate(['type' => ['required', Rule::in(array_keys(DriverMessageTemplate::TYPES))]]);
        $service->generate($driver, $data['type'], $request->user()->id);
        return back()->with('message', 'Contrato gerado. O condutor terá de o aceitar no próximo acesso.');
    }
    public function welcome(Request $request, Driver $driver, DriverOnboarding $service)
    {
        Gate::authorize('driver_edit');
        $request->validate(['confirm' => 'required|accepted']);
        abort_unless($driver->user && filter_var($driver->user->email, FILTER_VALIDATE_EMAIL), 422, 'Associe uma conta com e-mail válido.');
        $template = DriverMessageTemplate::where('key', 'welcome')->where('active', true)->firstOrFail();
        $values = $service->values($driver);
        $body = $service->render($template->body, $values);
        $subject = $service->render($template->title, $values);
        Mail::raw($body, function ($message) use($driver, $subject) {
            $message->to($driver->user->email)->subject($subject);
        });
        DB::table('driver_welcome_deliveries')->insert(['driver_id' => $driver->id, 'sent_by' => $request->user()->id, 'recipient' => $driver->user->email, 'subject' => $subject, 'body' => $body, 'mailer' => config('mail.default'), 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('message', in_array(config('mail.default'), ['log', 'array']) ? 'E-mail simulado no ambiente local; não foi enviado ao condutor.' : 'E-mail de boas-vindas enviado.');
    }
    public function document(DriverContractVersion $contract)
    {
        Gate::authorize('admin_contract_show');
        return Pdf::loadView('contracts.document', compact('contract'))->stream('contrato-v' . $contract->version . '.pdf');
    }
}
