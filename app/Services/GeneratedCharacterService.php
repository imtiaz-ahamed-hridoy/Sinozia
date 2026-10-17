<?php

namespace App\Services;

use App\Models\GeneratedCharacter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class GeneratedCharacterService
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
        // Reads the IMAGE_PREVIEW_DRIVER env key
        $this->driver = strtolower(config('services.image_preview.driver', 'openai'));

        // OpenAI config
        $this->openAiKey = config('services.openai.key') ?? '';
        $this->openAiBaseUrl = rtrim(config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $this->openAiModel = config('services.openai.image_model', 'dall-e-3');
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

    /**
     * Build HTTP client with local environment SSL fallback.
     */
    private function client(int $timeout = 300, int $connectTimeout = 30)
    {
        $client = Http::timeout($timeout)->connectTimeout($connectTimeout);
        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Build a descriptive text prompt from the character's attributes.
     */
    public function buildPrompt(GeneratedCharacter $character): string
    {
        $parts = ['A photorealistic portrait of'];

        if ($character->age) {
            $ageVal = (int) $character->age;
            if ($ageVal < 25) {
                $parts[] = 'a young adult';
            } elseif ($ageVal >= 25 && $ageVal < 40) {
                $parts[] = "a {$ageVal}-year-old adult";
            } elseif ($ageVal >= 40 && $ageVal < 60) {
                $parts[] = "a {$ageVal}-year-old middle-aged adult";
            } else {
                $parts[] = "a {$ageVal}-year-old senior adult";
            }
        } else {
            $parts[] = 'an adult';
        }

        if ($character->ethnicity) {
            $parts[] = ucfirst($character->ethnicity);
        }

        if ($character->gender) {
            $parts[] = $character->gender;
        }

        $parts[] = 'person.';

        if ($character->eye_color) {
            $parts[] = ucfirst($character->eye_color).' eyes.';
        }

        if ($character->build) {
            $buildStr = strtolower($character->build);
            if ($buildStr === 'curvy') {
                $buildStr = 'shapely, well-proportioned';
            }
            $parts[] = ucfirst($buildStr).' body build.';
        }

        if ($character->style) {
            $parts[] = 'Wearing '.$character->style.' style clothing.';
        }

        if ($character->expression) {
            $parts[] = ucfirst($character->expression).' expression.';
        }

        if ($character->custom_modification) {
            $parts[] = $character->custom_modification;
        }

        $parts[] = 'High detail, natural lighting, studio photo quality, sharp focus, photorealistic skin texture.';

        return implode(' ', $parts);
    }

    /**
     * Generate an AI portrait image for the character.
     * Uses IMAGE_PREVIEW_DRIVER to decide which provider to call.
     *
     * @return string Relative storage path on the 'public' disk
     */
    public function generate(GeneratedCharacter $character): string
    {
        $prompt = $this->buildPrompt($character);

        try {
            return match ($this->driver) {
                'nanobanana', 'gemini' => $this->generateViaNanaBanana($prompt, $character->id),
                default => $this->generateViaOpenAi($prompt, $character->id),
            };
        } catch (\Throwable $e) {
            Log::warning("GeneratedCharacterService primary driver [{$this->driver}] failed: ".$e->getMessage().'. Attempting fallback driver.');

            if ($this->driver === 'nanobanana' || $this->driver === 'gemini') {
                return $this->generateViaOpenAi($prompt, $character->id);
            } else {
                return $this->generateViaNanaBanana($prompt, $character->id);
            }
        }
    }

    // ── OpenAI — text-to-image generation

    private function generateViaOpenAi(string $prompt, int $characterId): string
    {
        $url = "{$this->openAiBaseUrl}/images/generations";

        Log::info('GeneratedCharacterService [OpenAI] REQUEST', [
            'url' => $url,
            'model' => $this->openAiModel,
            'size' => $this->openAiSize,
            'prompt' => $prompt,
            'characterId' => $characterId,
        ]);

        $payload = [
            'model' => $this->openAiModel,
            'prompt' => $prompt,
            'n' => 1,
            'size' => $this->openAiSize,
        ];

        $response = $this->client(300, 30)
            ->retry(2, 5000)
            ->withToken($this->openAiKey, 'Bearer')
            ->post($url, $payload);

        if (! $response->ok()) {
            Log::error('GeneratedCharacterService [OpenAI] ERROR', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException("OpenAI API error [{$response->status()}]: ".$response->body());
        }

        $data = $response->json();
        $b64 = $data['data'][0]['b64_json'] ?? null;
        $imgUrl = $data['data'][0]['url'] ?? null;

        if ($b64) {
            return $this->saveBase64Image($b64, $characterId);
        }

        if ($imgUrl) {
            $imageContent = $this->client(60)->get($imgUrl)->body();
            $path = "generated-characters/{$characterId}/portrait.jpg";
            Storage::disk('public')->put($path, $imageContent);

            return $path;
        }

        Log::error('GeneratedCharacterService [OpenAI] MISSING IMAGE', ['response' => $data]);
        throw new RuntimeException('No image returned from OpenAI API.');
    }

    // ── NanaBanana (Gemini) — text-to-image generation

    private function generateViaNanaBanana(string $prompt, int $characterId): string
    {
        $url = "{$this->nanaBananaBaseUrl}/{$this->nanaBananaModel}:generateContent?key={$this->nanaBananaKey}";

        Log::info('GeneratedCharacterService [NanaBanana] REQUEST', [
            'url' => $url,
            'model' => $this->nanaBananaModel,
            'prompt' => $prompt,
            'characterId' => $characterId,
        ]);

        $response = $this->client(300, 30)
            ->retry(2, 5000)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseModalities' => ['TEXT', 'IMAGE'],
                ],
            ]);

        if (! $response->ok()) {
            Log::error('GeneratedCharacterService [NanaBanana] ERROR', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
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
            Log::error('GeneratedCharacterService [NanaBanana] MISSING IMAGE', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('No image returned from NanaBanana API.');
        }

        return $this->saveBase64Image($b64, $characterId);
    }

    // ── Shared helper

    private function saveBase64Image(string $base64, int $characterId): string
    {
        $path = "generated-characters/{$characterId}/portrait.jpg";
        Storage::disk('public')->put($path, base64_decode($base64));

        return $path;
    }
}
