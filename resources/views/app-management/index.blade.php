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
                                class="{{ request('section') == 'contacts' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                Contacts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'templates']) }}"
                                class="{{ request('section') == 'templates' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                Message Templates
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'db-connection']) }}"
                                class="{{ request('section') == 'db-connection' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                DB Connection
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('app-management.index', ['section' => 'credit-balance']) }}"
                                class="{{ request('section') == 'credit-balance' ? 'text-black font-bold' : 'text-black dark:text-[#4b5563]' }}">
                                Credit Balance
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
                                        class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Import College
                                    </button>
                                </form>

                                <!-- Import Program Button -->
                                <form method="POST" action="{{ route('import.programs') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Import Program
                                    </button>
                                </form>

                                <!-- Import Major Button inside Modal -->
                                <form method="POST" action="{{ route('import.majors') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                        Import Major
                                    </button>
                                </form>

                                <!-- Import Years Button inside Modal -->
                                <form method="POST" action="{{ route('import.years') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                        Import Years
                                    </button>
                                </form>

                                <!-- Import Students Button inside Modal -->
                                <form method="POST" action="{{ route('import.students') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
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
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold">Campuses</h4>
                            <!-- Add Campus Button -->
                            <button id="addCampusButton"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Add Campus
                            </button>
                        </div>

                        <!-- Add Campus Modal -->
                        <x-modal name="addCampusModal" maxWidth="md">
                            <div class="mx-5 my-5">
                                <h2 class="text-lg font-semibold mb-4">Add Campus</h2>
                                <form id="addCampusForm">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="campusName" class="block font-medium text-gray-700">Campus
                                            Name</label>
                                        <input type="text" id="campusName" name="campus_name"
                                            class="border rounded w-full px-4 py-2" required />
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" class="px-4 py-2 bg-gray-300 rounded"
                                            x-on:click="$dispatch('close-modal', 'addCampusModal')">Cancel</button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>

                        <!-- Edit Campus Modal -->
                        <x-modal name="editCampusModal" maxWidth="md">
                            <div class="mx-5 my-5">
                                <h2 class="text-lg font-semibold mb-4">Edit Campus</h2>
                                <form id="editCampusForm">
                                    @csrf
                                    <input type="hidden" id="editCampusId" name="campus_id" />
                                    <div class="mb-4">
                                        <label for="editCampusName" class="block font-medium text-gray-700">Campus
                                            Name</label>
                                        <input type="text" id="editCampusName" name="campus_name"
                                            class="border rounded w-full px-4 py-2" required />
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" class="px-4 py-2 bg-gray-300 rounded"
                                            x-on:click="$dispatch('close-modal', 'editCampusModal')">Cancel</button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>


                        <table class="min-w-full border border-gray-500">
                            <thead class="bg-gray-700 text-white">
                                <tr class="border border-gray-500">
                                    <th class="py-2 px-4 ">Campus ID</th>
                                    <th class="py-2 px-4 ">Campus Name</th>
                                    <th class="py-2 px-4 ">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campuses as $campus)
                                    <tr class="hover:bg-red-100 text-center border border-gray-500">
                                        <td class="py-2 px-4 border border-gray-500">{{ $campus->campus_id }}</td>
                                        <td class="py-2 px-4 border border-gray-500">{{ $campus->campus_name }}</td>
                                        <td class="py-2 px-4 text-center border border-gray-500">
                                            <button data-campus-id="{{ $campus->campus_id }}"
                                                data-campus-name="{{ $campus->campus_name }}"
                                                class="edit-campus-btn text-white bg-blue-500 px-3 py-1 rounded-lg">Edit</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                @elseif (request('section') == 'credit-balance')
                    <!-- Credit Balance Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Edit Credit Balance</h3>

                        <form method="POST" action="{{ route('credit-balance.update') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="creditBalance" class="block font-medium text-gray-700">Current Credit
                                    Balance</label>
                                <input type="number" id="creditBalance" name="credit_balance"
                                    value="{{ $creditBalance }}" class="border rounded w-full px-4 py-2" required />
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                            </div>
                        </form>

                    </div>
                @else
                    <!-- Contacts Section -->
                    <div>
                        <form id="filterForm" method="GET" action="{{ route('app-management.index') }}"
                            class="mb-6">
                            @csrf
                            <div class="flex items-center space-x-4">
                                <!-- Search Input -->
                                <input type="text" id="searchInput" name="search"
                                    value="{{ request('search') }}" placeholder="Search contacts..."
                                    class="border rounded px-4 py-2 text-gray-700 w-full" />

                                <!-- Campus Filter Dropdown -->
                                <select id="campusFilter" name="campus_id"
                                    class="border rounded dark:bg-white dark:text-gray-700">
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
                                    class="border rounded dark:bg-white dark:text-gray-700">
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
        <div class="px-5 py-5" <h2 class="text-lg font-semibold my-5">Edit Contact Number</h2>
            <form id="editForm">
                @csrf
                <input type="hidden" id="contactId" name="contact_id" />

                <div class="my-5">
                    <label for="contactName" class="block font-medium text-gray-700">Name</label>
                    <input type="text" id="contactName" name="contact_name"
                        class="border rounded w-full px-4 py-2" readonly />
                </div>

                <div class="my-5">
                    <label for="contactNumber" class="block font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contactNumber" name="contact_number"
                        class="border rounded w-full px-4 py-2" pattern="\d{11}" maxlength="11"
                        title="Please enter a valid 11-digit number" required />
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded"
                        x-on:click="$dispatch('close-modal', 'editContactModal')">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
                </div>
            </form>
    </x-modal>

    <!-- Include the realTimeSearch.js script for real-time filtering -->
    @vite(['resources/js/contactsFilter.js', 'resources/js/dbConnection.js', 'resources/js/campusFunctions.js', 'resources/js/creditBalance.js'])
</x-app-layout>
