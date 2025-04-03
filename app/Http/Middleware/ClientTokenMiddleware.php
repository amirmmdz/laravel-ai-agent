<?php

namespace App\Http\Middleware;

use App\Models\AiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-AiClient-Token');
        
        if (!$token) {
            return response()->json(['error' => 'Client token is required'], 401);
        }
        
        $client = AiClient::where('token', $token)
            ->where('is_active', true)
            ->first();
            
        if (!$client) {
            return response()->json(['error' => 'Invalid or inactive client token'], 403);
        }
        
        // Add client to request for controllers to use
        $request->merge(['client' => $client]);
        
        return $next($request);
    }
}