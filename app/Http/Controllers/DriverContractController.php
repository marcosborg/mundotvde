<?php

namespace App\Http\Controllers;

use App\Models\{Driver, DriverContractVersion};
use App\Services\DriverOnboarding;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class DriverContractController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(config('driver_onboarding.enabled'), 404);
            return $next($request);
        });
    }

    private function current(Request $request)
    {
        return DriverContractVersion::where('user_id', $request->user()->id)->whereIn('driver_id', Driver::where('user_id', $request->user()->id)->select('id'))->current()->orderByRaw('accepted_at IS NOT NULL')->orderByDesc('id')->firstOrFail();
    }
    public function show(Request $request)
    {
        $contract = $this->current($request);
        if ($request->is('api/*')) {
            return response()->json(['id' => $contract->id, 'version' => $contract->version, 'title' => $contract->title, 'body' => $contract->body, 'content_hash' => $contract->content_hash, 'accepted_at' => $contract->accepted_at, 'acceptance_text' => 'Li e concordo com este contrato.']);
        }
        return view('contracts.accept', compact('contract'));
    }
    public function accept(Request $request, DriverOnboarding $service)
    {
        $data = $request->validate(['contract_id' => 'required|integer', 'content_hash' => 'required|string|size:64', 'agree' => 'required|accepted']);
        $service->accept(DriverContractVersion::findOrFail($data['contract_id']), $request->user(), $data['content_hash'], $request->ip(), (string) $request->userAgent());
        return $request->is('api/*') ? response()->json(['accepted' => true]) : redirect()->route('admin.home')->with('message', 'Contrato aceite.');
    }
    public function pdf(Request $request)
    {
        $contract = $this->current($request);
        return Pdf::loadView('contracts.document', compact('contract'))->download('contrato-v' . $contract->version . '.pdf');
    }
}
