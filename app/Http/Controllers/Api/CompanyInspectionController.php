<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendInspectionReminderJob;
use App\Models\InspectionAssignment;
use App\Models\InspectionSchedule;
use App\Models\InspectionSubmission;
use App\Models\InspectionTemplate;
use App\Models\VehicleItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyInspectionController extends Controller
{
    protected function ensureCompanyAccess(Request $request): void
    {
        if (!$request->user() || !$request->user()->is_admin) {
            abort(Response::HTTP_FORBIDDEN, 'Acesso restrito à empresa.');
        }
    }

    public function templatesIndex(Request $request)
    {
        $this->ensureCompanyAccess($request);

        return response()->json([
            'data' => InspectionTemplate::orderByDesc('id')->paginate(20),
        ]);
    }

    public function templatesStore(Request $request)
    {
        $this->ensureCompanyAccess($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'performer_type' => 'required|in:driver,company',
            'schema_json' => 'nullable',
            'required_photo_angles_json' => 'nullable|array',
            'required_photo_angles_json.*' => 'in:front,rear,left,right,front_left,front_right,interior,odometer,other',
            'requires_signature' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $template = InspectionTemplate::create([
            ...$data,
            'created_by' => $request->user()->id,
            'requires_signature' => (bool) ($data['requires_signature'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'schema_json' => is_array($data['schema_json'] ?? null) ? $data['schema_json'] : null,
        ]);

        return response()->json(['data' => $template], Response::HTTP_CREATED);
    }

    public function templatesUpdate(Request $request, InspectionTemplate $template)
    {
        $this->ensureCompanyAccess($request);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'performer_type' => 'sometimes|required|in:driver,company',
            'schema_json' => 'nullable',
            'required_photo_angles_json' => 'nullable|array',
            'required_photo_angles_json.*' => 'in:front,rear,left,right,front_left,front_right,interior,odometer,other',
            'requires_signature' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if (array_key_exists('schema_json', $data) && !is_array($data['schema_json'])) {
            $data['schema_json'] = null;
        }

        $template->update($data);

        return response()->json(['data' => $template->fresh()]);
    }

    public function templatesDestroy(Request $request, InspectionTemplate $template)
    {
        $this->ensureCompanyAccess($request);
        $template->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function schedulesIndex(Request $request)
    {
        $this->ensureCompanyAccess($request);

        $query = InspectionSchedule::with(['vehicle.driver.user', 'template']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->integer('vehicle_id'));
        }

        return response()->json(['data' => $query->orderByDesc('id')->paginate(20)]);
    }

    public function schedulesStore(Request $request)
    {
        $this->ensureCompanyAccess($request);

        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicle_items,id',
            'template_id' => 'required|exists:inspection_templates,id',
            'frequency_days' => 'required|integer|min:1|max:365',
            'due_time' => 'required|date_format:H:i',
            'grace_hours' => 'required|integer|min:0|max:168',
            'reminder_policy_json' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule = InspectionSchedule::create([
            ...$data,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return response()->json(['data' => $schedule], Response::HTTP_CREATED);
    }

    public function schedulesUpdate(Request $request, InspectionSchedule $schedule)
    {
        $this->ensureCompanyAccess($request);

        $data = $request->validate([
            'vehicle_id' => 'sometimes|required|exists:vehicle_items,id',
            'template_id' => 'sometimes|required|exists:inspection_templates,id',
            'frequency_days' => 'sometimes|required|integer|min:1|max:365',
            'due_time' => 'sometimes|required|date_format:H:i',
            'grace_hours' => 'sometimes|required|integer|min:0|max:168',
            'reminder_policy_json' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule->update($data);

        return response()->json(['data' => $schedule->fresh()]);
    }

    public function schedulesDestroy(Request $request, InspectionSchedule $schedule)
    {
        $this->ensureCompanyAccess($request);
        $schedule->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function assignmentsIndex(Request $request)
    {
        $this->ensureCompanyAccess($request);

        $query = InspectionAssignment::with(['vehicle.driver.user', 'template', 'submission.photos', 'submission.defects']);

        foreach (['status', 'vehicle_id', 'assigned_user_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('due_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('due_at', '<=', $request->input('date_to'));
        }

        return response()->json(['data' => $query->orderByDesc('due_at')->paginate(30)]);
    }

    public function review(Request $request, InspectionAssignment $assignment)
    {
        $this->ensureCompanyAccess($request);

        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:2000',
        ]);

        $submission = $assignment->submission;
        if (!$submission || !$submission->submitted_at) {
            return response()->json([
                'message' => 'Não existe submissão para revisão.',
                'code' => 'inspection_submission_missing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($data['action'] === 'approve') {
            $assignment->update(['status' => 'reviewed']);
        } else {
            $assignment->update(['status' => 'rejected']);
            SendInspectionReminderJob::dispatch($assignment->id, 'rejected_resubmit');
        }

        $submission->update([
            'summary_notes' => trim(($submission->summary_notes ?? '') . "\n\n[Review] " . ($data['notes'] ?? '')),
        ]);

        return response()->json(['data' => $assignment->fresh('submission')]);
    }
}

