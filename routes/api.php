<?php

use App\Http\Controllers\{AuthController, TaskController};
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'profile']);

    Route::get('/projects', [ProjectController::class, 'getProjects']);
});
