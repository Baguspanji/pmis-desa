@extends('layouts.error')

@section('content')
    <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 md:p-12">
            <!-- Error Code -->
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-2">
                    <svg class="w-16 h-16 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h1 class="font-bold text-indigo-600 dark:text-indigo-400 mb-2 leading-0" style="font-size: 4.5rem;">401</h1>
                <h2 class="text-2xl md:text-3xl font-semibold text-neutral-800 dark:text-neutral-100 mb-4">
                    Tidak Terautentikasi
                </h2>
                <p class="text-neutral-600 dark:text-neutral-400 text-lg mb-8">
                    Maaf, Anda harus login terlebih dahulu untuk mengakses halaman ini.
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
                        class="px-6 py-3 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg font-semibold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-200">
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
