<?php

use Livewire\Volt\Component;
use App\Models\Resident;

new class extends Component {
    public $search = '';
    public $genderFilter = '';
    public $dusunFilter = '';
    public $statusFilter = '';

    public array $residentData = [];
    public $residentPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $genders = [];
    public array $dusuns = [];
    public array $statuses = [];
    public $residentIdToDelete = null;

    protected $listeners = [
        'resident-saved' => 'fetchData',
        'confirm-delete-resident' => 'performDelete',
    ];

    public function mount()
    {
        $this->genders = [
            (object) ['name' => 'Laki-laki', 'value' => 'male'],
            (object) ['name' => 'Perempuan', 'value' => 'female'],
        ];

        $this->statuses = [
            (object) ['name' => 'Aktif', 'value' => '1'],
            (object) ['name' => 'Tidak Aktif', 'value' => '0'],
        ];

        // Get unique dusuns from residents
        $uniqueDusuns = Resident::whereNotNull('dusun')
            ->distinct()
            ->pluck('dusun')
            ->map(fn($dusun) => (object) ['name' => $dusun, 'value' => $dusun])
            ->toArray();

        $this->dusuns = $uniqueDusuns;

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = Resident::query()->orderBy('created_at', 'desc');

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
        $this->residentIdToDelete = $residentId;
        $resident = Resident::find($residentId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus data warga "' . $resident->full_name . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-resident',
        ]);
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
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Data Warga" description="Kelola data warga desa di sini." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Warga']]">
        <x-slot:actions>
            <flux:button wire:click="create" variant="primary">
                Tambah Warga
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>


    <!-- Filter -->
    <div class="flex flex-col lg:flex-row items-center justify-between mt-6 mb-4 gap-2">
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
        <div class="w-full">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari warga (nama, NIK, KK, telepon)..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
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
                            <span class="text-xs font-mono text-gray-500">KK: {{ $resident->kk_number ?? '-' }}</span>
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
                            <span class="text-sm text-gray-500">{{ $resident->formatted_birth_date }}</span>
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
                        <flux:button icon="square-pen" size="xs" wire:click="edit({{ $resident->id }})" />
                        <flux:button icon="trash" size="xs" variant="danger"
                            wire:click="delete({{ $resident->id }})" />
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="7" align="center">Tidak ada data warga</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$residentPagination" />
    </div>

    <!-- Include Resident Form Modal -->
    @livewire('resident.form')
</div>
