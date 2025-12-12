@extends('layouts.guest')

@section('content')
    @include('home.hero')

    @include('home.features')

    @include('home.visi-misi')

    @include('home.footer')

    <!-- Floating Complaint Button -->
    <button onclick="Livewire.dispatch('open-complaint-form')"
        class="fixed bottom-6 right-6 z-50 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 group"
        title="Kirim Pengaduan">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
        </svg>
        <span
            class="absolute right-full mr-3 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-sm px-3 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
            Kirim Pengaduan
        </span>
    </button>

    <!-- Include Complaint Form Modal -->
    @livewire('complaint.form')
@endsection
