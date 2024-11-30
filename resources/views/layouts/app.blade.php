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

    <style>
        /* Responsive Sidebar */
        .sidebar {
            width: 18rem;
            /* 72 in Tailwind */
            background-color: #f8fafc;
            /* Light gray */
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
            transform: translateX(0);
            z-index: 50;
            /* Ensure sidebar is above other content */
        }

        /* Sidebar for mobile (hidden by default) */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }

        /* Main content adjustments */
        .main-content {
            margin-left: 18rem;
            /* Matches sidebar width */
            transition: margin-left 0.3s ease-in-out;
            z-index: 40;
            /* Ensure main content is below the sidebar */
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        /* Wave Effect Adjustments for Mobile */
        @media (max-width: 768px) {
            #waveEffect {
                display: none;
            }
        }

        /* Navigation Toggle Button */
        .nav-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            /* Ensure toggle button is above the sidebar */
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-toggle {
                display: block;
            }
        }

        /* Adjusting the header content on responsive screens */
        @media (max-width: 768px) {
            header.bg-white.shadow div {
                padding-left: 5rem;
                /* Adjust to desired value */
            }
        }
    </style>

</head>

<body class="font-sans antialiased">
    <!-- Sidebar (Navigation) -->
    <div class="sidebar" id="sidebar">
        @include('layouts.navigation')
    </div>

    <!-- Navigation Toggle Button for Mobile -->
    <button class="nav-toggle" id="navToggle">
        ☰
    </button>

    <!-- Main Content Area -->
    <div class="main-content bg-gray-200 flex-1 overflow-auto z-0 h-screen">
        <!-- Page Heading -->
        @isset($header)
            <header class="bg-[#800000] shadow pl-16">
                <div class="max-w-7xl mx-auto py-6">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="relative z-10 p-4 md:p-8">
            {{ $slot }}
        </main>

        <!-- Wave Effect -->
        <img id="waveEffect" src="/images/wave.png" alt="Wave Effect"
            class="absolute bottom-0 left-0 w-full h-auto z-0 opacity-70">
    </div>

    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const navToggle = document.getElementById('navToggle');

        navToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    </script>
</body>

</html>
