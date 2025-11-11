@props([
    'sortable' => false,
    'sortBy' => null,
    'currentSort' => null,
    'sortDirection' => null,
    'align' => 'left',
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];

    $isSorted = $sortable && $currentSort === $sortBy;
@endphp

<th
    {{ $attributes->merge([
        'class' =>
            'px-4 py-3 text-sm font-semibold tracking-wider text-gray-700 uppercase bg-gray-50 ' .
            ($alignClasses[$align] ?? 'text-left'),
    ]) }}>
    @if ($sortable && $sortBy)
        <button type="button" wire:click="sortBy('{{ $sortBy }}')"
            class="inline-flex items-center gap-1 hover:text-gray-900 transition-colors">
            {{ $slot }}

            <span class="inline-flex flex-col">
                @if ($isSorted && $sortDirection === 'asc')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 10l5-5 5 5H5z" />
                    </svg>
                @elseif ($isSorted && $sortDirection === 'desc')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M15 10l-5 5-5-5h10z" />
                    </svg>
                @else
                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 10l5-5 5 5H5z" />
                    </svg>
                @endif
            </span>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
