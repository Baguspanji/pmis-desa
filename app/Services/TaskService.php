<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getTaskByPic()
    {
        $query = Task::where('assigned_user_id', Auth::id())->get();

        return $query;
    }
}
