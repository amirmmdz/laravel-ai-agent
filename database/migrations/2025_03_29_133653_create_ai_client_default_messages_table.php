<?php

use App\Models\AiClient;
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
        Schema::create('ai_client_default_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AiClient::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('content');
            $table->integer('ordering');
            $table->string('role')->default('system');
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_client_default_messages');
    }
};
