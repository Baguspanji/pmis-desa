<?php

use Livewire\Volt\Component;
use App\Models\Task;
use App\Models\Program;

new class extends Component {
    public $search = '';
    public $statusFilter = '';
    public $programFilter = '';

    public array $taskData = [];
    public $taskPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $statuses = [];
    public array $programs = [];

    protected $listeners = ['task-saved' => 'fetchData'];

    public function mount()
    {
        $this->statuses = [(object) ['name' => 'Belum Dimulai', 'value' => 'not_started'], (object) ['name' => 'Sedang Berjalan', 'value' => 'in_progress'], (object) ['name' => 'Selesai', 'value' => 'completed'], (object) ['name' => 'Ditunda', 'value' => 'on_hold'], (object) ['name' => 'Dibatalkan', 'value' => 'cancelled']];

        $this->programs = Program::all()->toArray();

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = Task::with(['program', 'assignedUser', 'parentTask']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('task_name', 'like', '%' . $this->search . '%')->orWhere('task_description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->programFilter) {
            $query->where('program_id', $this->programFilter);
        }

        $paginated = $query->paginate(10);

        $this->taskData = $paginated->items();
        $this->taskPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function create()
    {
        $this->dispatch('open-task-form');
    }

    public function edit($taskId)
    {
        $this->dispatch('open-task-form', taskId: $taskId);
    }

    public function delete($taskId)
    {
        try {
            Task::findOrFail($taskId)->delete();
            session()->flash('message', 'Tugas berhasil dihapus.');
            $this->fetchData();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Tugas" description="Kelola tugas-tugas dalam program pembangunan desa." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Tugas']]">
        <x-slot:actions>
            <flux:button wire:click="create" variant="primary">
                Tambah Tugas
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>


    <!-- Filter -->
    <div class="flex flex-row items-center justify-between mt-6 mb-4 gap-2">
        <div class="w-1/3">
            <flux:select wire:model="programFilter" placeholder="Filter berdasarkan program" wire:change="fetchData">
                <option value="">Semua Program</option>
                @foreach ($programs as $program)
                    <option value="{{ $program['id'] }}">{{ $program['program_name'] }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-1/3">
            <flux:select wire:model="statusFilter" placeholder="Filter berdasarkan status" wire:change="fetchData">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari tugas..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Nama Tugas</x-table.column>
            <x-table.column>Program</x-table.column>
            <x-table.column>PIC</x-table.column>
            <x-table.column>Prioritas</x-table.column>
            <x-table.column>Status</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($taskData as $task)
                <x-table.row>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span>{{ $task->task_name }}</span>
                            <span class="text-sm text-gray-500">{{ Str::limit($task->task_description, 50) }}</span>
                            @if ($task->parent_task_id)
                                <span class="text-xs text-gray-400">Sub-tugas dari:
                                    {{ $task->parentTask?->task_name }}</span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span>{{ $task->program?->program_name ?? '-' }}</span>
                            <span class="text-xs text-gray-500">
                                {{ $task->start_date?->format('d/m/Y') }} - {{ $task->end_date?->format('d/m/Y') }}
                            </span>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        {{ $task->assignedUser?->full_name ?? '-' }}
                    </x-table.cell>
                    <x-table.cell>
                        @if ($task->priority === 'high')
                            <flux:badge color="red">Tinggi</flux:badge>
                        @elseif ($task->priority === 'medium')
                            <flux:badge color="yellow">Sedang</flux:badge>
                        @else
                            <flux:badge color="green">Rendah</flux:badge>
                        @endif
                    </x-table.cell>
                    <x-table.cell>
                        @foreach ($statuses as $status)
                            @if ($task->status === $status->value)
                                <flux:badge>{{ $status->name }}</flux:badge>
                            @endif
                        @endforeach
                    </x-table.cell>
                    <x-table.cell align="center">
                        <flux:button size="sm" wire:click="edit({{ $task->id }})">
                            <flux:icon name="square-pen" class="w-4 h-4" />
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="delete({{ $task->id }})">
                            <flux:icon name="trash" class="w-4 h-4" />
                        </flux:button>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="6" align="center">Tidak ada data tugas</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$taskPagination" />
    </div>

    <!-- Include Task Form Modal -->
    @livewire('project-task.form')
</div>
