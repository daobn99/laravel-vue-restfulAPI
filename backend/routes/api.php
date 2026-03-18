<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/posts', PostController::class);

Route::apiResource('tasks', TaskController::class);

Route::apiResource('projects.tasks', ProjectTaskController::class)->scoped()->only(['index', 'store']);
