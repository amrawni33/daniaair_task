<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function getTasksByStatus($status)
    {
        return Task::where('status', $status)->get();
    }
}
