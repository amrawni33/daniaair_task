<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCollection;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    private TaskService $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function taskEfficiency()
    {
        $taskEfficiency = $this->taskService->calculateEfficiency();

        return response()->api([
            'task_efficiency' => $taskEfficiency,
        ]);
    }

    public function assignTasksRoundRobin()
{
    try {
        $tasksSortedByPriority = Task::orderBy('priority', 'asc')->where('status', 'pending')->get();

        if ($tasksSortedByPriority->isEmpty()) {
            return response()->api(['message' => 'No pending tasks to assign']);
        }

        foreach ($tasksSortedByPriority as $task) {
            if (method_exists($this->taskService, 'assignTaskRoundRobin')) {
                $this->taskService->assignTaskRoundRobin($task);
            } else {
                return response()->api(['error' => 'Task assignment method not found'], 500);
            }
        }

        $unassignedTasks = Task::where('assigned_user', null)->get();
        if ($unassignedTasks->isNotEmpty()) {
            return response()->api([
                'unassigned_tasks' => new TaskCollection($unassignedTasks),
                'message' => 'Some tasks remain unassigned',
            ]);
        }

        return response()->api(['message' => 'All tasks assigned successfully']);
    } catch (\Exception $e) {
        return response()->api([
            'error' => 'An error occurred while assigning tasks',
            'details' => $e->getMessage(),
        ], 500);
    }
}

}
