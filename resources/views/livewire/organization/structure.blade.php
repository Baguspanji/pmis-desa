<?php

use Livewire\Volt\Component;
use App\Models\OrganizationStructure;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

new #[Title('Struktur Organisasi')] class extends Component {
    use WithPagination;

    public $search = '';
    public $organizationTypeFilter = '';
    public $levelFilter = '';

    public array $structuresData = [];
    public $structuresPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $organizationTypes = [];
    public array $levels = [];
    public array $parentOptions = [];

    // Form properties
    public $showFormModal = false;
    public $isEditing = false;
    public $structureIdToEdit = null;
    public $structureIdToDelete = null;

    public $name = '';
    public $position = '';
    public $organization_type = '';
    public $level = '';
    public $parent_id = '';
    public $order = '';
    public $description = '';

    protected $listeners = [
        'confirm-delete-structure' => 'performDelete',
    ];

    public function mount()
    {
        $this->organizationTypes = [
            (object) ['name' => 'Pemerintah Desa', 'value' => 'Pemerintah Desa'],
            (object) ['name' => 'Badan Permasyarakatan Desa', 'value' => 'Badan Permasyarakatan Desa'],
        ];

        $this->levels = [
            (object) ['name' => 'Kepala / Ketua', 'value' => 'head'],
            (object) ['name' => 'Wakil / Sekretaris', 'value' => 'vice'],
            (object) ['name' => 'Staff', 'value' => 'staff'],
            (object) ['name' => 'Anggota / Member', 'value' => 'member'],
        ];

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = OrganizationStructure::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('position', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->organizationTypeFilter) {
            $query->where('organization_type', $this->organizationTypeFilter);
        }

        if ($this->levelFilter) {
            $query->where('level', $this->levelFilter);
        }

        $paginated = $query->with('parent')->latest()->paginate(15);

        $this->structuresData = $paginated->items();
        $this->structuresPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function loadParentOptions()
    {
        if ($this->organization_type) {
            $this->parentOptions = OrganizationStructure::where('organization_type', $this->organization_type)
                ->where('id', '!=', $this->structureIdToEdit)
                ->get()
                ->map(fn($item) => (object) ['id' => $item->id, 'name' => "{$item->position} - {$item->name}"])
                ->toArray();
        } else {
            $this->parentOptions = [];
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showFormModal = true;
    }

    public function openEditModal($structureId)
    {
        try {
            $structure = OrganizationStructure::findOrFail($structureId);

            $this->structureIdToEdit = $structure->id;
            $this->name = $structure->name;
            $this->position = $structure->position;
            $this->organization_type = $structure->organization_type;
            $this->level = $structure->level;
            $this->parent_id = $structure->parent_id ?? '';
            $this->order = $structure->order;
            $this->description = $structure->description;

            $this->loadParentOptions();
            $this->isEditing = true;
            $this->showFormModal = true;
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => 'Data tidak ditemukan.',
            ]);
        }
    }

    public function saveStructure()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'organization_type' => 'required|in:Pemerintah Desa,Badan Permasyarakatan Desa',
            'level' => 'required|in:head,vice,staff,member',
            'parent_id' => 'nullable|exists:organization_structures,id',
            'order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ];

        $this->validate($rules, [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'position.required' => 'Posisi/Jabatan wajib diisi',
            'position.max' => 'Posisi tidak boleh lebih dari 255 karakter',
            'organization_type.required' => 'Tipe organisasi wajib dipilih',
            'organization_type.in' => 'Tipe organisasi tidak valid',
            'level.required' => 'Level wajib dipilih',
            'level.in' => 'Level tidak valid',
            'parent_id.exists' => 'Parent tidak valid',
        ]);

        try {
            if ($this->isEditing) {
                $structure = OrganizationStructure::findOrFail($this->structureIdToEdit);
                $structure->update([
                    'name' => $this->name,
                    'position' => $this->position,
                    'organization_type' => $this->organization_type,
                    'level' => $this->level,
                    'parent_id' => $this->parent_id ?: null,
                    'order' => $this->order ?: 0,
                    'description' => $this->description,
                ]);

                $message = 'Struktur organisasi berhasil diperbarui.';
            } else {
                OrganizationStructure::create([
                    'name' => $this->name,
                    'position' => $this->position,
                    'organization_type' => $this->organization_type,
                    'level' => $this->level,
                    'parent_id' => $this->parent_id ?: null,
                    'order' => $this->order ?: 0,
                    'description' => $this->description,
                ]);

                $message = 'Struktur organisasi berhasil ditambahkan.';
            }

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => $message,
            ]);

            $this->closeFormModal();
            $this->fetchData();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->position = '';
        $this->organization_type = '';
        $this->level = '';
        $this->parent_id = '';
        $this->order = '';
        $this->description = '';
        $this->structureIdToEdit = null;
        $this->parentOptions = [];
    }

    public function delete($structureId)
    {
        $this->structureIdToDelete = $structureId;
        $structure = OrganizationStructure::find($structureId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus "' . $structure->position . ' - ' . $structure->name . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-structure',
        ]);
    }

    public function performDelete()
    {
        try {
            OrganizationStructure::findOrFail($this->structureIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Struktur organisasi berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->structureIdToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function getLevelLabel($level)
    {
        return match ($level) {
            'head' => 'Kepala / Ketua',
            'vice' => 'Wakil / Sekretaris',
            'staff' => 'Staff',
            'member' => 'Anggota / Member',
            default => $level,
        };
    }

    public function getLevelColor($level)
    {
        return match ($level) {
            'head' => 'red',
            'vice' => 'blue',
            'staff' => 'yellow',
            'member' => 'green',
            default => 'gray',
        };
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Struktur Organisasi" description="Kelola struktur organisasi pemerintah desa dan badan permasyarakatan."
        :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Struktur Organisasi']]">
    </x-app-header-page>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 mb-4">
        @php
            $totalStructures = OrganizationStructure::count();
            $governmentCount = OrganizationStructure::government()->count();
            $consultativeCount = OrganizationStructure::consultativeBody()->count();
            $headCount = OrganizationStructure::where('level', 'head')->count();
        @endphp

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Anggota</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalStructures }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600">Pemerintah Desa</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $governmentCount }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m3.419 0H5m0 0h-.581m.581 0a1 1 0 100-2 1 1 0 000 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-cyan-600">Badan Permasyarakatan</p>
                    <p class="text-2xl font-bold text-cyan-900">{{ $consultativeCount }}</p>
                </div>
                <div class="bg-cyan-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.582V9" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600">Kepala / Ketua</p>
                    <p class="text-2xl font-bold text-red-900">{{ $headCount }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end mb-4">
        <flux:button wire:click="openCreateModal" variant="primary">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Struktur Organisasi
            </div>
        </flux:button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-2">
        <div class="w-full md:w-1/4">
            <flux:select wire:model="organizationTypeFilter" placeholder="Filter berdasarkan tipe" wire:change="fetchData">
                <option value="">Semua Tipe</option>
                @foreach ($organizationTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model="levelFilter" placeholder="Filter berdasarkan level" wire:change="fetchData">
                <option value="">Semua Level</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->value }}">{{ $level->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:flex-1">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari nama atau posisi..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Nama</x-table.column>
            <x-table.column>Posisi</x-table.column>
            <x-table.column>Tipe Organisasi</x-table.column>
            <x-table.column>Level</x-table.column>
            <x-table.column>Atasan</x-table.column>
            <x-table.column>Urutan</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($structuresData as $structure)
                <x-table.row>
                    <x-table.cell>
                        <div class="font-semibold text-gray-900">{{ $structure->name }}</div>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="text-sm text-gray-600">{{ $structure->position }}</div>
                    </x-table.cell>
                    <x-table.cell>
                        @if ($structure->organization_type === 'Pemerintah Desa')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $structure->organization_type }}</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800">{{ $structure->organization_type }}</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell>
                        @php
                            $color = $this->getLevelColor($structure->level);
                            $label = $this->getLevelLabel($structure->level);
                            $bgColor = match($color) {
                                'red' => 'bg-red-100 text-red-800',
                                'blue' => 'bg-blue-100 text-blue-800',
                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                'green' => 'bg-green-100 text-green-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $bgColor }}">{{ $label }}</span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="text-sm text-gray-600">
                            @if ($structure->parent)
                                {{ $structure->parent->position }} - {{ $structure->parent->name }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="text-sm text-gray-600">{{ $structure->order }}</div>
                    </x-table.cell>
                    <x-table.cell align="center">
                        <div class="flex justify-center gap-2">
                            <button wire:click="openEditModal({{ $structure->id }})"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="delete({{ $structure->id }})"
                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="7" align="center">Tidak ada data struktur organisasi</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$structuresPagination" />
    </div>

    <!-- Form Modal -->
    <flux:modal name="structure-form-modal" class="lg:min-w-[700px] max-h-[90vh] overflow-y-auto"
        wire:model.self="showFormModal" wire:close="closeFormModal">
        <form wire:submit="saveStructure">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Struktur Organisasi' : 'Tambah Struktur Organisasi Baru' }}
            </flux:heading>

            <div class="space-y-4 mt-6">
                <!-- Name -->
                <div>
                    <flux:field>
                        <flux:label>Nama <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="name" type="text" placeholder="Masukkan nama lengkap" />
                        @error('name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Position -->
                <div>
                    <flux:field>
                        <flux:label>Posisi / Jabatan <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="position" type="text" placeholder="Contoh: Kepala Desa, Sekretaris, Staff" />
                        @error('position')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Organization Type -->
                <div>
                    <flux:field>
                        <flux:label>Tipe Organisasi <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="organization_type" wire:change="loadParentOptions" placeholder="Pilih tipe organisasi">
                            <option value="">-- Pilih Tipe Organisasi --</option>
                            @foreach ($organizationTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('organization_type')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Level -->
                <div>
                    <flux:field>
                        <flux:label>Level <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="level" placeholder="Pilih level organisasi">
                            <option value="">-- Pilih Level --</option>
                            @foreach ($levels as $lv)
                                <option value="{{ $lv->value }}">{{ $lv->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('level')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Parent -->
                @if (count($parentOptions) > 0)
                <div>
                    <flux:field>
                        <flux:label>Atasan</flux:label>
                        <flux:select wire:model="parent_id" placeholder="Pilih atasan (opsional)">
                            <option value="">-- Tidak Ada Atasan --</option>
                            @foreach ($parentOptions as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('parent_id')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>
                @endif

                <!-- Order -->
                <div>
                    <flux:field>
                        <flux:label>Urutan</flux:label>
                        <flux:input wire:model="order" type="number" placeholder="Masukkan urutan (opsional)" />
                        @error('order')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Description -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi</flux:label>
                        <flux:textarea wire:model="description" placeholder="Masukkan deskripsi (opsional)" rows="3" />
                        @error('description')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="closeFormModal" type="button">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $isEditing ? 'Perbarui' : 'Tambahkan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
