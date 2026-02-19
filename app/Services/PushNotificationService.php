<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $serverKey = env('FCM_SERVER_KEY');
        if (!$serverKey) {
            return;
        }

        $tokens = DeviceToken::where('user_id', $userId)
            ->whereNull('revoked_at')
            ->pluck('token')
            ->filter()
            ->values()
            ->all();

        if (empty($tokens)) {
            return;
        }

        foreach ($tokens as $token) {
            Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'priority' => 'high',
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ]);
        }
    }
}

