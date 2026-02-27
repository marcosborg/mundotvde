<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionRequest;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionStepRequest;
use App\Models\Driver;
use App\Models\Inspection;
use App\Models\InspectionCheckItem;
use App\Models\InspectionDamage;
use App\Models\User;
use App\Models\VehicleItem;
use App\Services\Inspections\InspectionWorkflowService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class InspectionController extends Controller
{
    private InspectionWorkflowService $workflow;

    public function __construct(InspectionWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = Inspection::with(['vehicle', 'driver', 'createdBy']);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->integer('vehicle_id'));
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->integer('driver_id'));
        }

        if ($request->filled('created_by_user_id')) {
            $query->where('created_by_user_id', $request->integer('created_by_user_id'));
        }

        if ($request->filled('plate')) {
            $plate = trim((string) $request->input('plate'));
            $query->whereHas('vehicle', function ($q) use ($plate) {
                $q->where('license_plate', 'like', '%' . $plate . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $inspections = $query->latest('id')->paginate(25)->withQueryString();

        $vehicles = VehicleItem::orderBy('license_plate')->get(['id', 'license_plate']);
        $drivers = Driver::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        $summary = [
            'total' => Inspection::count(),
            'in_progress' => Inspection::whereIn('status', ['draft', 'in_progress', 'ready_to_sign'])->count(),
            'closed' => Inspection::where('status', 'closed')->count(),
            'today' => Inspection::whereDate('created_at', now()->toDateString())->count(),
        ];

        return view('admin.inspections.index', compact('inspections', 'vehicles', 'drivers', 'users', 'summary'));
    }

    public function create()
    {
        abort_if(Gate::denies('inspection_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicles = VehicleItem::with(['vehicle_brand', 'vehicle_model', 'driver'])->orderBy('license_plate')->get();
        $drivers = Driver::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.inspections.create', compact('vehicles', 'drivers'));
    }

    public function store(StoreInspectionRequest $request)
    {
        $inspection = $this->workflow->create($request->validated(), $request->user());

        return redirect()->route('admin.inspections.edit', $inspection->id)
            ->with('message', 'Inspeção iniciada com sucesso.');
    }

    public function show(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->load([
            'vehicle.vehicle_brand',
            'vehicle.vehicle_model',
            'driver',
            'photos',
            'damages.photos',
            'signatures',
            'report',
            'audits.user',
            'previousInspection',
        ]);

        return view('admin.inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->load([
            'vehicle.vehicle_brand',
            'vehicle.vehicle_model',
            'driver',
            'photos',
            'damages.photos',
            'signatures',
            'stepStates',
            'checkItems',
            'report',
        ]);

        $steps = config('inspections.step_labels');
        $requiredExterior = config('inspections.required_slots.exterior');
        $requiredInterior = config('inspections.required_slots.interior');
        $slotLabels = config('inspections.slot_labels', []);

        $exteriorBySlot = [];
        foreach ($requiredExterior as $slot) {
            $exteriorBySlot[$slot] = $inspection->photos->first(function ($photo) use ($slot) {
                return $photo->category === 'exterior' && $photo->slot === $slot;
            });
        }

        $interiorBySlot = [];
        foreach ($requiredInterior as $slot) {
            $interiorBySlot[$slot] = $inspection->photos->first(function ($photo) use ($slot) {
                return $photo->category === 'interior' && $photo->slot === $slot;
            });
        }

        $checklist = [];
        foreach ($inspection->checkItems as $item) {
            $checklist[$item->group_key][$item->item_key] = $item->value_bool;
        }

        $checklistPhotoBySlot = [];
        foreach (['dua', 'insurance', 'inspection_periodic', 'tvde_stickers', 'no_smoking_sticker'] as $slot) {
            $checklistPhotoBySlot[$slot] = $inspection->photos->first(function ($photo) use ($slot) {
                return $photo->category === 'document' && $photo->slot === 'doc_' . $slot;
            });
        }

        $drivers = Driver::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $missingItems = $this->buildMissingItems($inspection);
        $signatureNames = [
            'responsible' => optional($inspection->signatures->firstWhere('role', 'responsible'))->signed_by_name,
            'driver' => optional($inspection->signatures->firstWhere('role', 'driver'))->signed_by_name,
        ];

        return view('admin.inspections.edit', [
            'inspection' => $inspection,
            'steps' => $steps,
            'requiredExterior' => $requiredExterior,
            'requiredInterior' => $requiredInterior,
            'slotLabels' => $slotLabels,
            'exteriorBySlot' => $exteriorBySlot,
            'interiorBySlot' => $interiorBySlot,
            'damageLocations' => config('inspections.damage_locations'),
            'damageTypes' => config('inspections.damage_types'),
            'checklist' => $checklist,
            'checklistPhotoBySlot' => $checklistPhotoBySlot,
            'drivers' => $drivers,
            'missingItems' => $missingItems,
            'signatureNames' => $signatureNames,
        ]);
    }

    public function updateStep(UpdateInspectionStepRequest $request, Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validated();
        $step = (int) $validated['step'];
        $action = $validated['action'] ?? 'save';

        if ($step < 1 || $step > 10) {
            throw ValidationException::withMessages(['step' => 'Etapa inválida.']);
        }

        if ($step > $inspection->current_step) {
            throw ValidationException::withMessages(['step' => 'Não pode avançar sem concluir etapas anteriores.']);
        }

        if ($step === 2 && !empty($validated['driver_id'])) {
            $inspection->update(['driver_id' => $validated['driver_id']]);
        }

        if ($step === 3) {
            $this->storeChecklist($inspection, $request->input('checklist', []));
            $this->storeChecklistPhotos($inspection, (array) $request->file('checklist_photos', []));
        }

        if ($step === 4) {
            $this->storeSlotPhotos($inspection, (array) $request->file('exterior_photos', []), 'exterior');
        }

        if ($step === 5) {
            $this->storeSlotPhotos($inspection, (array) $request->file('interior_photos', []), 'interior');
        }

        if (in_array($step, [6, 7], true) && !empty($validated['location']) && !empty($validated['part']) && !empty($validated['damage_type'])) {
            if (!$request->hasFile('damage_photo')) {
                throw ValidationException::withMessages(['damage_photo' => 'Cada dano deve ter pelo menos 1 foto.']);
            }

            $this->workflow->addDamage($inspection, [
                'scope' => $step === 6 ? 'exterior' : 'interior',
                'location' => $validated['location'],
                'part' => $validated['part'],
                'part_section' => $validated['part_section'] ?? null,
                'damage_type' => $validated['damage_type'],
                'notes' => $validated['damage_notes'] ?? null,
                'damage_photo' => $request->file('damage_photo'),
            ]);
        }

        if ($step === 8) {
            $inspection->update(['extra_observations' => $validated['extra_observations'] ?? null]);
            if ($request->hasFile('extra_photos')) {
                foreach ((array) $request->file('extra_photos') as $extraPhoto) {
                    if ($extraPhoto) {
                        $this->workflow->uploadPhoto($inspection, $extraPhoto, 'extra');
                    }
                }
            }
        }

        if ($step === 9) {
            if (!empty($validated['inspector_name'])) {
                $this->workflow->sign($inspection, 'responsible', $validated['inspector_name']);
            }
            if (!empty($validated['driver_signature_name'])) {
                $this->workflow->sign($inspection, 'driver', $validated['driver_signature_name']);
            }
        }

        if ($action === 'complete' && $step < 10) {
            $this->workflow->completeStep($inspection, $step, [
                'extra_observations' => $validated['extra_observations'] ?? null,
            ]);

            return redirect()->route('admin.inspections.edit', $inspection->id)->with('message', 'Etapa concluída com sucesso.');
        }

        return redirect()->route('admin.inspections.edit', $inspection->id)->with('message', 'Dados guardados com sucesso.');
    }

    public function backStep(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($inspection->locked_at) {
            return back()->withErrors(['inspection' => 'Inspeções fechadas não podem regredir etapas.']);
        }

        if ((int) $inspection->current_step <= 1) {
            return back()->withErrors(['inspection' => 'A inspeção já está na primeira etapa.']);
        }

        $inspection->update([
            'current_step' => max(1, ((int) $inspection->current_step) - 1),
            'status' => 'in_progress',
        ]);

        return redirect()->route('admin.inspections.edit', $inspection->id)->with('message', 'Regrediu para a etapa anterior.');
    }

    public function resolveDamage(Inspection $inspection, InspectionDamage $damage)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($damage->inspection_id !== $inspection->id, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->workflow->resolveDamage($damage);

        return back()->with('message', 'Dano marcado como reparado.');
    }

    public function close(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->workflow->close($inspection, false);

        return redirect()->route('admin.inspections.show', $inspection->id)->with('message', 'Inspeção fechada e PDF gerado.');
    }

    public function destroy(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($inspection->locked_at) {
            return back()->withErrors(['inspection' => 'Inspeções fechadas não podem ser eliminadas.']);
        }

        $inspection->delete();

        return back();
    }

    public function massDestroy(MassDestroyInspectionRequest $request)
    {
        $inspections = Inspection::whereIn('id', $request->input('ids', []))->get();

        foreach ($inspections as $inspection) {
            if (!$inspection->locked_at) {
                $inspection->delete();
            }
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    private function storeChecklist(Inspection $inspection, array $checklist): void
    {
        $rows = [];

        foreach ($checklist as $groupKey => $items) {
            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $itemKey => $value) {
                $rows[] = [
                    'inspection_id' => $inspection->id,
                    'group_key' => (string) $groupKey,
                    'item_key' => (string) $itemKey,
                    'value_bool' => (bool) $value,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }
        }

        if (!empty($rows)) {
            InspectionCheckItem::upsert($rows, ['inspection_id', 'group_key', 'item_key'], ['value_bool', 'updated_at']);
        }
    }

    private function storeSlotPhotos(Inspection $inspection, array $filesBySlot, string $category): void
    {
        foreach ($filesBySlot as $slot => $file) {
            if (!$file) {
                continue;
            }

            $existing = $inspection->photos()
                ->where('category', $category)
                ->where('slot', (string) $slot)
                ->latest('id')
                ->first();

            if ($existing) {
                Storage::disk('public')->delete($existing->path);
                $existing->delete();
            }

            $this->workflow->uploadPhoto($inspection, $file, $category, (string) $slot);
        }
    }

    private function storeChecklistPhotos(Inspection $inspection, array $filesByKey): void
    {
        foreach ($filesByKey as $key => $file) {
            if (!$file) {
                continue;
            }

            $slot = 'doc_' . (string) $key;
            $existing = $inspection->photos()
                ->where('category', 'document')
                ->where('slot', $slot)
                ->latest('id')
                ->first();

            if ($existing) {
                Storage::disk('public')->delete($existing->path);
                $existing->delete();
            }

            $this->workflow->uploadPhoto($inspection, $file, 'document', $slot);
        }
    }

    private function buildMissingItems(Inspection $inspection): array
    {
        $inspection->loadMissing(['photos', 'checkItems', 'signatures']);

        $missing = [];

        if (!$inspection->driver_id) {
            $missing[] = ['group' => 'Etapa 2', 'item' => 'Condutor não selecionado'];
        }

        $docs = [
            'dua' => 'Documento Unico Automovel (DUA)',
            'insurance' => 'Seguro',
            'inspection_periodic' => 'Inspecao periodica',
            'tvde_stickers' => 'Disticos TVDE',
            'no_smoking_sticker' => 'Autocolante de proibicao de fumar',
        ];

        $checked = [];
        foreach ($inspection->checkItems->where('group_key', 'documents') as $item) {
            $checked[$item->item_key] = (bool) $item->value_bool;
        }

        foreach ($docs as $key => $label) {
            if (empty($checked[$key])) {
                $missing[] = ['group' => 'Etapa 3', 'item' => $label . ' não assinalado'];
                continue;
            }

            $hasPhoto = $inspection->photos->first(function ($photo) use ($key) {
                return $photo->category === 'document' && $photo->slot === 'doc_' . $key;
            });

            if (!$hasPhoto) {
                $missing[] = ['group' => 'Etapa 3', 'item' => $label . ' sem foto'];
            }
        }

        $extLabels = config('inspections.slot_labels.exterior', []);
        foreach (config('inspections.required_slots.exterior', []) as $slot) {
            $has = $inspection->photos->first(function ($photo) use ($slot) {
                return $photo->category === 'exterior' && $photo->slot === $slot;
            });
            if (!$has) {
                $missing[] = ['group' => 'Etapa 4', 'item' => 'Foto exterior em falta: ' . ($extLabels[$slot] ?? $slot)];
            }
        }

        $intLabels = config('inspections.slot_labels.interior', []);
        foreach (config('inspections.required_slots.interior', []) as $slot) {
            $has = $inspection->photos->first(function ($photo) use ($slot) {
                return $photo->category === 'interior' && $photo->slot === $slot;
            });
            if (!$has) {
                $missing[] = ['group' => 'Etapa 5', 'item' => 'Foto interior em falta: ' . ($intLabels[$slot] ?? $slot)];
            }
        }

        $roles = $inspection->signatures->pluck('role')->toArray();
        if (!in_array('driver', $roles, true)) {
            $missing[] = ['group' => 'Etapa 9', 'item' => 'Assinatura do condutor em falta'];
        }
        if (in_array($inspection->type, ['initial', 'handover', 'return'], true) && !in_array('responsible', $roles, true)) {
            $missing[] = ['group' => 'Etapa 9', 'item' => 'Assinatura do responsavel em falta'];
        }

        return $missing;
    }
}
