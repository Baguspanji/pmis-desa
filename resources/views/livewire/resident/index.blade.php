<?php

use Livewire\Volt\Component;
use App\Models\Resident;
use App\Models\HeadResident;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

new #[Title('Warga')] class extends Component {
    public $search = '';
    public $genderFilter = '';
    public $dusunFilter = '';
    public $statusFilter = '';

    public $viewMode = 'kk'; // 'resident' or 'kk'

    public array $residentData = [];
    public $residentPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $headResidentData = [];
    public $headResidentPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $genders = [];
    public array $dusuns = [];
    public array $statuses = [];
    public $residentIdToDelete = null;
    public $headResidentIdToDelete = null;

    protected $listeners = [
        'resident-saved' => 'fetchData',
        'confirm-delete-resident' => 'performDelete',
        'confirm-delete-head-resident' => 'performDeleteHeadResident',
    ];

    public function mount()
    {
        $this->genders = [(object) ['name' => 'Laki-laki', 'value' => 'male'], (object) ['name' => 'Perempuan', 'value' => 'female']];

        $this->statuses = [(object) ['name' => 'Aktif', 'value' => '1'], (object) ['name' => 'Tidak Aktif', 'value' => '0']];

        // Get unique dusuns from residents
        $uniqueDusuns = Resident::whereNotNull('dusun')->distinct()->pluck('dusun')->map(fn($dusun) => (object) ['name' => $dusun, 'value' => $dusun])->toArray();

        $this->dusuns = $uniqueDusuns;

        $this->fetchData();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'resident' ? 'kk' : 'resident';
        $this->search = '';
        $this->genderFilter = '';
        $this->dusunFilter = '';
        $this->statusFilter = '';
        $this->fetchData();
    }

    public function fetchData()
    {
        if ($this->viewMode === 'resident') {
            $this->fetchResidentData();
        } else {
            $this->fetchHeadResidentData();
        }
    }

    private function fetchResidentData()
    {
        $query = Resident::query()->orderBy('created_at', 'desc');

        if (Auth::user()->role == 'kasun') {
            $query->where('dusun', Auth::user()->dusun);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('kk_number', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->genderFilter) {
            $query->where('gender', $this->genderFilter);
        }

        if ($this->dusunFilter) {
            $query->where('dusun', $this->dusunFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', (bool) $this->statusFilter);
        }

        $paginated = $query->paginate(10);

        $this->residentData = $paginated->items();
        $this->residentPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    private function fetchHeadResidentData()
    {
        $query = HeadResident::query()->withCount('residents')->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('head_name', 'like', '%' . $this->search . '%')
                    ->orWhere('head_nik', 'like', '%' . $this->search . '%')
                    ->orWhere('kk_number', 'like', '%' . $this->search . '%');
            });
        }

        $paginated = $query->paginate(15);

        $this->headResidentData = $paginated->items();
        $this->headResidentPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function create()
    {
        $this->dispatch('open-resident-form');
    }

    public function edit($residentId)
    {
        $this->dispatch('open-resident-form', residentId: $residentId);
    }

    public function delete($residentId)
    {
        if ($this->viewMode === 'resident') {
            $this->residentIdToDelete = $residentId;
            $resident = Resident::find($residentId);

            $this->dispatch('show-confirm', [
                'type' => 'warning',
                'title' => 'Konfirmasi Hapus',
                'content' => 'Apakah Anda yakin ingin menghapus data warga "' . $resident->full_name . '"? Tindakan ini tidak dapat dibatalkan.',
                'callback' => 'confirm-delete-resident',
            ]);
        } else {
            $this->headResidentIdToDelete = $residentId;
            $headResident = HeadResident::find($residentId);
            $this->dispatch('show-confirm', [
                'type' => 'warning',
                'title' => 'Konfirmasi Hapus',
                'content' => 'Apakah Anda yakin ingin menghapus data keluarga "' . $headResident->head_name . '"? Tindakan ini tidak dapat dibatalkan.',
                'callback' => 'confirm-delete-head-resident',
            ]);
        }
    }

    public function performDelete()
    {
        try {
            Resident::findOrFail($this->residentIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Data warga berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->residentIdToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function performDeleteHeadResident()
    {
        try {
            HeadResident::findOrFail($this->headResidentIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Data keluarga berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->headResidentIdToDelete = null;
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
    <x-app-header-page title="Data Warga" description="Kelola data warga desa di sini." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Warga']]">
        <x-slot:actions>
            @canany(['admin', 'operator', 'kasun'])
                <flux:button wire:click="create" variant="primary">
                    Tambah Warga
                </flux:button>
            @endcanany
            <!-- Switch View Mode -->
            <flux:button wire:click="toggleViewMode" :icon="$viewMode === 'resident' ? 'list' : 'users'" />
        </x-slot:actions>
    </x-app-header-page>

    <!-- Filter -->
    <div class="flex flex-col lg:flex-row items-center justify-between mt-6 mb-4 gap-2">
        @if ($viewMode === 'resident')
            <div class="w-full lg:w-1/4">
                <flux:select wire:model="genderFilter" placeholder="Filter Jenis Kelamin" wire:change="fetchData">
                    <option value="">Semua Jenis Kelamin</option>
                    @foreach ($genders as $gender)
                        <option value="{{ $gender->value }}">{{ $gender->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-full lg:w-1/4">
                <flux:select wire:model="dusunFilter" placeholder="Filter Dusun" wire:change="fetchData">
                    <option value="">Semua Dusun</option>
                    @foreach ($dusuns as $dusun)
                        <option value="{{ $dusun->value }}">{{ $dusun->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="w-full lg:w-1/4">
                <flux:select wire:model="statusFilter" placeholder="Filter Status" wire:change="fetchData">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        @endif
        <div class="w-full">
            <flux:input wire:model.debounce.500ms="search" type="text"
                placeholder="{{ $viewMode === 'resident' ? 'Cari warga (nama, NIK, KK, telepon)...' : 'Cari kepala keluarga (nama, NIK, KK)...' }}"
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    @if ($viewMode === 'resident')
        <!-- Resident Table -->
        <x-table-container>
            <x-slot:columns>
                <x-table.column>NIK / KK</x-table.column>
                <x-table.column>Nama Lengkap</x-table.column>
                <x-table.column>Tempat, Tanggal Lahir</x-table.column>
                <x-table.column>Jenis Kelamin</x-table.column>
                <x-table.column>Alamat</x-table.column>
                <x-table.column>Status</x-table.column>
                <x-table.column align="center">Aksi</x-table.column>
            </x-slot:columns>

            <x-slot:rows>
                @forelse ($residentData as $resident)
                    <x-table.row>
                        <x-table.cell>
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-semibold">NIK: {{ $resident->nik }}</span>
                                <span class="text-xs font-mono text-gray-500">KK:
                                    {{ $resident->kk_number ?? '-' }}</span>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <div class="flex flex-col">
                                <span class="font-semibold">{{ $resident->full_name }}</span>
                                <span class="text-sm text-gray-500">{{ $resident->phone ?? '-' }}</span>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $resident->birth_place ?? '-' }}</span>
                                <span class="text-sm text-gray-500">{{ $resident->formatted_birth_date }} ({{ $resident->age }} Tahun)</span>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <span class="text-sm">{{ $resident->gender_label }}</span>
                        </x-table.cell>
                        <x-table.cell>
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $resident->address ?? '-' }}</span>
                                @if ($resident->dusun || $resident->rt || $resident->rw)
                                    <span class="text-xs text-gray-500">
                                        @if ($resident->dusun)
                                            Dusun {{ $resident->dusun }}
                                        @endif
                                        @if ($resident->rt)
                                            RT {{ $resident->rt }}
                                        @endif
                                        @if ($resident->rw)
                                            RW {{ $resident->rw }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            @if ($resident->is_active)
                                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-md">
                                    Aktif
                                </span>
                            @else
                                <span class="text-xs px-2 py-0.5 bg-gray-200 text-gray-700 rounded-md">
                                    Tidak Aktif
                                </span>
                            @endif
                        </x-table.cell>
                        <x-table.cell align="center">
                            @canany(['admin', 'operator', 'kasun'])
                                <flux:button icon="square-pen" size="xs" wire:click="edit({{ $resident->id }})" />
                                <flux:button icon="trash" size="xs" variant="danger"
                                    wire:click="delete({{ $resident->id }})" />
                            @endcanany
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="7" align="center">Tidak ada data warga</x-table.cell>
                    </x-table.row>
                @endforelse
            </x-slot:rows>
        </x-table-container>

        <!-- Resident Pagination -->
        <div class="mt-4">
            <x-simple-pagination :pagination="$residentPagination" />
        </div>
    @else
        <!-- Head Resident (KK) Table -->
        <x-table-container>
            <x-slot:columns>
                <x-table.column>Nomor KK</x-table.column>
                <x-table.column>NIK Kepala Keluarga</x-table.column>
                <x-table.column>Nama Kepala Keluarga</x-table.column>
                <x-table.column>Jumlah Anggota</x-table.column>
                {{-- <x-table.column>Tanggal Dibuat</x-table.column> --}}
                {{-- <x-table.column align="center">Aksi</x-table.column> --}}
            </x-slot:columns>

            <x-slot:rows>
                @forelse ($headResidentData as $headResident)
                    <x-table.row>
                        <x-table.cell>
                            <span class="text-xs font-mono font-semibold">{{ $headResident->kk_number }}</span>
                        </x-table.cell>
                        <x-table.cell>
                            <span class="text-xs font-mono">{{ $headResident->head_nik }}</span>
                        </x-table.cell>
                        <x-table.cell>
                            <div class="flex flex-col">
                                <span class="font-semibold">{{ $headResident->head_name }}</span>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <span
                                class="inline-flex items-center justify-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">
                                {{ $headResident->resident_count }} Orang
                            </span>
                        </x-table.cell>
                        {{-- <x-table.cell>
                            <span class="text-sm text-gray-600">{{ $headResident->created_at->format('d M Y') }}</span>
                        </x-table.cell> --}}
                        {{-- <x-table.cell align="center">
                            <flux:button icon="trash" size="xs" variant="danger"
                                wire:click="delete({{ $headResident->id }})" />
                        </x-table.cell> --}}
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="4" align="center">Tidak ada data kepala keluarga</x-table.cell>
                    </x-table.row>
                @endforelse
            </x-slot:rows>
        </x-table-container>

        <!-- Head Resident Pagination -->
        <div class="mt-4">
            <x-simple-pagination :pagination="$headResidentPagination" />
        </div>
    @endif <!-- Include Resident Form Modal -->
    @livewire('resident.form')
</div>
