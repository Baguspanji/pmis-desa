<?php

use Livewire\Volt\Component;
use App\Models\Program;

new class extends Component {
    public $projectId = null;
    public $showModal = false;
    public $project = null;

    protected $listeners = ['open-project-detail' => 'openModal'];

    public function openModal($projectId)
    {
        $this->projectId = $projectId;
        $this->loadProject();
        $this->showModal = true;
    }

    public function loadProject()
    {
        $this->project = Program::with([
            'pic',
            'creator',
            'tasks' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'tasks.budgetRealizations',
            'taskTargets',
            'attachments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ])->findOrFail($this->projectId);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->projectId = null;
        $this->project = null;
    }

    public function getStatusLabel($status)
    {
        $statuses = [
            'planned' => 'Direncanakan',
            'in_progress' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'on_hold' => 'Ditunda',
            'cancelled' => 'Dibatalkan',
        ];

        return $statuses[$status] ?? $status;
    }
}; ?>

<div>
    <flux:modal name="project-detail-modal" class="lg:min-w-[900px] max-h-[90vh] overflow-y-auto" wire:model.self="showModal"
        wire:close="closeModal">
        <form wire:submit="closeModal">
            <div class="flex justify-between pr-8">
                <flux:heading size="lg">Detail Program</flux:heading>
                @if ($project)
                    <a href="{{ route('projects.tasks', $project->id) }}"
                        class="text-sm text-white bg-blue-600 hover:bg-blue-800 rounded px-2 py-0.5 cursor-pointer">
                        Lihat Tugas
                    </a>
                @endif
            </div>

            @if ($project)
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Nama Program</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $project->program_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $project->program_description ?? '-' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Lokasi</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $project->location ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-700">PIC</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $project->pic?->full_name ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Status Program</h3>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-1">
                                @if ($project->status === 'planned')
                                    <flux:badge color="gray">{{ $this->getStatusLabel($project->status) }}
                                    </flux:badge>
                                @elseif ($project->status === 'in_progress')
                                    <flux:badge color="blue">{{ $this->getStatusLabel($project->status) }}
                                    </flux:badge>
                                @elseif ($project->status === 'completed')
                                    <flux:badge color="green">{{ $this->getStatusLabel($project->status) }}
                                    </flux:badge>
                                @elseif ($project->status === 'on_hold')
                                    <flux:badge color="yellow">{{ $this->getStatusLabel($project->status) }}
                                    </flux:badge>
                                @else
                                    <flux:badge color="red">{{ $this->getStatusLabel($project->status) }}
                                    </flux:badge>
                                @endif
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
                                    {{ $project->start_date?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $project->end_date?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Total Anggaran</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $project->total_budget ? 'Rp ' . number_format($project->total_budget, 0, ',', '.') : '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Total Realisasi</label>
                                @php
                                    $totalRealization = $project->tasks->flatMap(function($task) {
                                        return $task->budgetRealizations ?? collect();
                                    })->sum('amount');
                                @endphp
                                <p class="mt-1 text-sm font-semibold {{ $totalRealization > 0 ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $totalRealization > 0 ? 'Rp ' . number_format($totalRealization, 0, ',', '.') : 'Rp 0' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    @if ($project->tasks && $project->tasks->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Tugas ({{ $project->tasks->count() }})</h3>
                            <div class="space-y-2">
                                @foreach ($project->tasks as $task)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex-1">
                                            <span class="text-sm text-gray-900">{{ $task->task_name }}</span>
                                            @if ($task->task_description)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($task->task_description, 80) }}</p>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            @if ($task->status === 'completed')
                                                <flux:badge size="sm" color="green">Selesai</flux:badge>
                                            @elseif ($task->status === 'in_progress')
                                                <flux:badge size="sm" color="blue">Berjalan</flux:badge>
                                            @elseif ($task->status === 'on_hold')
                                                <flux:badge size="sm" color="yellow">Ditunda</flux:badge>
                                            @elseif ($task->status === 'cancelled')
                                                <flux:badge size="sm" color="red">Dibatalkan</flux:badge>
                                            @else
                                                <flux:badge size="sm" color="gray">Belum Mulai</flux:badge>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Targets -->
                    @if ($project->taskTargets && $project->taskTargets->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Target Program
                                ({{ $project->taskTargets->count() }})</h3>
                            <div class="space-y-2">
                                @foreach ($project->taskTargets as $target)
                                    <div class="p-2 bg-gray-50 rounded">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $target->target_name }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                {{ number_format($target->achieved_value ?? 0, 0, ',', '.') }} /
                                                {{ number_format($target->target_value, 0, ',', '.') }}
                                                {{ $target->target_unit }}
                                            </span>
                                        </div>
                                        @if ($target->target_description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $target->target_description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if ($project->attachments && $project->attachments->count() > 0)
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold mb-4">Lampiran ({{ $project->attachments->count() }})</h3>
                            <div class="space-y-2">
                                @foreach ($project->attachments as $attachment)
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

                    <!-- Summary Statistics -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Ringkasan</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-blue-50 rounded">
                                <label class="text-sm font-medium text-blue-700">Total Tugas</label>
                                <p class="mt-1 text-2xl font-bold text-blue-900">
                                    {{ $project->tasks->count() }}
                                </p>
                            </div>
                            <div class="p-3 bg-purple-50 rounded">
                                <label class="text-sm font-medium text-purple-700">Total Target</label>
                                <p class="mt-1 text-2xl font-bold text-purple-900">
                                    {{ $project->taskTargets->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Calculation -->
                    <div class="border-b pb-4">
                        <h3 class="text-lg font-semibold mb-4">Progres Program</h3>
                        <div class="space-y-4">
                            @php
                                // Calculate task-based progress
                                $totalTasks = $project->tasks->count();
                                $completedTasks = $project->tasks->where('status', 'completed')->count();
                                $taskProgress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                            @endphp

                            <!-- Task Progress -->
                            @if ($totalTasks > 0)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <span class="text-sm font-semibold text-gray-900">Progres Berdasarkan
                                                Tugas</span>
                                            <p class="text-xs text-gray-600 mt-1">
                                                {{ $completedTasks }} dari {{ $totalTasks }} tugas selesai
                                            </p>
                                        </div>
                                        <span class="text-lg font-bold text-gray-700">
                                            {{ number_format(min($taskProgress, 100), 1) }}%
                                        </span>
                                    </div>
                                    <div class="bg-gray-200 rounded-full h-3">
                                        <div class="h-3 rounded-full transition-all duration-500 {{ $taskProgress >= 100 ? 'bg-green-500' : ($taskProgress >= 50 ? 'bg-blue-500' : 'bg-yellow-500') }}"
                                            style="width: {{ min($taskProgress, 100) }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Progress Breakdown -->
                            @if ($totalTasks > 0)
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="p-2 bg-green-50 rounded text-center">
                                        <p class="text-xs text-green-600 font-medium">Selesai</p>
                                        <p class="text-lg font-bold text-green-700">
                                            {{ $project->tasks->where('status', 'completed')->count() }}
                                        </p>
                                    </div>
                                    <div class="p-2 bg-blue-50 rounded text-center">
                                        <p class="text-xs text-blue-600 font-medium">Berjalan</p>
                                        <p class="text-lg font-bold text-blue-700">
                                            {{ $project->tasks->where('status', 'in_progress')->count() }}
                                        </p>
                                    </div>
                                    <div class="p-2 bg-gray-50 rounded text-center">
                                        <p class="text-xs text-gray-600 font-medium">Belum Mulai</p>
                                        <p class="text-lg font-bold text-gray-700">
                                            {{ $project->tasks->where('status', 'not_started')->count() }}
                                        </p>
                                    </div>
                                    <div class="p-2 bg-yellow-50 rounded text-center">
                                        <p class="text-xs text-yellow-600 font-medium">Ditunda</p>
                                        <p class="text-lg font-bold text-yellow-700">
                                            {{ $project->tasks->where('status', 'on_hold')->count() }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Informasi Sistem</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Dibuat Oleh</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $project->creator?->full_name ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Dibuat Pada</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $project->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium text-gray-700">Terakhir Diperbarui</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $project->updated_at?->format('d/m/Y H:i') ?? '-' }}
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
