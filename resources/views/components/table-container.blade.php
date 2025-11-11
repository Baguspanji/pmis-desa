@props([
    'emptyMessage' => 'Tidak ada data',
])

<div class="p-2 bg-white rounded-md shadow-sm">
    <x-table>
        <x-table.header>
            {{ $columns ?? $slot }}
        </x-table.header>

        <x-table.body>
            @if (isset($rows))
                {{ $rows }}
            @else
                <x-table.row>
                    <x-table.cell colspan="100" align="center" class="text-gray-500 py-8">
                        {{ $emptyMessage }}
                    </x-table.cell>
                </x-table.row>
            @endif
        </x-table.body>
    </x-table>
</div>
