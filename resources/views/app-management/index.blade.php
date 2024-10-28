<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('App Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-black overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Sub-navigation for different sections -->
                <nav class="mb-6">
                    <ul class="flex space-x-4">
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'contacts']) }}"
                               class="{{ request('section') == 'contacts' ? 'text-blue-500 font-bold' : 'text-white dark:text-gray-300' }}">
                                Contacts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'templates']) }}"
                               class="{{ request('section') == 'templates' ? 'text-blue-500 font-bold' : 'text-white dark:text-gray-300' }}">
                                Message Templates
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Display section based on active tab -->
                @if (request('section') == 'templates')
                    <!-- Message Templates Section -->
                    <x-message-template-table :messageTemplates="$messageTemplates" />
                    <x-create-message-template-modal />
                @else
                    <!-- Contacts Section -->
                    <div>
                        <form method="GET" action="{{ route('app-management.index') }}" class="mb-6">
                            <div class="flex items-center space-x-4">
                                <!-- Search Input -->
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search by email"
                                       class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100" />
                    
                                <!-- Campus Filter Dropdown -->
                                <select name="campus_id" class="border rounded px-8 py-2 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">All Campuses</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->campus_id }}"
                                            {{ request('campus_id') == $campus->campus_id ? 'selected' : '' }}>
                                            {{ $campus->campus_name }}
                                        </option>
                                    @endforeach
                                </select>
                    
                                <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded">Filter</button>
                            </div>
                        </form>
                    </div>                    

                    <x-students-table :students="$students" :totalStudents="$totalStudents" />
                    <x-employees-table :employees="$employees" :totalEmployees="$totalEmployees" />
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
