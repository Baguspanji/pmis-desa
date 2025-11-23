<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['program', 'assignedUser', 'parentTask']);

        // Filter by program
        if ($request->has('program_id') && $request->program_id !== '') {
            $query->where('program_id', $request->program_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->has('assigned_user_id') && $request->assigned_user_id !== '') {
            $query->where('assigned_user_id', $request->assigned_user_id);
        }

        // Search by name or description
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('task_name', 'like', '%'.$request->search.'%')
                    ->orWhere('task_description', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by parent task (only main tasks or only sub-tasks)
        if ($request->has('parent_task_id')) {
            if ($request->parent_task_id === 'null' || $request->parent_task_id === '') {
                $query->whereNull('parent_task_id');
            } else {
                $query->where('parent_task_id', $request->parent_task_id);
            }
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tasks = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->responsePaginate(
            data: $tasks,
            message: 'Berhasil mendapatkan data tugas'
        );
    }

    /**
     * Display the specified task.
     */
    public function show(int $id): JsonResponse
    {
        $task = Task::with([
            'program',
            'parentTask',
            'subTasks',
            'assignedUser',
            'targets',
            'budgetRealizations',
            'logbooks' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'logbooks.attachments',
        ])->find($id);

        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        // Calculate additional statistics
        $totalRealization = $task->budgetRealizations->sum('amount');
        $totalTarget = $task->targets->sum('target_value');
        $totalAchieved = $task->targets->sum('achieved_value');
        $overallProgress = $totalTarget > 0 ? ($totalAchieved / $totalTarget) * 100 : 0;

        // Sub-tasks statistics
        $totalSubTasks = $task->subTasks->count();
        $completedSubTasks = $task->subTasks->where('status', 'completed')->count();

        $taskData = $task->toArray();
        $taskData['statistics'] = [
            'total_budget_realization' => $totalRealization,
            'total_target_value' => $totalTarget,
            'total_achieved_value' => $totalAchieved,
            'overall_progress_percentage' => round($overallProgress, 2),
            'total_sub_tasks' => $totalSubTasks,
            'completed_sub_tasks' => $completedSubTasks,
            'budget_usage_percentage' => $task->estimated_budget > 0
                ? round(($totalRealization / $task->estimated_budget) * 100, 2)
                : 0,
        ];

        return $this->customResponse(
            data: $taskData,
            message: 'Berhasil mendapatkan data tugas'
        );
    }
}
