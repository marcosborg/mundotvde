<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionScheduleRequest;
use App\Http\Requests\StoreInspectionScheduleRequest;
use App\Http\Requests\UpdateInspectionScheduleRequest;
use App\Models\Driver;
use App\Models\InspectionSchedule;
use App\Models\VehicleItem;
use App\Services\Inspections\InspectionRoutineSchedulerService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InspectionScheduleController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedules = InspectionSchedule::with(['vehicle', 'driver', 'creator'])->orderByDesc('id')->paginate(25);

        return view('admin.inspectionSchedules.index', compact('schedules'));
    }

    public function create()
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicles = VehicleItem::with('driver')->orderBy('license_plate')->get();
        $drivers = Driver::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $inspectionSchedule = new InspectionSchedule();

        return view('admin.inspectionSchedules.create', compact('vehicles', 'drivers', 'inspectionSchedule'));
    }

    public function store(StoreInspectionScheduleRequest $request)
    {
        $data = $request->validated();

        $exists = InspectionSchedule::query()
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('driver_id', $data['driver_id'] ?? null)
            ->where('is_active', true)
            ->exists();

        if ($exists) {
            return back()->withErrors(['vehicle_id' => 'Ja existe um agendamento ativo para esta viatura/motorista.'])->withInput();
        }

        $schedule = InspectionSchedule::create([
            'vehicle_id' => $data['vehicle_id'],
            'driver_id' => $data['driver_id'] ?? null,
            'frequency_days' => $data['frequency_days'],
            'start_at' => $data['start_at'] ?? now(),
            'next_run_at' => $data['next_run_at'] ?? ($data['start_at'] ?? now()),
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            'notes' => $data['notes'] ?? null,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.inspection-schedules.edit', $schedule->id)->with('message', 'Agendamento criado.');
    }

    public function edit(InspectionSchedule $inspectionSchedule)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $vehicles = VehicleItem::with('driver')->orderBy('license_plate')->get();
        $drivers = Driver::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.inspectionSchedules.edit', compact('inspectionSchedule', 'vehicles', 'drivers'));
    }

    public function update(UpdateInspectionScheduleRequest $request, InspectionSchedule $inspectionSchedule)
    {
        $data = $request->validated();

        $exists = InspectionSchedule::query()
            ->where('id', '!=', $inspectionSchedule->id)
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('driver_id', $data['driver_id'] ?? null)
            ->where('is_active', true)
            ->exists();

        if ($exists && !empty($data['is_active'])) {
            return back()->withErrors(['vehicle_id' => 'Ja existe outro agendamento ativo para esta viatura/motorista.'])->withInput();
        }

        $inspectionSchedule->update([
            'vehicle_id' => $data['vehicle_id'],
            'driver_id' => $data['driver_id'] ?? null,
            'frequency_days' => $data['frequency_days'],
            'start_at' => $data['start_at'] ?? $inspectionSchedule->start_at,
            'next_run_at' => $data['next_run_at'] ?? $inspectionSchedule->next_run_at,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : false,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.inspection-schedules.index')->with('message', 'Agendamento atualizado.');
    }

    public function show(InspectionSchedule $inspectionSchedule)
    {
        abort_if(Gate::denies('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionSchedule->load(['vehicle', 'driver', 'creator']);

        return view('admin.inspectionSchedules.show', compact('inspectionSchedule'));
    }

    public function runNow(InspectionSchedule $inspectionSchedule, InspectionRoutineSchedulerService $scheduler)
    {
        abort_if(Gate::denies('inspection_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $result = $scheduler->run(false, $inspectionSchedule);

        return back()->with('message', 'Geracao manual: created=' . $result['created'] . ', skipped=' . $result['skipped']);
    }

    public function destroy(InspectionSchedule $inspectionSchedule)
    {
        abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionSchedule->delete();

        return back();
    }

    public function massDestroy(MassDestroyInspectionScheduleRequest $request)
    {
        $schedules = InspectionSchedule::whereIn('id', $request->input('ids', []))->get();

        foreach ($schedules as $schedule) {
            $schedule->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
