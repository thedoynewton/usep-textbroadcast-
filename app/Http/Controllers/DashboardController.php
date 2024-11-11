<?php

namespace App\Http\Controllers;

use App\Models\CreditBalance;
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
        $query = MessageLog::query();
    
        // Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere(function ($q3) use ($search) {
                        if (strtolower($search) === 'all campuses') {
                            $q3->whereNull('campus_id');
                        } else {
                            $q3->whereHas('campus', function ($q4) use ($search) {
                                $q4->where('campus_name', 'like', "%{$search}%");
                            });
                        }
                    })
                    ->orWhere('message_type', 'like', "%{$search}%");
            });
        }
    
        // Apply recipient type filter
        if ($recipientType = $request->input('recipient_type')) {
            $query->where('recipient_type', $recipientType);
        }
    
        // Apply status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
    
        // Paginate the results
        $messageLogs = $query->with(['user', 'campus'])
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->appends($request->all());
    
        // Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.message-logs-content', compact('messageLogs'))->render(),
            ]);
        }
    
        // Fetch unique message data for the widgets
        $totalMessages = MessageLog::where(function ($q) {
            $q->where('message_type', 'instant')
              ->orWhere(function ($q2) {
                  $q2->where('message_type', 'scheduled')
                     ->where('status', 'sent'); // Only count sent scheduled messages
              });
        })->count();
        $scheduledMessages = MessageLog::where('message_type', 'scheduled')
                                    ->where('status', 'sent') // Only count delivered scheduled messages
                                    ->count();
        $immediateMessages = MessageLog::where('message_type', 'instant')->count();
        $failedMessages = MessageRecipient::where('sent_status', 'Failed')->count();
        $cancelledMessages = MessageLog::where('status', 'cancelled')->count();
        $pendingMessages = MessageLog::where('status', 'pending')->count();
    
        $balanceData = $this->moviderService->getBalance();
        $balance = $balanceData['balance'] ?? 0;
    
        // Retrieve the credit balance from the CreditBalance model
        $creditBalance = CreditBalance::first()->balance ?? 0;
    
        return view('dashboard', compact(
            'messageLogs',
            'totalMessages',
            'scheduledMessages',
            'immediateMessages',
            'failedMessages',
            'cancelledMessages',
            'pendingMessages',
            'balance',
            'creditBalance'
        ));
    }    

    // public function getRecipients(Request $request)
    // {
    //     $type = $request->query('type');
    //     $perPage = $request->query('perPage', 5); // Default to 5 recipients per page

    //     switch ($type) {
    //         case 'total':
    //             $recipients = MessageRecipient::where('sent_status', 'Sent')->paginate($perPage);
    //             break;
    //         case 'scheduled':
    //             $recipients = MessageRecipient::where('sent_status', 'Sent')
    //                 ->whereHas('messageLog', function ($query) {
    //                     $query->where('message_type', 'scheduled');
    //                 })->paginate($perPage);
    //             break;
    //         case 'instant':
    //             $recipients = MessageRecipient::where('sent_status', 'Sent')
    //                 ->whereHas('messageLog', function ($query) {
    //                     $query->where('message_type', 'instant');
    //                 })->paginate($perPage);
    //             break;
    //         case 'failed':
    //             $recipients = MessageRecipient::where('sent_status', 'Failed')->paginate($perPage);
    //             break;
    //         default:
    //             $recipients = collect([])->paginate($perPage); // Return empty collection if no valid type is provided
    //     }

    //     if ($request->ajax()) {
    //         return view('recipients.pagination', compact('recipients'))->render();
    //     }

    //     return response()->json(['recipients' => $recipients]);
    // }

    public function getRecipientsByMessageLog($message_log_id, Request $request)
    {
        // Retrieve recipients with pagination, 5 per page
        $recipients = MessageRecipient::where('message_log_id', $message_log_id)
            ->paginate(5, ['fname', 'lname', 'email', 'c_num']);
    
        return response()->json([
            'recipients' => $recipients
        ]);
    }
}
