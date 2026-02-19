<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InspectionAssignment;
use App\Models\InspectionDefect;
use App\Models\InspectionSchedule;
use App\Models\InspectionTemplate;
use App\Models\VehicleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class InspectionController extends Controller
{
    protected function ensureAdmin(): void
    {
        abort_if(!auth()->check() || !auth()->user()->is_admin, Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function index()
    {
        $this->ensureAdmin();

        $stats = [
            'upcoming_due' => InspectionAssignment::whereIn('status', ['pending', 'in_progress', 'rejected'])->where('due_at', '>=', now())->count(),
            'overdue' => InspectionAssignment::where('status', 'overdue')->count(),
            'awaiting_review' => InspectionAssignment::where('status', 'submitted')->count(),
        ];

        $recentAssignments = InspectionAssignment::with(['vehicle.driver', 'template', 'assignedUser'])
            ->orderByDesc('due_at')
            ->limit(15)
            ->get();

        return view('admin.inspections.index', compact('stats', 'recentAssignments'));
    }

    public function templates()
    {
        $this->ensureAdmin();

        $templates = InspectionTemplate::orderByDesc('id')->paginate(20);

        return view('admin.inspections.templates', compact('templates'));
    }

    public function templateStore(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'performer_type' => 'required|in:driver,company',
            'required_photo_angles_json' => 'nullable|array',
            'required_photo_angles_json.*' => 'in:front,rear,left,right,front_left,front_right,interior,odometer,other',
            'schema_json' => 'nullable|string',
            'requires_signature' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $schema = null;
        if (!empty($data['schema_json'])) {
            $decoded = json_decode($data['schema_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['schema_json' => 'JSON de esquema inválido.'])->withInput();
            }
            $schema = $decoded;
        }

        InspectionTemplate::create([
            'name' => $data['name'],
            'performer_type' => $data['performer_type'],
            'required_photo_angles_json' => $data['required_photo_angles_json'] ?? ['front', 'rear', 'left', 'right'],
            'schema_json' => $schema,
            'requires_signature' => (bool) ($data['requires_signature'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.inspections.templates')->with('message', 'Template criado com sucesso.');
    }

    public function templateUpdate(Request $request, InspectionTemplate $template)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'performer_type' => 'required|in:driver,company',
            'required_photo_angles_json' => 'nullable|array',
            'required_photo_angles_json.*' => 'in:front,rear,left,right,front_left,front_right,interior,odometer,other',
            'schema_json' => 'nullable|string',
            'requires_signature' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $schema = null;
        if (!empty($data['schema_json'])) {
            $decoded = json_decode($data['schema_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['schema_json' => 'JSON de esquema inválido.']);
            }
            $schema = $decoded;
        }

        $template->update([
            'name' => $data['name'],
            'performer_type' => $data['performer_type'],
            'required_photo_angles_json' => $data['required_photo_angles_json'] ?? ['front', 'rear', 'left', 'right'],
            'schema_json' => $schema,
            'requires_signature' => (bool) ($data['requires_signature'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.inspections.templates')->with('message', 'Template atualizado com sucesso.');
    }

    public function templateDestroy(InspectionTemplate $template)
    {
        $this->ensureAdmin();

        $template->delete();

        return redirect()->route('admin.inspections.templates')->with('message', 'Template removido.');
    }

    public function schedules(Request $request)
    {
        $this->ensureAdmin();

        $schedules = InspectionSchedule::with(['vehicle.driver', 'template'])
            ->when($request->filled('vehicle_id'), fn($q) => $q->where('vehicle_id', $request->integer('vehicle_id')))
            ->orderByDesc('id')
            ->paginate(20);

        $vehicles = VehicleItem::with('driver')->orderBy('license_plate')->limit(500)->get();
        $templates = InspectionTemplate::where('is_active', true)->orderBy('name')->get();

        return view('admin.inspections.schedules', compact('schedules', 'vehicles', 'templates'));
    }

    public function scheduleStore(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicle_items,id',
            'vehicle_ids' => 'nullable|array',
            'vehicle_ids.*' => 'integer|exists:vehicle_items,id',
            'template_id' => 'required|exists:inspection_templates,id',
            'frequency_days' => 'required|integer|min:1|max:365',
            'due_time' => 'required|date_format:H:i',
            'grace_hours' => 'required|integer|min:0|max:168',
            'is_active' => 'nullable|boolean',
            'reminder_policy_json' => 'nullable|string',
        ]);

        $vehicleIds = collect($data['vehicle_ids'] ?? [])
            ->push($data['vehicle_id'] ?? null)
            ->filter()
            ->unique()
            ->values();

        if ($vehicleIds->isEmpty()) {
            return back()->withErrors(['vehicle_ids' => 'Selecione pelo menos uma viatura.'])->withInput();
        }

        $policy = null;
        if (!empty($data['reminder_policy_json'])) {
            $policy = json_decode($data['reminder_policy_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['reminder_policy_json' => 'JSON de reminders inválido.'])->withInput();
            }
        }

        $created = 0;
        foreach ($vehicleIds as $vehicleId) {
            InspectionSchedule::create([
                'vehicle_id' => $vehicleId,
                'template_id' => $data['template_id'],
                'frequency_days' => $data['frequency_days'],
                'due_time' => $data['due_time'],
                'grace_hours' => $data['grace_hours'],
                'is_active' => (bool) ($data['is_active'] ?? true),
                'reminder_policy_json' => $policy,
            ]);
            $created++;
        }

        return redirect()->route('admin.inspections.schedules')->with('message', "Plano criado com sucesso para {$created} viatura(s).");
    }

    public function scheduleUpdate(Request $request, InspectionSchedule $schedule)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'frequency_days' => 'required|integer|min:1|max:365',
            'due_time' => 'required|date_format:H:i',
            'grace_hours' => 'required|integer|min:0|max:168',
            'is_active' => 'nullable|boolean',
            'reminder_policy_json' => 'nullable|string',
        ]);

        $policy = null;
        if (!empty($data['reminder_policy_json'])) {
            $policy = json_decode($data['reminder_policy_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['reminder_policy_json' => 'JSON de reminders inválido.']);
            }
        }

        $schedule->update([
            'frequency_days' => $data['frequency_days'],
            'due_time' => $data['due_time'],
            'grace_hours' => $data['grace_hours'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'reminder_policy_json' => $policy,
        ]);

        return redirect()->route('admin.inspections.schedules')->with('message', 'Plano atualizado.');
    }

    public function scheduleDestroy(InspectionSchedule $schedule)
    {
        $this->ensureAdmin();

        $schedule->delete();

        return redirect()->route('admin.inspections.schedules')->with('message', 'Plano removido.');
    }

    public function assignments(Request $request)
    {
        $this->ensureAdmin();

        $query = InspectionAssignment::with(['vehicle.driver', 'template', 'assignedUser', 'submission']);
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->integer('vehicle_id'));
        }

        $assignments = $query->orderByDesc('due_at')->paginate(30);
        $vehicles = VehicleItem::with('driver')->orderBy('license_plate')->limit(500)->get();
        $templates = InspectionTemplate::where('is_active', true)->orderBy('name')->get();

        return view('admin.inspections.assignments', compact('assignments', 'vehicles', 'templates'));
    }

    public function assignmentStore(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicle_items,id',
            'template_id' => 'required|exists:inspection_templates,id',
            'due_at' => 'required|date',
            'grace_hours' => 'nullable|integer|min:0|max:168',
        ]);

        $vehicle = VehicleItem::with('driver')->findOrFail($data['vehicle_id']);
        abort_if(!$vehicle->driver || !$vehicle->driver->user_id, 422, 'Viatura sem motorista atribuído.');

        $dueAt = Carbon::parse($data['due_at']);

        InspectionAssignment::create([
            'vehicle_id' => $vehicle->id,
            'template_id' => $data['template_id'],
            'assigned_user_id' => $vehicle->driver->user_id,
            'period_start' => $dueAt->copy()->startOfDay(),
            'period_end' => $dueAt->copy()->endOfDay(),
            'due_at' => $dueAt,
            'grace_hours' => (int) ($data['grace_hours'] ?? 24),
            'status' => 'pending',
            'generated_by' => 'manual',
        ]);

        return redirect()->route('admin.inspections.assignments')->with('message', 'Inspeção criada manualmente.');
    }

    public function assignmentShow(InspectionAssignment $assignment)
    {
        $this->ensureAdmin();

        $assignment->load([
            'vehicle.driver.user',
            'template',
            'submission.photos',
            'submission.defects.photos',
            'submission.creator',
        ]);

        return view('admin.inspections.show', compact('assignment'));
    }

    public function review(Request $request, InspectionAssignment $assignment)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:2000',
        ]);

        abort_if(!$assignment->submission || !$assignment->submission->submitted_at, 422, 'Sem submissão para rever.');

        DB::transaction(function () use ($assignment, $data) {
            $assignment->update([
                'status' => $data['action'] === 'approve' ? 'reviewed' : 'rejected',
            ]);

            if (!empty($data['notes'])) {
                $assignment->submission->update([
                    'summary_notes' => trim(($assignment->submission->summary_notes ?? '') . "\n\n[Review] " . $data['notes']),
                ]);
            }
        });

        return redirect()->route('admin.inspections.show', $assignment->id)->with('message', 'Revisão guardada com sucesso.');
    }

    public function defectUpdate(Request $request, InspectionDefect $defect)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $defect->update($data);

        return back()->with('message', 'Defeito atualizado.');
    }

    public function photoDownload(int $photo)
    {
        $this->ensureAdmin();

        $photoModel = \App\Models\InspectionPhoto::findOrFail($photo);
        abort_if(!Storage::disk($photoModel->file_disk)->exists($photoModel->file_path), 404);

        return Storage::disk($photoModel->file_disk)->download($photoModel->file_path);
    }

    public function evidenceZip(InspectionAssignment $assignment)
    {
        $this->ensureAdmin();

        $assignment->load('submission.photos');
        abort_if(!$assignment->submission, 404, 'Sem submissão.');

        $zipName = sprintf('inspection_%d_evidence.zip', $assignment->id);
        $tmpPath = storage_path('app/tmp_' . $zipName);

        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Falha a gerar ZIP.');
        }

        foreach ($assignment->submission->photos as $photo) {
            if (!Storage::disk($photo->file_disk)->exists($photo->file_path)) {
                continue;
            }

            $stream = Storage::disk($photo->file_disk)->get($photo->file_path);
            $ext = pathinfo($photo->file_path, PATHINFO_EXTENSION) ?: 'jpg';
            $zip->addFromString(sprintf('%s_%d.%s', $photo->angle, $photo->id, $ext), $stream);
        }

        $zip->close();

        return response()->download($tmpPath, $zipName)->deleteFileAfterSend(true);
    }
}

