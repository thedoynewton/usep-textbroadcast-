<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'USeP Text Broadcast System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Standard favicon -->
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="32x32">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="64x64">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="128x128">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>

</head>

<body class="font-sans antialiased">
    <div class="flex h-screen">
        <!-- Sidebar (Navigation) -->
        @include('layouts.navigation')

        <!-- Main Content Area -->
        <div class="bg-gray-200 flex-1 ml-72 overflow-auto z-0">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative z-10 p-8">
                {{ $slot }}
            </main>

            <!-- Wave Effect -->
            <img id="waveEffect" src="/images/wave.png" alt="Wave Effect"
                class="absolute bottom-0 left-0 w-full h-auto z-0">
        </div>
    </div>
</body>

</html>
