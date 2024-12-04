<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use App\Repositories\TaskRepository;

class TaskService
{
    protected $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function calculateEfficiency()
    {
        $totalTasks = $this->repository->getTasksByStatus('completed')->count();
        return $totalTasks / Task::count() * 100;
    }

    public function assignTaskRoundRobin($task)
    {
        $users = User::role('User')
            ->withCount('tasks')
            ->orderBy('tasks_count')
            ->get();

        $choeasedUser = $users->first();
        $task->assigned_user = $users->first()->id;
        $task->save();
        $message = "A new task titled '{$task->title}' has been assigned to you.";
        $choeasedUser->notify(new TaskNotification($task, $message));
    }
}
