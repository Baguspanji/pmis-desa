<?php

use Livewire\Volt\Component;
use App\Models\Program;
use Livewire\Attributes\Url;

new class extends Component {
    #[Url('q')]
    public $search = '';
    #[Url('status')]
    public $statusFilter = '';
    #[Url('year')]
    public $yearFilter = '';

    public array $projectData = [];
    public $projectPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $statuses = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'yearFilter' => ['except' => ''],
    ];
    public array $yearOptions = [];
    public $projectIdToDelete = null;

    protected $listeners = [
        'project-saved' => 'fetchData',
        'confirm-delete-project' => 'performDelete',
    ];

    public function mount()
    {
        $this->statuses = [(object) ['name' => 'Direncanakan', 'value' => 'planned'], (object) ['name' => 'Sedang Berjalan', 'value' => 'in_progress'], (object) ['name' => 'Selesai', 'value' => 'completed'], (object) ['name' => 'Ditunda', 'value' => 'on_hold'], (object) ['name' => 'Dibatalkan', 'value' => 'cancelled']];

        // this year and past 5 years
        $currentYear = date('Y');
        for ($i = 0; $i < 6; $i++) {
            $this->yearOptions[] = $currentYear - $i;
        }

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

        if ($this->yearFilter) {
            $query->whereYear('start_date', $this->yearFilter)->orWhereYear('end_date', $this->yearFilter);
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
    <x-app-header-page title="Program" description="Kelola program/proyek desa di sini." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Program']]">
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
        <div class="w-1/3">
            <flux:select wire:model="yearFilter" placeholder="Filter Tahun" wire:change="fetchData">
                <option value="">Semua Tahun</option>
                @foreach ($yearOptions as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Table Data -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($projectData as $project)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-6 space-y-2">
                    <div class="flex items-center gap-1 pb-2 border-b border-gray-200">
                        @foreach ($statuses as $status)
                            @if ($project->status === $status->value)
                                <flux:badge color="sky">{{ $status->name }}</flux:badge>
                            @endif
                        @endforeach

                        <flux:spacer />
                        <flux:dropdown position="bottom" align="end">
                            <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" inset="top bottom">
                            </flux:button>

                            <flux:menu>
                                <flux:menu.item icon="square-check" href="{{ route('projects.tasks', $project->id) }}">
                                    Lihat Tugas</flux:menu.item>
                                <flux:menu.item icon="eye" wire:click="viewDetail({{ $project->id }})">Lihat Detail
                                </flux:menu.item>
                                <flux:menu.item icon="square-pen" wire:click="edit({{ $project->id }})">Edit
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" variant="danger"
                                    wire:click="delete({{ $project->id }})">Hapus</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 hover:underline cursor-pointer"
                        wire:click="viewDetail({{ $project->id }})">
                        {{ Str::limit($project->program_name, 80) }}
                    </h3>

                    <p class="text-sm text-gray-600">{{ Str::limit($project->program_description, 100) }}</p>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="map-pin" class="w-4 h-4 mr-2" />
                            <span>{{ $project->location ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <flux:icon name="user" class="w-4 h-4 mr-2" />
                            @if ($project->pic?->position)
                                <span>({{ $project->pic?->position }}) {{ $project->pic?->full_name ?? '-' }}</span>
                            @else
                                <span>{{ $project->pic?->full_name ?? '-' }}</span>
                            @endif
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
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-500 border border-dashed border-gray-300 rounded-lg">
                <flux:icon name="document-text" class="w-16 h-16 mb-4 text-gray-400" />
                <p class="text-lg font-medium text-gray-700">Tidak ada data program</p>
                <p class="text-sm text-gray-500 mt-1">Belum ada program yang tersedia untuk ditampilkan</p>
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
