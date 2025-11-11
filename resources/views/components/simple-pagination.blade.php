@props(['pagination'])

@if ($pagination && $pagination['last_page'] > 1)
    <div class="flex items-center justify-between">
        <div class="flex justify-between flex-1">
            @if ($pagination['prev_page_url'])
                <a href="{{ $pagination['prev_page_url'] }}"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Sebelumnya
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-lg cursor-not-allowed">
                    Sebelumnya
                </span>
            @endif

            @if ($pagination['next_page_url'])
                <a href="{{ $pagination['next_page_url'] }}"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Selanjutnya
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-lg cursor-not-allowed">
                    Selanjutnya
                </span>
            @endif
        </div>
    </div>
@endif
