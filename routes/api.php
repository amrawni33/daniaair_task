<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/auth/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/register', [UserController::class, 'register'])->middleware('role:Admin');
    
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->middleware('permission:view tasks');
        Route::post('/', [TaskController::class, 'store'])->middleware('permission:create tasks');
        Route::put('/{task}', [TaskController::class, 'update'])->middleware('permission:edit tasks');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->middleware('permission:delete tasks');
    });

    Route::put('task/assign-to-user/{task}', [TaskController::class, 'assignTaskToUser'])->middleware('permission:assign tasks');
    Route::put('task/update-task-status/{task}', [TaskController::class, 'updateTaskStatus'])->middleware('permission:update task status');

    Route::get('tasks-efficiency', [ManagerController::class, 'taskEfficiency'])->middleware('permission:view tasks efficiency');
    Route::get('apply-round-robin', [ManagerController::class, 'assignTasksRoundRobin'])->middleware('permission:task auto assign by round-robin');

    Route::get('admin-dashboard', [AdminController::class, 'index'])->middleware('role:Admin');
});

