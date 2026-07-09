<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'KROMA' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col">

    <header class="border-b-4 border-ink bg-bone sticky top-0 z-40">
        <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-display text-3xl">KROMA</a>

            <nav class="flex items-center gap-6 font-mono text-sm">
                <a href="{{ route('home') }}#featured">SHOP</a>
                <livewire:search-bar />

                @auth
                    <a href="{{ route('account.orders') }}">ORDERS</a>
                    <a href="{{ route('account.wishlist') }}">WISHLIST</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">ADMIN</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">LOGOUT</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">LOGIN</a>
                @endauth

                <livewire:cart-counter />
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="bg-electric text-bone font-mono text-sm px-6 py-3 text-center">
            {{ session('success') }}
        </div>
    @endif

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t-4 border-ink bg-ink text-bone px-6 py-10 font-mono text-xs">
        <div class="mx-auto max-w-6xl flex justify-between">
            <span>&copy; {{ date('Y') }} KROMA. All rights reserved.</span>
            <span>Made for the FSEGA thesis project.</span>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
