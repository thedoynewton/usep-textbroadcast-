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
                <form id="form" method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-4 items-center">
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
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>


                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Message Logs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Recent Messages</h3>

                    @if ($messageLogs->isEmpty())
                        <p>No messages have been logged yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 border">User</th>
                                        <th class="px-4 py-2 border">Campus</th>
                                        <th class="px-4 py-2 border">Recipient Type</th>
                                        <th class="px-4 py-2 border">Message</th>
                                        <th class="px-4 py-2 border">Message Type</th>
                                        <th class="px-4 py-2 border">Total Recipients</th>
                                        <th class="px-4 py-2 border">Sent Count</th>
                                        <th class="px-4 py-2 border">Failed Count</th>
                                        <th class="px-4 py-2 border">Status</th>
                                        <th class="px-4 py-2 border">Created At</th>
                                        <th class="px-4 py-2 border">Sent At</th>
                                        <th class="px-4 py-2 border">Scheduled At</th>
                                        <th class="px-4 py-2 border">Cancelled At</th>
                                        <th class="px-4 py-2 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="messageTableBody">
                                    @foreach ($messageLogs as $log)
                                        <tr>
                                            <td class="border px-4 py-2">{{ $log->user->name ?? 'Unknown' }}</td>
                                            <td class="border px-4 py-2">
                                                {{ $log->campus->campus_name ?? 'All Campuses' }}</td>
                                            <td class="border px-4 py-2">{{ ucfirst($log->recipient_type) }}</td>
                                            <td class="border px-4 py-2">{{ $log->content ?? 'No Content' }}</td>
                                            <td class="border px-4 py-2">{{ ucfirst($log->message_type) }}</td>
                                            <td class="border px-4 py-2">{{ $log->total_recipients ?? 'N/A' }}</td>
                                            <td class="border px-4 py-2">{{ $log->sent_count ?? 0 }}</td>
                                            <td class="border px-4 py-2">{{ $log->failed_count ?? 0 }}</td>
                                            <td class="border px-4 py-2">{{ ucfirst($log->status) }}</td>
                                            <td class="border px-4 py-2">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                                            <td class="border px-4 py-2">
                                                {{ isset($log->sent_at) ? \Carbon\Carbon::parse($log->sent_at)->format('Y-m-d H:i') : 'N/A' }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                {{ isset($log->scheduled_at) ? \Carbon\Carbon::parse($log->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                {{ isset($log->cancelled_at) ? \Carbon\Carbon::parse($log->cancelled_at)->format('Y-m-d H:i') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if ($log->status === 'pending' && $log->message_type === 'scheduled')
                                                    <form action="{{ route('messages.cancel', $log->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to cancel this scheduled message?');">
                                                        @csrf
                                                        @method('PATCH') <!-- Important to specify the PATCH method -->
                                                        <button type="submit"
                                                            class="btn btn-danger btn-sm">Cancel</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div id="paginationContainer" class="mt-4">
                            {{ $messageLogs->appends(request()->input())->links() }}
                            <!-- Keep filters when paginating -->
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/recipientsModal.js', 'resources/js/dashboardFilter.js'])
</x-app-layout>
