<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of programs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Program::with(['pic', 'creator', 'tasks', 'attachments']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year !== '') {
            $query->where(function ($q) use ($request) {
                $q->whereYear('start_date', $request->year)
                    ->orWhereYear('end_date', $request->year);
            });
        }

        // Search by name or location
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('program_name', 'like', '%'.$request->search.'%')
                    ->orWhere('location', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by PIC user
        if ($request->has('pic_user_id') && $request->pic_user_id !== '') {
            $query->where('pic_user_id', $request->pic_user_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $programs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->responsePaginate(
            data: $programs,
            message: 'Berhasil mendapatkan data program'
        );
    }

    /**
     * Display the specified program.
     */
    public function show(int $id): JsonResponse
    {
        $program = Program::with([
            'pic',
            'creator',
            'tasks' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'tasks.budgetRealizations',
            'tasks.assignedUser',
            'taskTargets',
            'attachments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ])->find($id);

        if (! $program) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
            ], 404);
        }

        // Calculate additional statistics
        $totalTasks = $program->tasks->count();
        $completedTasks = $program->tasks->where('status', 'completed')->count();
        $inProgressTasks = $program->tasks->where('status', 'in_progress')->count();
        $notStartedTasks = $program->tasks->where('status', 'not_started')->count();
        $onHoldTasks = $program->tasks->where('status', 'on_hold')->count();

        // Calculate total budget realization
        $totalRealization = $program->tasks->flatMap(function ($task) {
            return $task->budgetRealizations ?? collect();
        })->sum('amount');

        // Calculate task progress
        $taskProgress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        $programData = $program->toArray();
        $programData['statistics'] = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'not_started_tasks' => $notStartedTasks,
            'on_hold_tasks' => $onHoldTasks,
            'total_realization' => $totalRealization,
            'task_progress_percentage' => round($taskProgress, 2),
        ];

        return $this->customResponse(
            data: $programData,
            message: 'Berhasil mendapatkan data program'
        );
    }
}
