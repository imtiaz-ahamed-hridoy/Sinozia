<?php

namespace App\Http\Resources;

use App\Models\GeneratedCharacter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TargetCharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Support both TargetCharacter and GeneratedCharacter models without altering response structure
        if ($this->resource instanceof GeneratedCharacter) {
            $imagePath = $this->generated_image_path;

            return [
                'id' => $this->id,
                'title' => $this->title ?: 'AI Character #'.$this->id,
                'description' => "{$this->gender} {$this->ethnicity} ({$this->age}y)",
                'image' => $imagePath ? Storage::disk('public')->url($imagePath) : null,
                'is_generated' => true,
            ];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'is_generated' => false,
        ];
    }
}
