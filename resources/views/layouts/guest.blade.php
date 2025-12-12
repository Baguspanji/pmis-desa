<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')

    @stack('script')
</head>

<body class="font-sans text-gray-800">
    @yield('content')
</body>

</html>
