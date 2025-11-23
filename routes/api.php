<?php

use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\TaskController as ApiTaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'profile']);

    // Legacy routes
    // Route::get('/projects', [ProjectController::class, 'getProjects']);
    // Route::get('/projects/{projectId}', [ProjectController::class, 'getProjects']);
    // Route::get('/projects/{projectId}/tasks', [TaskController::class, 'getProjectTasks']);
    // Route::get('/projects/{projectId}/tasks/{taskId}', [TaskController::class, 'getTaskDetails']);

    // New API routes for Programs
    Route::prefix('programs')->group(function () {
        Route::get('/', [ProgramController::class, 'index']); // GET /api/programs
        Route::get('/{id}', [ProgramController::class, 'show']); // GET /api/programs/{id}
    });

    // New API routes for Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [ApiTaskController::class, 'index']); // GET /api/tasks
        Route::get('/{id}', [ApiTaskController::class, 'show']); // GET /api/tasks/{id}
    });
});
