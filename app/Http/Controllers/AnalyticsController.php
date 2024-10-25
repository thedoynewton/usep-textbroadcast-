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
        // Define the cost per SMS (this can be modified based on actual rates)
        $costPerSms = 0.0065;
    
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
            ->orderBy('date', 'asc')
            ->get();
    
        // Prepare data for the charts
        $dates = [];
        $successCounts = [];
        $failedCounts = [];
        $costs = []; // To store the cost data
    
        foreach ($logs as $log) {
            $dates[] = $log->date;
            $successCounts[] = $log->success_count;
            $failedCounts[] = $log->failed_count;
            $costs[] = round($log->success_count * $costPerSms, 2); // Calculate the total cost for each day
        }
    
        return view('analytics.index', compact('startDate', 'endDate', 'dates', 'successCounts', 'failedCounts', 'costs'));
    }    

}
