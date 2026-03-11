<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/projects', [ProjectController::class, 'store']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects/{id}/tasks', [TaskController::class, 'store']);
Route::patch('/tasks/{id}', [TaskController::class, 'updateStatus']);
