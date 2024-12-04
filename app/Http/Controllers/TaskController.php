<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();

        return response()->api([
            "tasks" => (new TaskCollection($tasks))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        return response()->api([
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->api([
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return response()->api([
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->api();
    }

    public function assignTaskToUser(Request $request, Task $task)
    {

        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($task->assigned_user) {
            return response()->api([
                'error' => 'That task is already assigned'
            ], 500);
        }
        $task->assigned_user = $request->user_id;
        $message = "A new task titled '{$task->title}' has been assigned to you.";
        $user = User::where('id', $request->user_id);
        $user->notify(new TaskNotification($task, $message));
        $task->save();
    }

    public function updateTaskStatus(Request $request, Task $task)
    {

        $request->validate([
            'status' => 'required|string|in:pending,in-progress,completed'
        ]);

        if ($task->status == $request->status) {
            return response()->api([
                'error' => 'That task is already ' . $request->status
            ], 500);
        }
        $task->status = $request->status;
        $message = "Task titled '{$task->title}' has been updated.";
        $user = User::where('id', $task->assigned_user);
        $user->notify(new TaskNotification($task, $message));
        $task->save();
    }
}
