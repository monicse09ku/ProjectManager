<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Client;
use App\Http\Controllers\Admin\ClientsController AS AdminClientsController;
use App\Http\Controllers\Admin\ProjectsController AS AdminProjectsController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $clients = Client::all();
    return Inertia::render('Dashboard', [
        'clients' => $clients,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('clients', AdminClientsController::class);
    Route::resource('projects', AdminProjectsController::class);
});

require __DIR__.'/settings.php';
