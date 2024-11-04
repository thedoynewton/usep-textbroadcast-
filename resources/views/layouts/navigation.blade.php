<nav x-data="{ open: false }" class="bg-white border-r border-gray-100 w-64 flex-shrink-0">
    <!-- Primary Navigation Menu -->
    <div class="px-6 py-4">
        <!-- Logo -->
        <div class="flex flex-col items-center mt-6">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                <h1 style="font-size: 20px;" class="mt-2 text-2xl font-bold text-red-700">SePhi</h1>
                <p style="font-size: 8px;" class="text-gray-600">Text Broadcast</p>
            </a>
        </div>

        <hr class="my-4 border-t-2 border-gray-200 w-full">

        <!-- Settings Dropdown -->
        <div class="flex items-center justify-center w-full my-6">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                    class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-500 bg-white hover:text-gray-700 focus:outline-none">
                        <!-- Display smaller avatar -->
                        <img src="{{ Auth::user()->avatar }}" alt="User Avatar" class="w-10 h-auto rounded-full" />

                        <!-- Display user name -->
                        <div class="text-black pl-2 text-sm font-medium">{{ Auth::user()->name }}</div>

                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                    this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <hr class="my-4 border-t-2 border-gray-200 w-full">

        <!-- Navigation Links -->
        <div class="mt-6 flex flex-col space-y-2">

            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>

            @if (Auth::user()->role === 'admin' || Auth::user()->role === 'subadmin')
                <x-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.index')">
                    {{ __('Messages') }}
                </x-nav-link>
                <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.index')">
                    {{ __('Analytics') }}
                </x-nav-link>
            @endif

            @if (Auth::user()->role === 'admin')
                <x-nav-link :href="route('user-management')" :active="request()->routeIs('user-management')">
                    {{ __('User Management') }}
                </x-nav-link>
                <x-nav-link :href="route('app-management.index')" :active="request()->routeIs('app-management.index')">
                    {{ __('App Management') }}
                </x-nav-link>
            @endif
        </div>


        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
