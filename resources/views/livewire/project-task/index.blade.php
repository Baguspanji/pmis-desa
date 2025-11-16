<?php

use Livewire\Volt\Component;
use App\Models\Task;
use App\Models\Program;

new class extends Component {
    public $search = '';
    public $statusFilter = '';
    public $programFilter = '';

    public $programId = null;
    public $program = null;
    public array $taskData = [];
    public $taskPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $statuses = [];
    public array $programs = [];
    public $taskIdToDelete = null;

    protected $listeners = [
        'task-saved' => 'fetchData',
        'confirm-delete-task' => 'performDelete',
    ];

    public function mount($id)
    {
        $this->statuses = [(object) ['name' => 'Belum Dimulai', 'value' => 'not_started'], (object) ['name' => 'Sedang Berjalan', 'value' => 'in_progress'], (object) ['name' => 'Selesai', 'value' => 'completed'], (object) ['name' => 'Ditunda', 'value' => 'on_hold'], (object) ['name' => 'Dibatalkan', 'value' => 'cancelled']];

        $this->programs = Program::all()->toArray();

        $this->programId = $id;
        $this->loadProgram();

        $this->fetchData();
    }

    public function loadProgram()
    {
        $this->program = Program::findOrFail($this->programId);
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

        $query->where('program_id', $this->programId);

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

    public function viewDetail($taskId)
    {
        $this->dispatch('open-task-detail', taskId: $taskId);
    }

    public function delete($taskId)
    {
        $this->taskIdToDelete = $taskId;
        $task = Task::find($taskId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus tugas "' . $task->task_name . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-task',
        ]);
    }

    public function performDelete()
    {
        try {
            Task::findOrFail($this->taskIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Tugas berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->taskIdToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Tugas : {{ $program->program_name }}"
        description="Kelola tugas-tugas dalam program pembangunan desa." :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Program', 'url' => route('projects')],
            ['label' => 'Tugas'],
        ]">
        <x-slot:actions>
            <flux:button wire:click="create" variant="primary">
                Tambah Tugas
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>

    <!-- Filter -->
    <div class="grid grid-cols-1 lg:flex lg:flex-row items-center justify-between mt-6 mb-4 gap-2">
        <div class="lg:w-1/3">
            <flux:select wire:model="programFilter" placeholder="Filter berdasarkan program" wire:change="fetchData">
                <option value="">Semua Program</option>
                @foreach ($programs as $program)
                    <option value="{{ $program['id'] }}">{{ $program['program_name'] }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="lg:w-1/3">
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

    <!-- Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($taskData as $task)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-6 space-y-2">
                    <div class="flex items-center gap-1 pb-2 border-b border-gray-200">
                        @foreach ($statuses as $status)
                            @if ($task->status === $status->value)
                                <flux:badge color="gray">{{ $status->name }}</flux:badge>
                            @endif
                        @endforeach

                        <flux:spacer />

                        @if ($task->priority === 'high')
                            <flux:badge variant="solid" color="red">Tinggi</flux:badge>
                        @elseif ($task->priority === 'medium')
                            <flux:badge variant="solid" color="yellow">Sedang</flux:badge>
                        @else
                            <flux:badge variant="solid" color="green">Rendah</flux:badge>
                        @endif

                        <flux:dropdown position="bottom" align="end">
                            <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" inset="top bottom">
                            </flux:button>

                            <flux:menu>
                                <flux:menu.item icon="square-check"
                                    href="{{ route('projects.tasks.targets', ['id' => $programId, 'taskId' => $task->id]) }}">
                                    Lihat Tugas</flux:menu.item>
                                <flux:menu.item icon="eye" wire:click="viewDetail({{ $task->id }})">Lihat Detail
                                </flux:menu.item>
                                <flux:menu.item icon="square-pen" wire:click="edit({{ $task->id }})">Edit
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" variant="danger"
                                    wire:click="delete({{ $task->id }})">Hapus</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 hover:underline cursor-pointer"
                        wire:click="viewDetail({{ $task->id }})">
                        {{ $task->task_name }}
                    </h3>

                    <p class="text-sm text-gray-600">{{ Str::limit($task->task_description, 80) }}</p>

                    <div class="space-y-2">
                        @if ($task->parent_task_id)
                            <div class="flex items-center text-sm text-gray-700">
                                <flux:icon name="link" class="w-4 h-4 mr-2" />
                                <span class="text-xs">Sub-tugas dari: {{ $task->parentTask?->task_name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="folder" class="w-4 h-4 mr-2" />
                            <span>{{ $task->program?->program_name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="user" class="w-4 h-4 mr-2" />
                            @if ($task->assignedUser?->position)
                                <span>({{ $task->assignedUser?->position }})
                                    {{ $task->assignedUser?->full_name ?? '-' }}</span>
                            @else
                                <span>{{ $task->assignedUser?->full_name ?? '-' }}</span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="calendar" class="w-4 h-4 mr-2" />
                            <span>{{ $task->start_date?->format('d/m/Y') }} -
                                {{ $task->end_date?->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="col-span-full flex flex-col items-center justify-center py-16 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                <flux:icon name="document-text" class="w-16 h-16 mb-4 text-gray-400" />
                <p class="text-lg font-medium text-gray-700">Tidak ada data tugas</p>
                <p class="text-sm text-gray-500 mt-1">Belum ada tugas yang tersedia untuk ditampilkan</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$taskPagination" />
    </div>

    <!-- Include Task Form Modal -->
    @livewire('project-task.form')

    <!-- Include Task Detail Modal -->
    @livewire('project-task.detail')
</div>
