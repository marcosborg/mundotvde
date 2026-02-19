<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\InspectionPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class InspectionCommonController extends Controller
{
    public function registerDeviceToken(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:android,ios',
            'token' => 'required|string|max:512',
        ]);

        $record = DeviceToken::updateOrCreate(
            ['platform' => $request->platform, 'token' => $request->token],
            [
                'user_id' => $request->user()->id,
                'last_seen_at' => now(),
                'revoked_at' => null,
            ]
        );

        return response()->json(['data' => $record]);
    }

    public function notifications(Request $request)
    {
        return response()->json([
            'data' => $request->user()->notifications()->paginate(20),
        ]);
    }

    public function photoDownload(Request $request, InspectionPhoto $photo)
    {
        if (!$request->hasValidSignature()) {
            abort(Response::HTTP_FORBIDDEN, 'URL expirada ou inválida.');
        }

        $user = $request->user();
        $assignment = optional(optional($photo->submission)->assignment);

        if (!$assignment) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $canAccess = $user && (
            $user->is_admin ||
            (int) $assignment->assigned_user_id === (int) $user->id
        );

        if (!$canAccess) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if (!Storage::disk($photo->file_disk)->exists($photo->file_path)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return Storage::disk($photo->file_disk)->download($photo->file_path);
    }

    public function photoSignedUrl(Request $request, InspectionPhoto $photo)
    {
        $user = $request->user();
        $assignment = optional(optional($photo->submission)->assignment);

        if (!$assignment) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $canAccess = $user && (
            $user->is_admin ||
            (int) $assignment->assigned_user_id === (int) $user->id
        );

        if (!$canAccess) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'url' => URL::temporarySignedRoute('api.inspections.photos.download', now()->addMinutes(15), ['photo' => $photo->id]),
        ]);
    }
}

