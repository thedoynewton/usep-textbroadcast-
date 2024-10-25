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
    
        // Query for message status counts within the date range
        $query = MessageLog::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
    
        // Group by status and count each status
        $statusCounts = $query->selectRaw('status, COUNT(*) as count')
                              ->groupBy('status')
                              ->pluck('count', 'status')->toArray();
    
        // Prepare data for the chart
        $statuses = array_keys($statusCounts);
        $counts = array_values($statusCounts);
    
        return view('analytics.index', compact('startDate', 'endDate', 'statuses', 'counts'));
    }       

}
