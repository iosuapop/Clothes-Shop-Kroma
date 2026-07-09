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
<body class="min-h-screen bg-riot-yellow flex items-center justify-center px-6">

    <div class="card-sticker bg-bone w-full max-w-sm p-8">
        <a href="{{ route('home') }}" class="font-display text-3xl block text-center mb-8">KROMA</a>
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
