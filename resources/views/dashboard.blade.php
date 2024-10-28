<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Widgets Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- Total Messages Card -->
                <div class="bg-white p-6 rounded-lg shadow-md cursor-pointer" id="totalMessagesCard">
                    <h3 class="text-lg font-semibold mb-2">Total Messages Delivered</h3>
                    <p class="text-2xl">{{ $totalMessages }}</p>
                </div>

                <!-- Scheduled Messages Card -->
                <div class="bg-white p-6 rounded-lg shadow-md cursor-pointer" id="scheduledMessagesCard">
                    <h3 class="text-lg font-semibold mb-2">Scheduled Messages Delivered</h3>
                    <p class="text-2xl">{{ $scheduledMessages }}</p>
                </div>

                <!-- Immediate Messages Card -->
                <div class="bg-white p-6 rounded-lg shadow-md cursor-pointer" id="immediateMessagesCard">
                    <h3 class="text-lg font-semibold mb-2">Immediate Messages Delivered</h3>
                    <p class="text-2xl">{{ $immediateMessages }}</p>
                </div>

                <!-- Failed Messages Card -->
                <div class="bg-white p-6 rounded-lg shadow-md cursor-pointer" id="failedMessagesCard">
                    <h3 class="text-lg font-semibold mb-2">Failed Messages Sent</h3>
                    <p class="text-2xl">{{ $failedMessages }}</p>
                </div>

                <!-- Cancelled Messages Card -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Cancelled Scheduled Messages</h3>
                    <p class="text-2xl">{{ $cancelledMessages }}</p>
                </div>

                <!-- Pending Messages Card -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Pending Scheduled Messages</h3>
                    <p class="text-2xl">{{ $pendingMessages }}</p>
                </div>

                <!-- Movider Balance Card -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="text-gray-800 font-bold text-lg">
                        SMS Balance
                    </div>
                    <div class="text-2xl mt-2">
                        ${{ number_format($balance, 4) }} <!-- Display the balance with 4 decimals -->
                    </div>
                </div>
            </div>

            <!-- Modal for displaying recipients -->
            <div id="recipientsModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4 text-center">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <div
                        class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recipients Details</h3>

                            <ul id="recipientList" class="divide-y divide-gray-200">
                                <!-- Recipients will be injected here dynamically -->
                            </ul>

                            <!-- Pagination links container -->
                            <div id="paginationContainer" class="mt-4 flex justify-center space-x-2">
                                <!-- Pagination buttons will be injected here -->
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="btn btn-secondary" id="closeModal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                <form id="form" method="GET" action="{{ route('dashboard') }}"
                    class="flex flex-wrap gap-4 items-center">
                    <!-- Search Bar -->
                    <div class="flex-grow">
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Search by Message, User, Campus, or Message Type...">
                    </div>

                    <!-- Recipient Type Filter -->
                    <div>
                        <select name="recipient_type"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">All Recipient Types</option>
                            <option value="students" {{ request('recipient_type') == 'students' ? 'selected' : '' }}>
                                Students</option>
                            <option value="employees" {{ request('recipient_type') == 'employees' ? 'selected' : '' }}>
                                Employees</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select name="status"
                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">All Status</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                        </select>
                    </div>

                    <div class="relative inline-block text-left">
                        <!-- Main button to trigger the dropdown -->
                        <button type="button" id="generateReportButton"
                            class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-blue-600 text-white font-semibold hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Generate Message Logs Report
                        </button>

                        <!-- Dropdown menu -->
                        <div id="reportDropdown"
                            class="hidden absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="#" id="downloadCsv"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download as CSV</a>
                                <a href="#" id="downloadPdf"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download as PDF</a>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

            <!-- Message Logs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Message Logs Table (AJAX-loaded content) -->
                    <div id="messageLogsContainer">
                        @include('partials.message-logs-content', ['messageLogs' => $messageLogs])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/recipientsModal.js', 'resources/js/dashboardFilter.js', 'resources/js/generateReport.js'])
</x-app-layout>
