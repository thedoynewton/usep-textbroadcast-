<?php

namespace App\Http\Controllers;

use App\Models\MessageCategory;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    /**
     * Display analytics data.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Define the threshold for low balance notifications
        $lowBalanceThreshold = 10000;

        try {
            // Fetch current balance using the MoviderService
            $balanceData = app('App\Services\MoviderService')->getBalance();
            $balance = $balanceData['balance'] ?? 0; // Default to 0 if balance is not available
        } catch (\Exception $e) {
            // Log any errors during balance fetching
            Log::error('Failed to fetch balance from MoviderService: ' . $e->getMessage());
            $balance = 0; // Default to 0 in case of an error
        }

        // Determine if the balance is below the threshold
        $lowBalance = $balance < $lowBalanceThreshold;

        // Retrieve the date range filters from the request
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Define a closure for date filtering queries
        $dateFilter = function ($query) use ($startDate, $endDate) {
            if (!empty($startDate)) {
                $query->whereRaw('CAST(created_at AS DATE) >= ?', [$startDate]); // Filter by start date
            }
            if (!empty($endDate)) {
                $query->whereRaw('CAST(created_at AS DATE) <= ?', [$endDate]); // Filter by end date
            }
        };

        // Fetch message categories and their associated message log counts
        $categories = MessageCategory::withCount([
            'messageTemplates as message_logs_count' => function ($query) use ($dateFilter) {
                // Count message logs within the specified date range
                $query->whereHas('messageLogs', $dateFilter);
            }
        ])->get();

        // Extract category names and their respective message log counts
        $categoryLabels = $categories->pluck('name')->toArray() ?? [];
        $categoryCounts = $categories->pluck('message_logs_count')->toArray() ?? [];

        // Fetch recipient type counts for "sent" messages within the date range
        $recipientTypeCounts = MessageLog::where('status', 'sent')
            ->where($dateFilter)
            ->select('recipient_type', DB::raw('count(*) as count'))
            ->groupBy('recipient_type')
            ->pluck('count', 'recipient_type')
            ->toArray();

        // Extract recipient types and counts for analytics
        $recipientTypes = array_keys($recipientTypeCounts) ?? [];
        $recipientCounts = array_values($recipientTypeCounts) ?? [];

        // Fetch message log counts grouped by date and status
        $statusCountsByDate = MessageLog::select(
            DB::raw('CAST(created_at AS DATE) as date'), // Format date for grouping
            'status',
            DB::raw('count(*) as count')
        )
            ->where($dateFilter)
            ->groupBy(DB::raw('CAST(created_at AS DATE)'), 'status')
            ->orderBy(DB::raw('CAST(created_at AS DATE)'))
            ->get();

        // Define statuses and their corresponding colors
        $statuses = ['sent', 'failed', 'cancelled', 'pending'];
        $statusColors = [
            'sent' => '#4dc9f6',
            'failed' => '#f67019',
            'cancelled' => '#f53794',
            'pending' => '#acc236',
        ];

        // Extract unique dates from the results
        $dates = $statusCountsByDate->pluck('date')->unique()->toArray() ?? [];
        $statusData = [];

        // Prepare data for each status to be used in visualizations
        foreach ($statuses as $status) {
            $statusData[] = [
                'label' => ucfirst($status), // Capitalize the status label
                'data' => array_map(function ($date) use ($status, $statusCountsByDate) {
                    // Get the count for a specific status on a specific date
                    return $statusCountsByDate->where('date', $date)->where('status', $status)->first()->count ?? 0;
                }, $dates),
                'backgroundColor' => $statusColors[$status] ?? '#cccccc', // Default color if not defined
            ];
        }

        // Return the data as JSON for API responses (for frontend charting)
        if ($request->wantsJson()) {
            return response()->json([
                'categoryLabels' => $categoryLabels,
                'categoryCounts' => $categoryCounts,
                'recipientTypes' => $recipientTypes,
                'recipientCounts' => $recipientCounts,
                'statusData' => $statusData,
                'dates' => $dates,
            ]);
        }

        // Pass all the prepared data to the view for rendering
        return view('analytics.index', [
            'categoryLabels' => $categoryLabels, // Category names
            'categoryCounts' => $categoryCounts, // Corresponding message log counts
            'recipientTypes' => $recipientTypes, // Recipient types
            'recipientCounts' => $recipientCounts, // Counts per recipient type
            'statusData' => $statusData, // Data for each status
            'dates' => $dates, // Dates for status data
            'lowBalance' => $lowBalance, // Indicator for low balance
        ]);
    }
}
