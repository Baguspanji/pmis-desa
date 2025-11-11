@props([
    'emptyMessage' => 'Tidak ada data',
    'pagination' => null,
])

<div class="p-2 bg-white rounded-md shadow-sm">
    <flux:table>
        <flux:columns>
            {{ $columns ?? '' }}
        </flux:columns>

        <flux:rows>
            {{ $rows ?? '' }}
        </flux:rows>
    </flux:table>

    @if ($pagination)
        <div class="mt-4">
            {{ $pagination->links() }}
        </div>
    @endif
</div>
