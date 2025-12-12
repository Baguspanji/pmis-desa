<!-- Berita Desa (Village News) Section -->
<section id="berita-desa" class="bg-white py-20 px-5">
    <div class="max-w-7xl mx-auto">
        <!-- Section Title -->
        <div class="mb-10 md:mb-12">
            <h2 class="text-xl md:text-3xl font-bold text-gray-900 mb-2">Berita Desa</h2>
            <p class="text-gray-600 text-sm md:text-base">Menyajikan informasi terbaru tentang peristiwa, berita terkini,
                dan artikel-artikel jurnalistik dari Desa Kertosari</p>
        </div>

        <!-- Featured Banner Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Banner 1 -->
            <div class="relative group overflow-hidden rounded-xl shadow-2xl h-64 md:h-80">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&h=600&fit=crop"
                    alt="Berita Utama"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <span class="inline-block px-3 py-1 bg-red-600 rounded-full text-xs font-semibold mb-3">BERITA
                        UTAMA</span>
                    <h3 class="text-2xl md:text-3xl font-bold mb-2 line-clamp-2">Posyadu Lansia dan Balita di Desa
                        Kertosari</h3>
                    <p class="text-gray-200 text-sm line-clamp-2">
                        Kegiatan pelayanan kesehatan masyarakat untuk lansia dan balita yang rutin dilaksanakan setiap
                        bulan
                    </p>
                </div>
            </div>

            <!-- Banner 2 -->
            <div class="relative group overflow-hidden rounded-xl shadow-2xl h-64 md:h-80">
                <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?w=800&h=600&fit=crop"
                    alt="Berita Terpopuler"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <span
                        class="inline-block px-3 py-1 bg-orange-600 rounded-full text-xs font-semibold mb-3">TERPOPULER</span>
                    <h3 class="text-2xl md:text-3xl font-bold mb-2 line-clamp-2">Arum Jeram Desa Wisata Kertosari</h3>
                    <p class="text-gray-200 text-sm line-clamp-2">
                        Destinasi wisata alam dengan tantangan arum jeram yang menarik di tengah keindahan alam
                        Kertosari
                    </p>
                </div>
            </div>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @for ($i = 0; $i < 2; $i++)
                <!-- News Card 1 -->
                <article
                    class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-[#035270]/20">
                    <div class="relative h-56 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&h=300&fit=crop"
                            alt="Posyadu Lansia dan Balita di desa kertosari"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4">
                            <span
                                class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-[#035270]">
                                12 Des 2025
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        {{-- <div class="flex items-center gap-2 mb-3">
                            <span class="inline-block w-2 h-2 bg-[#035270] rounded-full"></span>
                            <span class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Kesehatan</span>
                        </div> --}}
                        <h3
                            class="text-[#035270] text-xl font-bold mb-3 line-clamp-2 group-hover:text-[#046B8C] transition-colors">
                            Posyadu Lansia dan Balita di desa kertosari
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Provident modi ex velit aspernatur
                            exercitationem cumque, odio quia ipsa saepe. Voluptatum.
                        </p>
                        {{-- <a href="#"
                            class="inline-flex items-center text-[#035270] text-sm font-semibold hover:gap-2 transition-all duration-300">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a> --}}
                    </div>
                </article>

                <!-- News Card 2 -->
                <article
                    class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-[#035270]/20">
                    <div class="relative h-56 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?w=500&h=300&fit=crop"
                            alt="Arum Jeram Desa Wisata Kertosari"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4">
                            <span
                                class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-[#035270]">
                                11 Des 2025
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        {{-- <div class="flex items-center gap-2 mb-3">
                            <span class="inline-block w-2 h-2 bg-[#035270] rounded-full"></span>
                            <span class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Wisata</span>
                        </div> --}}
                        <h3
                            class="text-[#035270] text-xl font-bold mb-3 line-clamp-2 group-hover:text-[#046B8C] transition-colors">
                            Arum Jeram Desa Wisata Kertosari Pasuruan
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Provident modi ex velit aspernatur
                            exercitationem cumque, odio quia ipsa saepe. Voluptatum.
                        </p>
                        {{-- <a href="#"
                            class="inline-flex items-center text-[#035270] text-sm font-semibold hover:gap-2 transition-all duration-300">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a> --}}
                    </div>
                </article>

                <!-- News Card 3 -->
                <article
                    class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-[#035270]/20">
                    <div class="relative h-56 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=500&h=300&fit=crop"
                            alt="Air Terjun Baong Pasuruan"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4">
                            <span
                                class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-[#035270]">
                                10 Des 2025
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        {{-- <div class="flex items-center gap-2 mb-3">
                            <span class="inline-block w-2 h-2 bg-[#035270] rounded-full"></span>
                            <span class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Pariwisata</span>
                        </div> --}}
                        <h3
                            class="text-[#035270] text-xl font-bold mb-3 line-clamp-2 group-hover:text-[#046B8C] transition-colors">
                            Air Terjun Baong Pasuruan
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Provident modi ex velit aspernatur
                            exercitationem cumque, odio quia ipsa saepe. Voluptatum.
                        </p>
                        {{-- <a href="#"
                            class="inline-flex items-center text-[#035270] text-sm font-semibold hover:gap-2 transition-all duration-300">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a> --}}
                    </div>
                </article>
            @endfor
        </div>

        <!-- Simple Pagination -->
        <div class="flex justify-between items-center gap-2 mt-10 md:mt-12">
            <a href="#"
                class="inline-flex items-center px-4 py-2 bg-white border-2 border-[#035270] text-[#035270] text-sm font-semibold rounded-lg hover:bg-[#035270] hover:text-white transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Sebelumnya
            </a>
            <a href="#"
                class="inline-flex items-center px-4 py-2 bg-[#035270] border-2 border-[#035270] text-white text-sm font-semibold rounded-lg hover:bg-[#046B8C] hover:border-[#046B8C] transition-all duration-300">
                Selanjutnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section>
