<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiClient extends Model
{
    protected $fillable = [
        "user_id",
        "uuid",
        "name",
        "token",
        "is_active",
        "description"
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function messages(){
        return $this->hasMany(AiClientDefaultMessage::class);
    }
}
