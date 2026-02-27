<?php

namespace App\Services\Inspections;

use App\Models\Inspection;
use App\Models\InspectionDamage;
use App\Models\InspectionPhoto;
use App\Models\InspectionSignature;
use App\Models\InspectionStepState;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InspectionWorkflowService
{
    private InspectionPermissionsService $permissions;
    private InspectionSequenceService $sequence;
    private InspectionCompletenessValidator $completeness;
    private InspectionAuditService $audit;
    private InspectionReportService $reportService;

    public function __construct(
        InspectionPermissionsService $permissions,
        InspectionSequenceService $sequence,
        InspectionCompletenessValidator $completeness,
        InspectionAuditService $audit,
        InspectionReportService $reportService,
    ) {
        $this->permissions = $permissions;
        $this->sequence = $sequence;
        $this->completeness = $completeness;
        $this->audit = $audit;
        $this->reportService = $reportService;
    }

    public function create(array $data, User $user): Inspection
    {
        return DB::transaction(function () use ($data, $user) {
            $type = $data['type'];
            $this->permissions->ensureUserCanCreateType($user, $type);
            $previous = $this->sequence->validateCreationSequence($type, (int) $data['vehicle_id']);

            $inspection = Inspection::create([
                'type' => $type,
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'] ?? null,
                'created_by_user_id' => $user->id,
                'responsible_user_id' => $data['responsible_user_id'] ?? $user->id,
                'status' => 'in_progress',
                'current_step' => 1,
                'previous_inspection_id' => $previous?->id,
                'location_lat' => $data['location_lat'] ?? null,
                'location_lng' => $data['location_lng'] ?? null,
                'location_text' => $data['location_text'] ?? null,
                'location_accuracy' => $data['location_accuracy'] ?? null,
                'location_timezone' => $data['location_timezone'] ?? null,
                'started_at' => now(),
            ]);

            for ($step = 1; $step <= 10; $step++) {
                InspectionStepState::create([
                    'inspection_id' => $inspection->id,
                    'step' => $step,
                ]);
            }

            $this->sequence->cloneOpenDamages($inspection);
            $this->audit->log($inspection, 'inspection_created', ['type' => $type]);

            return $inspection;
        });
    }

    public function completeStep(Inspection $inspection, int $step, array $data = []): Inspection
    {
        $this->permissions->ensureCanEdit($inspection);

        if ($step < 1 || $step > 10) {
            throw ValidationException::withMessages(['step' => 'Etapa inválida.']);
        }

        if ($inspection->current_step < $step) {
            throw ValidationException::withMessages(['step' => 'Não pode avançar sem concluir as etapas anteriores.']);
        }

        InspectionStepState::where('inspection_id', $inspection->id)
            ->where('step', $step)
            ->update([
                'is_completed' => true,
                'completed_at' => now(),
                'completed_by_user_id' => auth()->id(),
            ]);

        $nextStep = min(10, $step + 1);

        $inspection->update([
            'current_step' => $nextStep,
            'status' => $nextStep >= 9 ? 'ready_to_sign' : 'in_progress',
            'extra_observations' => $data['extra_observations'] ?? $inspection->extra_observations,
        ]);

        $this->audit->log($inspection, 'step_completed', ['step' => $step]);

        return $inspection;
    }

    public function uploadPhoto(Inspection $inspection, UploadedFile $file, string $category, ?string $slot = null): InspectionPhoto
    {
        $this->permissions->ensureCanEdit($inspection);

        $path = $file->store('inspections/photos', 'public');

        $photo = InspectionPhoto::create([
            'inspection_id' => $inspection->id,
            'category' => $category,
            'slot' => $slot,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'taken_at' => now(),
            'uploaded_by_user_id' => auth()->id(),
        ]);

        $this->audit->log($inspection, 'photo_uploaded', ['photo_id' => $photo->id, 'category' => $category, 'slot' => $slot]);

        return $photo;
    }

    public function addDamage(Inspection $inspection, array $data): InspectionDamage
    {
        $this->permissions->ensureCanEdit($inspection);

        $damage = InspectionDamage::create([
            'inspection_id' => $inspection->id,
            'scope' => $data['scope'],
            'location' => $data['location'],
            'part' => $data['part'],
            'part_section' => $data['part_section'] ?? null,
            'damage_type' => $data['damage_type'],
            'notes' => $data['notes'] ?? null,
        ]);

        if (!empty($data['damage_photo']) && $data['damage_photo'] instanceof UploadedFile) {
            $photoPath = $data['damage_photo']->store('inspections/damages', 'public');
            $damage->photos()->create([
                'path' => $photoPath,
                'original_name' => $data['damage_photo']->getClientOriginalName(),
                'mime' => $data['damage_photo']->getMimeType(),
                'size' => $data['damage_photo']->getSize(),
                'taken_at' => now(),
                'uploaded_by_user_id' => auth()->id(),
            ]);
        }

        $this->audit->log($inspection, 'damage_added', ['damage_id' => $damage->id]);

        return $damage;
    }

    public function resolveDamage(InspectionDamage $damage): void
    {
        $damage->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by_user_id' => auth()->id(),
        ]);

        $this->audit->log($damage->inspection, 'damage_resolved', ['damage_id' => $damage->id]);
    }

    public function sign(Inspection $inspection, string $role, string $name, ?string $document = null, ?UploadedFile $signature = null): InspectionSignature
    {
        $this->permissions->ensureCanEdit($inspection);

        if ($signature) {
            $path = $signature->store('inspections/signatures', 'public');
            $hash = hash_file('sha256', Storage::disk('public')->path($path));
        } else {
            $path = 'typed-signature:' . $role;
            $hash = hash('sha256', $inspection->id . '|' . $role . '|' . $name . '|' . now()->toIso8601String());
        }

        $record = InspectionSignature::updateOrCreate(
            ['inspection_id' => $inspection->id, 'role' => $role],
            [
                'signed_by_name' => $name,
                'signed_by_document' => $document,
                'signature_path' => $path,
                'signature_hash' => $hash,
                'signed_at' => now(),
            ]
        );

        $inspection->update(['status' => 'signed']);

        $this->audit->log($inspection, 'inspection_signed', ['role' => $role]);

        return $record;
    }

    public function close(Inspection $inspection, bool $strict = true): void
    {
        $this->permissions->ensureCanEdit($inspection);
        if ($strict) {
            $this->completeness->assertCanClose($inspection);
        }

        $this->reportService->generateAndLock($inspection);
        $this->audit->log($inspection, 'inspection_closed');
    }
}
