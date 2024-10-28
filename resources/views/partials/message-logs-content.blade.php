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
                        <td class="border px-4 py-2">{{ $log->campus->campus_name ?? 'All Campuses' }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($log->recipient_type) }}</td>
                        <td class="border px-4 py-2">{{ $log->content ?? 'No Content' }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($log->message_type) }}</td>
                        <td class="border px-4 py-2">{{ $log->total_recipients ?? 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $log->sent_count ?? 0 }}</td>
                        <td class="border px-4 py-2">{{ $log->failed_count ?? 0 }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($log->status) }}</td>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="border px-4 py-2">{{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $log->scheduled_at ? \Carbon\Carbon::parse($log->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $log->cancelled_at ? \Carbon\Carbon::parse($log->cancelled_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td class="border px-4 py-2">
                            @if ($log->status === 'pending' && $log->message_type === 'scheduled')
                                <form action="{{ route('messages.cancel', $log->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this scheduled message?');">
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
    <div id="paginationContainer" class="mt-4">
        {{ $messageLogs->links() }}
    </div>
@endif
