<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use App\Models\MessageRecipient;
use App\Services\MoviderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $moviderService;

    public function __construct(MoviderService $moviderService)
    {
        $this->moviderService = $moviderService; // Inject MoviderService
    }

    public function index(Request $request)
    {
        // Query logic for search and filtering
        $query = MessageLog::query();

        // Apply search and filters
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($recipientType = $request->input('recipient_type')) {
            $query->where('recipient_type', $recipientType);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Paginate the logs and append the search/filter parameters
        $messageLogs = $query->with(['user', 'campus'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // Fetch card data counts from the message_recipients table
        // Total Messages (only recipients with 'Sent' status)
        $totalMessages = MessageRecipient::where('sent_status', 'Sent')->count();

        // Scheduled Messages (recipients with 'Sent' status linked to 'scheduled' message type)
        $scheduledMessages = MessageRecipient::where('sent_status', 'Sent')
            ->whereHas('messageLog', function ($q) {
                $q->where('message_type', 'scheduled');
            })->count();

        // Immediate Messages (recipients with 'Sent' status linked to 'instant' message type)
        $immediateMessages = MessageRecipient::where('sent_status', 'Sent')
            ->whereHas('messageLog', function ($q) {
                $q->where('message_type', 'instant');
            })->count();

        // Failed Messages (recipients with 'Failed' status)
        $failedMessages = MessageRecipient::where('sent_status', 'Failed')->count();

        // Cancelled Messages (messages in the message_logs table with a 'cancelled' status)
        $cancelledMessages = MessageLog::where('status', 'cancelled')->count();

        // Pending Messages (recipients with 'Pending' status)
        $pendingMessages = MessageRecipient::where('sent_status', 'Pending')->count();

        // Fetch Movider balance using MoviderService
        $balanceData = $this->moviderService->getBalance();
        $balance = $balanceData['balance'] ?? 0;

        return view('dashboard', compact(
            'messageLogs',
            'totalMessages',
            'scheduledMessages',
            'immediateMessages',
            'failedMessages',
            'cancelledMessages',
            'pendingMessages',
            'balance'
        ));
    }

    public function getRecipients(Request $request)
    {
        $type = $request->query('type');
        $perPage = $request->query('perPage', 5); // Default to 5 recipients per page

        switch ($type) {
            case 'total':
                $recipients = MessageRecipient::where('sent_status', 'Sent')->paginate($perPage);
                break;
            case 'scheduled':
                $recipients = MessageRecipient::where('sent_status', 'Sent')
                    ->whereHas('messageLog', function ($query) {
                        $query->where('message_type', 'scheduled');
                    })->paginate($perPage);
                break;
            case 'instant':
                $recipients = MessageRecipient::where('sent_status', 'Sent')
                    ->whereHas('messageLog', function ($query) {
                        $query->where('message_type', 'instant');
                    })->paginate($perPage);
                break;
            case 'failed':
                $recipients = MessageRecipient::where('sent_status', 'Failed')->paginate($perPage);
                break;
            default:
                $recipients = collect([])->paginate($perPage); // Return empty collection if no valid type is provided
        }

        if ($request->ajax()) {
            return view('recipients.pagination', compact('recipients'))->render();
        }

        return response()->json(['recipients' => $recipients]);
    }
}
