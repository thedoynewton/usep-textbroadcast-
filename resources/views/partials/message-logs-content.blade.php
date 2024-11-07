@if ($messageLogs->isEmpty())
    <p class="flex items-center justify-center text-gray-600 text-sm mt-6 bg-gray-100 py-3 px-4 rounded-lg">
        No messages have been logged yet.
    </p>
@else
    <div class="table-container max-h-96 overflow-y-auto relative bg-white border rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach (['User', 'Campus', 'Recipient Type', 'Message', 'Message Type', 'Total Recipients', 'Sent Count', 'Failed Count', 'Status', 'Created At', 'Sent At', 'Scheduled At', 'Cancelled At', 'Action'] as $header)
                        <th
                            class="py-2 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="messageTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                @foreach ($messageLogs as $log)
                    <tr class="hover:bg-red-100 transition duration-150 ease-in-out">
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ $log->user->name ?? 'Unknown' }}
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">
                            {{ $log->campus->campus_name ?? 'All Campuses' }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">
                            {{ ucfirst($log->recipient_type) }}
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700">{{ $log->content ?? 'No Content' }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ ucfirst($log->message_type) }}
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700 text-center">{{ $log->total_recipients ?? 'N/A' }}
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700 text-center">{{ $log->sent_count ?? 0 }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 text-center">{{ $log->failed_count ?? 0 }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">{{ ucfirst($log->status) }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">
                            {{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">
                            {{ $log->scheduled_at ? \Carbon\Carbon::parse($log->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="py-2 px-4 text-xs text-gray-700 whitespace-nowrap">
                            {{ $log->cancelled_at ? \Carbon\Carbon::parse($log->cancelled_at)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 border-b text-gray-600">
                            @if ($log->status === 'pending' && $log->message_type === 'scheduled')
                                <form action="{{ route('messages.cancel', $log->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel this scheduled message?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div id="paginationContainer" class="sticky bottom-0 bg-white p-4 shadow-md border-t">
        {{ $messageLogs->links() }}
    </div>
@endif
