<?php

namespace App\Services;

use App\Interfaces\IAiChatbotHandler;
use App\Models\AiChat;
use App\Models\AiChatMessage;
use App\Models\AiClient;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class DeepseekChatbotHandlerService implements IAiChatbotHandler{
    public function startChat(AiClient $client, string $userMessage){
        $chat = AiChat::create([
            "ai_client_id" => $client->id,
            "uuid" => Str::uuid()->toString(),
            "model_name" => "deepseek",
            "model_exact_name" => env('AI_DEEPSEEK_MODEL_NAME', 'deepseek/deepseek-r1:free'),
        ]);

        $defaultMessages = $client->messages()
            ->orderBy('ordering')
            ->get()
            ->map(fn($item) => [
                'ai_chat_id' => $chat->id,
                'role' => $item->role,
                'content' => $item->content,
                'type' => 2,
                "uuid" => Str::uuid()->toString(),
                'answer' => null
            ])
            ->toArray();

        $defaultMessages[] = [
            'ai_chat_id' => $chat->id,
            'role' => 'user',
            'content' => $userMessage,
            'type' => 3,
            "uuid" => Str::uuid()->toString(),
            'answer' => null
        ];
        

        $answer = AiChatService::ask($defaultMessages);
        $defaultMessages[count($defaultMessages) - 1]['answer'] = $answer;

        $chat->messages()->createMany($defaultMessages);

        return $chat;
    }
    public function getChat(AiChat $chat, bool $includeMessages = false): AiChat{
        return AiChat::where("id", $chat->id)
            ->when($includeMessages, function($query) {
                $query->with([
                    "messages" => function($query) {
                        $query->orderBy("created_at", "desc");
                    }
                ]);
            })
            ->first();
    }
    public function sendMessage(AiChat $chat, string $message, string $role, int $type = 3): AiChatMessage{
        $allMessages = $chat->messages()
            ->orderBy("created_at", "desc")
            ->get()
            ->map(fn($item) => [
                'role' => $item->role,
                'content' => $item->content
            ])
            ->toArray();
        $allMessages[] = [
            'role' => $role,
            'content' => $message
        ];
        $answer = AiChatService::ask($allMessages);
        $chatMessage = $chat->messages()->create([
            'ai_chat_id' => $chat->id,
            'role' => 'user',
            'content' => $message,
            'type' => 3,
            "uuid" => Str::uuid()->toString(),
            'answer' => $answer
        ]);
        return $chatMessage;
    }
    public function getMessages(AiChat $chat): Collection {
        return AiChatMessage::where("ai_chat_id", $chat->id)
            ->orderBy("created_at", "desc")
            ->get();
    }
    public function getMessageById(AiChat $chat, int $id): AiChatMessage{
        return AiChatMessage::where("id", $id)->first();
    }
    public function getMessageByUuid(AiChat $chat, string $uuid): AiChatMessage{
        return AiChatMessage::where("uuid", $uuid)->first();
    }
}