<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Services\SlugGenerator;
use App\Jobs\SendTaskCreatedNotification;
use App\Http\Requests\TaskRequest;

class TasksController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project', 'assignedUser'])->get();
        $projects = Project::all();
        $users = User::select('id', 'name')->get();

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function store(TaskRequest $request)
    {
        $validated = $request->validated();

        // generate slug from title
        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Task::class, $validated['title']);

        $task = Task::create($validated);

        // Dispatch email notification job
        SendTaskCreatedNotification::dispatch($task);

        return back()->with('success', 'Task created successfully.');
    }

    public function update(TaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Task::class, $validated['title'], $task->id);

        // Check if assigned user changed
        $assignedUserChanged = $task->assigned_user_id !== $validated['assigned_user_id'];

        $task->update($validated);

        // Send email notification if assigned user changed
        if ($assignedUserChanged) {
            SendTaskCreatedNotification::dispatch($task);
        }

        return back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
