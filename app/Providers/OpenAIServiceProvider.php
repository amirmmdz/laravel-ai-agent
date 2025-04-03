<?php

namespace App\Providers;

use App\Interfaces\IAiChatbotHandler;
use App\Services\DeepseekChatbotHandlerService;
use Illuminate\Support\ServiceProvider;
use OpenAI;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class OpenAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenAI::class, function ($app) {
            $apiKey = env('AI_API_KEY');
            $baseUrl = env('AI_BASE_URL');

            $httpClient = new Client([]);

            return OpenAI::factory()
                ->withApiKey($apiKey)
                ->withBaseUri($baseUrl)
                ->withHttpClient($httpClient)
                ->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $httpClient->send($request, [
                    'stream' => false
                ]))
                ->make();
        });

        
        $this->app->bind(IAiChatbotHandler::class, DeepseekChatbotHandlerService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
