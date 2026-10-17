<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TargetVoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'audio' => ($this->audio ?? $this->audio_path) ? Storage::disk('public')->url($this->audio ?? $this->audio_path) : null,
            // other fields...
        ];
    }
}
