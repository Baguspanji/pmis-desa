@props([
    'align' => 'left',
    'colspan' => null,
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
@endphp

<td @if ($colspan) colspan="{{ $colspan }}" @endif
    {{ $attributes->merge([
        'class' => 'px-4 py-3 text-sm text-gray-900 whitespace-nowrap ' . ($alignClasses[$align] ?? 'text-left'),
    ]) }}>
    {{ $slot }}
</td>
