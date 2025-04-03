<?php

use App\Models\AiChat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignIdFor(AiChat::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('role')->comment('The role of the message sender');
            $table->longText('content')->comment('The content of the message');
            $table->longText('answer')->nullable()->comment('The answer of the message');
            $table->tinyInteger('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};
