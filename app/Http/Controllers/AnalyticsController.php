<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\MessageCategory;
use App\Models\MessageRecipient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{

    public function index(Request $request)
{
    // Define low balance threshold
    $lowBalanceThreshold = 10000;

    // Fetch the current balance using MoviderService
    $balanceData = app('App\Services\MoviderService')->getBalance();
    $balance = $balanceData['balance'] ?? 0;

    // Determine if the balance is below the threshold
    $lowBalance = $balance < $lowBalanceThreshold;

    // Get date range from the request
    $startDate = $request->input('startDate');
    $endDate = $request->input('endDate');

    // Define a date filter query scope
    $dateFilter = function ($query) use ($startDate, $endDate) {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
    };

    // Retrieve counts of messages sent by each category through templates
    $categories = MessageCategory::withCount([
        'messageTemplates as message_logs_count' => function ($query) use ($dateFilter) {
            $query->whereHas('messageLogs', $dateFilter);
        }
    ])->get();

    $categoryLabels = $categories->pluck('name')->toArray();
    $categoryCounts = $categories->pluck('message_logs_count')->toArray();

    // Retrieve counts of sent messages by recipient type (excluding cancelled messages)
    $recipientTypeCounts = MessageLog::where('status', 'sent')
        ->where($dateFilter)
        ->select('recipient_type', DB::raw('count(*) as count'))
        ->groupBy('recipient_type')
        ->pluck('count', 'recipient_type')
        ->toArray();
    $recipientTypes = array_keys($recipientTypeCounts);
    $recipientCounts = array_values($recipientTypeCounts);

    // Retrieve counts of messages by status and created_at date
    $statusCountsByDate = MessageLog::where($dateFilter)
        ->select(
            DB::raw('CONVERT(DATE, created_at) as date'), // Use CONVERT for SQL Server
            'status',
            DB::raw('count(*) as count')
        )
        ->groupBy(DB::raw('CONVERT(DATE, created_at)'), 'status')
        ->orderBy(DB::raw('CONVERT(DATE, created_at)'))
        ->get();

    // Define the possible statuses with corresponding colors
    $statuses = ['Sent', 'failed', 'cancelled', 'pending'];
    $statusColors = [
        'Sent' => '#4dc9f6',
        'failed' => '#f67019',
        'cancelled' => '#f53794',
        'pending' => '#acc236'
    ];

    // Process data for the grouped bar chart
    $dates = $statusCountsByDate->pluck('date')->unique()->toArray();
    $statusData = [];

    foreach ($statuses as $status) {
        $statusData[] = [
            'label' => ucfirst($status), // Capitalize the status for display
            'data' => array_map(function ($date) use ($status, $statusCountsByDate) {
                return $statusCountsByDate->where('date', $date)->where('status', $status)->first()->count ?? 0;
            }, $dates),
            'backgroundColor' => $statusColors[$status],
        ];
    }

    return view('analytics.index', compact(
        'categoryLabels',
        'categoryCounts',
        'recipientTypes',
        'recipientCounts',
        'statusData',
        'dates',
        'lowBalance'
    ));
}

}
