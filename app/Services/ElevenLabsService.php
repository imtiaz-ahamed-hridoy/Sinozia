<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class ElevenLabsService
{
    private string $apiKey;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.elevenlabs.key') ?? '';
        $baseUrl = config('services.elevenlabs.base_url') ?: 'https://api.elevenlabs.io/v1';
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    private function client(int $timeout = 180)
    {
        $client = Http::withToken($this->apiKey)->timeout($timeout);
        if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
            $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Full voice swap pipeline per client requirements:
     *
     * 1. Extract audio from Kling final video (FFmpeg)
     * 2. Send extracted audio + elevenlabs_voice_id to Speech-to-Speech API
     * 3. ElevenLabs returns same audio with different voice (timing preserved)
     * 4. Merge transformed audio back into video (FFmpeg)
     */
    public function processVoiceSwap(
        string $videoPath,
        string $projectId,
        string $elevenLabsVoiceId
    ): string {

        $fullVideoPath = Storage::disk('public')->path($videoPath);

        // Step 1 — Extract audio from Kling video
        $extractedAudioPath = $this->extractAudio($fullVideoPath, $projectId);

        // Step 2 — Voice changer: same audio, different voice
        $transformedAudioPath = $this->speechToSpeech($extractedAudioPath, $elevenLabsVoiceId, $projectId);

        // Step 3 — Merge transformed audio back into video
        $finalLocalPath = $this->mergeAudioIntoVideo($fullVideoPath, $transformedAudioPath, $projectId);

        // Step 4 — Save and cleanup
        $relativePath = "projects/{$projectId}/final_with_voice.mp4";
        Storage::disk('public')->put($relativePath, file_get_contents($finalLocalPath));

        $this->cleanup($extractedAudioPath, $transformedAudioPath, $finalLocalPath);

        return $relativePath;
    }

    /**
     * Step 1 — FFmpeg: extract audio track from video.
     */
    private function extractAudio(string $videoPath, string $projectId): string
    {
        $ffmpeg = config('services.ffmpeg.path', 'ffmpeg');
        $outputPath = storage_path("app/temp/{$projectId}_extracted.mp3");

        $this->ensureDir($outputPath);

        $process = new Process([
            $ffmpeg,
            '-i', $videoPath,
            '-vn', // strip video, keep audio only
            '-acodec', 'mp3',
            '-q:a', '2',
            '-y', $outputPath,
        ]);

        $process->setTimeout(120)->run();
        if (! $process->isSuccessful()) {
            throw new RuntimeException('FFmpeg audio extraction failed: '.$process->getErrorOutput());
        }

        return $outputPath;
    }

    /**
     * Step 2 — ElevenLabs Speech-to-Speech (Voice Changer).
     */
    private function speechToSpeech(string $audioPath, string $voiceId, string $projectId): string
    {
        $outputPath = storage_path("app/temp/{$projectId}_transformed.mp3");
        $this->ensureDir($outputPath);

        $url = "{$this->baseUrl}/speech-to-speech/{$voiceId}";

        $response = $this->client(180)
            ->attach('audio', file_get_contents($audioPath), 'audio.mp3')
            ->post($url, [
                'model_id' => 'eleven_english_sts_v2',
                'voice_settings' => json_encode([
                    'stability' => 0.5,
                    'similarity_boost' => 0.75,
                    'style' => 0.0,
                    'use_speaker_boost' => true,
                ]),
            ]);

        if (! $response->ok()) {
            throw new RuntimeException("ElevenLabs Speech-to-Speech error [{$response->status()}]: ".$response->body());
        }

        file_put_contents($outputPath, $response->body());
        if (filesize($outputPath) === 0) {
            throw new RuntimeException('ElevenLabs returned empty audio — check voice ID is valid.');
        }

        return $outputPath;
    }

    /**
     * Step 3 — FFmpeg: replace video audio with transformed audio.
     */
    private function mergeAudioIntoVideo(string $videoPath, string $audioPath, string $projectId): string
    {
        $ffmpeg = config('services.ffmpeg.path', 'ffmpeg');
        $outputPath = storage_path("app/temp/{$projectId}_final.mp4");

        $this->ensureDir($outputPath);

        $process = new Process([
            $ffmpeg,
            '-i', $videoPath,
            '-i', $audioPath,
            '-c:v', 'copy',
            '-c:a', 'aac',
            '-map', '0:v:0',
            '-map', '1:a:0',
            '-shortest',
            '-y', $outputPath,
        ]);

        $process->setTimeout(300)->run();
        if (! $process->isSuccessful()) {
            throw new RuntimeException('FFmpeg merge failed: '.$process->getErrorOutput());
        }

        return $outputPath;
    }

    private function ensureDir(string $filePath): void
    {
        $dir = dirname($filePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function cleanup(string ...$paths): void
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
