<?php

namespace App\Services\LLM\Providers;

use App\Services\LLM\Contracts\LLMVisionContract;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiVisionProvider implements LLMVisionContract
{
    private string $apiKey;

    private string $model;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.vision_model', 'gemini-2.5-flash');
        $baseUrl = config('services.gemini.base_url') ?: 'https://generativelanguage.googleapis.com/v1beta';
        $baseUrl = rtrim($baseUrl, '/');
        if (str_ends_with($baseUrl, '/models')) {
            $baseUrl = substr($baseUrl, 0, -7);
        }
        $this->baseUrl = $baseUrl;
    }

    public function generatePrompt(string $baseImagePath, string $targetImagePath, string $systemPrompt): string
    {
        $baseB64 = base64_encode(file_get_contents($baseImagePath));
        $targetB64 = base64_encode(file_get_contents($targetImagePath));
        $mime = 'image/jpeg';

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [[
                'parts' => [
                    ['inline_data' => ['mime_type' => $mime, 'data' => $baseB64]],
                    ['inline_data' => ['mime_type' => $mime, 'data' => $targetB64]],
                    ['text' => 'Generate the NanoBanana editing prompt based on both images.'],
                ],
            ]],
            'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 1024],
        ];

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $client = Http::timeout(60);
        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        $response = $client->post($url, $payload);

        if (! $response->ok()) {
            throw new RuntimeException("Gemini API error [{$response->status()}]: ".$response->body());
        }

        return $response->json('candidates.0.content.parts.0.text') ?? throw new RuntimeException('Gemini returned empty response');
    }
}
