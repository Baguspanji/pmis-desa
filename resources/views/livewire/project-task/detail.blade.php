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
        $this->task = Task::with(['program', 'parentTask', 'subTasks', 'assignedUser', 'targets', 'budgetRealizations', 'attachments'])->findOrFail($this->taskId);
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
    <flux:modal name="task-detail-modal" class="lg:min-w-[900px] max-h-[90vh] overflow-y-auto" wire:model.self="showModal"
        wire:close="closeModal">
        <form wire:submit="closeModal">
            <div class="flex justify-between pr-8">
                <flux:heading size="lg">Detail Tugas</flux:heading>
                @if ($task)
                    <a href="{{ route('projects.tasks.targets', ['id' => $task->program->id, 'taskId' => $task->id]) }}"
                        class="text-sm text-white bg-blue-600 hover:bg-blue-800 rounded px-2 py-0.5 cursor-pointer">
                        Lihat Target
                    </a>
                @endif
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
                                    <label class="text-sm font-medium text-gray-700">PK</label>
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
                                        <flux:badge color="gray">{{ $this->getStatusLabel($task->status) }}
                                        </flux:badge>
                                    @elseif ($task->status === 'in_progress')
                                        <flux:badge color="blue">{{ $this->getStatusLabel($task->status) }}
                                        </flux:badge>
                                    @elseif ($task->status === 'completed')
                                        <flux:badge color="green">{{ $this->getStatusLabel($task->status) }}
                                        </flux:badge>
                                    @elseif ($task->status === 'on_hold')
                                        <flux:badge color="yellow">{{ $this->getStatusLabel($task->status) }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="red">{{ $this->getStatusLabel($task->status) }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Prioritas</label>
                                <div class="mt-1">
                                    @if ($task->priority === 'high')
                                        <flux:badge variant="solid" color="red">
                                            {{ $this->getPriorityLabel($task->priority) }}</flux:badge>
                                    @elseif ($task->priority === 'medium')
                                        <flux:badge variant="solid" color="yellow">
                                            {{ $this->getPriorityLabel($task->priority) }}</flux:badge>
                                    @else
                                        <flux:badge variant="solid" color="green">
                                            {{ $this->getPriorityLabel($task->priority) }}</flux:badge>
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
                            <div>
                                <label class="text-sm font-medium text-gray-700">Estimasi Anggaran</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $task->estimated_budget ? 'Rp ' . number_format($task->estimated_budget, 0, ',', '.') : '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Total Realisasi</label>
                                @php
                                    $totalRealization = $task->budgetRealizations->sum('amount');
                                @endphp
                                <p
                                    class="mt-1 text-sm font-semibold {{ $totalRealization > 0 ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $totalRealization > 0 ? 'Rp ' . number_format($totalRealization, 0, ',', '.') : '-' }}
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

                    <!-- Task Targets -->
                    @if ($task->targets && $task->targets->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Target Tugas ({{ $task->targets->count() }})</h3>
                            <div class="space-y-3">
                                @foreach ($task->targets as $target)
                                    <div class="p-3 bg-gray-50 rounded">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Target</label>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ number_format($target->target_value, 2, ',', '.') }}
                                                    @if ($task->progress_type === 'percentage')
                                                        %
                                                    @endif
                                                </p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Tercapai</label>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ number_format($target->achieved_value ?? 0, 2, ',', '.') }}
                                                    @if ($task->progress_type === 'percentage')
                                                        %
                                                    @endif
                                                </p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Tanggal Target</label>
                                                <p class="text-sm text-gray-900">
                                                    {{ $target->target_date?->format('d/m/Y') ?? '-' }}
                                                </p>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Progress</label>
                                                <div class="flex items-center gap-2">
                                                    @php
                                                        $progress =
                                                            $target->target_value > 0
                                                                ? ($target->achieved_value / $target->target_value) *
                                                                    100
                                                                : 0;
                                                    @endphp
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                        <div class="h-2 rounded-full {{ $progress >= 100 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : 'bg-yellow-500') }}"
                                                            style="width: {{ min($progress, 100) }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-600">
                                                        {{ number_format(min($progress, 100), 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($target->notes)
                                            <div class="mt-2 pt-2 border-t border-gray-200">
                                                <label class="text-xs font-medium text-gray-500">Catatan</label>
                                                <p class="text-sm text-gray-700 mt-1">{{ $target->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @php
                                    $totalTarget = $task->targets->sum('target_value');
                                    $totalAchieved = $task->targets->sum('achieved_value');
                                    $overallProgress = $totalTarget > 0 ? ($totalAchieved / $totalTarget) * 100 : 0;
                                @endphp
                                <div class="pt-3 border-t border-gray-300">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-semibold text-gray-900">Total Progress</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format(min($overallProgress, 100), 1) }}%
                                        </span>
                                    </div>
                                    <div class="bg-gray-200 rounded-full h-3">
                                        <div class="h-3 rounded-full {{ $overallProgress >= 100 ? 'bg-green-500' : ($overallProgress >= 50 ? 'bg-blue-500' : 'bg-yellow-500') }}"
                                            style="width: {{ min($overallProgress, 100) }}%"></div>
                                    </div>
                                </div>
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
                                                {{ $realization->transaction_date?->format('d/m/Y') }}
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
                                            Rp
                                            {{ number_format($task->budgetRealizations->sum('amount'), 0, ',', '.') }}
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
                                                <span
                                                    class="text-sm text-gray-900">{{ $attachment->file_name }}</span>
                                                @if ($attachment->description)
                                                    <p class="text-xs text-gray-500">{{ $attachment->description }}
                                                    </p>
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
