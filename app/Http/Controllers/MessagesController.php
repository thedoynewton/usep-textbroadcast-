<?php

namespace App\Http\Controllers;

use App\Jobs\SendScheduledMessageJob;
use App\Models\Employee;
use App\Models\MessageLog;
use App\Models\MessageRecipient;
use App\Models\Student;
use App\Models\Year;
use App\Services\MoviderService;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\MessageTemplate;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    protected $moviderService;

    public function __construct(MoviderService $moviderService)
    {
        $this->moviderService = $moviderService;
    }
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
    
        // Fetch remaining balance from Movider
        $balanceData = $this->moviderService->getBalance();
        $remainingBalance = $balanceData['balance'] ?? 0;
    
        $costPerSms = 0.0065; // Assume cost per SMS is $0.0065
        $campusId = $request->input('campus') === 'all' ? null : $request->input('campus');
        $collegeId = $request->input('academic_unit') === 'all' ? null : $request->input('academic_unit');
        $programId = $request->input('program') === 'all' ? null : $request->input('program');
        $majorId = $request->input('major') === 'all' ? null : $request->input('major');
        $yearId = $request->input('year') === 'all' ? null : $request->input('year');
    
        $officeId = $request->input('office') === 'all' ? null : $request->input('office');
        $statusId = $request->input('status') === 'all' ? null : $request->input('status');
        $typeId = $request->input('type') === 'all' ? null : $request->input('type');
    
        $recipientType = $request->input('tab') ?? 'all';
        $messageContent = $request->input('message');
        $totalRecipients = $request->input('total_recipients');
    
        // Calculate the total cost for all recipients
        $totalCost = $totalRecipients * $costPerSms;
    
        // Check if the remaining balance is sufficient
        if ($remainingBalance < $totalCost) {
            $maxRecipients = floor($remainingBalance / $costPerSms);
            return redirect()->back()->with('error', "Insufficient balance! You can only send to {$maxRecipients} out of {$totalRecipients} selected recipients. Please top-up or limit the recipients.");
        }
    
        // Prepare the scheduled date if 'send_later' is selected
        $scheduledAt = $sendType === 'later' ? Carbon::createFromFormat('Y-m-d\TH:i', $request->input('send_date'), 'Asia/Manila') : null;
    
        // Log the message in the MessageLog
        $messageLog = MessageLog::create([
            'user_id' => $user->id,
            'campus_id' => $campusId,
            'recipient_type' => $recipientType,
            'content' => $messageContent,
            'message_type' => $sendType === 'now' ? 'instant' : 'scheduled',
            'scheduled_at' => $scheduledAt,
            'sent_at' => $sendType === 'now' ? now() : null,
            'status' => $sendType === 'now' ? 'sent' : 'pending',
            'total_recipients' => $totalRecipients,
            'sent_count' => 0,
            'failed_count' => 0
        ]);
    
        // If sending now, process immediately
        if ($sendType === 'now') {
            $this->processImmediateSending($messageLog, $recipientType, $messageContent, $campusId, $collegeId, $programId, $majorId, $yearId, $officeId, $statusId, $typeId);
            return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
        }
    
        // If sending later, schedule the job
        if ($sendType === 'later') {
            // Dispatch the job to send the message at the scheduled time
            SendScheduledMessageJob::dispatch($messageLog->id)->delay($scheduledAt);
            return redirect()->route('messages.index')->with('success', 'Message scheduled successfully.');
        }
    }
    
    private function processImmediateSending($messageLog, $recipientType, $messageContent, $campusId, $collegeId, $programId, $majorId, $yearId, $officeId, $statusId, $typeId)
    {
        if ($recipientType === 'students' || $recipientType === 'all') {
            $students = Student::when($campusId, function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            })->when($collegeId, function ($query, $collegeId) {
                return $query->where('college_id', $collegeId);
            })->when($programId, function ($query, $programId) {
                return $query->where('program_id', $programId);
            })->when($majorId, function ($query, $majorId) {
                return $query->where('major_id', $majorId);
            })->when($yearId, function ($query, $yearId) {
                return $query->where('year_id', $yearId);
            })->get();
    
            foreach ($students as $student) {
                $formattedNumber = $this->formatPhoneNumber($student->stud_contact);
                $failureReason = '';
    
                try {
                    $this->moviderService->sendSMS($formattedNumber, $messageContent);
                    $sentStatus = 'Sent'; // Success
                } catch (\Exception $e) {
                    $sentStatus = 'Failed'; // SMS send failure
                    $failureReason = $e->getMessage();
                    Log::error("Failed to send SMS to {$formattedNumber}: " . $failureReason);
                }
    
                MessageRecipient::create([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'student',
                    'stud_id' => $student->stud_id,
                    'emp_id' => null,
                    'fname' => $student->stud_fname,
                    'lname' => $student->stud_lname,
                    'mname' => $student->stud_mname,
                    'c_num' => $formattedNumber,
                    'email' => $student->stud_email,
                    'campus_id' => $student->campus_id,
                    'college_id' => $student->college_id,
                    'program_id' => $student->program_id,
                    'major_id' => $student->major_id,
                    'year_id' => $student->year_id,
                    'sent_status' => $sentStatus,
                    'failure_reason' => $failureReason,  // Log the failure reason
                ]);
            }
        }
    
        if ($recipientType === 'employees' || $recipientType === 'all') {
            $employees = Employee::when($campusId, function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            })->when($officeId, function ($query, $officeId) {
                return $query->where('office_id', $officeId);
            })->when($statusId, function ($query, $statusId) {
                return $query->where('status_id', $statusId);
            })->when($typeId, function ($query, $typeId) {
                return $query->where('type_id', $typeId);
            })->get();
    
            foreach ($employees as $employee) {
                $formattedNumber = $this->formatPhoneNumber($employee->emp_contact);
                $failureReason = '';
    
                try {
                    $this->moviderService->sendSMS($formattedNumber, $messageContent);
                    $sentStatus = 'Sent'; // Success
                } catch (\Exception $e) {
                    $sentStatus = 'Failed'; // SMS send failure
                    $failureReason = $e->getMessage();
                    Log::error("Failed to send SMS to {$formattedNumber}: " . $failureReason);
                }
    
                MessageRecipient::create([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'employee',
                    'stud_id' => null,
                    'emp_id' => $employee->emp_id,
                    'fname' => $employee->emp_fname,
                    'lname' => $employee->emp_lname,
                    'mname' => $employee->emp_mname,
                    'c_num' => $formattedNumber,
                    'email' => $employee->emp_email,
                    'campus_id' => $employee->campus_id,
                    'office_id' => $employee->office_id,
                    'status_id' => $employee->status_id,
                    'type_id' => $employee->type_id,
                    'sent_status' => $sentStatus,
                    'failure_reason' => $failureReason,  // Log the failure reason
                ]);
            }
        }
    
        // Update sent and failed counts
        $messageLog->update([
            'sent_count' => $messageLog->recipients()->where('sent_status', 'Sent')->count(),
            'failed_count' => $messageLog->recipients()->where('sent_status', 'Failed')->count(),
        ]);
    }
        

    /**
     * Format phone numbers by extracting the first 10 digits and prepending +63.
     *
     * @param string $number
     * @return string
     */
    public function formatPhoneNumber($number)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/\D/', '', $number);

        // Get the last 10 digits
        $number = substr($number, -10);

        // Prepend +63 country code
        return '+63' . $number;
    }

}
