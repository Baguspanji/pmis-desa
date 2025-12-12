@extends('layouts.guest')

@section('content')
    @include('home.hero-2')

    @livewire('news-detail-component', ['slug' => $slug])

    @include('home.footer')
@endsection
