<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Services\SlugGenerator;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_user' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'status' => 'required|string',
        ]);

        // generate slug from title
        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Task::class, $validated['title']);

        Task::create($validated);

        return back()->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_user' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'status' => 'required|string',
        ]);

        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Task::class, $validated['title'], $task->id);

        $task->update($validated);

        return back()->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
