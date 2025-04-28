<!-- resources/views/layouts/layout.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'My Application')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 flex flex-col items-center p-6 lg:p-8 min-h-screen space-y-6">
<header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
    <nav class="flex items-center justify-between gap-4">
        <a href="{{ route('welcome')}}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Home</a>
        @if (Route::has('login'))
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </nav>
</header>

<div class="w-full lg:max-w-4xl max-w-[335px]">
    @yield('content')
</div>
</body>
</html>
