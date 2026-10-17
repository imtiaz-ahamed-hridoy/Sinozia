<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Storage;

class ProjectCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Project $project) {}

    public function broadcastOn()
    {
        return [new PrivateChannel("user.{$this->project->user_id}")];
    }

    public function broadcastWith()
    {
        return [
            'project_id' => $this->project->id,
            'status' => 'completed',
            'final_video_url' => Storage::disk('public')->url($this->project->final_video_path),
        ];
    }

    public function broadcastAs()
    {
        return 'ProjectCompleted';
    }
}
