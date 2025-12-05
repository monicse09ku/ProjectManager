<?php

namespace App\Jobs;

use App\Models\Task;
use App\Mail\TaskAssignedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTaskCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Task $task
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load the task with relations to ensure they're available for the email
        $task = Task::with(['project', 'assignedUser'])->find($this->task->id);

        if ($task && $task->assignedUser) {
            Mail::to($task->assignedUser->email)->send(new TaskAssignedNotification($task));
        }
    }
}
