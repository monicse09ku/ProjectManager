<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TaskController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks']);

    Route::apiResource('clients', ClientController::class);
    
    Route::apiResource('tasks', TaskController::class);
});
