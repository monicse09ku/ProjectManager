<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the user's assigned tasks.
     */
    public function index()
    {
        $user = auth()->user();
        $tasks = Task::with(['project', 'assignedUser'])
            ->where('assigned_user_id', $user->id)
            ->get();

        return Inertia::render('user-tasks/Index', [
            'tasks' => $tasks,
        ]);
    }
}
