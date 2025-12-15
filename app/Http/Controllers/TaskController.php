<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService;
    }

    public function getTasks()
    {
        $search = request()->query('search');

        $query = Task::query()
            ->select('tasks.*', 'programs.program_name as program_name')
            ->join('programs', 'tasks.program_id', '=', 'programs.id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tasks.taks_name', 'like', '%'.$search.'%')
                    ->orWhere('programs.program_name', 'like', '%'.$search.'%');
            });
        }

        $query = $query->where('assigned_user_id', Auth::id());

        $tasks = $query->latest()->paginate(15);

        return $this->responsePaginate(
            data: $tasks,
            message: 'Berhasil mendapatkan data tugas'
        );
    }

    public function assignTask(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'task_id' => ['required', 'exists:tasks,id'],
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validate->errors(),
            ], 422);
        }
    }
}
