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

        // Query for message status counts within the date range for doughnut chart
        $statusQuery = MessageLog::query();
        if ($startDate && $endDate) {
            $statusQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Group by status and count each status for doughnut chart
        $statusCounts = $statusQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // Prepare data for the doughnut chart
        $statuses = array_keys($statusCounts);
        $counts = array_values($statusCounts);

        // Query for sent and failed counts over time for line chart
        $timeQuery = MessageLog::query();
        if ($startDate && $endDate) {
            $timeQuery->whereBetween('scheduled_at', [$startDate, $endDate]);
        }

        // Group by date and sum sent and failed counts
        $logs = $timeQuery->selectRaw('CAST(scheduled_at AS DATE) as date, 
                                       SUM(sent_count) as sent_count,
                                       SUM(failed_count) as failed_count')
            ->groupByRaw('CAST(scheduled_at AS DATE)')
            ->orderBy('date', 'asc')
            ->get();

        // Prepare data for the line chart
        $dates = $logs->pluck('date')->toArray();
        $sentCounts = $logs->pluck('sent_count')->toArray();
        $failedCounts = $logs->pluck('failed_count')->toArray();

        // Query for recipient type analysis
        $recipientQuery = MessageRecipient::query();
        if ($startDate && $endDate) {
            $recipientQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Group by recipient_type and sent_status for stacked bar chart
        $recipientData = $recipientQuery->selectRaw('recipient_type, sent_status, COUNT(*) as count')
            ->groupBy('recipient_type', 'sent_status')
            ->get();

        // Prepare data for recipient type analysis
        $recipientTypes = $recipientData->pluck('recipient_type')->unique()->values()->toArray();
        $sentCountsByType = $recipientData->where('sent_status', 'Sent')->pluck('count', 'recipient_type')->toArray();
        $failedCountsByType = $recipientData->where('sent_status', 'Failed')->pluck('count', 'recipient_type')->toArray();

        return view('analytics.index', compact(
            'startDate',
            'endDate',
            'statuses',
            'counts',
            'dates',
            'sentCounts',
            'failedCounts',
            'recipientTypes',
            'sentCountsByType',
            'failedCountsByType'
        ));
    }

}
