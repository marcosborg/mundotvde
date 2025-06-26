<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Bot;
use App\Models\WebsiteMessage;
use App\Models\AppMessage;

class VirtualAssistantController extends Controller
{
    public function handleMessage(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'conversation' => 'required|array|max:10',
        ]);

        $email = $data['email'];
        $messages = $data['conversation'];

        // Obter instruções do bot com ID 2
        $bot = Bot::find(2);
        $instructions = $bot ? $bot->instructions : 'Responde de forma simpática e clara.';

        // Montar conversa com sistema + histórico
        $chatMessages = [
            ['role' => 'system', 'content' => $instructions],
            ...$messages
        ];

        // Enviar para a OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => $chatMessages,
            'temperature' => 0.7,
            'max_tokens' => 600,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Erro ao contactar o assistente.'], 500);
        }

        $reply = $response->json('choices.0.message.content');

        // 👉 Adicionar a resposta ao array de mensagens
        $messages[] = ['role' => 'assistant', 'content' => $reply];

        // 👉 Manter no máximo 10 mensagens
        $messages = array_slice($messages, -10);

        // 👉 Guardar ou atualizar registo no WebsiteMessage
        WebsiteMessage::updateOrCreate(
            ['email' => $email],
            ['messages' => json_encode($messages, JSON_UNESCAPED_UNICODE)]
        );

        return response()->json(['reply' => $reply]);
    }

    public function handleDriverMessage(Request $request)
    {
        $request->validate([
            'user' => 'required|string', // ID ou email do motorista
            'conversation' => 'required|array',
        ]);

        // Obter instruções do bot 3
        $bot = Bot::findOrFail(3);
        $instructions = $bot->instructions;

        $conversation = $request->conversation;

        $messages = collect($conversation)->map(function ($item) {
            return [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        })->toArray();

        // Adicionar instruções ao início
        array_unshift($messages, ['role' => 'system', 'content' => $instructions]);

        // Chamar o ChatGPT (pseudocódigo, usa o teu serviço real aqui)
        $reply = app('openai')->chat($messages); // ajusta conforme teu serviço

        // Gravar a nova conversa
        AppMessage::updateOrCreate(
            ['user' => $request->user],
            ['messages' => json_encode($conversation)]
        );

        return response()->json([
            'success' => true,
            'reply' => $reply,
        ]);
    }
}
