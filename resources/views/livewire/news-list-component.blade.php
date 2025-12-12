<div>
    <!-- Featured Banner Grid -->
    @if($featuredNews->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @foreach($featuredNews as $index => $featured)
        <a href="{{ route('news.detail', $featured->slug) }}" wire:navigate class="relative group overflow-hidden rounded-xl shadow-2xl h-64 md:h-80 block cursor-pointer">
            <img src="{{ $featured->image ? asset('storage/' . $featured->image) : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=600&fit=crop' }}"
                alt="{{ $featured->title }}"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <span class="inline-block px-3 py-1 {{ $index === 0 ? 'bg-red-600' : 'bg-orange-600' }} rounded-full text-xs font-semibold mb-3">
                    {{ $index === 0 ? 'BERITA UTAMA' : 'TERPOPULER' }}
                </span>
                <h3 class="text-2xl md:text-3xl font-bold mb-2 line-clamp-2">{{ $featured->title }}</h3>
                <p class="text-gray-200 text-sm line-clamp-2">
                    {{ $featured->excerpt ?? Str::limit(strip_tags($featured->content), 150) }}
                </p>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    <!-- News Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($news as $item)
        <article
            class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-[#035270]/20">
            <a href="{{ route('news.detail', $item->slug) }}" wire:navigate class="block">
                <div class="relative h-56 bg-gray-200 overflow-hidden">
                    <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&h=300&fit=crop' }}"
                        alt="{{ $item->title }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 right-4">
                        <span
                            class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-[#035270]">
                            {{ $item->published_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    @if($item->category)
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-block w-2 h-2 bg-[#035270] rounded-full"></span>
                        <span class="text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ $item->category }}</span>
                    </div>
                    @endif
                    <h3
                        class="text-[#035270] text-xl font-bold mb-3 line-clamp-2 group-hover:text-[#046B8C] transition-colors">
                        {{ $item->title }}
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">
                        {{ $item->excerpt ?? Str::limit(strip_tags($item->content), 120) }}
                    </p>
                </div>
            </a>
        </article>
        @empty
        <div class="col-span-full text-center py-10">
            <p class="text-gray-500 text-lg">Belum ada berita tersedia</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($news->hasPages())
    <div class="flex justify-between items-center gap-2 mt-10 md:mt-12">
        @if($news->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 bg-gray-100 border-2 border-gray-300 text-gray-400 text-sm font-semibold rounded-lg cursor-not-allowed">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Sebelumnya
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-white border-2 border-[#035270] text-[#035270] text-sm font-semibold rounded-lg hover:bg-[#035270] hover:text-white transition-all duration-300">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Sebelumnya
            </button>
        @endif

        @if($news->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-[#035270] border-2 border-[#035270] text-white text-sm font-semibold rounded-lg hover:bg-[#046B8C] hover:border-[#046B8C] transition-all duration-300">
                Selanjutnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        @else
            <span class="inline-flex items-center px-4 py-2 bg-gray-100 border-2 border-gray-300 text-gray-400 text-sm font-semibold rounded-lg cursor-not-allowed">
                Selanjutnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </div>
    @endif
</div>
