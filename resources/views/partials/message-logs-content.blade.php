@if ($messageLogs->isEmpty())
    <p class="flex items-center justify-center text-gray-600 text-sm mt-6 bg-gray-100 py-3 px-4 rounded-lg">
        No messages have been logged yet.
    </p>
@else
    <div class="table-container max-h-96 overflow-x-auto relative bg-white border">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-600 text-center">
                    @foreach (['User', 'Campus', 'Recipient Type', 'Message Title', 'Message Type', 'Total Recipients', 'Sent Count', 'Failed Count', 'Status', 'Created At', 'Sent At', 'Scheduled At', 'Cancelled At', 'Action'] as $header)
                        <th class="py-2 px-4 border-b text-xs sm:text-sm md:text-xs font-semibold text-white tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="messageTableBody" class="bg-white divide-y divide-gray-200 text-xs sm:text-xs md:text-xs">
                @foreach ($messageLogs as $log)
                    <!-- Main Row -->
                    <tr class="hover:bg-red-100 transition duration-150 ease-in-out text-center cursor-pointer toggle-row"
                        data-log-id="{{ $log->id }}">
                        <td class="py-3 px-1 text-gray-700 whitespace-nowrap">{{ $log->user->name ?? 'Unknown' }}</td>
                        <td class="py-3 px-1 text-gray-700 whitespace-nowrap">{{ $log->campus->campus_name ?? 'All Campuses' }}</td>
                        <td class="py-3 px-1 text-gray-700 whitespace-nowrap">{{ ucfirst($log->recipient_type) }}</td>
                        <td class="py-3 px-1 text-gray-700">{{ $log->template_name ?? 'No Template Name' }}</td>
                        <td class="py-3 px-1 text-gray-700 whitespace-nowrap">{{ ucfirst($log->message_type) }}</td>
                        <td class="py-3 px-1 text-gray-700">{{ $log->total_recipients ?? 'N/A' }}</td>
                        <td class="py-3 px-1 text-gray-700">{{ $log->sent_count ?? 0 }}</td>
                        <td class="py-3 px-1 text-gray-700">{{ $log->failed_count ?? 0 }}</td>
                        <td class="py-3 px-1 text-gray-700 whitespace-nowrap">{{ ucfirst($log->status) }}</td>
                        <td class="py-3 px-1 text-gray-700 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="py-2 px-4 text-gray-700 whitespace-nowrap">
                            {{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="py-2 px-4 text-gray-700 whitespace-nowrap">
                            {{ $log->scheduled_at ? \Carbon\Carbon::parse($log->scheduled_at)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="py-2 px-4 text-gray-700 whitespace-nowrap">
                            {{ $log->cancelled_at ? \Carbon\Carbon::parse($log->cancelled_at)->format('Y-m-d H:i') : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 border-b text-gray-600">
                            @if ($log->status === 'pending' && $log->message_type === 'scheduled')
                                <form id="cancel-form-{{ $log->id }}" action="{{ route('messages.cancel', $log->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm text-red-600" onclick="confirmCancel({{ $log->id }})">Cancel</button>
                            @endif
                        </td>
                        
                        <script>
                            function confirmCancel(logId) {
                                Swal.fire({
                                    title: 'Are you sure you want to cancel this message?',
                                    text: "You won't be able to revert this!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Yes, cancel it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Submit the form if user confirms
                                        document.getElementById('cancel-form-' + logId).submit();
                                    }
                                });
                            }
                        </script>
                        
                    </tr>

                    <!-- Toggle Row for Extra Information -->
                    <tr class="hidden bg-gray-100" id="toggle-row-{{ $log->id }}">
                        <td colspan="14" class="p-4 text-gray-700 text-xs sm:text-xs md:text-xs">
                            <div><strong>Message Title:</strong> {{ $log->template_name ?? 'N/A' }}</div>
                            <div><strong>Message Content:</strong> {{ $log->content ?? 'N/A' }}</div>
                            <div>
                                <strong>Total Recipients:</strong> {{ $log->total_recipients ?? 'N/A' }}
                                <a href="javascript:void(0);" class="text-blue-500 hover:underline ml-2 show-recipients"
                                    data-log-id="{{ $log->id }}">
                                    Show Recipients
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif
