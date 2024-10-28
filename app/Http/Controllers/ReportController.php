<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;

class ReportController extends Controller
{
    /**
     * Generate report in either CSV or PDF format.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        // Get the requested format from the query parameters (default to CSV if not specified)
        $format = $request->query('format', 'csv');

        // Fetch message logs with related user and campus data
        $messageLogs = MessageLog::with(['user', 'campus'])->get();

        // Generate CSV if format is CSV
        if ($format === 'csv') {
            return $this->generateCsvReport($messageLogs);
        }

        // Generate PDF if format is PDF
        if ($format === 'pdf') {
            return $this->generatePdfReport($messageLogs);
        }

        // Return an error response if the format is not supported
        return response()->json(['error' => 'Invalid format specified'], 400);
    }

    /**
     * Generate CSV report.
     *
     * @param  \Illuminate\Database\Eloquent\Collection $messageLogs
     * @return \Illuminate\Http\Response
     */
    private function generateCsvReport($messageLogs)
    {
        // Define CSV headers
        $csvContent = "User,Campus,Recipient Type,Content,Message Type,Scheduled At,Sent At,Cancelled At,Status,Total Recipients,Sent Count,Failed Count,Created At,Updated At\n";

        // Populate CSV rows with message log data
        foreach ($messageLogs as $log) {
            $csvContent .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",%d,%d,%d,\"%s\",\"%s\"\n",
                $log->user->name ?? 'Unknown',
                $log->campus->campus_name ?? 'All Campuses',
                $log->recipient_type,
                $log->content,
                $log->message_type,
                $this->formatDate($log->scheduled_at),
                $this->formatDate($log->sent_at),
                $this->formatDate($log->cancelled_at),
                $log->status,
                $log->total_recipients,
                $log->sent_count,
                $log->failed_count,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->updated_at->format('Y-m-d H:i:s')
            );
        }

        // Define filename and headers
        $fileName = "message_logs_report.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        // Return the CSV as a response
        return response()->make($csvContent, 200, $headers);
    }

    /**
     * Generate PDF report.
     *
     * @param  \Illuminate\Database\Eloquent\Collection $messageLogs
     * @return \Illuminate\Http\Response
     */
    private function generatePdfReport($messageLogs)
    {
        // Render the Blade view to HTML and load it into the PDF generator
        $pdf = Pdf::loadView('report_pdf', compact('messageLogs'));

        // Return the PDF as a download
        return $pdf->download('message_logs_report.pdf');
    }

    /**
     * Format date fields consistently.
     *
     * @param  mixed $date
     * @return string
     */
    private function formatDate($date)
    {
        // Return "N/A" for null, empty, or non-date values
        if (empty($date)) {
            return 'N/A';
        }

        try {
            // Attempt to parse and format the date if it’s a valid Carbon instance
            $carbonDate = Carbon::parse($date);
            return $carbonDate->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            // Return "N/A" if date is invalid
            return 'N/A';
        }
    }
}
