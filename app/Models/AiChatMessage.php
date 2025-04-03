<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $table = 'ai_chat_messages';
    protected $fillable = [
        'uuid',
        'ai_chat_id',
        'role',
        'content',
        'answer',
        'type'
    ];
    
    public const TYPES = [
        1 => 'default_message',
        2 => 'client_message',
        3 => 'user_message'
    ];

    public function ai_chat()
    {
        return $this->belongsTo(AiChat::class, 'ai_chat_id');
    }
}
