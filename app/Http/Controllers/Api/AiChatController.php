<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChat;
use App\Models\AiChatMessage;
use App\Services\DeepseekChatbotHandlerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="AI Chats",
 *     description="API Endpoints for AI Chat operations"
 * )
 */
class AiChatController extends Controller
{
    protected $chatbotHandler;
    
    public function __construct(DeepseekChatbotHandlerService $chatbotHandler)
    {
        $this->chatbotHandler = $chatbotHandler;
    }
    
    /**
     * Create a new chat session
     * 
     * @OA\Post(
     *     path="/api/v1/chats",
     *     summary="Create a new AI chat session",
     *     description="Starts a new chat session with the AI model using the provided message",
     *     operationId="createChat",
     *     tags={"AI Chats"},
     *     security={{"clientToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", example="Hello AI assistant", description="Initial message to start the chat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="chat",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="model_name", type="string", example="deepseek"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="messages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="uuid", type="string"),
     *                         @OA\Property(property="role", type="string", example="user"),
     *                         @OA\Property(property="content", type="string"),
     *                         @OA\Property(property="answer", type="string"),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Client token is required")
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $client = $request->client;
        $userMessage = $request->input('message');
        
        try {
            $chat = $this->chatbotHandler->startChat($client, $userMessage);
            $chat = $this->chatbotHandler->getChat($chat, true);
            
            return response()->json([
                'success' => true,
                'chat' => [
                    'uuid' => $chat->uuid,
                    'model_name' => $chat->model_name,
                    'created_at' => $chat->created_at,
                    'messages' => $chat->messages->map(function($message) {
                        return [
                            'uuid' => $message->uuid,
                            'role' => $message->role,
                            'content' => $message->content,
                            'answer' => $message->answer,
                            'created_at' => $message->created_at
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create chat: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get chat details
     * 
     * @OA\Get(
     *     path="/api/v1/chats/{uuid}",
     *     summary="Get chat details",
     *     description="Retrieves details of a specific chat by UUID",
     *     operationId="getChat",
     *     tags={"AI Chats"},
     *     security={{"clientToken": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the chat",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="include_messages",
     *         in="query",
     *         required=false,
     *         description="Whether to include messages in the response",
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="chat",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string"),
     *                 @OA\Property(property="model_name", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="messages",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="uuid", type="string"),
     *                         @OA\Property(property="role", type="string"),
     *                         @OA\Property(property="content", type="string"),
     *                         @OA\Property(property="answer", type="string"),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Chat not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getChat(Request $request, string $uuid)
    {
        $client = $request->client;
        $includeMessages = $request->boolean('include_messages', false);
        
        $chat = AiChat::where('uuid', $uuid)
            ->where('ai_client_id', $client->id)
            ->first();
            
        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }
        
        $chat = $this->chatbotHandler->getChat($chat, $includeMessages);
        
        return response()->json([
            'success' => true,
            'chat' => [
                'uuid' => $chat->uuid,
                'model_name' => $chat->model_name,
                'created_at' => $chat->created_at,
                'messages' => $includeMessages ? $chat->messages->map(function($message) {
                    return [
                        'uuid' => $message->uuid,
                        'role' => $message->role,
                        'content' => $message->content,
                        'answer' => $message->answer,
                        'created_at' => $message->created_at
                    ];
                }) : null
            ]
        ]);
    }
    
    /**
     * Get all messages for a chat
     * 
     * @OA\Get(
     *     path="/api/v1/chats/{uuid}/messages",
     *     summary="Get all messages for a chat",
     *     description="Retrieves all messages in a specific chat by UUID",
     *     operationId="getChatMessages",
     *     tags={"AI Chats"},
     *     security={{"clientToken": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the chat",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="messages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="uuid", type="string"),
     *                     @OA\Property(property="role", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="answer", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Chat not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getMessages(Request $request, string $uuid)
    {
        $client = $request->client;
        
        $chat = AiChat::where('uuid', $uuid)
            ->where('ai_client_id', $client->id)
            ->first();
            
        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }
        
        $messages = $this->chatbotHandler->getMessages($chat);
        
        return response()->json([
            'success' => true,
            'messages' => $messages->map(function($message) {
                return [
                    'uuid' => $message->uuid,
                    'role' => $message->role,
                    'content' => $message->content,
                    'answer' => $message->answer,
                    'created_at' => $message->created_at
                ];
            })
        ]);
    }
    
    /**
     * Send a new message in an existing chat
     * 
     * @OA\Post(
     *     path="/api/v1/chats/{uuid}/messages",
     *     summary="Send a new message",
     *     description="Sends a new message in an existing chat and gets AI response",
     *     operationId="sendMessage",
     *     tags={"AI Chats"},
     *     security={{"clientToken": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the chat",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", example="How can you help me?", description="Message content")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string"),
     *                 @OA\Property(property="role", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="answer", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Chat not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function sendMessage(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $client = $request->client;
        $userMessage = $request->input('message');
        
        $chat = AiChat::where('uuid', $uuid)
            ->where('ai_client_id', $client->id)
            ->first();
            
        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }
        
        try {
            $chatMessage = $this->chatbotHandler->sendMessage($chat, $userMessage, 'user');
            
            return response()->json([
                'success' => true,
                'message' => [
                    'uuid' => $chatMessage->uuid,
                    'role' => $chatMessage->role,
                    'content' => $chatMessage->content,
                    'answer' => $chatMessage->answer,
                    'created_at' => $chatMessage->created_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get a specific message by UUID
     * 
     * @OA\Get(
     *     path="/api/v1/chats/{chatUuid}/messages/{messageUuid}",
     *     summary="Get a specific message",
     *     description="Retrieves a specific message by its UUID within a chat",
     *     operationId="getMessage",
     *     tags={"AI Chats"},
     *     security={{"clientToken": {}}},
     *     @OA\Parameter(
     *         name="chatUuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the chat",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="messageUuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the message",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string"),
     *                 @OA\Property(property="role", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="answer", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat or message not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getMessage(Request $request, string $chatUuid, string $messageUuid)
    {
        $client = $request->client;
        
        $chat = AiChat::where('uuid', $chatUuid)
            ->where('ai_client_id', $client->id)
            ->first();
            
        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }
        
        $message = $this->chatbotHandler->getMessageByUuid($chat, $messageUuid);
        
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => [
                'uuid' => $message->uuid,
                'role' => $message->role,
                'content' => $message->content,
                'answer' => $message->answer,
                'created_at' => $message->created_at
            ]
        ]);
    }
}