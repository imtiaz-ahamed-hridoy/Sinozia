<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectFailed implements ShouldBroadcast
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
            'status' => 'failed',
            'error_message' => $this->project->error_message,
        ];
    }

    public function broadcastAs()
    {
        return 'ProjectFailed';
    }
}
