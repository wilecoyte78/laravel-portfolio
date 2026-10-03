<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%2314171F'/%3E%3Ctext x='32' y='43' font-family='Georgia, serif' font-size='26' fill='%23B8863F' text-anchor='middle'%3EBM%3C/text%3E%3C/svg%3E">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-inertia::head>
        <title>{{ config('app.name', 'Benjamin Mastrangelo') }}</title>
        <meta name="description" data-inertia="description" content="Benjamin Mastrangelo — Senior Full Stack Engineer specializing in PHP, Laravel, and Vue.js.">
        <meta property="og:title" data-inertia="og:title" content="Benjamin Mastrangelo">
        <meta property="og:description" data-inertia="og:description" content="Senior Full Stack Engineer specializing in PHP, Laravel, and Vue.js.">
        <meta property="og:type" data-inertia="og:type" content="website">
        <meta property="og:url" data-inertia="og:url" content="https://benjaminmastrangelo.com">
    </x-inertia::head>
</head>
<body class="antialiased">
    <x-inertia::app />
</body>
</html>
