<?php

namespace App\Services;

use OpenAI;
use OpenAI\Client;

class AiChatService
{
    public static function ask(array $allMessages = null) : string
    {
        $client = app(OpenAI::class);

        $allMessages = array_map(fn($item) => [
            'role' => $item['role'],
            'content' => $item['content']
        ], $allMessages);

        $response = $client->chat()->create([
            'model' => env('AI_DEEPSEEK_MODEL_NAME', 'deepseek/deepseek-r1:free'),
            'messages' => array_filter($allMessages),
        ]);

        return $response->choices[0]->message->content;
    }
}