<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('App Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

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
                                class="{{ request('section') == 'contacts' ? 'text-blue-500 font-bold' : 'text-black dark:text-gray-300' }}">
                                Contacts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'templates']) }}"
                                class="{{ request('section') == 'templates' ? 'text-blue-500 font-bold' : 'text-black dark:text-gray-300' }}">
                                Message Templates
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'db-connection']) }}"
                                class="{{ request('section') == 'db-connection' ? 'text-blue-500 font-bold' : 'text-black dark:text-gray-300' }}">
                                DB Connection
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Display section based on active tab -->
                @if (request('section') == 'templates')
                    <!-- Message Templates Section -->
                    <x-message-template-table :messageTemplates="$messageTemplates" />
                    <x-create-message-template-modal />
                @elseif (request('section') == 'db-connection')
                    <!-- DB Connection Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Database Connection Settings</h3>

                        <!-- Card with Obrero Name -->
                        <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 shadow-md max-w-md cursor-pointer"
                            id="obreroCard">
                            <h4 class="text-xl font-semibold mb-2 text-gray-800">Obrero</h4>
                            <p class="text-gray-600">Click to learn more about Obrero.</p>
                        </div>
                    </div>

                    <!-- Obrero Modal -->
                    <div id="obreroModal"
                        class="fixed inset-0 items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
                            <h3 class="text-2xl font-bold mb-4">Obrero</h3>
                            <p class="text-gray-700 mb-4">This is additional information about Obrero.</p>

                            <!-- Import Buttons -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <!-- Import College Button -->
                                <form method="POST" action="{{ route('import.college') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Import College
                                    </button>
                                </form>

                                <!-- Import Program Button -->
                                <form method="POST" action="{{ route('import.programs') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Import Program
                                    </button>
                                </form>

                                <!-- Import Major Button inside Modal -->
                                <form method="POST" action="{{ route('import.majors') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                        Import Major
                                    </button>
                                </form>

                                <!-- Import Years Button inside Modal -->
                                <form method="POST" action="{{ route('import.years') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                        Import Years
                                    </button>
                                </form>

                                <!-- Import Students Button inside Modal -->
                                <form method="POST" action="{{ route('import.students') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
                                        Import Students
                                    </button>
                                </form>

                            </div>

                            <div class="flex justify-end mt-4">
                                <button id="closeObreroModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Display Campus Data in a Table -->
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold mb-4">Campuses</h4>
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Campus ID</th>
                                    <th class="py-2 px-4 border-b">Campus Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campuses as $campus)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $campus->campus_id }}</td>
                                        <td class="py-2 px-4 border-b">{{ $campus->campus_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Contacts Section -->
                    <div>
                        <form id="filterForm" method="GET" action="{{ route('app-management.index') }}"
                            class="mb-6">
                            @csrf
                            <div class="flex items-center space-x-4">
                                <!-- Search Input -->
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                    placeholder="Search by email, first name, or last name"
                                    class="border rounded px-4 py-2 dark:bg-gray-700 dark:text-gray-100" />

                                <!-- Campus Filter Dropdown -->
                                <select id="campusFilter" name="campus_id"
                                    class="border rounded px-8 py-2 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">All Campuses</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->campus_id }}"
                                            {{ request('campus_id') == $campus->campus_id ? 'selected' : '' }}>
                                            {{ $campus->campus_name }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Type Filter Dropdown (Moved beside campus dropdown) -->
                                <select id="typeFilter" name="type"
                                    class="border rounded px-8 py-2 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">All Types</option>
                                    <option value="Student" {{ request('type') == 'Student' ? 'selected' : '' }}>
                                        Student</option>
                                    <option value="Employee" {{ request('type') == 'Employee' ? 'selected' : '' }}>
                                        Employee</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Include the contacts table using the partial -->
                    <div id="contactsResults">
                        @include('partials.contacts-table', ['contacts' => $contacts])
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Edit Contact Modal using modal.blade.php component -->
    <x-modal name="editContactModal" maxWidth="md">
        <h2 class="text-lg font-semibold mb-4">Edit Contact Number</h2>
        <form id="editForm">
            @csrf
            <input type="hidden" id="contactId" name="contact_id" />

            <div class="mb-4">
                <label for="contactName" class="block font-medium text-gray-700">Name</label>
                <input type="text" id="contactName" name="contact_name" class="border rounded w-full px-4 py-2"
                    readonly />
            </div>

            <div class="mb-4">
                <label for="contactNumber" class="block font-medium text-gray-700">Contact Number</label>
                <input type="text" id="contactNumber" name="contact_number"
                    class="border rounded w-full px-4 py-2" required />
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 bg-gray-300 rounded"
                    x-on:click="$dispatch('close-modal', 'editContactModal')">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
            </div>
        </form>
    </x-modal>

    <!-- Include the realTimeSearch.js script for real-time filtering -->
    @vite(['resources/js/contactsFilter.js', 'resources/js/dbConnection.js'])
</x-app-layout>
