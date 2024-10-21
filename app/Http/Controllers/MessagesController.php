<?php

namespace App\Http\Controllers;

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

        // Convert 'all' to null for database compatibility
        $campusId = $request->input('campus') === 'all' ? null : $request->input('campus');
        $collegeId = $request->input('academic_unit') === 'all' ? null : $request->input('academic_unit');
        $programId = $request->input('program') === 'all' ? null : $request->input('program');
        $majorId = $request->input('major') === 'all' ? null : $request->input('major');
        $yearId = $request->input('year') === 'all' ? null : $request->input('year');

        // Employee-specific filters
        $officeId = $request->input('office') === 'all' ? null : $request->input('office');
        $statusId = $request->input('status') === 'all' ? null : $request->input('status');
        $typeId = $request->input('type') === 'all' ? null : $request->input('type');

        // Handle recipient type based on tab selection
        $recipientType = $request->input('tab') ?? 'all';

        $messageContent = $request->input('message');
        $totalRecipients = $request->input('total_recipients');

        // Prepare the scheduled date if 'send_later' is selected
        $scheduledAt = $sendType === 'later' ? Carbon::createFromFormat('Y-m-d\TH:i', $request->input('send_date'))->format('Y-m-d H:i:s') : null;

        // Determine if a message template was selected
        $templateId = $request->input('template');
        if ($templateId) {
            // A template was selected, retrieve the template's name to log it
            $template = MessageTemplate::find($templateId);
            $logContent = $template->name; // Log the template name
        } else {
            // No template was selected, log the actual message content and create a new template
            $logContent = $messageContent;

            // Add the message as a new template
            $newTemplate = MessageTemplate::create([
                'name' => 'Custom Template ' . now()->format('Y-m-d H:i:s'),  // You can customize the name as needed
                'content' => $messageContent,
            ]);
        }

        // Log the message in the MessageLog
        $messageLog = MessageLog::create([
            'user_id' => $user->id,
            'campus_id' => $campusId,  // This will now be null if "All Campuses" is selected
            'recipient_type' => $recipientType,  // Ensure 'all', 'students', or 'employees' is logged
            'content' => $logContent,  // Log either the template name or the message content
            'message_type' => $sendType === 'now' ? 'instant' : 'scheduled',
            'scheduled_at' => $scheduledAt,
            'sent_at' => $sendType === 'now' ? now() : null,
            'status' => $sendType === 'now' ? 'sent' : 'scheduled',
            'total_recipients' => $totalRecipients,
            'sent_count' => 0,  // Initially set to 0
            'failed_count' => 0  // Initially set to 0
        ]);

        // Fetch and log students if recipient type is students or all
        if ($recipientType === 'students' || $recipientType === 'all') {
            // Fetch students based on the user's filters
            $students = Student::when($campusId, function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            })
                ->when($collegeId, function ($query, $collegeId) {
                    return $query->where('college_id', $collegeId);
                })
                ->when($programId, function ($query, $programId) {
                    return $query->where('program_id', $programId);
                })
                ->when($majorId, function ($query, $majorId) {
                    return $query->where('major_id', $majorId);
                })
                ->when($yearId, function ($query, $yearId) {
                    return $query->where('year_id', $yearId);
                })
                ->get();

            foreach ($students as $student) {
                // Format the phone number before sending the SMS
                $formattedNumber = $this->formatPhoneNumber($student->stud_contact);

                // Send SMS and log recipient
                try {
                    if ($sendType === 'now') {
                        // Use Movider service to send SMS
                        $this->moviderService->sendSMS($formattedNumber, $messageContent);
                        $sentStatus = 'Sent'; // Success

                    } else {
                        $sentStatus = 'Scheduled';
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send SMS to {$formattedNumber}: " . $e->getMessage());
                    $sentStatus = 'Failed'; // SMS send failure
                }

                // Log recipient
                MessageRecipient::create([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'student',
                    'stud_id' => $student->stud_id,
                    'emp_id' => null,  // For students, emp_id is null
                    'fname' => $student->stud_fname,
                    'lname' => $student->stud_lname,
                    'mname' => $student->stud_mname,
                    'c_num' => $formattedNumber,  // Use the formatted phone number
                    'email' => $student->stud_email,
                    'campus_id' => $student->campus_id,
                    'college_id' => $student->college_id,
                    'program_id' => $student->program_id,
                    'major_id' => $student->major_id,
                    'year_id' => $student->year_id,
                    'enrollment_stat' => $student->enrollment_stat,  // Include enrollment_stat
                    'sent_status' => $sentStatus,  // Updated status based on send result
                ]);
            }
        }

        // Fetch and log employees if recipient type is employees or all
        if ($recipientType === 'employees' || $recipientType === 'all') {
            $employees = Employee::when($campusId, function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            })
                ->when($officeId, function ($query, $officeId) {
                    return $query->where('office_id', $officeId);
                })
                ->when($statusId, function ($query, $statusId) {
                    return $query->where('status_id', $statusId);
                })
                ->when($typeId, function ($query, $typeId) {
                    return $query->where('type_id', $typeId);
                })
                ->get();

            foreach ($employees as $employee) {
                // Format the phone number before sending the SMS
                $formattedNumber = $this->formatPhoneNumber($employee->emp_contact);

                // Send SMS and log recipient
                try {
                    if ($sendType === 'now') {
                        // Use Movider service to send SMS
                        $this->moviderService->sendSMS($formattedNumber, $messageContent);
                        $sentStatus = 'Sent'; // Success

                    } else {
                        $sentStatus = 'Scheduled';
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send SMS to {$formattedNumber}: " . $e->getMessage());
                    $sentStatus = 'Failed'; // SMS send failure
                }

                // Log recipient
                MessageRecipient::create([
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'employee',
                    'stud_id' => null,  // For employees, stud_id is null
                    'emp_id' => $employee->emp_id,
                    'fname' => $employee->emp_fname,
                    'lname' => $employee->emp_lname,
                    'mname' => $employee->emp_mname,
                    'c_num' => $formattedNumber,  // Use the formatted phone number
                    'email' => $employee->emp_email,
                    'campus_id' => $employee->campus_id,
                    'office_id' => $employee->office_id,
                    'status_id' => $employee->status_id,
                    'type_id' => $employee->type_id,
                    'sent_status' => $sentStatus,  // Updated status based on send result
                ]);
            }
        }

        if ($sendType === 'now') {
            // Update sent and failed counts
            $messageLog->update([
                'sent_count' => $messageLog->recipients()->where('sent_status', 'Sent')->count(),
                'failed_count' => $messageLog->recipients()->where('sent_status', 'Failed')->count(),
            ]);

            return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
        } elseif ($sendType === 'later') {
            // Logic for scheduling the message
            return redirect()->route('messages.index')->with('success', 'Message scheduled successfully.');
        }
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
