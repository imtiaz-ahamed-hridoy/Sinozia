<?php

namespace App\Console\Commands;

use App\Jobs\PollKlingTaskJob;
use App\Models\Project;
use Illuminate\Console\Command;

class PollPendingKlingTasks extends Command
{
    protected $signature = 'kling:poll-pending';

    protected $description = 'Re-queue any Kling tasks stuck in generating_video status';

    public function handle(): void
    {
        // Safety net: catch any projects stuck in generating_video
        // that lost their polling job (e.g. after a server restart)
        $stuck = Project::where('status', 'generating_video')
            ->whereNotNull('kling_task_id')
            ->where('updated_at', '<', now()->subMinutes(2)) // only if idle >2 min
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('No stuck Kling tasks found.');

            return;
        }

        foreach ($stuck as $project) {
            PollKlingTaskJob::dispatch($project, 0);
            $this->info("Re-queued project {$project->id}");
        }

        $this->info("Re-queued {$stuck->count()} stuck project(s).");
    }
}
