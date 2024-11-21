<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div x-data="{ activeTab: 'logs' }" class="container mx-auto px-4 sm:px-6 lg:px-8">
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

        <div class="bg-white p-4 rounded-lg shadow-lg">
            @if (session('error'))
                <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Logs Section -->
            <div x-show="activeTab === 'logs'" class="space-y-6">
                <!-- Search and Filters -->
                <div class="p-4 bg-white shadow-sm sm:rounded-lg">
                    <form id="form" method="GET" action="{{ route('dashboard') }}"
                        class="flex flex-col md:flex-row gap-4 items-center">
                        <!-- Search Bar -->
                        <div class="flex-grow w-full md:w-auto">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Search logs...">
                        </div>

                        <!-- Recipient Type Filter -->
                        <div class="w-full md:w-auto">
                            <select name="recipient_type"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Recipient Types</option>
                                <option value="students" {{ request('recipient_type') == 'students' ? 'selected' : '' }}>
                                    Students</option>
                                <option value="employees"
                                    {{ request('recipient_type') == 'employees' ? 'selected' : '' }}>Employees
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="w-full md:w-auto">
                            <select name="status"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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

                        <!-- Generate Report Button -->
                        <div class="relative w-full md:w-auto">
                            <button type="button" id="generateReportButton"
                                class="w-full md:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-blue-600 text-white font-semibold hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Generate Message Logs Report
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Message Logs Table -->
                <div class="overflow-x-auto rounded-lg shadow-md border border-gray-300">
                    <div id="messageLogsContainer">
                        @include('partials.message-logs-content', ['messageLogs' => $messageLogs])
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
