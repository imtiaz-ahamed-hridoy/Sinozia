<?php

namespace App\Services\LLM\Providers;

use App\Services\LLM\Contracts\LLMVisionContract;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnthropicVisionProvider implements LLMVisionContract
{
    private string $apiKey;

    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key');
        $this->model = config('services.anthropic.model', 'claude-sonnet-4-20250514');
    }

    public function generatePrompt(string $baseImagePath, string $targetImagePath, string $systemPrompt): string
    {
        $baseB64 = base64_encode(file_get_contents($baseImagePath));
        $targetB64 = base64_encode(file_get_contents($targetImagePath));

        $client = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(60);

        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => 'image/jpeg',
                                'data' => $baseB64,
                            ],
                        ],
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => 'image/jpeg',
                                'data' => $targetB64,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Generate the NanoBanana editing prompt based on both images above.',
                        ],
                    ],
                ],
            ],
        ]);

        if (! $response->ok()) {
            throw new RuntimeException(
                "Anthropic API error [{$response->status()}]: ".$response->body()
            );
        }

        $content = $response->json('content.0.text');

        if (empty($content)) {
            throw new RuntimeException('Anthropic returned an empty response.');
        }

        return trim($content);
    }
}
