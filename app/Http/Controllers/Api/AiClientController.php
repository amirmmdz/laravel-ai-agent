<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="AI Clients",
 *     description="API Endpoints for AI Client operations"
 * )
 */
class AiClientController extends Controller
{
    /**
     * Get client details
     * 
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="Get client details",
     *     description="Retrieves details of the authenticated client",
     *     operationId="getClientDetails",
     *     tags={"AI Clients"},
     *     security={{"clientToken": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Client details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="client",
     *                 type="object",
     *                 @OA\Property(property="uuid", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getClientDetails(Request $request)
    {
        $client = $request->client;
        
        return response()->json([
            'success' => true,
            'client' => [
                'uuid' => $client->uuid,
                'name' => $client->name,
                'description' => $client->description,
                'created_at' => $client->created_at
            ]
        ]);
    }
    
    /**
     * Get default messages for a client
     * 
     * @OA\Get(
     *     path="/api/v1/clients/messages",
     *     summary="Get default messages",
     *     description="Retrieves default messages configured for the authenticated client",
     *     operationId="getDefaultMessages",
     *     tags={"AI Clients"},
     *     security={{"clientToken": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Default messages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="messages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="role", type="string", example="system"),
     *                     @OA\Property(property="content", type="string", example="You are a helpful assistant."),
     *                     @OA\Property(property="ordering", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function getDefaultMessages(Request $request)
    {
        $client = $request->client;
        $messages = $client->messages()
            ->where('is_active', true)
            ->orderBy('ordering')
            ->get();
            
        return response()->json([
            'success' => true,
            'messages' => $messages->map(function($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                    'ordering' => $message->ordering
                ];
            })
        ]);
    }
}