<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — KROMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-flash-coral flex items-center justify-center px-6">
    <div class="card-sticker bg-bone text-center p-12 max-w-md">
        <p class="font-display text-8xl">404</p>
        <p class="font-mono text-sm mt-4">This page sold out, or never dropped.</p>
        <a href="{{ route('home') }}" class="card-sticker inline-block bg-ink text-bone font-display px-6 py-3 mt-8">
            BACK TO SHOP
        </a>
    </div>
</body>
</html>
