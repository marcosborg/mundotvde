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

        if ($type === 'initial') {
            return $previous;
        }

        if (!$previous) {
            throw ValidationException::withMessages([
                'vehicle_id' => 'Esta viatura precisa de Inspeção Inicial antes deste tipo.',
            ]);
        }

        if ($type === 'handover' && !in_array($previous->type, ['initial', 'return'], true)) {
            throw ValidationException::withMessages([
                'type' => 'Para criar Entrega, a inspeção anterior tem de ser Inicial ou Recolha.',
            ]);
        }

        if (in_array($type, ['routine', 'return'], true) && $previous->type !== 'handover') {
            throw ValidationException::withMessages([
                'type' => 'Rotina/Recolha só podem ser criadas após uma inspeção de Entrega.',
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
