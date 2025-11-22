<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TaskReportController extends Controller
{
    /**
     * Generate PDF report for a specific task
     */
    public function generateTaskReport($taskId)
    {
        $task = Task::with([
            'program',
            'assignedUser',
            'parentTask',
            'subTasks.assignedUser',
            'targets',
            'budgetRealizations.creator',
            'attachments.uploader',
            'logbooks.creator',
            'logbooks.verifier',
            'logbooks.taskTarget'
        ])->findOrFail($taskId);

        // Calculate statistics
        $totalBudget = $task->budgetRealizations->sum('amount');
        $totalTargets = $task->targets->count();
        $achievedTargets = $task->targets->where('achieved_value', '>=', 'target_value')->count();
        $totalLogbooks = $task->logbooks->count();
        $verifiedLogbooks = $task->logbooks->whereNotNull('verified_at')->count();

        // Calculate progress percentage
        $progressPercentage = 0;
        if ($totalTargets > 0) {
            $progressPercentage = ($achievedTargets / $totalTargets) * 100;
        }

        $data = [
            'task' => $task,
            'totalBudget' => $totalBudget,
            'totalTargets' => $totalTargets,
            'achievedTargets' => $achievedTargets,
            'progressPercentage' => $progressPercentage,
            'totalLogbooks' => $totalLogbooks,
            'verifiedLogbooks' => $verifiedLogbooks,
            'generatedDate' => now()->format('d F Y H:i:s')
        ];

        $pdf = Pdf::loadView('reports.task-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Task_' . $task->id . '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate PDF report for all tasks in a program
     */
    public function generateProgramTasksReport($programId)
    {
        $tasks = Task::with([
            'program',
            'assignedUser',
            'targets',
            'budgetRealizations',
            'logbooks'
        ])->where('program_id', $programId)
          ->whereNull('parent_task_id')
          ->get();

        if ($tasks->isEmpty()) {
            return back()->with('error', 'Tidak ada task yang ditemukan untuk program ini.');
        }

        $program = $tasks->first()->program;

        // Calculate program statistics
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $totalBudget = $tasks->sum('estimated_budget');
        $totalRealization = 0;

        foreach ($tasks as $task) {
            $totalRealization += $task->budgetRealizations->sum('amount');
        }

        $data = [
            'program' => $program,
            'tasks' => $tasks,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'totalBudget' => $totalBudget,
            'totalRealization' => $totalRealization,
            'generatedDate' => now()->format('d F Y H:i:s')
        ];

        $pdf = Pdf::loadView('reports.program-tasks-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Program_' . $program->id . '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }
}
