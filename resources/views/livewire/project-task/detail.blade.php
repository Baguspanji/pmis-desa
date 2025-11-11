<?php

use Livewire\Volt\Component;
use App\Models\Task;

new class extends Component {
    public $taskId = null;
    public $showModal = false;
    public $task = null;

    protected $listeners = [
        'open-task-detail' => 'openModal',
    ];

    public function openModal($taskId)
    {
        $this->taskId = $taskId;
        $this->loadTask();
        $this->showModal = true;
    }

    public function loadTask()
    {
        $this->task = Task::with([
            'program',
            'parentTask',
            'subTasks',
            'assignedUser',
            'budgetRealizations',
            'attachments',
        ])->findOrFail($this->taskId);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->taskId = null;
        $this->task = null;
    }

    public function getStatusLabel($status)
    {
        $statuses = [
            'not_started' => 'Belum Dimulai',
            'in_progress' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'on_hold' => 'Ditunda',
            'cancelled' => 'Dibatalkan',
        ];

        return $statuses[$status] ?? $status;
    }

    public function getPriorityLabel($priority)
    {
        $priorities = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
        ];

        return $priorities[$priority] ?? $priority;
    }

    public function getProgressTypeLabel($progressType)
    {
        $types = [
            'percentage' => 'Persentase',
            'status' => 'Status',
        ];

        return $types[$progressType] ?? $progressType;
    }
}; ?>

<div>
    <flux:modal name="task-detail-modal" class="min-w-[700px] max-h-[90vh] overflow-y-auto" wire:model.self="showModal" wire:close="closeModal">
        <form wire:submit="closeModal">
            <div>
                <flux:heading size="lg">Detail Tugas</flux:heading>
            </div>

            @if ($task)
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Nama Tugas</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $task->task_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->task_description ?? '-' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Program</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $task->program?->program_name ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">PIC</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $task->assignedUser?->full_name ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status and Priority -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Status & Prioritas</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    @if ($task->status === 'not_started')
                                        <flux:badge color="gray">{{ $this->getStatusLabel($task->status) }}</flux:badge>
                                    @elseif ($task->status === 'in_progress')
                                        <flux:badge color="blue">{{ $this->getStatusLabel($task->status) }}</flux:badge>
                                    @elseif ($task->status === 'completed')
                                        <flux:badge color="green">{{ $this->getStatusLabel($task->status) }}</flux:badge>
                                    @elseif ($task->status === 'on_hold')
                                        <flux:badge color="yellow">{{ $this->getStatusLabel($task->status) }}</flux:badge>
                                    @else
                                        <flux:badge color="red">{{ $this->getStatusLabel($task->status) }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Prioritas</label>
                                <div class="mt-1">
                                    @if ($task->priority === 'high')
                                        <flux:badge color="red">{{ $this->getPriorityLabel($task->priority) }}</flux:badge>
                                    @elseif ($task->priority === 'medium')
                                        <flux:badge color="yellow">{{ $this->getPriorityLabel($task->priority) }}</flux:badge>
                                    @else
                                        <flux:badge color="green">{{ $this->getPriorityLabel($task->priority) }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tipe Progress</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $this->getProgressTypeLabel($task->progress_type) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule and Budget -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Jadwal & Anggaran</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->start_date?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->end_date?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium text-gray-700">Estimasi Anggaran</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->estimated_budget ? 'Rp ' . number_format($task->estimated_budget, 0, ',', '.') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Parent Task -->
                    @if ($task->parent_task_id)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Tugas Induk</h3>
                            <div>
                                <p class="text-sm text-gray-900">{{ $task->parentTask?->task_name ?? '-' }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Sub Tasks -->
                    @if ($task->subTasks && $task->subTasks->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Sub Tugas ({{ $task->subTasks->count() }})</h3>
                            <div class="space-y-2">
                                @foreach ($task->subTasks as $subTask)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <span class="text-sm text-gray-900">{{ $subTask->task_name }}</span>
                                        @if ($subTask->status === 'completed')
                                            <flux:badge size="sm" color="green">Selesai</flux:badge>
                                        @elseif ($subTask->status === 'in_progress')
                                            <flux:badge size="sm" color="blue">Berjalan</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="gray">Belum Mulai</flux:badge>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Budget Realizations -->
                    @if ($task->budgetRealizations && $task->budgetRealizations->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Realisasi Anggaran
                                ({{ $task->budgetRealizations->count() }})</h3>
                            <div class="space-y-2">
                                @foreach ($task->budgetRealizations as $realization)
                                    <div class="p-2 bg-gray-50 rounded">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-900">
                                                {{ $realization->realization_date?->format('d/m/Y') }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">
                                                Rp {{ number_format($realization->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        @if ($realization->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $realization->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="pt-2 border-t">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-900">Total Realisasi</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($task->budgetRealizations->sum('amount'), 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if ($task->attachments && $task->attachments->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Lampiran ({{ $task->attachments->count() }})</h3>
                            <div class="space-y-2">
                                @foreach ($task->attachments as $attachment)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex items-center space-x-2">
                                            <flux:icon name="paperclip" class="w-4 h-4 text-gray-400" />
                                            <div>
                                                <span class="text-sm text-gray-900">{{ $attachment->file_name }}</span>
                                                @if ($attachment->description)
                                                    <p class="text-xs text-gray-500">{{ $attachment->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ $attachment->file_path }}" target="_blank"
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                            Lihat
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Informasi Sistem</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Dibuat Pada</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Terakhir Diperbarui</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeModal">Tutup</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
