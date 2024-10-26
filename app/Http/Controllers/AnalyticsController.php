<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\MessageRecipient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    protected $costPerSms = 0.0065;

    public function index(Request $request)
    {
        // Retrieve filter parameters with defaults
        $filters = $this->getFilters($request);

        // Retrieve list of campuses for view
        $campuses = Campus::all();

        // Get Costs Overview data
        $costOverview = $this->getCostOverview($filters);

        // Get Messages Overview data
        $messageOverview = $this->getMessageOverview($filters);

        return view('analytics.index', array_merge(
            $costOverview,
            $messageOverview,
            $filters,
            compact('campuses')
        ));
    }

    protected function getFilters(Request $request)
    {
        return [
            'startDate' => $request->input('start_date', now()->subDays(7)->format('Y-m-d')),
            'endDate' => $request->input('end_date', now()->format('Y-m-d')),
            'messageType' => $request->input('message_type'),
            'recipientType' => $request->input('recipient_type'),
            'campusId' => $request->input('campus'),
            'collegeId' => $request->input('college_id'),
            'programId' => $request->input('program_id'),
            'majorId' => $request->input('major_id'),
            'year' => $request->input('year'),
            'officeId' => $request->input('office_id'),
            'type' => $request->input('type'),
            'status' => $request->input('status')
        ];
    }

    protected function getCostOverview($filters)
    {
        $costData = MessageLog::selectRaw("CONVERT(DATE, created_at) as date, SUM(sent_count) as total_sent")
            ->where('status', 'Sent')
            ->whereDate('created_at', '>=', $filters['startDate'])
            ->whereDate('created_at', '<=', $filters['endDate'])
            ->when($filters['messageType'], fn($query) => $query->where('message_type', $filters['messageType']))
            ->groupByRaw("CONVERT(DATE, created_at)")
            ->get();

        $costDates = [];
        $costs = [];
        foreach ($costData as $data) {
            $costDates[] = $data->date;
            $costs[] = $data->total_sent * $this->costPerSms;
            Log::info("Cost Overview - Date: {$data->date}, Total Sent: {$data->total_sent}, Daily Cost: " . end($costs));
        }

        return compact('costDates', 'costs');
    }

    protected function getMessageOverview($filters)
    {
        $messageData = MessageRecipient::selectRaw("CONVERT(DATE, created_at) as date, sent_status, COUNT(*) as count")
            ->whereIn('sent_status', ['Sent', 'Failed'])
            ->whereDate('created_at', '>=', $filters['startDate'])
            ->whereDate('created_at', '<=', $filters['endDate'])
            ->when($filters['campusId'], fn($query) => $query->where('campus_id', $filters['campusId']))
            ->when($filters['recipientType'], function($query) use ($filters) {
                $query->where('recipient_type', $filters['recipientType']);
                $this->applyRecipientSpecificFilters($query, $filters);
            })
            ->groupByRaw("CONVERT(DATE, created_at), sent_status")
            ->get();

        $messageDates = [];
        $successCounts = [];
        $failedCounts = [];

        foreach ($messageData->groupBy('date') as $date => $records) {
            $messageDates[] = $date;
            $successCounts[] = $records->firstWhere('sent_status', 'Sent')->count ?? 0;
            $failedCounts[] = $records->firstWhere('sent_status', 'Failed')->count ?? 0;
        }

        return compact('messageDates', 'successCounts', 'failedCounts');
    }

    protected function applyRecipientSpecificFilters($query, $filters)
    {
        if ($filters['recipientType'] === 'Student') {
            $query->when($filters['campusId'], fn($q) => $q->where('campus_id', $filters['campusId']))
                  ->when($filters['collegeId'], fn($q) => $q->where('college_id', $filters['collegeId']))
                  ->when($filters['programId'], fn($q) => $q->where('program_id', $filters['programId']))
                  ->when($filters['majorId'], fn($q) => $q->where('major_id', $filters['majorId']))
                  ->when($filters['year'], fn($q) => $q->where('year_id', $filters['year']));
        } elseif ($filters['recipientType'] === 'Employee') {
            $query->when($filters['officeId'], fn($q) => $q->where('office_id', $filters['officeId']))
                  ->when($filters['type'], fn($q) => $q->where('type_id', $filters['type']))
                  ->when($filters['status'], fn($q) => $q->where('status_id', $filters['status']));
        }
    }
}
