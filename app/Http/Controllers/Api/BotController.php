<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bot;
use App\Models\WhatsappMessage;

class BotController extends Controller
{
    public function getInstructions($id)
    {
        $bot = Bot::findOrFail($id);
        return response()->json([
            'success' => true,
            'instructions' => $bot->instructions,
        ]);
    }

    public function saveMessage(Request $request)
    {
        $request->validate([
            'user' => 'required|string',
            'messages' => 'required|array',
        ]);

        $existing = WhatsappMessage::where('user', $request->user)->first();

        if ($existing) {
            $existing->update([
                'messages' => json_encode($request->messages),
            ]);

            $message = $existing;
        } else {
            $message = WhatsappMessage::create([
                'user' => $request->user,
                'messages' => json_encode($request->messages),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function getMessage(Request $request)
    {
        $user = $request->query('user');

        $message = WhatsappMessage::where('user', $user)->first();

        return response()->json([
            'success' => true,
            'messages' => $message ? (is_array($message->messages) ? $message->messages : json_decode($message->messages, true)) : [],
        ]);
    }
}
