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

            <!-- Message Logs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Recent Messages</h3>

                    @if($messageLogs->isEmpty())
                        <p>No messages have been logged yet.</p>
                    @else
                        <div class="overflow-x-auto"> <!-- Add scroll for responsiveness -->
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($messageLogs as $log)
                                        <tr>
                                            <td class="border px-4 py-2">{{ $log->user->name ?? 'Unknown' }}</td>
                                            <td class="border px-4 py-2">{{ $log->campus->campus_name ?? 'All Campuses' }}</td>
                                            <td class="border px-4 py-2">{{ ucfirst($log->recipient_type) }}</td>
                                            <td class="border px-4 py-2">{{ $log->content ?? 'No Content' }}</td>
                                            <td class="border px-4 py-2">{{ ucfirst($log->message_type) }}</td>
                                            <td class="border px-4 py-2">{{ $log->total_recipients ?? 'N/A' }}</td>
                                            <td class="border px-4 py-2">{{ $log->sent_count ?? 0 }}</td>
                                            <td class="border px-4 py-2">{{ $log->failed_count ?? 0 }}</td>
                                            <td class="border px-4 py-2">{{ ucfirst($log->status) }}</td>
                                            <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                                            <td class="border px-4 py-2">
                                                {{ isset($log->sent_at) ? \Carbon\Carbon::parse($log->sent_at)->format('Y-m-d H:i') : 'N/A' }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                {{ isset($log->scheduled_at) ? \Carbon\Carbon::parse($log->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                {{ isset($log->cancelled_at) ? \Carbon\Carbon::parse($log->cancelled_at)->format('Y-m-d H:i') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- End of scrollable div -->

                        <!-- Pagination Links -->
                        <div class="mt-4">
                            {{ $messageLogs->links() }}  <!-- Display pagination links -->
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
