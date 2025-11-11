<?php
/**
 * Example usage of custom table components
 * This demonstrates how to use the new table components in your Livewire views
 */
?>

<!-- Option 1: Using the table-container wrapper -->
<x-table-container :pagination="$userPagination">
    <x-slot:columns>
        <x-table.column>Nama</x-table.column>
        <x-table.column align="center">Email</x-table.column>
        <x-table.column>Peran</x-table.column>
        <x-table.column align="center">Aksi</x-table.column>
    </x-slot:columns>

    <x-slot:rows>
        @forelse ($userData as $user)
            <x-table.row>
                <x-table.cell>{{ $user->name }}</x-table.cell>
                <x-table.cell align="center">{{ $user->email }}</x-table.cell>
                <x-table.cell>
                    @foreach ($user->roles as $role)
                        <flux:badge>{{ $role->name }}</flux:badge>
                    @endforeach
                </x-table.cell>
                <x-table.cell align="center">
                    <flux:button size="sm" wire:click="edit({{ $user->id }})">
                        Edit
                    </flux:button>
                    <flux:button size="sm" variant="danger" wire:click="delete({{ $user->id }})">
                        Hapus
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

<!-- Option 2: Using components directly with more control -->
<div class="p-2 bg-white rounded-md shadow-sm">
    <x-table>
        <x-table.header>
            <x-table.column sortable sortBy="name" :currentSort="$sortField" :sortDirection="$sortDirection">
                Nama
            </x-table.column>
            <x-table.column sortable sortBy="email" :currentSort="$sortField" :sortDirection="$sortDirection">
                Email
            </x-table.column>
            <x-table.column>Peran</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-table.header>

        <x-table.body>
            @forelse ($userData as $user)
                <x-table.row hoverable>
                    <x-table.cell>{{ $user->name }}</x-table.cell>
                    <x-table.cell>{{ $user->email }}</x-table.cell>
                    <x-table.cell>
                        @foreach ($user->roles as $role)
                            <flux:badge>{{ $role->name }}</flux:badge>
                        @endforeach
                    </x-table.cell>
                    <x-table.cell align="center">
                        <div class="flex gap-2 justify-center">
                            <flux:button size="sm" wire:click="edit({{ $user->id }})">
                                Edit
                            </flux:button>
                            <flux:button size="sm" variant="danger" wire:click="delete({{ $user->id }})">
                                Hapus
                            </flux:button>
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="4" align="center" class="text-gray-500 py-8">
                        Tidak ada data pengguna
                    </x-table.cell>
                </x-table.row>
            @endforelse
        </x-table.body>
    </x-table>

    @if ($userPagination)
        <div class="mt-4">
            {{ $userPagination->links() }}
        </div>
    @endif
</div>
