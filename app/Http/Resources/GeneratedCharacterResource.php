<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GeneratedCharacterResource extends JsonResource
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
            'age' => $this->age,
            'gender' => $this->gender,
            'ethnicity' => $this->ethnicity,
            'eye_color' => $this->eye_color,
            'build' => $this->build,
            'style' => $this->style,
            'expression' => $this->expression,
            'custom_modification' => $this->custom_modification,
            'generated_image_url' => $this->generated_image_path
                ? Storage::disk('public')->url($this->generated_image_path)
                : null,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'is_active' => $this->is_active,
        ];
    }
}
