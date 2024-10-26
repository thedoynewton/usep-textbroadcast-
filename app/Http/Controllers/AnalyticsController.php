<?php

namespace App\Http\Controllers;

use App\Models\Campus;
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
        $recipientType = $request->input('recipient_type');
        $campusId = $request->input('campus');
        $collegeId = $request->input('college_id');
        $programId = $request->input('program_id');
        $majorId = $request->input('major_id');
        $year = $request->input('year'); // Capture selected year as year_id
        $officeId = $request->input('office_id'); // Capture selected office ID
        $type = $request->input('type'); // Capture selected type
        $status = $request->input('status');

        // Retrieve list of campuses from the database
        $campuses = Campus::all();

        // Define cost per SMS
        $costPerSms = 0.0065;

        // Query for costs per day from message_logs
        $costQuery = MessageLog::selectRaw("CONVERT(DATE, created_at) as date, SUM(sent_count) as total_sent")
            ->where('status', 'Sent')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupByRaw("CONVERT(DATE, created_at)");

        // Apply only the message type filter to the cost query
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
            $costs[] = $dailyCost;
            Log::info("Cost Overview - Date: {$data->date}, Total Sent: {$data->total_sent}, Daily Cost: {$dailyCost}");
        }

        // Query for Messages Overview (Success and Failed) from message_recipients
        $messageQuery = MessageRecipient::selectRaw("CONVERT(DATE, created_at) as date, sent_status, COUNT(*) as count")
            ->whereIn('sent_status', ['Sent', 'Failed'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupByRaw("CONVERT(DATE, created_at), sent_status");

        // Apply filters based on recipient type
        if ($recipientType) {
            $messageQuery->where('recipient_type', $recipientType);

            if ($recipientType === 'Student') {
                if (!empty($campusId))
                    $messageQuery->where('campus_id', $campusId);
                if (!empty($collegeId))
                    $messageQuery->where('college_id', $collegeId);
                if (!empty($programId))
                    $messageQuery->where('program_id', $programId);
                if (!empty($majorId))
                    $messageQuery->where('major_id', $majorId);
                if (!empty($year))
                    $messageQuery->where('year_id', $year);
            } elseif ($recipientType === 'Employee') {
                if (!empty($officeId))
                    $messageQuery->where('office_id', $officeId);
                if (!empty($type))
                    $messageQuery->where('type_id', $type);
                if (!empty($status))
                    $messageQuery->where('status_id', $status);
            }
        }

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
            'messageType',
            'recipientType',
            'campuses',
            'campusId',
            'collegeId',
            'programId',
            'majorId',
            'year', // Pass selected year_id to the view
            'officeId', // Pass selected office ID to the view
            'type', // Pass selected type to the view
            'status'
        ));
    }

}
