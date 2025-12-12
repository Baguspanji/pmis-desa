<div>
    <!-- News Detail Section -->
    <section class="bg-gray-50 py-12 px-5">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <a href="{{ url('/#berita-desa') }}"
                class="inline-flex items-center text-[#035270] hover:text-[#046B8C] mb-6 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Berita Desa
            </a>

            <!-- Main Article -->
            <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Featured Image -->
                @if ($news->image)
                    <div class="w-full h-96 bg-gray-200 overflow-hidden">
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                            class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Article Content -->
                <div class="p-8 md:p-12">
                    <!-- Category & Date -->
                    <div class="flex items-center gap-4 mb-4 text-sm">
                        @if ($news->category)
                            <span class="inline-flex items-center gap-2 text-[#035270] font-semibold">
                                <span class="w-2 h-2 bg-[#035270] rounded-full"></span>
                                {{ $news->category }}
                            </span>
                        @endif
                        <span class="text-gray-500">
                            {{ $news->published_at->format('d F Y') }}
                        </span>
                        @if ($news->is_featured)
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold">
                                FEATURED
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        {{ $news->title }}
                    </h1>

                    <!-- Author Info -->
                    {{-- @if ($news->creator)
                        <div class="flex items-center gap-3 pb-6 mb-6 border-b border-gray-200">
                            <div
                                class="w-10 h-10 bg-[#035270] rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($news->creator->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $news->creator->name }}</p>
                                <p class="text-sm text-gray-500">Penulis</p>
                            </div>
                        </div>
                    @endif --}}

                    <!-- Excerpt -->
                    @if ($news->excerpt)
                        <div
                            class="text-lg text-gray-600 leading-relaxed mb-8 p-4 bg-gray-50 border-l-4 border-[#035270] rounded">
                            {{ $news->excerpt }}
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($news->content)) !!}
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Related News Section -->
    @if ($relatedNews->count() > 0)
        <section class="bg-white py-16 px-5">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">Berita Terkait</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($relatedNews as $related)
                        <article
                            class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-[#035270]/20">
                            <a href="{{ route('news.detail', $related->slug) }}" wire:navigate>
                                <div class="relative h-48 bg-gray-200 overflow-hidden">
                                    <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&h=300&fit=crop' }}"
                                        alt="{{ $related->title }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute top-4 right-4">
                                        <span
                                            class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-[#035270]">
                                            {{ $related->published_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    @if ($related->category)
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="inline-block w-2 h-2 bg-[#035270] rounded-full"></span>
                                            <span
                                                class="text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ $related->category }}</span>
                                        </div>
                                    @endif
                                    <h3
                                        class="text-[#035270] text-lg font-bold mb-3 line-clamp-2 group-hover:text-[#046B8C] transition-colors">
                                        {{ $related->title }}
                                    </h3>
                                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
                                        {{ $related->excerpt ?? Str::limit(strip_tags($related->content), 100) }}
                                    </p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
