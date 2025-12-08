@props(['title', 'description' => null, 'breadcrumbs' => []])

<div class="flex flex-col gap-4 w-full">
    @if (count($breadcrumbs) > 0)
        <nav class="flex flex-col-reverse md:flex-row justify-between" aria-label="Breadcrumb">
            <!-- Breadcrumbs -->
            <ol class="inline-flex items-center space-x-1 md:space-x-2 max-w-sm md:max-w-md flex-wrap">
                @foreach ($breadcrumbs as $index => $breadcrumb)
                    <li class="inline-flex items-center shrink-0">
                        @if ($index > 0)
                            <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        @endif
                        @if (isset($breadcrumb['url']) && !$loop->last)
                            <a href="{{ $breadcrumb['url'] }}"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white whitespace-nowrap">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @else
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $breadcrumb['label'] }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>

            <!-- Role User -->
            <span class="mb-2 ml-auto md:mb-0">
                @if (Auth::user())
                    @php
                        switch (Auth::user()->role) {
                            case 'admin':
                                $jabatan = 'Administrator';
                                break;
                            case 'operator':
                                $jabatan = 'Operator';
                                break;
                            case 'kepala_desa':
                                $jabatan = 'Kepala Desa';
                                break;
                            case 'kasun':
                                $jabatan = 'Kepala Dusun';
                                break;
                            default:
                                # code...
                                $jabatan = Auth::user()->position ? Auth::user()->position : 'Staff';
                                break;
                        }
                    @endphp
                    <flux:badge color="green" variant="outline">
                        {{ $jabatan }}
                    </flux:badge>
                @endif
            </span>
        </nav>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
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
