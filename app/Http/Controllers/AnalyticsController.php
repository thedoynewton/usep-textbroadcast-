<?php

namespace App\Http\Controllers;

use App\Models\MessageRecipient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $messageType = $request->input('message_type');

        // Define cost per SMS
        $costPerSms = 0.0065;

        // Query for costs per day from message_logs
        $costQuery = MessageLog::selectRaw("CONVERT(DATE, created_at) as date, SUM(sent_count) as total_sent")
            ->where('status', 'Sent')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupByRaw("CONVERT(DATE, created_at)");

        if ($messageType) {
            $costQuery->where('message_type', $messageType);
        }

        $costData = $costQuery->get();

        // Prepare data for Costs Overview chart
        $costDates = [];
        $costs = [];

        foreach ($costData as $data) {
            $costDates[] = $data->date;
            $dailyCost = $data->total_sent * $costPerSms;
            $costs[] = $dailyCost; // Removed rounding
            Log::info("Cost Overview - Date: {$data->date}, Total Sent: {$data->total_sent}, Daily Cost: {$dailyCost}");
        }


        // Query for Messages Overview (Success and Failed) from message_recipients
        $messageQuery = MessageRecipient::selectRaw("CONVERT(DATE, created_at) as date, sent_status, COUNT(*) as count")
            ->whereIn('sent_status', ['Sent', 'Failed'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupByRaw("CONVERT(DATE, created_at), sent_status");

        $messageData = $messageQuery->get();

        // Prepare data for Messages Overview chart
        $messageDates = [];
        $successCounts = [];
        $failedCounts = [];

        foreach ($messageData->groupBy('date') as $date => $records) {
            $messageDates[] = $date;
            $successCounts[] = $records->firstWhere('sent_status', 'Sent')->count ?? 0;
            $failedCounts[] = $records->firstWhere('sent_status', 'Failed')->count ?? 0;
        }

        return view('analytics.index', compact(
            'costDates',
            'costs',
            'messageDates',
            'successCounts',
            'failedCounts',
            'startDate',
            'endDate',
            'messageType'
        ));
    }

}
