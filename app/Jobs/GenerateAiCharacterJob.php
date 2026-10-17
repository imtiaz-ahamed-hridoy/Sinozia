<?php

namespace App\Jobs;

use App\Events\GeneratedCharacterReady;
use App\Models\GeneratedCharacter;
use App\Services\GeneratedCharacterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAiCharacterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public int $backoff = 30;

    public function __construct(public GeneratedCharacter $generatedCharacter) {}

    public function handle(GeneratedCharacterService $service): void
    {
        $this->generatedCharacter->update(['status' => 'generating']);

        $imagePath = $service->generate($this->generatedCharacter);

        $this->generatedCharacter->update([
            'generated_image_path' => $imagePath,
            'status' => 'completed',
        ]);

        try {
            broadcast(new GeneratedCharacterReady($this->generatedCharacter));
        } catch (\Throwable $e) {
            Log::warning('GenerateAiCharacterJob failed to broadcast event: '.$e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        $this->generatedCharacter->update([
            'status' => 'failed',
            'error_message' => 'Character generation failed: '.$e->getMessage(),
        ]);
    }
}
