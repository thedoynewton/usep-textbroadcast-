<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('App Management') }}
        </h2>
    </x-slot>

    <script>
        // Check if there's a success message in the session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000 // Auto-close the alert after 2 seconds
            });
        @endif

        // Check if there's an error message in the session
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                showConfirmButton: true // Show the "OK" button
            });
        @endif
    </script>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Tabs -->
                <ul class="flex-wrap flex border-b-2">
                    <li>
                        <a href="{{ route('app-management.index', ['section' => 'contacts']) }}"
                            class="inline-block py-2 px-4 text-black font-semibold focus:outline-none rounded-tl-lg rounded-tr-lg transition duration-200 ease-in-out 
            {{ request('section') == 'contacts' || !request('section') ? 'text-white bg-[#333333] border-b-2' : 'hover:bg-gray-100' }}">
                            Contacts
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('app-management.index', ['section' => 'db-connection']) }}"
                            class="inline-block py-2 px-4 text-black font-semibold focus:outline-none rounded-tl-lg rounded-tr-lg transition duration-200 ease-in-out 
            {{ request('section') == 'db-connection' ? 'text-white bg-[#333333] border-b-2' : 'hover:bg-gray-100' }}">
                            Import Student Data
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('app-management.index', ['section' => 'import-employees']) }}"
                            class="inline-block py-2 px-4 text-black font-semibold focus:outline-none rounded-tl-lg rounded-tr-lg transition duration-200 ease-in-out 
            {{ $section === 'import-employees' ? 'text-white bg-[#333333] border-b-2' : 'hover:bg-gray-100' }}">
                            Import Employee Data
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('app-management.index', ['section' => 'credit-balance']) }}"
                            class="inline-block py-2 px-4 text-black font-semibold focus:outline-none rounded-tl-lg rounded-tr-lg transition duration-200 ease-in-out 
            {{ request('section') == 'credit-balance' ? 'text-white bg-[#333333] border-b-2' : 'hover:bg-gray-100' }}">
                            Credit Balance
                        </a>
                    </li>
                </ul>


                <!-- Display section based on active tab -->
                @if (request('section') == 'db-connection')
                    <div class="flex flex-col justify-between items-start mb-4">
                        <!-- Title and Instruction -->
                        <div class="flex flex-col items-start my-4">
                            <h4 class="text-lg font-semibold">List of Campuses</h4>
                            <h5 class="text-sm font-medium text-gray-600 italic mt-2">(Click a row to show import
                                options.)</h5>
                        </div>

                        <!-- Add Campus Button -->
                        <div class="">
                            <button id="addCampusButton"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Add Campus
                            </button>
                        </div>

                        <!-- Add Campus Modal -->
                        <x-modal name="addCampusModal" maxWidth="sm">
                            <div class=" mx-5 my-5 max-xs">
                                <h2 class="text-sm sm:text-lg font-semibold mb-4">Add Campus</h2>
                                <form id="addCampusForm">
                                    @csrf
                                    <!-- Campus Name Input -->
                                    <div class="mb-4">
                                        <label for="campusName" class="block font-medium text-gray-700 text-sm sm:text-lg">Campus
                                            Name</label>
                                        <input type="text" id="campusName" name="campus_name"
                                            class="border rounded w-full px-4 py-2" required />
                                    </div>
                                    <!-- Campus ID Input -->
                                    <div class="mb-4">
                                        <label for="campusId" class="block font-medium text-gray-700 text-sm sm:text-lg">Campus ID</label>
                                        <input type="text" id="campusId" name="campus_id"
                                            class="border rounded w-full px-4 py-2 text-sm sm:text-lg" required />
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" class="px-4 py-2 bg-gray-300 rounded text-sm sm:text-lg"
                                            x-on:click="$dispatch('close-modal', 'addCampusModal')">Cancel</button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-blue-500 text-white rounded text-sm sm:text-lg">Save</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>

                        <!-- Import Options Modal -->
                        <div id="importModal"
                            class="fixed inset-0 items-center justify-center bg-black bg-opacity-50 hidden">
                            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
                                <h3 id="importModalTitle" class="text-2xl font-bold mb-4"></h3>
                                <p class="text-gray-700 mb-4">Select an option to import data for this campus.</p>

                                <!-- Import Buttons -->
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <form method="POST" action="{{ route('import.college') }}"
                                        style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="campus_id" class="campus-id-input" value="">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Import
                                            College</button>
                                    </form>
                                    <form method="POST" action="{{ route('import.programs') }}"
                                        style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="campus_id" class="campus-id-input" value="">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Import
                                            Program</button>
                                    </form>
                                    <form method="POST" action="{{ route('import.majors') }}"
                                        style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="campus_id" class="campus-id-input" value="">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Import
                                            Major</button>
                                    </form>
                                    <form method="POST" action="{{ route('import.years') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="campus_id" class="campus-id-input" value="">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">Import
                                            Years</button>
                                    </form>
                                    <form method="POST" action="{{ route('import.students') }}"
                                        style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="campus_id" class="campus-id-input" value="">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">Import
                                            Students</button>
                                    </form>
                                </div>

                                <div class="flex justify-end mt-4">
                                    <button id="closeImportModal"
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Close</button>
                                </div>
                            </div>
                        </div>

                        {{-- Loading screen --}}
                        <div id="loadingScreen"
                            class="fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center hidden z-50">
                            <div class="text-center">
                                <svg class="animate-spin h-10 w-10 text-white mx-auto mb-4"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4zm2 5.291l-2.029 2.03a12 12 0 004.567 3.304L9 18h-3z">
                                    </path>
                                </svg>
                                <p class="text-white font-semibold">Loading...</p>
                            </div>
                        </div>

                        <!-- Campus Data Table -->
                        <table class="min-w-full border border-gray-300 mt-8">
                            <thead class="bg-gray-600 text-white">
                                <tr>
                                    <th class="py-2 px-4">Campus ID</th>
                                    <th class="py-2 px-4">Campus Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campuses as $campus)
                                    <tr class="hover:bg-red-100 text-center border-b border-gray-300"
                                        data-campus-id="{{ $campus->campus_id }}">
                                        <td class="py-2 px-4">{{ $campus->campus_id }}</td>
                                        <td class="py-2 px-4">{{ $campus->campus_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif (request('section') == 'import-employees')
                    <div class="mt-6">
                        <h1 class="text-2xl font-bold mb-4">Import Employees</h1>

                        <!-- Import Offices Button -->
                        <form action="{{ route('import.offices') }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-red-900 text-white rounded hover:bg-red-700">Import
                                Offices</button>
                        </form>

                        <!-- Import Employment Types Button -->
                        <form action="{{ route('import.employment-types') }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-600">Import Employment
                                Types</button>
                        </form>
                        <!-- Import Employment Statuses Button -->
                        <form action="{{ route('import.employment-statuses') }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-500">Import
                                Employment
                                Statuses</button>
                        </form>
                        <!-- Import Employees Button -->
                        <form action="{{ route('import.employees') }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-400">Import
                                Employees</button>
                        </form>
                    </div>
                @elseif (request('section') == 'credit-balance')
                    <!-- Credit Balance Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Edit Credit Balance</h3>
                        <p class="text-sm italic mb-4">(Credit balance is handled manually due to the limitations of
                            the
                            Movider
                            API that can provided to the developers.)</p>

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
                            class="my-4">
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
