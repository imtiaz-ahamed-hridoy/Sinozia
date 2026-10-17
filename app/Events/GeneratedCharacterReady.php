<?php

namespace App\Events;

use App\Models\GeneratedCharacter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Storage;

class GeneratedCharacterReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public GeneratedCharacter $generatedCharacter) {}

    public function broadcastOn()
    {
        return [new PrivateChannel("user.{$this->generatedCharacter->user_id}")];
    }

    public function broadcastWith()
    {
        return [
            'generated_character_id' => $this->generatedCharacter->id,
            'status' => $this->generatedCharacter->status,
            'image_url' => $this->generatedCharacter->generated_image_path
                ? Storage::disk('public')->url($this->generatedCharacter->generated_image_path)
                : null,
        ];
    }

    public function broadcastAs()
    {
        return 'GeneratedCharacterReady';
    }
}
