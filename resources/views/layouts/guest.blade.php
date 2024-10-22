<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Standard favicon -->
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="32x32">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="64x64">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="128x128">
</head>

<body class="bg-gray-100 h-screen relative">
    <div class="h-full flex justify-between items-center relative">
        <!-- Left Side Content -->
        <div class="flex flex-col items-center ml-40 mb-16">
            <h1 class="font-semibold text-2xl text-center text-red-700">USeP TEXT BROADCAST SYSTEM</h1>
            <!-- Illustration Image -->
            <img src="{{ asset('svg/loginIllus.svg') }}" alt="Broadcasting Image"
                class="w-[400px] h-auto lg:w-[500px] lg:h-auto">
        </div>

        <!-- Right Side Login Form -->
        <div class="w-full max-w-md p-8 bg-white shadow-md rounded-lg z-10 mr-40">
            {{ $slot }} <!-- This allows injecting the content (login form) here -->
        </div>

        <!-- Wave SVG at the Bottom -->
        <img src="{{ asset('images/wave.png') }}" alt="Wave Effect" class="absolute bottom-0 left-0 w-full h-auto z-0">
    </div>
</body>

</html>
