<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\AiClientController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['client.token'])->prefix('v1')->group(function () {
    // AI Chat routes
    Route::prefix('chats')->group(function () {
        // Create a new chat session
        Route::post('/', [AiChatController::class, 'create']);
        
        // Get chat details with optional messages
        Route::get('/{uuid}', [AiChatController::class, 'getChat']);
        
        // Get all messages for a specific chat
        Route::get('/{uuid}/messages', [AiChatController::class, 'getMessages']);
        
        // Send a new message in an existing chat
        Route::post('/{uuid}/messages', [AiChatController::class, 'sendMessage']);
        
        // Get a specific message by UUID
        Route::get('/{chatUuid}/messages/{messageUuid}', [AiChatController::class, 'getMessage']);
    });
    
    // AI Client routes - optional, depending on whether you want to expose these
    Route::prefix('clients')->group(function () {
        // Get client details
        Route::get('/', [AiClientController::class, 'getClientDetails']);
        
        // Get default messages for a client
        Route::get('/messages', [AiClientController::class, 'getDefaultMessages']);
    });
});