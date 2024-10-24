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
use Illuminate\Support\Facades\Queue;

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
            'batch_size' => 'required|integer|min:1'
        ]);

        $sendType = $request->input('send_message');  // 'now' or 'later'
        $batchSize = $request->input('batch_size');
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

        // Determine if a message template was selected
        $templateId = $request->input('template');
        if ($templateId) {
            $template = MessageTemplate::find($templateId);
            $logContent = $template->name;
        } else {
            $logContent = $messageContent;
            $newTemplate = MessageTemplate::create([
                'name' => 'Custom Template ' . now()->format('Y-m-d H:i:s'),
                'content' => $messageContent,
            ]);
        }

        // Log the message in the MessageLog
        $messageLog = MessageLog::create([
            'user_id' => $user->id,
            'campus_id' => $campusId,
            'recipient_type' => $recipientType,
            'content' => $logContent,
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
            $this->processImmediateSending($messageLog, $recipientType, $messageContent, $campusId, $collegeId, $programId, $majorId, $yearId, $officeId, $statusId, $typeId, $batchSize);
            return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
        }

        // If sending later, schedule the job
        if ($sendType === 'later') {
            // Prepare the scheduled date in the user's timezone (Asia/Manila in your case)
            $scheduledAt = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('send_date'), 'Asia/Manila');

            // Filters for student/employee recipients
            $filters = [
                'campus_id' => $campusId,
                'college_id' => $collegeId,
                'program_id' => $programId,
                'major_id' => $majorId,
                'year_id' => $yearId,
                'office_id' => $officeId,
                'status_id' => $statusId,
                'type_id' => $typeId,
            ];

            // Dispatch the job to send the message at the scheduled time
            SendScheduledMessageJob::dispatch($messageLog, $recipientType, $messageContent, $filters, $batchSize)
                ->delay($scheduledAt); // Schedule for the specified time

            return redirect()->route('messages.index')->with('success', 'Message scheduled successfully.');
        }

    }

    public function processImmediateSending($messageLog, $recipientType, $messageContent, $campusId, $collegeId, $programId, $majorId, $yearId, $officeId, $statusId, $typeId, $batchSize)
    {
        // Initialize a collection to store unique recipients by phone number
        $recipientsByPhone = collect();
        $recipientDetails = collect(); // To store all the recipients, even if they share the same phone number
    
        // Process students if applicable
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
    
            // Add students to the recipients collection
            $students->each(function ($student) use ($recipientsByPhone, $recipientDetails, $messageLog) {
                $formattedNumber = $this->formatPhoneNumber($student->stud_contact);
    
                // Store recipients by their unique phone number
                if (!$recipientsByPhone->contains('c_num', $formattedNumber)) {
                    $recipientsByPhone->push([
                        'c_num' => $formattedNumber,
                        'sent_status' => '', // Will be updated to either Sent or Failed
                        'failure_reason' => '',
                    ]);
                }
    
                // Store recipient details, to be logged later for each unique number
                $recipientDetails->push([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'student',
                    'stud_id' => $student->stud_id,
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
                ]);
            });
        }
    
        // Process employees if applicable
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
    
            // Add employees to the recipients collection
            $employees->each(function ($employee) use ($recipientsByPhone, $recipientDetails, $messageLog) {
                $formattedNumber = $this->formatPhoneNumber($employee->emp_contact);
    
                // Store recipients by their unique phone number
                if (!$recipientsByPhone->contains('c_num', $formattedNumber)) {
                    $recipientsByPhone->push([
                        'c_num' => $formattedNumber,
                        'sent_status' => '', // Will be updated to either Sent or Failed
                        'failure_reason' => '',
                    ]);
                }
    
                // Store recipient details, to be logged later for each unique number
                $recipientDetails->push([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'employee',
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
                ]);
            });
        }
    
        // Split unique recipients by phone number into batches according to the $batchSize
        $batches = $recipientsByPhone->chunk($batchSize);
        $sentCount = 0;
        $failedCount = 0;
    
        foreach ($batches as $batch) {
            foreach ($batch as $recipient) {
                try {
                    // Send individual SMS using Movider service for each unique number
                    $this->moviderService->sendBulkSMS([$recipient['c_num']], $messageContent);
    
                    // Update sent status after successful sending
                    $recipient['sent_status'] = 'Sent'; // Mark as sent
                    $sentCount++; // Increment sent count
                } catch (\Exception $e) {
                    // Log error and mark status as failed
                    Log::error("Failed to send SMS for Message Log ID: {$messageLog->id}. Error: " . $e->getMessage());
                    $recipient['sent_status'] = 'Failed'; // Mark as failed
                    $recipient['failure_reason'] = $e->getMessage();
                    $failedCount++; // Increment failed count
                }
    
                // Log each recipient in the MessageRecipient table (even if they share the same number)
                $recipientDetails->where('c_num', $recipient['c_num'])->each(function ($detail) use ($recipient) {
                    // **Fix:** Make sure to check if message sending was successful before marking as failed
                    if ($recipient['sent_status'] === 'Sent') {
                        $detail['sent_status'] = 'Sent';
                        $detail['failure_reason'] = null;
                    } else {
                        $detail['sent_status'] = 'Failed';
                        $detail['failure_reason'] = $recipient['failure_reason'];
                    }
    
                    // Log the recipient in the database
                    MessageRecipient::create($detail);
                });
            }
        }
    
        // **Final Update:** Update sent and failed counts in message_log
        $messageLog->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ]);

        // Log the final sent and failed count
        Log::info("Message Log ID: {$messageLog->id} - Sent: {$sentCount}, Failed: {$failedCount}");
    }    

    public function cancel($id)
    {
        // Find the message log
        $messageLog = MessageLog::findOrFail($id);

        // Ensure the message is still pending and scheduled
        if ($messageLog->status === 'pending' && $messageLog->message_type === 'scheduled') {
            // Mark the message as canceled and record the cancellation time
            $messageLog->update([
                'status' => 'cancelled',
                'cancelled_at' => now(), // Log the cancellation time
            ]);

            return redirect()->back()->with('success', 'Scheduled message has been canceled.');
        }

        // If the message is not pending or scheduled, show an error
        return redirect()->back()->with('error', 'Message cannot be canceled.');
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
