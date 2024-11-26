<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'USeP Text Broadcast System') }}</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Standard favicon -->
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="32x32">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="64x64">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="128x128">
</head>

<body class="bg-gray-100 h-screen relative">
    <div class="h-full flex flex-col md:flex-row justify-center items-center relative">
        <!-- Left Side Content (Illustration) -->
        <div class="flex flex-col items-center justify-center md:items-start md:w-1/2 px-4 py-8 md:py-0">
            <h1 class="font-semibold text-2xl text-red-700 text-center md:text-left pl-0 md:pl-20 mb-4">
                USeP TEXT BROADCAST SYSTEM
            </h1>
            <img src="{{ asset('svg/loginIllus.svg') }}" alt="Broadcasting Image"
                class="w-[300px] h-auto md:w-[500px] lg:w-[600px]">
        </div>
        

        <!-- Right Side Login Form -->
        <div class="w-full max-w-md p-8 bg-white shadow-md rounded-lg z-10 md:w-1/3 mt-[-40px] mb-8">
            {{ $slot }} <!-- This allows injecting the content (login form) here -->
        </div>



        <!-- Wave SVG at the Bottom -->
        <img src="{{ asset('images/wave.png') }}" alt="Wave Effect" class="absolute bottom-0 left-0 w-full h-auto z-0">
</body>

</html>
