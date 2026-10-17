<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class NanaBananaService
{
    private string $apiKey;

    private string $baseUrl;

    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.nanobanana.key');
        $this->baseUrl = config('services.nanobanana.base_url');
        $this->model = config('services.nanobanana.model', 'gemini-3.1-flash-image-preview');
    }

    /**
     * Send base image + target image + LLM prompt to NanaBanana.
     * Returns the preview image saved to storage and returns its relative path.
     */
    public function generatePreview(
        string $baseImagePath,
        string $targetImagePath,
        string $prompt,
        string $projectId,
        string $quality = 'std'
    ): string {

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $baseImage = base64_encode(Storage::disk('public')->get($baseImagePath));
        $targetImage = base64_encode(Storage::disk('public')->get($targetImagePath));

        Log::info('NanoBanana REQUEST DATA', [
            'baseImage' => $baseImage,
            'targetImage' => $targetImage,
            'prompt' => $prompt,
            'projectId' => $projectId,
        ]);

        $response = Http::timeout(600)
            ->connectTimeout(60)
            ->retry(2, 5000)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => 'image/jpeg',
                                    'data' => $baseImage,
                                ],
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => 'image/jpeg',
                                    'data' => $targetImage,
                                ],
                            ],
                        ],
                    ],
                ],

                'generationConfig' => [
                    'responseModalities' => ['TEXT', 'IMAGE'],
                ],
            ]);

        if (! $response->ok()) {
            throw new RuntimeException("API error [{$response->status()}]: ".$response->body());
        }

        $data = $response->json();

        Log::info('NanoBanana API RESPONSE', [
            'response' => $data,
        ]);

        $parts = $data['candidates'][0]['content']['parts'] ?? [];

        $base64 = null;

        foreach ($parts as $part) {
            if (! empty($part['inlineData']['data'])) {
                $base64 = $part['inlineData']['data'];
                break;
            }
        }

        if (! $base64) {
            Log::error('NanoBanana FAILED RESPONSE', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('No image returned from API.');
        }

        return $this->saveBase64Image($base64, $projectId);
    }

    private function saveBase64Image(string $base64, string $projectId): string
    {
        $path = "projects/{$projectId}/preview.jpg";
        Storage::disk('public')->put($path, base64_decode($base64));

        return $path;
    }

    private function downloadAndSaveImage(string $url, string $projectId): string
    {
        $imageContent = Http::timeout(60)->get($url)->body();
        $path = "projects/{$projectId}/preview.jpg";
        Storage::disk('public')->put($path, $imageContent);

        return $path;
    }
}
