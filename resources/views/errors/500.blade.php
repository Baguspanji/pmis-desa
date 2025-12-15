@extends('layouts.error')

@section('content')
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 md:p-12">
            <!-- Error Code -->
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-100 dark:bg-orange-900/30 mb-2">
                    <svg class="w-16 h-16 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h1 class="font-bold text-orange-600 dark:text-orange-400 mb-2 leading-0" style="font-size: 4.5rem;">500</h1>
                <h2 class="text-2xl md:text-3xl font-semibold text-neutral-800 dark:text-neutral-100 mb-4">
                    Terjadi Kesalahan Server
                </h2>
                <p class="text-neutral-600 dark:text-neutral-400 text-lg mb-8">
                    Maaf, terjadi kesalahan di server kami. Tim kami sedang bekerja untuk memperbaikinya.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="history.back()"
                        class="px-6 py-3 bg-neutral-200 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-lg font-semibold hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors duration-200">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </span>
                    </button>
                    <a href="{{ route('home') }}"
                        class="px-6 py-3 bg-orange-600 dark:bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-700 dark:hover:bg-orange-600 transition-colors duration-200">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Halaman Utama
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
