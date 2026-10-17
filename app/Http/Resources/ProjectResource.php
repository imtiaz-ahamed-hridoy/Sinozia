<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Relations
            'user_id' => $this->user_id,
            'target_character_id' => $this->target_character_id,
            'target_voice_id' => $this->target_voice_id,

            // File URLs
            'uploaded_video_url' => $this->original_video_path ? Storage::disk('public')->url($this->original_video_path) : null,
            'base_image_url' => $this->base_image_path ? Storage::disk('public')->url($this->base_image_path) : null,
            'target_character_url' => $this->target_character_path ? Storage::disk('public')->url($this->target_character_path) : null,

            'preview_image_url' => $this->preview_image_path ? Storage::disk('public')->url($this->preview_image_path) : null,
            'final_video_url' => $this->final_video_path ? Storage::disk('public')->url($this->final_video_path) : null,

            'target_character' => $this->targetCharacter ? [
                'id' => $this->targetCharacter->id,
                'name' => $this->targetCharacter->title ?? $this->targetCharacter->name ?? null,
                'image_url' => $this->targetCharacter->image ? Storage::disk('public')->url($this->targetCharacter->image) : null,
            ] : null,

            'target_voice' => $this->targetVoice ? [
                'id' => $this->targetVoice->id,
                'title' => $this->targetVoice->title,
                'name' => $this->targetVoice->title,
                'audio_url' => ($this->targetVoice->audio ?? $this->targetVoice->audio_path) ? Storage::disk('public')->url($this->targetVoice->audio ?? $this->targetVoice->audio_path) : null,
            ] : null,

            // Transformation
            'transformation_type' => $this->transformation_type,
            'voice_enabled' => (bool) $this->voice_enabled,
            'quality' => $this->quality,

            // AI / Prompt
            'llm_generated_prompt' => $this->llm_generated_prompt,
            'llm_provider_used' => $this->llm_provider_used,

            // External Providers
            'kling_task_id' => $this->kling_task_id,
            'elevenlabs_job_id' => $this->elevenlabs_job_id,

            // Status
            'status' => $this->status,
            'error_message' => $this->error_message,

            // Extra Data
            'metadata' => $this->metadata,

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
