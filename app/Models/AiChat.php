<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChat extends Model
{
    protected $fillable = [
        "ai_client_id",
        "uuid",
        "model_name",
        "model_exact_name",
    ];

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'ai_chat_id');
    }
    public function ai_client()
    {
        return $this->belongsTo(AiClient::class, 'ai_client_id');
    }
}
