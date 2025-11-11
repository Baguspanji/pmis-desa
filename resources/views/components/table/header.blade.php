@props([])

<thead {{ $attributes->merge(['class' => 'bg-gray-50']) }}>
    <tr>
        {{ $slot }}
    </tr>
</thead>
