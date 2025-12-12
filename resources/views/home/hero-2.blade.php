<!-- Hero Section with Header -->
<section class="h-sm flex flex-col text-white bg-linear-to-r from-black/40 to-black/40 rounded-b-3xl"
    style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('assets/landing-page.png'); background-size: cover; background-position: center;">

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
                    class="hover:text-[#FDB913] transition hover:font-medium {{ Route::is('home') ? 'text-[#FDB913] font-medium' : 'text-white' }}">
                    Beranda
                </a>
                <a href="{{ route('profile') }}"
                    class="hover:text-[#FDB913] transition hover:font-medium {{ Route::is('profile') ? 'text-[#FDB913] font-medium' : 'text-white' }}">
                    Profil Desa
                </a>
                <a href="{{ route('news') }}"
                    class="hover:text-[#FDB913] transition hover:font-medium {{ Route::is('news') ? 'text-[#FDB913] font-medium' : 'text-white' }}">
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
</section>

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
    });
</script>
