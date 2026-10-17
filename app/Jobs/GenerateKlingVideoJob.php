<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\KlingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateKlingVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600; // 10 min max

    public function __construct(public Project $project) {}

    public function handle(KlingService $kling): void
    {
        $this->project->update(['status' => 'generating_video']);

        $taskId = $kling->submitVideoGeneration(
            $this->project->original_video_path,
            $this->project->preview_image_path,
            $this->project->quality
        );

        $this->project->update(['kling_task_id' => $taskId]);

        // Poll every 30 seconds, start in 5 seconds
        PollKlingTaskJob::dispatch($this->project)->delay(now()->addSeconds(5));
    }
}
