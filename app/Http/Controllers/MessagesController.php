<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use App\Models\Year;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\MessageTemplate;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all'); // Default to 'all' if no tab is selected
        $campusId = $request->get('campus'); // Get the selected campus

        $campuses = Campus::all(); // Fetch all campuses from the database
        $messageTemplates = MessageTemplate::all(); // Fetch all message templates
        $years = Year::all(); // Fetch all years to populate the year dropdown
        $statuses = Status::all(); // Fetch all statuses to populate the status dropdown

        // Return the view with the necessary data for rendering the page
        return view('messages.index', compact('campuses', 'campusId', 'messageTemplates', 'years', 'statuses'));
    }
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'message' => 'required|max:160',
            'send_message' => 'required|in:now,later',
            'send_date' => 'required_if:send_message,later|nullable|date|after:now',
        ]);
    
        $sendType = $request->input('send_message');  // 'now' or 'later'
        $user = Auth::user();
    
        // Convert 'all' to null for database compatibility
        $campusId = $request->input('campus') === 'all' ? null : $request->input('campus');
    
        // Handle recipient type properly based on tab selection
        $recipientType = $request->input('tab') === 'all' ? 'all' : $request->input('tab');
        
        // Make sure recipient_type is never null
        if ($recipientType === null) {
            $recipientType = 'all';  // Set default to 'all'
        }
    
        $messageContent = $request->input('message');
        $totalRecipients = $request->input('total_recipients');
    
        // Prepare the scheduled date if 'send_later' is selected
        $scheduledAt = $sendType === 'later' ? Carbon::createFromFormat('Y-m-d\TH:i', $request->input('send_date'))->format('Y-m-d H:i:s') : null;
    
        // Log the message in the MessageLog
        $messageLog = MessageLog::create([
            'user_id' => $user->id,
            'campus_id' => $campusId,  // This will now be null if "All Campuses" is selected
            'recipient_type' => $recipientType,  // Ensure 'all', 'students', or 'employees' is logged
            'content' => $messageContent,
            'message_type' => $sendType === 'now' ? 'instant' : 'scheduled',
            'scheduled_at' => $scheduledAt,
            'sent_at' => $sendType === 'now' ? now() : null,
            'status' => $sendType === 'now' ? 'sent' : 'scheduled',
            'total_recipients' => $totalRecipients,
            'sent_count' => 0,  // Initially set to 0
            'failed_count' => 0  // Initially set to 0
        ]);
    
        if ($sendType === 'now') {
            // Logic for sending the message immediately
            // After sending, update 'sent_count' and 'failed_count'
            $messageLog->update([
                'sent_count' => $totalRecipients,
                'failed_count' => 0,  // Assuming no failures, you can adjust this as needed
            ]);
    
            return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
        } elseif ($sendType === 'later') {
            // Logic for scheduling the message to be sent later
            return redirect()->route('messages.index')->with('success', 'Message scheduled successfully.');
        }
    }    
    
}
