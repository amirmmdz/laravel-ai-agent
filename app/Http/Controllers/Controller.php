<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="AI Agent API",
 *     version="1.0.0",
 *     description="API documentation for the AI Agent Application",
 *     @OA\Contact(
 *         email="support@example.com",
 *         name="Support Team"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="clientToken",
 *     type="apiKey",
 *     name="X-AiClient-Token",
 *     in="header",
 *     description="Client authentication token"
 * )
 */

abstract class Controller
{
    //
}
