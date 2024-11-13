<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <!-- Display Session Error Message -->
    @if(session('error'))
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <h2 class="font-bold">Error</h2>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    

    <img src="/images/SePhi Favicon.png" alt="USeP Logo" class="w-26 h-24 mx-auto">
    <h1 class="font-bold text-2xl text-center mt-4 text-red-700">WELCOME BACK</h1>
    <p class="text-center text-gray-600 mt-2 mb-6">Log in using your account on:</p>

    <!-- Google Login Button -->
    <a href="{{ route('google.login') }}"
        class="inline-flex items-center justify-center px-4 py-2 mb-4 bg-red-700 text-white font-semibold rounded-lg shadow-md w-full">
        Continue with Google
    </a>

    {{-- <!-- Divider -->
    <div class="flex items-center my-4">
        <hr class="flex-grow border-gray-300">
        <span class="mx-4 text-gray-500">or</span>
        <hr class="flex-grow border-gray-300">
    </div>

    <!-- Email Login Form -->
    <form method="POST" action="{{ route('login.email') }}">
        @csrf
        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-left mb-2">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your USeP email"
                class="border rounded border-gray-500 py-2 px-3 w-full" required>
        </div>

        <!-- Email Login Button -->
        <button type="submit" class="bg-red-700 text-white font-semibold px-4 py-2 rounded-lg w-full mb-6">
            Continue with email
        </button>
    </form> --}}

    <!-- Footer -->
    <p class="text-gray-500 text-center text-sm">
        Copyright © 2024. All Rights Reserved.
    </p>
    <div class="mt-4 text-center">
        <a href="#" class="text-red-700 hover:underline mx-2">Terms of Use</a> |
        <a href="#" class="text-red-700 hover:underline mx-2">Privacy Policy</a>
    </div>
</x-guest-layout>
