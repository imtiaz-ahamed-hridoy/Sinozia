<?php

namespace App\Services\LLM\Providers;

use App\Services\LLM\Contracts\LLMVisionContract;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIVisionProvider implements LLMVisionContract
{
    private string $apiKey;

    private string $model;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o');
        $this->baseUrl = rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
    }

    public function generatePrompt(
        string $baseImagePath,
        string $targetImagePath,
        string $systemPrompt,
        ?string $projectId = null
    ): string {
        $baseB64 = base64_encode(file_get_contents($baseImagePath));
        $targetB64 = base64_encode(file_get_contents($targetImagePath));

        $url = "{$this->baseUrl}/chat/completions";

        $client = Http::withToken($this->apiKey)->timeout(60);
        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        $response = $client->post($url, [
            'model' => $this->model,
            'max_completion_tokens' => 2000,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [

                        // 1. Full system instructions first
                        [
                            'type' => 'text',
                            'text' => $systemPrompt,
                        ],

                        // 2. Image A — base image (person from video)
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,{$baseB64}",
                                'detail' => 'high', // high detail = better face/body analysis
                            ],
                        ],

                        // 3. Image B — target character image
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,{$targetB64}",
                                'detail' => 'high',
                            ],
                        ],

                        // 4. Final instruction — clarify which image is which
                        [
                            'type' => 'text',
                            'text' => 'The first image above is IMAGE A (base image — the person from the video). The second image above is IMAGE B (target character — source of face/body/background). Now generate the editing prompt following all the rules above.',
                        ],

                    ],
                ],
            ],
        ]);

        if (! $response->ok()) {
            throw new RuntimeException("OpenAI API error [{$response->status()}]: ".$response->body());
        }

        $content = $response->json('choices.0.message.content');

        if (empty($content)) {
            throw new RuntimeException('OpenAI returned an empty response.');
        }

        return trim($content);
    }
}
