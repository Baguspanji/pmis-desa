@props([
    'striped' => false,
    'bordered' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="w-full divide-y divide-gray-200">
        {{ $slot }}
    </table>
</div>
