<?php

namespace App\Services\Inspections;

use App\Models\Inspection;
use App\Models\InspectionDamage;
use Illuminate\Validation\ValidationException;

class InspectionSequenceService
{
    public function resolvePreviousInspection(int $vehicleId): ?Inspection
    {
        return Inspection::query()
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', ['signed', 'closed'])
            ->latest('completed_at')
            ->latest('id')
            ->first();
    }

    public function validateCreationSequence(string $type, int $vehicleId): ?Inspection
    {
        $previous = $this->resolvePreviousInspection($vehicleId);

        // Novo ciclo so abre com Initial e apenas depois de uma Entrega.
        if ($type === 'initial') {
            if ($previous && $previous->type !== 'handover') {
                throw ValidationException::withMessages([
                    'type' => 'Ja existe um ciclo ativo. A Inspecao Inicial so pode abrir novo ciclo apos uma Entrega.',
                ]);
            }

            return $previous;
        }

        if (!$previous) {
            throw ValidationException::withMessages([
                'vehicle_id' => 'Esta viatura precisa de Inspecao Inicial antes deste tipo.',
            ]);
        }

        // Entrega fecha o ciclo atual.
        if ($type === 'handover' && !in_array($previous->type, ['initial', 'routine', 'return'], true)) {
            throw ValidationException::withMessages([
                'type' => 'Para criar Entrega, a inspecao anterior tem de ser Inicial, Rotina ou Recolha.',
            ]);
        }

        // Rotina/Recolha so existem entre Initial e Entrega.
        if (in_array($type, ['routine', 'return'], true) && !in_array($previous->type, ['initial', 'routine', 'return'], true)) {
            throw ValidationException::withMessages([
                'type' => 'Rotina/Recolha so podem existir entre a Inicial e a Entrega.',
            ]);
        }

        // Depois de Entrega, nao pode criar mais itens do ciclo sem nova Initial.
        if ($previous->type === 'handover' && in_array($type, ['routine', 'return', 'handover'], true)) {
            throw ValidationException::withMessages([
                'type' => 'Apos Entrega, precisa de nova Inspecao Inicial para abrir novo ciclo.',
            ]);
        }

        return $previous;
    }

    public function cloneOpenDamages(Inspection $inspection): void
    {
        if (!$inspection->previous_inspection_id) {
            return;
        }

        $previousDamages = InspectionDamage::query()
            ->where('inspection_id', $inspection->previous_inspection_id)
            ->where('is_resolved', false)
            ->get();

        foreach ($previousDamages as $damage) {
            InspectionDamage::create([
                'inspection_id' => $inspection->id,
                'origin_damage_id' => $damage->id,
                'scope' => $damage->scope,
                'location' => $damage->location,
                'part' => $damage->part,
                'part_section' => $damage->part_section,
                'damage_type' => $damage->damage_type,
                'notes' => $damage->notes,
                'is_resolved' => false,
            ]);
        }
    }
}
