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

    public function index()
    {
        // Define low balance threshold
        $lowBalanceThreshold = 10000;

        // Fetch the current balance using MoviderService
        $balanceData = app('App\Services\MoviderService')->getBalance();
        $balance = $balanceData['balance'] ?? 0;

        // Determine if the balance is below the threshold
        $lowBalance = $balance < $lowBalanceThreshold;

        // Retrieve counts of messages sent by each category through templates
        $categories = MessageCategory::withCount([
            'messageTemplates as message_logs_count' => function ($query) {
                $query->whereHas('messageLogs');
            }
        ])->get();

        $categoryLabels = $categories->pluck('name')->toArray();
        $categoryCounts = $categories->pluck('message_logs_count')->toArray();

        // Retrieve counts of sent messages by recipient type (excluding cancelled messages)
        $recipientTypeCounts = MessageLog::where('status', 'Sent')
            ->select('recipient_type', DB::raw('count(*) as count'))
            ->groupBy('recipient_type')
            ->pluck('count', 'recipient_type')
            ->toArray();
        $recipientTypes = array_keys($recipientTypeCounts);
        $recipientCounts = array_values($recipientTypeCounts);

        // Retrieve counts of messages by status
        $statusCounts = MessageLog::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Pass data to the view
        return view('analytics.index', compact('categoryLabels', 'categoryCounts', 'recipientTypes', 'recipientCounts', 'statusCounts', 'lowBalance'));
    }

}
