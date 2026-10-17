<?php

namespace App\Jobs;

use App\Events\PreviewReady;
use App\Models\Project;
use App\Services\GptImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateNanoBananaPreviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300; // job level

    public int $backoff = 30;  // wait 30s before retry

    public function __construct(public Project $project) {}

    public function handle(GptImageService $nanaBanana): void
    {
        $this->project->update(['status' => 'generating_preview']);

        $prompt = 'Replace the entire person in the first photo with the entire person in the second photo. Maintain the same pose as the person in the first photo.';
        // $previewPath = $nanaBanana->generatePreview(
        //     baseImagePath: $this->project->base_image_path,
        //     targetImagePath: $this->project->target_character_path,
        //     prompt: $this->project->llm_generated_prompt,
        //     projectId: $this->project->id,
        //     quality: $this->project->quality ?? 'std',
        // );
        $previewPath = $nanaBanana->generatePreview(
            baseImagePath: $this->project->base_image_path,
            targetImagePath: $this->project->target_character_path,
            prompt: $prompt,
            projectId: $this->project->id,
            quality: $this->project->quality ?? 'std',
        );

        $this->project->update([
            'preview_image_path' => $previewPath,
            'status' => 'awaiting_user_validation',
        ]);

        broadcast(new PreviewReady($this->project));
    }

    public function failed(\Throwable $e): void
    {
        $this->project->update([
            'status' => 'failed',
            'error_message' => 'Preview generation failed: '.$e->getMessage(),
        ]);
    }
}
