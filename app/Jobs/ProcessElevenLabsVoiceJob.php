<?php

namespace App\Jobs;

use App\Events\ProjectCompleted;
use App\Models\Project;
use App\Models\TargetVoice;
use App\Services\ElevenLabsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessElevenLabsVoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 600;

    public function __construct(public Project $project) {}

    public function handle(ElevenLabsService $elevenLabs): void
    {
        $this->project->update(['status' => 'processing_voice']);

        $targetVoice = TargetVoice::findOrFail($this->project->target_voice_id);

        // elevenlabs_voice_id must not be null — validated at admin level
        if (empty($targetVoice->elevenlabs_voice_id)) {
            throw new \RuntimeException("TargetVoice ID {$targetVoice->id} has no elevenlabs_voice_id set.");
        }

        $finalVideoPath = $elevenLabs->processVoiceSwap(
            videoPath: $this->project->final_video_path,
            projectId: $this->project->id,
            elevenLabsVoiceId: $targetVoice->elevenlabs_voice_id,
        );

        $this->project->update([
            'final_video_path' => $finalVideoPath,
            'status' => 'completed',
        ]);

        broadcast(new ProjectCompleted($this->project));
    }

    public function failed(\Throwable $e): void
    {
        Log::error("ElevenLabs voice job failed for project {$this->project->id}: ".$e->getMessage());

        // Voice failed but video is ready — still mark completed without voice
        $this->project->update([
            'status' => 'completed',
            'error_message' => 'Voice cloning failed, video delivered without voice swap.',
        ]);

        broadcast(new ProjectCompleted($this->project));
    }
}
