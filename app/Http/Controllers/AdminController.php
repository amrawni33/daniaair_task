<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

class AdminController extends Controller
{

    public function index()
    {
        $totalUsers = User::role('User')->count();
        $taskStats = Task::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
        $priorityStats = Task::selectRaw('priority, COUNT(*) as count')->groupBy('priority')->get();
        $avgCompletionTime = Task::where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time')
            ->value('avg_time');

        return response()->api([
            'total_users_number' => $totalUsers,
            'taskStats' => $taskStats,
            'priorityStats' => $priorityStats,
            'avgCompletionTime' => $avgCompletionTime,
        ]);
    }

}
