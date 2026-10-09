<?php

namespace App\Http\Middleware;

use App\Models\DriverContractVersion;
use Closure;
class RequireDriverContract
{
    public function handle($request, Closure $next)
    {
        if ($request->routeIs('driver-contract.*', 'api.driver-contract.*', 'logout') || $request->is('api/login')) {
            return $next($request);
        }
        $contract = DriverContractVersion::pendingFor($request->user());
        if (!$contract) {
            return $next($request);
        }
        $message = $contract->version > 1 ? 'O seu contrato foi atualizado e precisa de ser assinado.' : 'O seu contrato precisa de ser assinado.';
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json(['code' => 'CONTRACT_ACCEPTANCE_REQUIRED', 'message' => $message, 'contract_id' => $contract->id, 'contract_url' => url('/api/app/contract')], 423);
        }
        return redirect()->route('driver-contract.show');
    }
}
