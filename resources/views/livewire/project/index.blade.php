<?php

use Livewire\Volt\Component;
use App\Models\Program;

new class extends Component {
    public $search = '';
    public $statusFilter = '';

    public array $projectData = [];
    public $projectPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $statuses = [];
    public $projectIdToDelete = null;

    protected $listeners = [
        'project-saved' => 'fetchData',
        'confirm-delete-project' => 'performDelete',
    ];

    public function mount()
    {
        $this->statuses = [(object) ['name' => 'Direncanakan', 'value' => 'planned'], (object) ['name' => 'Sedang Berjalan', 'value' => 'in_progress'], (object) ['name' => 'Selesai', 'value' => 'completed'], (object) ['name' => 'Ditunda', 'value' => 'on_hold'], (object) ['name' => 'Dibatalkan', 'value' => 'cancelled']];

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = Program::with(['pic', 'creator']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('program_name', 'like', '%' . $this->search . '%')->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $paginated = $query->paginate(10);

        $this->projectData = $paginated->items();
        $this->projectPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function create()
    {
        $this->dispatch('open-project-form');
    }

    public function edit($projectId)
    {
        $this->dispatch('open-project-form', projectId: $projectId);
    }

    public function viewDetail($projectId)
    {
        $this->dispatch('open-project-detail', projectId: $projectId);
    }

    public function viewTasks($projectId)
    {
        $this->redirect(route('projects.tasks', $projectId));
    }

    public function delete($projectId)
    {
        $this->projectIdToDelete = $projectId;
        $project = Program::find($projectId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus program "' . $project->program_name . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-project',
        ]);
    }

    public function performDelete()
    {
        try {
            Program::findOrFail($this->projectIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Program berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->projectIdToDelete = null;
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
    <x-app-header-page title="Program" description="Kelola program/proyek pembangunan desa di sini." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Program']]">
        <x-slot:actions>
            <flux:button wire:click="create" variant="primary">
                Tambah Program
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>


    <!-- Filter -->
    <div class="flex flex-row items-center justify-between mt-6 mb-4 gap-2">
        <div class="w-1/3">
            <flux:select wire:model="statusFilter" placeholder="Filter berdasarkan status" wire:change="fetchData">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari program..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($projectData as $project)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ Str::limit($project->program_name, 45) }}</h3>
                        @foreach ($statuses as $status)
                            @if ($project->status === $status->value)
                                <flux:badge>{{ $status->name }}</flux:badge>
                            @endif
                        @endforeach
                    </div>

                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($project->program_description, 80) }}</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="map-pin" class="w-4 h-4 mr-2" />
                            <span>{{ $project->location ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="user" class="w-4 h-4 mr-2" />
                            <span>{{ $project->pic?->full_name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="calendar" class="w-4 h-4 mr-2" />
                            <span>{{ $project->start_date?->format('d/m/Y') }} -
                                {{ $project->end_date?->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center text-sm font-semibold text-gray-900">
                            <flux:icon name="wallet" class="w-4 h-4 mr-2" />
                            <span>Rp {{ number_format($project->total_budget, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                        <flux:button size="sm" href="{{ route('projects.tasks', $project->id) }}">
                            <flux:icon name="square-check" class="w-4 h-4" />
                        </flux:button>
                        <flux:button size="sm" variant="ghost" wire:click="viewDetail({{ $project->id }})">
                            <flux:icon name="eye" class="w-4 h-4" />
                        </flux:button>
                        <flux:button size="sm" wire:click="edit({{ $project->id }})">
                            <flux:icon name="square-pen" class="w-4 h-4" />
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="delete({{ $project->id }})">
                            <flux:icon name="trash" class="w-4 h-4" />
                        </flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                Tidak ada data program
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$projectPagination" />
    </div>

    <!-- Include Project Form Modal -->
    @livewire('project.form')

    <!-- Include Project Detail Modal -->
    @livewire('project.detail')
</div>
