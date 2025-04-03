<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiClientDefaultMessage extends Model
{
    protected $fillable = [
        "ai_client_id",
        "content",
        "ordering",
        "role",
        "is_active"
    ];
    public function ai_client()
    {
        return $this->belongsTo(AiClient::class);
    }
}
