<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Http\Controllers\Admin\ClientsController AS AdminClientsController;
use App\Http\Controllers\Admin\ProjectsController AS AdminProjectsController;
use App\Http\Controllers\Admin\TasksController AS AdminTasksController;
use App\Http\Controllers\User\TasksController AS UserTasksController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $clients = Client::all();
            $clientCount = $clients->count();
            $projectCount = Project::count();
            $taskCount = Task::count();
            
            return Inertia::render('Dashboard', [
                'clients' => $clients,
                'clientCount' => $clientCount,
                'projectCount' => $projectCount,
                'taskCount' => $taskCount,
            ]);
        } else {
            // Redirect regular users to their tasks
            return redirect()->route('user-tasks.index');
        }
    })->name('dashboard');

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::resource('clients', AdminClientsController::class);
        Route::resource('projects', AdminProjectsController::class);
        Route::resource('tasks', AdminTasksController::class);
    });

    // User routes
    Route::get('my-tasks', [UserTasksController::class, 'index'])->name('user-tasks.index');
});

require __DIR__.'/settings.php';
