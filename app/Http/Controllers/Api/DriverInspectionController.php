<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionAssignment;
use App\Models\InspectionDefect;
use App\Models\InspectionPhoto;
use App\Models\InspectionSubmission;
use App\Models\VehicleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class DriverInspectionController extends Controller
{
    public function next(Request $request)
    {
        $user = $request->user();

        $assignment = InspectionAssignment::with(['template', 'vehicle.driver', 'submission.photos'])
            ->where('assigned_user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'rejected', 'overdue'])
            ->orderBy('due_at')
            ->first();

        if (!$assignment) {
            return response()->json(['data' => null]);
        }

        $requiredAngles = (array) ($assignment->template->required_photo_angles_json ?? ['front', 'rear', 'left', 'right']);

        return response()->json([
            'data' => [
                'assignment' => $assignment,
                'required_angles' => $requiredAngles,
                'template_schema' => $assignment->template->schema_json,
                'instructions' => [
                    'take_all_sides' => true,
                    'damage_optional' => true,
                ],
            ],
        ]);
    }

    public function start(Request $request, InspectionAssignment $assignment)
    {
        $this->authorizeDriverAssignment($request, $assignment);

        if (in_array($assignment->status, ['submitted', 'reviewed'], true)) {
            return response()->json([
                'message' => 'Inspeção já submetida/revista.',
                'code' => 'inspection_already_closed',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $submission = $assignment->submission;
        if (!$submission) {
            $submission = InspectionSubmission::create([
                'assignment_id' => $assignment->id,
                'started_at' => now(),
                'created_by_user_id' => $request->user()->id,
            ]);
        }

        if ($assignment->status === 'pending' || $assignment->status === 'overdue' || $assignment->status === 'rejected') {
            $assignment->update(['status' => 'in_progress']);
        }

        return response()->json(['data' => $submission]);
    }

    public function photo(Request $request, InspectionAssignment $assignment)
    {
        $this->authorizeDriverAssignment($request, $assignment);

        $request->validate([
            'angle' => 'required|in:front,rear,left,right,front_left,front_right,interior,odometer,other',
            'file' => 'required|file|mimetypes:image/jpeg,image/png,image/webp|max:' . env('INSPECTION_MAX_PHOTO_KB', 10240),
            'captured_at' => 'nullable|date',
        ]);

        $submission = $assignment->submission;
        if (!$submission) {
            $submission = InspectionSubmission::create([
                'assignment_id' => $assignment->id,
                'started_at' => now(),
                'created_by_user_id' => $request->user()->id,
            ]);
        }

        $file = $request->file('file');
        $disk = 'inspections_private';
        $path = $file->store('inspections/' . $assignment->id, ['disk' => $disk]);

        $photo = InspectionPhoto::create([
            'submission_id' => $submission->id,
            'angle' => $request->input('angle'),
            'file_disk' => $disk,
            'file_path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'sha256' => hash_file('sha256', $file->getRealPath()),
            'captured_at' => $request->input('captured_at'),
            'uploaded_at' => now(),
            'meta_json' => [
                'original_name' => $file->getClientOriginalName(),
            ],
        ]);

        $preview = URL::temporarySignedRoute(
            'api.inspections.photos.download',
            now()->addMinutes(15),
            ['photo' => $photo->id]
        );

        return response()->json([
            'data' => [
                'photo_id' => $photo->id,
                'preview_url' => $preview,
            ],
            'message' => 'Foto carregada com sucesso.',
        ], Response::HTTP_CREATED);
    }

    public function deletePhoto(Request $request, InspectionAssignment $assignment, InspectionPhoto $photo)
    {
        $this->authorizeDriverAssignment($request, $assignment);

        if (!$assignment->submission || $photo->submission_id !== $assignment->submission->id) {
            return response()->json(['message' => 'Foto não encontrada para a inspeção.'], Response::HTTP_NOT_FOUND);
        }

        if (in_array($assignment->status, ['submitted', 'reviewed'], true)) {
            return response()->json(['message' => 'Inspeção já fechada.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Storage::disk($photo->file_disk)->delete($photo->file_path);
        $photo->delete();

        return response()->json(['message' => 'Foto removida.']);
    }

    public function submit(Request $request, InspectionAssignment $assignment)
    {
        $this->authorizeDriverAssignment($request, $assignment);

        $payload = $request->validate([
            'summary_notes' => 'nullable|string|max:2000',
            'location_json' => 'nullable|array',
            'defects' => 'nullable|array',
            'defects.*.title' => 'required_with:defects|string|max:255',
            'defects.*.description' => 'nullable|string|max:2000',
            'defects.*.severity' => 'required_with:defects|in:non_critical,critical',
            'defects.*.photo_ids' => 'nullable|array',
            'defects.*.photo_ids.*' => 'integer|exists:inspection_photos,id',
        ]);

        $submission = $assignment->submission;
        if (!$submission) {
            return response()->json([
                'message' => 'Inicie a inspeção antes de submeter.',
                'code' => 'inspection_not_started',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $requiredAngles = (array) ($assignment->template->required_photo_angles_json ?? ['front', 'rear', 'left', 'right']);
        $existingAngles = $submission->photos()->pluck('angle')->unique()->values()->all();
        $missing = array_values(array_diff($requiredAngles, $existingAngles));
        if (!empty($missing)) {
            return response()->json([
                'message' => 'Faltam fotos obrigatórias.',
                'code' => 'inspection_missing_required_angles',
                'missing_angles' => $missing,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::transaction(function () use ($assignment, $submission, $payload, $request) {
            $submission->update([
                'summary_notes' => $payload['summary_notes'] ?? null,
                'location_json' => $payload['location_json'] ?? null,
                'submitted_at' => now(),
                'created_by_user_id' => $request->user()->id,
            ]);

            foreach (($payload['defects'] ?? []) as $defectData) {
                $defect = InspectionDefect::create([
                    'vehicle_id' => $assignment->vehicle_id,
                    'created_from_submission_id' => $submission->id,
                    'title' => $defectData['title'],
                    'description' => $defectData['description'] ?? null,
                    'severity' => $defectData['severity'],
                    'status' => 'open',
                    'created_by_user_id' => $request->user()->id,
                ]);

                if (!empty($defectData['photo_ids'])) {
                    $validPhotoIds = $submission->photos()
                        ->whereIn('id', $defectData['photo_ids'])
                        ->pluck('id')
                        ->all();
                    $defect->photos()->sync($validPhotoIds);
                }
            }

            $assignment->update(['status' => 'submitted']);
        });

        return response()->json(['message' => 'Inspeção submetida com sucesso.']);
    }

    protected function authorizeDriverAssignment(Request $request, InspectionAssignment $assignment): void
    {
        if ((int) $assignment->assigned_user_id !== (int) $request->user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'Acesso negado a esta inspeção.');
        }

        if (!$assignment->vehicle || !$assignment->vehicle->driver || (int) $assignment->vehicle->driver->user_id !== (int) $request->user()->id) {
            abort(Response::HTTP_FORBIDDEN, 'Esta viatura não está atribuída ao motorista autenticado.');
        }
    }
}

