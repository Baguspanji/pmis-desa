<?php

use Livewire\Volt\Component;
use App\Models\User;

new class extends Component {
    public $search = '';
    public $roleFilter = '';

    public array $userData = [];
    public $userPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $roles = [];

    protected $listeners = ['user-saved' => 'fetchData'];

    public function mount()
    {
        $this->roles = [(object) ['name' => 'Admin', 'value' => 'admin'], (object) ['name' => 'Operator', 'value' => 'operator'], (object) ['name' => 'Kepala Desa', 'value' => 'kepala_desa'], (object) ['name' => 'Staff', 'value' => 'staff']];

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $paginated = $query->paginate(10);

        $this->userData = $paginated->items();
        $this->userPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function create()
    {
        $this->dispatch('open-user-form');
    }

    public function edit($userId)
    {
        $this->dispatch('open-user-form', userId: $userId);
    }

    public function delete($userId)
    {
        try {
            User::findOrFail($userId)->delete();
            session()->flash('message', 'Pengguna berhasil dihapus.');
            $this->fetchData();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Pengguna" description="Kelola pengguna aplikasi Anda di sini." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Users']]">
        <x-slot:actions>
            <flux:button wire:click="create" variant="primary">
                Tambah Pengguna
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>


    <!-- Filter -->
    <div class="flex flex-row items-center justify-between mt-6 mb-4 gap-2">
        <div class="w-1/3">
            <flux:select wire:model="roleFilter" placeholder="Filter berdasarkan peran" wire:change="fetchData">
                <option value="">Semua Peran</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}">{{ $role->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari pengguna..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Nama</x-table.column>
            <x-table.column>Email</x-table.column>
            <x-table.column>Peran</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($userData as $user)
                <x-table.row>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span>{{ $user->full_name }}</span>
                            <span class="text-sm text-gray-500">{{ $user->phone ?? '-' }}</span>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span class="text-xs font-mono font-semibold">{{ $user->username ?? '-' }}</span>
                            <span>{{ $user->email }}</span>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        @foreach ($roles as $role)
                            @if ($user->role === $role->value)
                                <flux:badge>{{ $role->name }}</flux:badge>
                            @endif
                        @endforeach
                    </x-table.cell>
                    <x-table.cell align="center">
                        <flux:button size="sm" wire:click="edit({{ $user->id }})">
                            <flux:icon name="square-pen" class="w-4 h-4" />
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="delete({{ $user->id }})">
                            <flux:icon name="trash" class="w-4 h-4" />
                        </flux:button>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="4" align="center">Tidak ada data pengguna</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$userPagination" />
    </div>

    <!-- Include User Form Modal -->
    @livewire('user.form')
</div>
