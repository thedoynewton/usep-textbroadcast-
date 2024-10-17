<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MessageLog;
use App\Models\MessageRecipient;
use App\Models\Student;
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
    
        // Convert 'all' to 0 for database compatibility
        $campusId = $request->input('campus') === 'all' ? null : $request->input('campus');
    
        // Handle recipient type based on tab selection
        $recipientType = $request->input('tab') ?? 'all';
        
        $messageContent = $request->input('message');
        $totalRecipients = $request->input('total_recipients');
    
        // Prepare the scheduled date if 'send_later' is selected
        $scheduledAt = $sendType === 'later' ? Carbon::createFromFormat('Y-m-d\TH:i', $request->input('send_date'))->format('Y-m-d H:i:s') : null;
    
        // Log the message in the MessageLog
        $messageLog = MessageLog::create([
            'user_id' => $user->id,
            'campus_id' => $campusId,  // This will now be 0 if "All Campuses" is selected
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
    
        // Fetch recipients based on the selected tab (students, employees, or all)
        if ($recipientType === 'students') {
            $recipients = Student::where('campus_id', $campusId)->get(); // Adjust filtering as needed
        } elseif ($recipientType === 'employees') {
            $recipients = Employee::where('campus_id', $campusId)->get(); // Adjust filtering as needed
        } else {
            // For 'all', retrieve both students and employees
            $recipients = Student::where('campus_id', $campusId)->get()->merge(
                Employee::where('campus_id', $campusId)->get()
            );
        }
    
        // Log each recipient in the message_recipients table
        foreach ($recipients as $recipient) {
            if ($recipient instanceof Student) {
                // Handle students
                MessageRecipient::create([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'student',
                    'stud_id' => $recipient->stud_id,
                    'emp_id' => null,  // For students, emp_id is null
                    'fname' => $recipient->stud_fname,
                    'lname' => $recipient->stud_lname,
                    'mname' => $recipient->stud_mname,
                    'c_num' => $recipient->stud_contact,
                    'email' => $recipient->stud_email,
                    'campus_id' => $recipient->campus_id,
                    'college_id' => $recipient->college_id,
                    'program_id' => $recipient->program_id,
                    'major_id' => $recipient->major_id,
                    'year_id' => $recipient->year_id,
                    'sent_status' => 'Failed',  // Default to 'Failed', can update after sending
                ]);
            } elseif ($recipient instanceof Employee) {
                // Handle employees
                MessageRecipient::create([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'employee',
                    'stud_id' => null,  // For employees, stud_id is null
                    'emp_id' => $recipient->emp_id,
                    'fname' => $recipient->emp_fname,
                    'lname' => $recipient->emp_lname,
                    'mname' => $recipient->emp_mname,
                    'c_num' => $recipient->emp_contact,
                    'email' => $recipient->emp_email,
                    'campus_id' => $recipient->campus_id,
                    'office_id' => $recipient->office_id,
                    'status_id' => $recipient->status_id,
                    'type_id' => $recipient->type_id,
                    'sent_status' => 'Failed',  // Default to 'Failed', can update after sending
                ]);
            }
        }
    
        if ($sendType === 'now') {
            // Logic for sending the message immediately
            // After sending, update 'sent_count' and 'failed_count'
            $messageLog->update([
                'sent_count' => $totalRecipients,
                'failed_count' => 0,  // Adjust this if there are failures
            ]);
    
            // Optionally, update the sent status for all recipients
            MessageRecipient::where('message_log_id', $messageLog->id)->update(['sent_status' => 'Sent']);
    
            return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
        } elseif ($sendType === 'later') {
            // Logic for scheduling the message
    
            return redirect()->route('messages.index')->with('success', 'Message scheduled successfully.');
        }
    }
    
}
