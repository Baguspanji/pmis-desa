<!-- Hero Section with Header -->
<section id="hero-section"
    class="h-screen flex flex-col text-white bg-linear-to-r from-black/40 to-black/40 rounded-b-3xl relative overflow-hidden"
    style="background-size: cover; background-position: center; transition: background-image 1s ease-in-out;">

    <!-- Background Slider Images -->
    <div class="absolute inset-0 -z-10">
        <div id="bg-slider" class="w-full h-full relative">
            <div class="hero-slide absolute inset-0 opacity-100 transition-opacity duration-1000"
                style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{ asset('assets/landing-page.webp') }}); background-size: cover; background-position: center;">
            </div>
            <!-- Add more slides here - you can duplicate this div for more images -->
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000"
                style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{ asset('assets/landing-page-1.webp') }}); background-size: cover; background-position: center;">
            </div>
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000"
                style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url({{ asset('assets/landing-page-2.webp') }}); background-size: cover; background-position: center;">
            </div>
        </div>
    </div>

    <!-- Slider Navigation Dots -->
    <div class="absolute bottom-6 md:bottom-24 left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
        <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-slide="0"></button>
        <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-slide="1"></button>
        <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-slide="2"></button>
    </div>

    <!-- Header with Transparent Background -->
    <header class="bg-transparent py-4 w-full z-50">
        <div class="max-w-7xl mx-auto px-5 flex md:grid md:grid-cols-3 items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo_paskab.png') }}" alt="logo-pemerintah-mandiri" class="h-14">
                <div class="flex flex-col">
                    <span class="text-xl font-semibold leading-5">Desa Kertosari</span>
                    <span class="text-sm">Kabupaten Pasuruan</span>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center justify-center gap-8">
                <a href="{{ route('home') }}"
                    class="px-4 py-1.5 rounded-full bg-white/10 hover:text-[#FDB913] transition hover:font-medium {{ Route::is('home') ? 'text-[#FDB913] font-medium' : 'text-white' }}">
                    Beranda
                </a>
                <a href="{{ route('profile') }}"
                    class="px-4 py-1.5 rounded-full bg-white/10 hover:text-[#FDB913] transition hover:font-medium {{ Route::is('profile') ? 'text-[#FDB913] font-medium' : 'text-white' }}">
                    Profil Desa
                </a>
                <a href="{{ route('news') }}"
                    class="px-4 py-1.5 rounded-full bg-white/10 hover:text-[#FDB913] transition hover:font-medium {{ Route::is('news') ? 'text-[#FDB913] font-medium' : 'text-white' }}">
                    Berita & Acara
                </a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center justify-end gap-3">
                {{-- <input type="text"
                    class="hidden sm:block px-4 py-2 border border-white/30 bg-white/10 backdrop-blur-sm rounded-full text-sm text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 w-48 md:w-64"
                    placeholder="Cari Program"> --}}
                <a href="{{ route('dashboard') }}"
                    class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:bg-white/30 transition">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" stroke="white" stroke-width="2" />
                        <path d="M6 21V19C6 16.7909 7.79086 15 10 15H14C16.2091 15 18 16.7909 18 19V21" stroke="white"
                            stroke-width="2" />
                    </svg>
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn"
                    class="md:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 relative">
                    <span class="block w-6 h-0.5 bg-white transition-all duration-300 origin-center"
                        id="line1"></span>
                    <span class="block w-6 h-0.5 bg-white transition-all duration-300" id="line2"></span>
                    <span class="block w-6 h-0.5 bg-white transition-all duration-300 origin-center"
                        id="line3"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <nav id="mobile-menu"
            class="hidden md:hidden absolute top-20 left-0 right-0 bg-black/90 backdrop-blur-sm border-t border-white/10 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-5 py-4 flex flex-col gap-4">
                <a href="{{ route('home') }}"
                    class="text-white hover:text-[#FDB913] transition hover:font-medium py-2 {{ Route::is('home') ? 'text-[#FDB913] font-medium' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('profile') }}"
                    class="text-white hover:text-[#FDB913] transition hover:font-medium py-2 {{ Route::is('profile') ? 'text-[#FDB913] font-medium' : '' }}">
                    Profil Desa
                </a>
                <a href="{{ route('news') }}"
                    class="text-white hover:text-[#FDB913] transition hover:font-medium py-2 {{ Route::is('news') ? 'text-[#FDB913] font-medium' : '' }}">
                    Berita & Acara
                </a>
                {{-- <div class="pt-2 border-t border-white/10">
                    <input type="text"
                        class="w-full px-4 py-2 border border-white/30 bg-white/10 backdrop-blur-sm rounded-full text-sm text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50"
                        placeholder="Cari Program">
                </div> --}}
            </div>
        </nav>
    </header>

    <!-- Hero Content -->
    <div class="flex-1 flex items-center justify-center text-center">
        <div class="max-w-4xl px-5">
            <h2 class="text-2xl md:text-3xl font-normal mb-3">Selamat Datang di</h2>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-5 capitalize">Website Resmi Desa Kertosari</h1>
            <h3 class="text-2xl md:text-3xl lg:text-4xl font-semibold mb-5">Manajemen APBDesa Kertosari</h3>
            <p class="text-base md:text-lg leading-tight mb-8 max-w-3xl mx-auto">
                Desa Kertosari secara resmi ditetapkan sebagai Desa Wisata Kertosari melalui Keputusan Bupati
                Pasuruan pada tahun 2019. Visi kami adalah menjadi kawasan wisata pedesaan yang menjadi percontohan
                dalam pembangunan pariwisata berkelanjutan, dengan fokus pada konservasi alam, pelestarian seni
                budaya, dan peningkatan kesejahteraan masyarakat (Perekonomian Kerakyatan). Kami dikelola oleh
                Kelompok Sadar Wisata (POKDARWIS) yang beranggotakan masyarakat setempat.
            </p>
            <button onclick="document.getElementById('features').scrollIntoView({ behavior: 'smooth' })"
                class="bg-[#FDB913] hover:bg-yellow-500 text-gray-800 px-9 py-2 rounded-full text-xl font-semibold transition-all hover:-translate-y-1 hover:shadow-xl cursor-pointer">
                Selengkapnya
            </button>
        </div>
    </div>
</section>

@push('script')
    <script>
        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const line1 = document.getElementById('line1');
            const line2 = document.getElementById('line2');
            const line3 = document.getElementById('line3');
            let isMenuOpen = false;

            menuBtn.addEventListener('click', function() {
                isMenuOpen = !isMenuOpen;

                if (isMenuOpen) {
                    // Show menu
                    mobileMenu.classList.remove('hidden');
                    menuBtn.classList.add('pl-3');
                    // Animate hamburger to X
                    line1.style.transform = 'rotate(45deg) translateY(11.5px)';
                    line2.style.opacity = '0';
                    line3.style.transform = 'rotate(-45deg) translateY(-11px)';
                } else {
                    // Hide menu
                    mobileMenu.classList.add('hidden');
                    menuBtn.classList.remove('pl-3');
                    // Reset hamburger
                    line1.style.transform = 'none';
                    line2.style.opacity = '1';
                    line3.style.transform = 'none';
                }
            });

            // Close menu when a link is clicked
            const menuLinks = mobileMenu.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    isMenuOpen = false;
                    mobileMenu.classList.add('hidden');
                    line1.style.transform = 'none';
                    line2.style.opacity = '1';
                    line3.style.transform = 'none';
                });
            });

            // Background Image Slider
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.slider-dot');
            let currentSlide = 0;
            const slideInterval = 5000; // Change image every 5 seconds

            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => {
                    slide.style.opacity = '0';
                });

                // Remove active state from all dots
                dots.forEach(dot => {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white/50');
                });

                // Show current slide
                slides[index].style.opacity = '100';

                // Highlight current dot
                dots[index].classList.remove('bg-white/50');
                dots[index].classList.add('bg-white');

                currentSlide = index;
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            // Auto slide
            let autoSlide = setInterval(nextSlide, slideInterval);

            // Dot click handlers
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    clearInterval(autoSlide); // Stop auto slide when user clicks
                    showSlide(index);
                    autoSlide = setInterval(nextSlide, slideInterval); // Restart auto slide
                });
            });

            // Initialize first slide
            showSlide(0);
        });
    </script>
@endpush
