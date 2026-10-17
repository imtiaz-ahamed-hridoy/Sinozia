<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ExtractVideoFrameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public Project $project) {}

    public function handle(): void
    {
        $this->project->update(['status' => 'extracting_frame']);

        $videoPath = Storage::disk('public')->path($this->project->original_video_path);
        $outputDir = Storage::disk('public')->path("projects/{$this->project->id}");
        $outputPath = $outputDir.DIRECTORY_SEPARATOR.'base_frame.jpg';

        // Create output directory if it doesn't exist
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $configured = config('services.ffmpeg.path', '/usr/bin/ffmpeg');
        $ffmpeg = (PHP_OS_FAMILY === 'Windows' && ! file_exists($configured)) ? 'ffmpeg' : $configured;

        $process = new Process([
            $ffmpeg,
            '-i', $videoPath,
            '-vframes', '1',
            '-q:v', '2',
            '-y', $outputPath,
        ]);

        $process->setTimeout(60)->run();

        if (! $process->isSuccessful() || ! file_exists($outputPath)) {
            Log::warning('FFmpeg frame extraction warning: '.$process->getErrorOutput().'. Using fallback image.');

            if ($this->project->target_character_path && Storage::disk('public')->exists($this->project->target_character_path)) {
                Storage::disk('public')->copy($this->project->target_character_path, "projects/{$this->project->id}/base_frame.jpg");
            } else {
                $this->project->update([
                    'status' => 'failed',
                    'error_message' => 'FFmpeg frame extraction failed: '.$process->getErrorOutput(),
                ]);

                return;
            }
        }

        $relativePath = "projects/{$this->project->id}/base_frame.jpg";

        $this->project->update([
            'base_image_path' => $relativePath,
        ]);

        // Chain next job
        GenerateLLMPromptJob::dispatch($this->project);
    }

    public function failed(\Throwable $e): void
    {
        $this->project->update([
            'status' => 'failed',
            'error_message' => 'ExtractVideoFrameJob exception: '.$e->getMessage(),
        ]);
    }
}
