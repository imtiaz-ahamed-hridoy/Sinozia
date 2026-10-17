<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GptImageService
{
    private string $driver;

    // ── OpenAI
    private string $openAiKey;

    private string $openAiBaseUrl;

    private string $openAiModel;

    private string $openAiSize;

    // ── NanaBanana (Gemini)
    private string $nanaBananaKey;

    private string $nanaBananaBaseUrl;

    private string $nanaBananaModel;

    public function __construct()
    {
        $this->driver = strtolower(config('services.image_preview.driver', 'openai'));

        // OpenAI config
        $this->openAiKey = config('services.openai.key') ?? '';
        $this->openAiBaseUrl = rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $this->openAiModel = config('services.openai.image_model', 'gpt-image-2');
        $this->openAiSize = config('services.openai.image_size', '1024x1024');

        // NanaBanana (Gemini) config with URL normalization & key fallback
        $this->nanaBananaKey = config('services.nanobanana.key') ?: (config('services.gemini.key') ?? '');
        $baseUrl = config('services.nanobanana.base_url') ?: 'https://generativelanguage.googleapis.com/v1beta/models';
        $baseUrl = rtrim($baseUrl, '/');
        if (! str_contains($baseUrl, '/models')) {
            $baseUrl .= '/models';
        }
        $this->nanaBananaBaseUrl = $baseUrl;
        $this->nanaBananaModel = config('services.nanobanana.model', 'gemini-3.1-flash-image-preview');
    }

    private function client(int $timeout = 600, int $connectTimeout = 60)
    {
        $client = Http::timeout($timeout)->connectTimeout($connectTimeout);
        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Generate a character-swap preview image.
     *
     * @param  string  $baseImagePath  Relative path on the 'public' disk (base / scene image)
     * @param  string  $targetImagePath  Relative path on the 'public' disk (target character)
     * @param  string  $prompt  Editing instruction
     * @param  string  $projectId  Used to build the output storage path
     * @param  string  $quality  Unused for OpenAI edits; kept for interface parity
     * @return string Relative path of the saved preview on the 'public' disk
     */
    public function generatePreview(string $baseImagePath, string $targetImagePath, string $prompt, string $projectId, string $quality = 'std'): string
    {
        try {
            return match ($this->driver) {
                'nanobanana', 'gemini' => $this->generateViaNanaBanana($baseImagePath, $targetImagePath, $prompt, $projectId),
                default => $this->generateViaOpenAi($baseImagePath, $targetImagePath, $prompt, $projectId),
            };
        } catch (\Throwable $e) {
            Log::warning("GptImageService primary driver [{$this->driver}] failed: ".$e->getMessage().'. Attempting fallback driver.');

            if ($this->driver === 'nanobanana' || $this->driver === 'gemini') {
                return $this->generateViaOpenAi($baseImagePath, $targetImagePath, $prompt, $projectId);
            } else {
                return $this->generateViaNanaBanana($baseImagePath, $targetImagePath, $prompt, $projectId);
            }
        }
    }

    // ── OpenAI — Image Editing

    private function generateViaOpenAi(string $baseImagePath, string $targetImagePath, string $prompt, string $projectId): string
    {
        $url = "{$this->openAiBaseUrl}/images/edits";
        $baseImageBytes = Storage::disk('public')->get($baseImagePath);
        $targetImageBytes = Storage::disk('public')->get($targetImagePath);

        if (! $baseImageBytes || ! $targetImageBytes) {
            throw new RuntimeException('One or both source images could not be read from storage.');
        }

        Log::info('GptImageService [OpenAI] REQUEST', [
            'url' => $url,
            'model' => $this->openAiModel,
            'size' => $this->openAiSize,
            'prompt' => $prompt,
            'projectId' => $projectId,
            'baseImagePath' => $baseImagePath,
            'targetImagePath' => $targetImagePath,
        ]);

        $response = $this->client(600, 60)
            ->retry(2, 5000)
            ->withToken($this->openAiKey, 'Bearer')
            ->attach('image[]', $baseImageBytes, 'base_image.jpg', ['Content-Type' => 'image/jpeg'])
            ->attach('image[]', $targetImageBytes, 'target_image.jpg', ['Content-Type' => 'image/jpeg'])
            ->post($url, [
                'model' => $this->openAiModel,
                'prompt' => $prompt,
                'n' => 1,
                'size' => $this->openAiSize,
            ]);

        if (! $response->ok()) {
            Log::error('GptImageService [OpenAI] ERROR', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException("OpenAI API error [{$response->status()}]: ".$response->body());
        }

        $data = $response->json();
        $b64 = $data['data'][0]['b64_json'] ?? null;
        $imgUrl = $data['data'][0]['url'] ?? null;

        if ($b64) {
            return $this->saveBase64Image($b64, $projectId);
        }

        if ($imgUrl) {
            $imageContent = $this->client(60)->get($imgUrl)->body();
            $path = "projects/{$projectId}/preview.jpg";
            Storage::disk('public')->put($path, $imageContent);

            return $path;
        }

        Log::error('GptImageService [OpenAI] MISSING IMAGE', ['response' => $data]);
        throw new RuntimeException('No image returned from OpenAI API.');
    }

    // ── NanaBanana (Gemini)

    private function generateViaNanaBanana(string $baseImagePath, string $targetImagePath, string $prompt, string $projectId): string
    {
        $url = "{$this->nanaBananaBaseUrl}/{$this->nanaBananaModel}:generateContent?key={$this->nanaBananaKey}";

        $baseImage = base64_encode(Storage::disk('public')->get($baseImagePath));
        $targetImage = base64_encode(Storage::disk('public')->get($targetImagePath));

        Log::info('GptImageService [NanaBanana] REQUEST', [
            'url' => $url,
            'projectId' => $projectId,
            'prompt' => $prompt,
        ]);

        $response = $this->client(600, 60)
            ->retry(2, 5000)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => $baseImage]],
                            ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => $targetImage]],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseModalities' => ['TEXT', 'IMAGE'],
                ],
            ]);

        if (! $response->ok()) {
            Log::error('GptImageService [NanaBanana] ERROR', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException("NanaBanana API error [{$response->status()}]: ".$response->body());
        }

        $data = $response->json();
        $parts = $data['candidates'][0]['content']['parts'] ?? [];
        $b64 = null;

        foreach ($parts as $part) {
            if (! empty($part['inlineData']['data'])) {
                $b64 = $part['inlineData']['data'];
                break;
            }
        }

        if (! $b64) {
            Log::error('GptImageService [NanaBanana] MISSING IMAGE', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException('No image returned from NanaBanana API.');
        }

        return $this->saveBase64Image($b64, $projectId);
    }

    // ── Shared helpers

    private function saveBase64Image(string $base64, string $projectId): string
    {
        $path = "projects/{$projectId}/preview.jpg";
        Storage::disk('public')->put($path, base64_decode($base64));

        return $path;
    }
}
