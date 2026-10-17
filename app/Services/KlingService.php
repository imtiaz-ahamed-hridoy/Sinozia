<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class KlingService
{
    private string $accessKey;

    private string $secretKey;

    private string $baseUrl;

    private string $model;

    public function __construct()
    {
        $this->accessKey = config('services.kling.access_key') ?? '';
        $this->secretKey = config('services.kling.secret_key') ?? '';
        $baseUrl = config('services.kling.base_url') ?: 'https://api.klingai.com';
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->model = config('services.kling.model', 'kling-v2-6');
    }

    private function client(int $timeout = 60)
    {
        $client = Http::withToken($this->generateJWT())->timeout($timeout);
        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        return $client;
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function generateJWT(): string
    {
        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]));

        $payload = $this->base64UrlEncode(json_encode([
            'iss' => $this->accessKey,
            'exp' => time() + 1800,
            'nbf' => time() - 5,
        ]));

        $signature = hash_hmac('sha256', "$header.$payload", $this->secretKey, true);
        $signature = $this->base64UrlEncode($signature);

        return "$header.$payload.$signature";
    }

    public function submitVideoGeneration(string $videoPath, string $previewImagePath, string $mode = 'std'): string
    {
        $videoUrl = Storage::disk('public')->url($videoPath);
        $imageUrl = Storage::disk('public')->url($previewImagePath);

        Log::info("Submitting to Kling ({$this->baseUrl}) with video URL: {$videoUrl} and image URL: {$imageUrl}");

        $url = "{$this->baseUrl}/v1/videos/motion-control";

        $response = $this->client(60)->post($url, [
            'model_name' => $this->model,
            'image_url' => $imageUrl,
            'video_url' => $videoUrl,
            'prompt' => 'Generated from Laravel system',
            'keep_original_sound' => 'yes',
            'character_orientation' => 'image',
            'mode' => $mode, // std | pro
            'callback_url' => '',
            'external_task_id' => uniqid('kling_'),
        ]);

        if (! $response->ok()) {
            throw new RuntimeException("Kling submit failed [{$response->status()}]: ".$response->body());
        }

        $data = $response->json();

        if (! isset($data['data']['task_id'])) {
            throw new RuntimeException('Kling did not return task_id: '.json_encode($data));
        }

        return $data['data']['task_id'];
    }

    public function pollTask(string $taskId): array
    {
        $url = "{$this->baseUrl}/v1/videos/motion-control/{$taskId}";
        $response = $this->client(30)->get($url);

        if (! $response->ok()) {
            throw new RuntimeException("Kling poll failed [{$response->status()}]: ".$response->body());
        }

        return $response->json('data') ?? [];
    }
}
