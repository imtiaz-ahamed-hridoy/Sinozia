<?php

namespace App\Jobs;

use App\Events\ProjectCompleted;
use App\Events\ProjectFailed;
use App\Models\Project;
use App\Services\KlingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PollKlingTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // each poll is one attempt; re-dispatches itself

    public int $timeout = 60;

    // Stop polling after 30 minutes total (60 polls * 30s)
    private const MAX_POLLS = 60;

    private const POLL_INTERVAL = 30; // seconds

    public function __construct(public Project $project, public int $pollCount = 0) {}

    public function handle(KlingService $kling): void
    {
        if ($this->pollCount >= self::MAX_POLLS) {
            $this->project->update([
                'status' => 'failed',
                'error_message' => 'Kling AI timed out after 30 minutes.',
            ]);
            try {
                broadcast(new ProjectFailed($this->project));
            } catch (\Throwable $e) {
                Log::warning('Failed to broadcast ProjectFailed: '.$e->getMessage());
            }

            return;
        }

        $taskData = $kling->pollTask($this->project->kling_task_id);
        $status = $taskData['task_status'] ?? 'processing'; // submitted, processing, succeed, failed

        Log::info("Kling poll #{$this->pollCount} for project {$this->project->id}: {$status}", ['taskData' => $taskData]);

        if ($status === 'succeed' || $status === 'completed') {
            $this->handleCompleted($taskData);

            return;
        }

        if ($status === 'failed') {
            $this->project->update([
                'status' => 'failed',
                'error_message' => 'Kling AI generation failed: '.($taskData['message'] ?? 'Unknown error'),
            ]);
            try {
                broadcast(new ProjectFailed($this->project));
            } catch (\Throwable $e) {
                Log::warning('Failed to broadcast ProjectFailed: '.$e->getMessage());
            }

            return;
        }

        // Still processing — re-dispatch with incremented counter
        self::dispatch($this->project, $this->pollCount + 1)->delay(now()->addSeconds(self::POLL_INTERVAL));
    }

    private function handleCompleted(array $taskData): void
    {
        // Extract from correct path
        $videoUrl = $taskData['task_result']['videos'][0]['url'] ?? null;

        Log::info("Kling video URL: {$videoUrl} for project {$this->project->id}");

        if (! $videoUrl) {
            $this->project->update([
                'status' => 'failed',
                'error_message' => 'Kling completed but returned no video URL.',
            ]);

            return;
        }

        try {
            // Download video with local SSL verification bypass
            $client = Http::timeout(300);
            if (config('app.env') === 'local' || config('services.ssl_verify') === false) {
                $client->withoutVerifying();
            }
            $response = $client->get($videoUrl);

            if (! $response->ok()) {
                throw new \Exception('Failed to download video from Kling URL: '.$response->status());
            }

            $finalPath = "projects/{$this->project->id}/final_video.mp4";

            Storage::disk('public')->put($finalPath, $response->body());

            Log::info("Saved Kling video to {$finalPath} for project {$this->project->id}");

            $this->project->update([
                'final_video_path' => $finalPath,
                'status' => $this->project->voice_enabled ? 'processing_voice' : 'completed',
            ]);

            if ($this->project->voice_enabled) {
                ProcessElevenLabsVoiceJob::dispatch($this->project);

                return;
            }

            try {
                broadcast(new ProjectCompleted($this->project));
            } catch (\Throwable $e) {
                Log::warning('Failed to broadcast ProjectCompleted: '.$e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error("Kling video download failed for project {$this->project->id}: ".$e->getMessage());
            $this->project->update([
                'status' => 'failed',
                'error_message' => 'Video download failed: '.$e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("PollKlingTaskJob failed for project {$this->project->id}: ".$e->getMessage());
    }
}
