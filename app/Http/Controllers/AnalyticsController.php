<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MessageLog;
use App\Models\MessageRecipient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve and format start and end dates
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;

        // Query for message recipients within the date range
        $query = MessageRecipient::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Adjusted query for SQL Server
        $logs = $query->selectRaw('CAST(created_at AS DATE) as date, 
                               SUM(CASE WHEN sent_status = \'Sent\' THEN 1 ELSE 0 END) as success_count,
                               SUM(CASE WHEN sent_status = \'Failed\' THEN 1 ELSE 0 END) as failed_count')
            ->groupByRaw('CAST(created_at AS DATE)')
            ->orderBy('date', 'asc') // Optional: Ensure results are ordered by date
            ->get();

        // Prepare data for the chart
        $dates = [];
        $successCounts = [];
        $failedCounts = [];

        foreach ($logs as $log) {
            $dates[] = $log->date;
            $successCounts[] = $log->success_count;
            $failedCounts[] = $log->failed_count;
        }

        return view('analytics.index', compact('startDate', 'endDate', 'dates', 'successCounts', 'failedCounts'));
    }

}
