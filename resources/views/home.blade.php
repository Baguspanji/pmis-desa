@extends('layouts.guest')

@section('content')
    @include('home.hero')

    @include('home.features')

    @include('home.visi-misi')

    @livewire('suggestion-page-component')

    @include('home.footer')
@endsection
