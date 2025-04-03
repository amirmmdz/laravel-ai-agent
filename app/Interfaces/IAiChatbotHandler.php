<?php

namespace App\Interfaces;

use App\Models\AiChat;
use App\Models\AiChatMessage;
use App\Models\AiClient;
use Illuminate\Database\Eloquent\Collection;


interface IAiChatbotHandler
{
    public function startChat(AiClient $client, string $userMessage);
    public function getChat(AiChat $chat, bool $includeMessages = false): AiChat;
    public function sendMessage(AiChat $chat, string $message, string $role, int $type): AiChatMessage;
    public function getMessages(AiChat $chat): Collection;
    public function getMessageByUuid(AiChat $chat, string $uuid): AiChatMessage;
    public function getMessageById(AiChat $chat, int $id): AiChatMessage;
}