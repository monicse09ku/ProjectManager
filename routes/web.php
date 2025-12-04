<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Client;

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

Route::get('clients', function () {
    $clients = Client::all();
    return Inertia::render('Client', [
        'clients' => $clients,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
