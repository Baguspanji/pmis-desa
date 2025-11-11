@props(['title', 'description' => null, 'breadcrumbs' => []])

<div class="flex flex-col gap-4 w-full">
    @if (count($breadcrumbs) > 0)
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                @foreach ($breadcrumbs as $index => $breadcrumb)
                    <li class="inline-flex items-center">
                        @if ($index > 0)
                            <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        @endif
                        @if (isset($breadcrumb['url']) && !$loop->last)
                            <a href="{{ $breadcrumb['url'] }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @else
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $breadcrumb['label'] }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="flex flex-row items-center justify-between gap-2">
        <div class="flex flex-col">
            <flux:heading size="xl">{{ $title }}</flux:heading>
            @if ($description)
                <flux:subheading>{{ $description }}</flux:subheading>
            @endif
        </div>

        @if (isset($actions))
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
