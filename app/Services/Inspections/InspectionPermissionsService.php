<?php

namespace App\Services\Inspections;

use App\Models\Inspection;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class InspectionPermissionsService
{
    public function ensureUserCanCreateType(User $user, string $type): void
    {
        $roles = $user->roles->pluck('title')->map(fn ($r) => mb_strtolower((string) $r))->toArray();

        $isManager = in_array('gestor', $roles, true) || in_array('admin', $roles, true);
        $isDriver = in_array('motorista', $roles, true);

        if (in_array($type, ['initial', 'handover', 'return', 'fleet_exit'], true) && !$isManager) {
            throw ValidationException::withMessages([
                'type' => 'Apenas Gestor/Admin pode criar inspecoes Inicial, Entrega, Recolha ou Saida da frota.',
            ]);
        }

        if ($type === 'routine' && !($isManager || $isDriver)) {
            throw ValidationException::withMessages([
                'type' => 'Apenas Gestor/Admin ou Motorista pode criar inspecoes de Rotina.',
            ]);
        }
    }

    public function ensureCanEdit(Inspection $inspection): void
    {
        if ($inspection->locked_at) {
            throw ValidationException::withMessages([
                'inspection' => 'Inspecao bloqueada apos assinatura/fecho. Edicao nao permitida.',
            ]);
        }
    }
}
