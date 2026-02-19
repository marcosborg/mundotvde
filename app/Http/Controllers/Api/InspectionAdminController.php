<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionAssignment;
use App\Models\InspectionSchedule;
use App\Models\InspectionSetting;
use App\Models\InspectionTemplate;
use App\Models\VehicleItem;
use Illuminate\Http\Request;

class InspectionAdminController extends Controller
{
    protected function ensureAdmin(Request $request): void
    {
        abort_if(!$request->user() || !$request->user()->is_admin, 403);
    }

    public function dashboard(Request $request)
    {
        $this->ensureAdmin($request);

        return response()->json([
            'upcoming_due' => InspectionAssignment::whereIn('status', ['pending', 'in_progress', 'rejected'])->where('due_at', '>=', now())->count(),
            'overdue' => InspectionAssignment::where('status', 'overdue')->count(),
            'awaiting_review' => InspectionAssignment::where('status', 'submitted')->count(),
            'settings' => InspectionSetting::current(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'push_enabled' => 'nullable|boolean',
            'default_reminder_policy_json' => 'nullable|array',
        ]);

        $settings = InspectionSetting::current();
        $settings->update([
            'push_enabled' => $data['push_enabled'] ?? $settings->push_enabled,
            'default_reminder_policy_json' => $data['default_reminder_policy_json'] ?? $settings->default_reminder_policy_json,
        ]);

        return response()->json(['data' => $settings->fresh()]);
    }

    public function vehicles(Request $request)
    {
        $this->ensureAdmin($request);
        return response()->json(['data' => VehicleItem::with(['driver.user', 'vehicle_brand', 'vehicle_model'])->limit(200)->get()]);
    }

    public function templatesForSelect(Request $request)
    {
        $this->ensureAdmin($request);
        return response()->json(['data' => InspectionTemplate::where('is_active', true)->orderBy('name')->get(['id', 'name', 'performer_type'])]);
    }

    public function schedulesForVehicle(Request $request, VehicleItem $vehicle)
    {
        $this->ensureAdmin($request);
        return response()->json(['data' => InspectionSchedule::with('template')->where('vehicle_id', $vehicle->id)->get()]);
    }
}

