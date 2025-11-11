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
        $this->statuses = [
            (object) ['name' => 'Direncanakan', 'value' => 'planned'],
            (object) ['name' => 'Sedang Berjalan', 'value' => 'in_progress'],
            (object) ['name' => 'Selesai', 'value' => 'completed'],
            (object) ['name' => 'Ditunda', 'value' => 'on_hold'],
            (object) ['name' => 'Dibatalkan', 'value' => 'cancelled'],
        ];

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
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Nama Program</x-table.column>
            <x-table.column>Lokasi</x-table.column>
            <x-table.column>PIC</x-table.column>
            <x-table.column>Total Anggaran</x-table.column>
            <x-table.column>Periode</x-table.column>
            <x-table.column>Status</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($projectData as $project)
                <x-table.row>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span>{{ $project->program_name }}</span>
                            <span class="text-sm text-gray-500">{{ Str::limit($project->program_description, 50) }}</span>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span>{{ $project->location ?? '-' }}</span>
                            <span class="text-xs text-gray-500">
                                {{ $project->start_date?->format('d/m/Y') }} - {{ $project->end_date?->format('d/m/Y') }}
                            </span>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        {{ $project->pic?->full_name ?? '-' }}
                    </x-table.cell>
                    <x-table.cell>
                        Rp {{ number_format($project->total_budget, 2, ',', '.') }}
                    </x-table.cell>
                    <x-table.cell>
                        {{ $project->start_date?->format('d/m/Y') }} - {{ $project->end_date?->format('d/m/Y') }}
                    </x-table.cell>
                    <x-table.cell>
                        @foreach ($statuses as $status)
                            @if ($project->status === $status->value)
                                <flux:badge>{{ $status->name }}</flux:badge>
                            @endif
                        @endforeach
                    </x-table.cell>
                    <x-table.cell align="center">
                        <flux:button size="sm" wire:click="edit({{ $project->id }})">
                            <flux:icon name="square-pen" class="w-4 h-4" />
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="delete({{ $project->id }})">
                            <flux:icon name="trash" class="w-4 h-4" />
                        </flux:button>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="5" align="center">Tidak ada data program</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$projectPagination" />
    </div>

    <!-- Include Project Form Modal -->
    @livewire('project.form')
</div>
