<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="font-sans text-gray-800">
    <div
        class="min-h-screen flex items-center justify-center bg-linear-to-br from-blue-50 to-indigo-100 dark:from-neutral-900 dark:to-neutral-800 px-4">
        <div class="max-w-7xl w-full flex items-center justify-center">
            @yield('content')
        </div>
    </div>
</body>

</html>
