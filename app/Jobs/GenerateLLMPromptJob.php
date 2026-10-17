<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\LLM\LLMVisionFactory;
use App\Services\LLM\SystemPromptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateLLMPromptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public Project $project) {}

    public function handle(SystemPromptService $promptService): void
    {
        $this->project->update(['status' => 'generating_prompt']);

        $provider = app(LLMVisionFactory::class)::make(); // reads LLM_PROVIDER from .env

        // $systemPrompt = $promptService->get($this->project->transformation_type);
        $systemPrompt = $promptService->buildFromProject($this->project->transformation_type);

        $generatedPrompt = $provider->generatePrompt(
            Storage::disk('public')->path($this->project->base_image_path),
            Storage::disk('public')->path($this->project->target_character_path),
            $systemPrompt
        );

        $this->project->update([
            'llm_generated_prompt' => $generatedPrompt,
            'llm_provider_used' => config('services.llm.provider'),
        ]);

        GenerateNanoBananaPreviewJob::dispatch($this->project);
    }
}
