<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponse;
use App\Models\Task;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Services\SlugGenerator;
use App\Jobs\SendTaskCreatedNotification;

class TaskController extends Controller
{
    use ApiResponse;

    /**
     * Get all tasks - Admins see all, users see only assigned tasks
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $tasks = Task::with(['project', 'assignedUser'])->get();
        } else {
            $tasks = Task::with(['project', 'assignedUser'])
                ->where('assigned_user_id', $user->id)
                ->get();
        }

        return $this->success(TaskResource::collection($tasks), 'Tasks retrieved successfully');
    }

    /**
     * Show a single task
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return $this->success(new TaskResource($task->load(['project', 'assignedUser'])), 'Task retrieved successfully');
    }

    /**
     * Create a task - Only admins
     */
    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();
        $validated['slug'] = (new SlugGenerator())->generate(Task::class, $validated['title']);

        $task = Task::create($validated);

        // Dispatch email notification job
        SendTaskCreatedNotification::dispatch($task);

        return $this->success(new TaskResource($task->load(['project', 'assignedUser'])), 'Task created successfully', 201);
    }

    /**
     * Update a task - Admins can update all, users can update deadline and status only
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $user = auth()->user();
        $validated = $request->validated();

        // Validate field restrictions for users
        if ($user->role === 'user') {
            $fields = array_keys($validated);
            if (!$this->authorize('updateFields', [$task, $fields])) {
                return $this->error('You can only update deadline and status fields', 403);
            }
        } else {
            // Admin updating - regenerate slug if title changed
            if (isset($validated['title'])) {
                $validated['slug'] = (new SlugGenerator())->generate(Task::class, $validated['title'], $task->id);
            }

            // Check if assigned user changed for notification
            if (isset($validated['assigned_user_id']) && $task->assigned_user_id !== $validated['assigned_user_id']) {
                $task->update($validated);
                SendTaskCreatedNotification::dispatch($task);
            } else {
                $task->update($validated);
            }

            return $this->success(new TaskResource($task->load(['project', 'assignedUser'])), 'Task updated successfully');
        }

        // User updating their task (only deadline/status)
        $task->update($validated);

        return $this->success(new TaskResource($task->load(['project', 'assignedUser'])), 'Task updated successfully');
    }

    /**
     * Delete a task - Only admins
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return $this->success(null, 'Task deleted successfully');
    }
}
