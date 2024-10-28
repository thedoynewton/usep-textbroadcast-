<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Logs Report</title>
    <style>
        /* Load font for PDF rendering */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

        /* Global font settings */
        body { font-family: 'Inter', sans-serif; font-size: 12px; color: #333; }
        
        /* Table layout adjustments */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { padding: 6px; border: 1px solid #e2e8f0; text-align: left; }
        th { background-color: #f1f5f9; color: #374151; font-weight: 600; }
        
        /* Title styling */
        h2 { font-size: 1.2rem; font-weight: 600; color: #1f2937; margin-bottom: 16px; text-align: center; }

        /* Responsive adjustments */
        .text-xs { font-size: 0.75rem; }
    </style>
</head>
<body class="p-8">
    <h2 class="text-lg font-bold text-gray-800 mb-6">Message Logs Report</h2>
    <table class="bg-white rounded-lg overflow-hidden">
        <thead>
            <tr>
                <th class="px-2 py-1">User</th>
                <th class="px-2 py-1">Campus</th>
                <th class="px-2 py-1">Recipient Type</th>
                <th class="px-2 py-1">Content</th>
                <th class="px-2 py-1">Message Type</th>
                <th class="px-2 py-1">Scheduled At</th>
                <th class="px-2 py-1">Sent At</th>
                <th class="px-2 py-1">Cancelled At</th>
                <th class="px-2 py-1">Status</th>
                <th class="px-2 py-1">Total Recipients</th>
                <th class="px-2 py-1">Sent Count</th>
                <th class="px-2 py-1">Failed Count</th>
                <th class="px-2 py-1">Created At</th>
                <th class="px-2 py-1">Updated At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($messageLogs as $log)
                <tr>
                    <td class="px-2 py-1">{{ $log->user->name ?? 'Unknown' }}</td>
                    <td class="px-2 py-1">{{ $log->campus->campus_name ?? 'All Campuses' }}</td>
                    <td class="px-2 py-1">{{ $log->recipient_type }}</td>
                    <td class="px-2 py-1">{{ Str::limit($log->content, 50) }}</td> <!-- Limit content length -->
                    <td class="px-2 py-1">{{ $log->message_type }}</td>
                    <td class="px-2 py-1">{{ $log->scheduled_at ? \Carbon\Carbon::parse($log->scheduled_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    <td class="px-2 py-1">{{ $log->sent_at ? \Carbon\Carbon::parse($log->sent_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    <td class="px-2 py-1">{{ $log->cancelled_at ? \Carbon\Carbon::parse($log->cancelled_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                    <td class="px-2 py-1">{{ $log->status }}</td>
                    <td class="px-2 py-1">{{ $log->total_recipients }}</td>
                    <td class="px-2 py-1">{{ $log->sent_count }}</td>
                    <td class="px-2 py-1">{{ $log->failed_count }}</td>
                    <td class="px-2 py-1">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="px-2 py-1">{{ $log->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
