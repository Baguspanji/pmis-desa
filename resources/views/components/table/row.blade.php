@props([
    'hoverable' => true,
    'clickable' => false,
])

<tr
    {{ $attributes->merge([
        'class' =>
            'border-b border-gray-200 ' .
            ($hoverable ? 'hover:bg-gray-100 hover:shadow-sm transition-all duration-200 ' : '') .
            ($clickable ? 'cursor-pointer ' : ''),
    ]) }}>
    {{ $slot }}
</tr>
