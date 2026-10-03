<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page not found — {{ config('app.name', 'Benjamin Mastrangelo') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-paper font-sans text-ink">
    <div class="max-w-md px-6 text-center">
        <p class="font-display text-7xl text-brass">404</p>
        <h1 class="mt-4 font-display text-2xl text-ink">This page doesn't exist.</h1>
        <p class="mt-3 text-ink/70">The link might be broken, or the page may have moved.</p>
        <a href="/" class="mt-6 inline-block text-ink underline decoration-brass decoration-2 underline-offset-4 hover:text-brass">
            Back to the homepage
        </a>
    </div>
</body>
</html>
