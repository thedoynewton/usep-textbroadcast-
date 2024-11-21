<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div x-data="{ activeTab: 'logs' }" class="container mx-auto">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 mb-6 flex justify-center">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button @click="activeTab = 'logs'"
                    :class="{ 'text-blue-600 font-bold border-b-2 border-blue-600': activeTab === 'logs', 'text-gray-600': activeTab !== 'logs' }"
                    class="px-3 py-2 font-medium focus:outline-none">
                    Logs
                </button>
                <button @click="activeTab = 'widgets'"
                    :class="{ 'text-blue-600 font-bold border-b-2 border-blue-600': activeTab === 'widgets', 'text-gray-600': activeTab !== 'widgets' }"
                    class="px-3 py-2 font-medium focus:outline-none">
                    Summary
                </button>
            </nav>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg mb-8 transition-transform duration-200 hover:scale-101">

            @if (session('error'))
                <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Logs Section -->
            <div x-show="activeTab === 'logs'">
                <!-- Search and Filters -->
                <div class="mb-4 p-4 bg-white shadow-sm sm:rounded-lg">
                    <form id="form" method="GET" action="{{ route('dashboard') }}"
                        class="flex flex-wrap gap-4 items-center">
                        <!-- Search Bar -->
                        <div class="flex-grow">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Search logs...">
                        </div>

                        <!-- Recipient Type Filter -->
                        <div>
                            <select name="recipient_type"
                                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Recipient Types</option>
                                <option value="students"
                                    {{ request('recipient_type') == 'students' ? 'selected' : '' }}>
                                    Students</option>
                                <option value="employees"
                                    {{ request('recipient_type') == 'employees' ? 'selected' : '' }}>
                                    Employees</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select name="status"
                                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Status</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
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
                                class="hidden absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="#" id="downloadCsv"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download as
                                        CSV</a>
                                    <a href="#" id="downloadPdf"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download as
                                        PDF</a>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>

                <!-- Message Logs Table -->
                <div class="overflow-x-auto max-h-[450px] rounded-lg shadow-md border border-gray-300">
                    <div>
                        <!-- Message Logs Table (AJAX-loaded content) -->
                        <div id="messageLogsContainer">
                            @include('partials.message-logs-content', ['messageLogs' => $messageLogs])
                        </div>
                    </div>
                </div>

                <!-- Separate Modal for displaying recipients -->
                <div id="recipientsModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                        </div>

                        <div
                            class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recipients Details</h3>
                                <ul id="recipientList" class="divide-y divide-gray-200">
                                    <!-- Recipients will be dynamically injected here -->
                                </ul>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" class="btn btn-secondary"
                                    id="closeRecipientsModal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widgets Section -->
            <div x-show="activeTab === 'widgets'" class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Messages Card -->
                {{-- id="totalMessagesCard" --}}
                <div class="bg-green-100 p-4 border-l-4 border-[#4CAF50] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer"
                    >
                    <h3 class="text-l font-bold text-[#4CAF50]">Total Messages Delivered</h3>
                    <p class="text-2xl font-semibold text-[#4CAF50]">{{ $totalMessages }}</p>
                </div>

                <!-- Scheduled Messages Card -->
                {{-- id="scheduledMessagesCard" --}}
                <div class="bg-[rgb(227,255,227)] p-4 border-l-4 border-[#4CAF50] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer"
                    >
                    <h3 class="text-l font-bold text-[#4CAF50]">Scheduled Messages Delivered</h3>
                    <p class="text-2xl font-semibold text-[#4CAF50]">{{ $scheduledMessages }}</p>
                </div>

                <!-- Immediate Messages Card -->
                {{-- id="immediateMessagesCard" --}}
                <div class="bg-green-50 p-4 border-l-4 border-[#4CAF50] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer"
                    >
                    <h3 class="text-l font-bold text-[#4CAF50]">Immediate Messages Delivered</h3>
                    <p class="text-2xl font-semibold text-[#4CAF50]">{{ $immediateMessages }}</p>
                </div>

                <!-- Failed Messages Card -->
                {{-- id="failedMessagesCard" --}}
                <div class="bg-red-200 p-4 border-l-4 border-[#990000] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer"
                    >
                    <h3 class="text-l font-bold text-[#990000]">Failed Messages</h3>
                    <p class="text-2xl font-semibold text-[#990000]">{{ $failedMessages }}</p>
                </div>

                <!-- Cancelled Messages Card -->
                <div
                    class="bg-orange-50 p-4 border-l-4 border-[#FF9800] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer">
                    <h3 class="text-l font-bold text-[#FF9800]">Cancelled Scheduled Messages</h3>
                    <p class="text-2xl font-semibold text-[#FF9800]">{{ $cancelledMessages }}</p>
                </div>

                <!-- Pending Messages Card -->
                <div
                    class="bg-blue-50 p-4 border-l-4 border-[#2196F3] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer">
                    <h3 class="text-l font-bold text-[#2196F3]">Pending Scheduled Messages</h3>
                    <p class="text-2xl font-semibold text-[#2196F3]">{{ $pendingMessages }}</p>
                </div>

                <!-- Movider Balance Card -->
                <div
                    class="bg-purple-100 p-4 border-l-4 border-[#7e57c2] rounded-lg shadow-md hover:shadow-lg transition-all duration-200 ease-in-out hover:scale-105 cursor-pointer">
                    <div class="text-l font-bold text-[#7e57c2]">
                        Remaining Credit Balance
                    </div>
                    <div class="text-2xl font-semibold text-[#7e57c2]">
                        {{ number_format($creditBalance) }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    @vite(['resources/js/dashboardFilter.js', 'resources/js/generateReport.js'])
</x-app-layout>
