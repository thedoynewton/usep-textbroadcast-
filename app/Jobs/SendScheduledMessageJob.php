<?php

namespace App\Jobs;

use App\Models\MessageLog;
use App\Models\MessageRecipient;
use App\Models\Student;
use App\Models\Employee;
use App\Services\MoviderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduledMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageLogId;
    protected $batchSize;
    /**
     * Create a new job instance.
     *
     * @param int $messageLogId
     */
    public function __construct($messageLogId, $batchSize = 1)
    {
        $this->messageLogId = $messageLogId;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     *
     * @param MoviderService $moviderService
     * @return void
     */
    public function handle(MoviderService $moviderService)
    {
        // Fetch the message log
        $messageLog = MessageLog::find($this->messageLogId);
    
        // Check if the message log exists
        if (!$messageLog) {
            Log::error("Message log not found for ID: {$this->messageLogId}");
            return;
        }
    
        // Check if the message was canceled
        if ($messageLog->status === 'cancelled') {
            Log::info("Scheduled message {$this->messageLogId} was canceled at {$messageLog->cancelled_at}, skipping job.");
            return; // Skip further processing if the message is canceled
        }
    
        $messageContent = $messageLog->content;
        $recipientType = $messageLog->recipient_type;
    
        // Handle sending to students
        if ($recipientType === 'students' || $recipientType === 'all') {
            $this->processStudents($messageLog, $messageContent);
        }
    
        // Handle sending to employees
        if ($recipientType === 'employees' || $recipientType === 'all') {
            $this->processEmployees($messageLog, $messageContent);
        }
    
        // Update sent/failed counts and status
        $messageLog->update([
            'sent_count' => $messageLog->recipients()->where('sent_status', 'Sent')->count(),
            'failed_count' => $messageLog->recipients()->where('sent_status', 'Failed')->count(),
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }    

    private function processStudents($messageLog, $messageContent)
    {
        // Fetch all students based on the message log's campus
        $students = Student::where('campus_id', $messageLog->campus_id)->get();
    
        // Split the students into batches
        $batches = $students->chunk($this->batchSize);
    
        foreach ($batches as $batch) {
            $recipients = [];
            $recipientDetails = [];
    
            foreach ($batch as $student) {
                $formattedNumber = $this->formatPhoneNumber($student->stud_contact);
                $recipients[] = $formattedNumber;
    
                $recipientDetails[] = [
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'student',
                    'stud_id' => $student->stud_id,
                    'emp_id' => null,
                    'fname' => $student->stud_fname ?? 'Unknown',
                    'lname' => $student->stud_lname ?? 'Unknown',
                    'mname' => $student->stud_mname ?? '',
                    'c_num' => $formattedNumber,
                    'email' => $student->stud_email ?? '',
                    'campus_id' => $student->campus_id,
                    'college_id' => $student->college_id,
                    'program_id' => $student->program_id,
                    'major_id' => $student->major_id,
                    'year_id' => $student->year_id,
                    'sent_status' => 'Pending',
                    'failure_reason' => '',
                ];
            }
    
            try {
                // Send bulk SMS for this batch
                app(MoviderService::class)->sendBulkSMS($recipients, $messageContent);
    
                // Mark the recipients as sent
                foreach ($recipientDetails as &$details) {
                    $details['sent_status'] = 'Sent';
                }
            } catch (\Exception $e) {
                // Log errors and mark as failed
                Log::error('Failed to send SMS: ' . $e->getMessage());
                foreach ($recipientDetails as &$details) {
                    $details['sent_status'] = 'Failed';
                    $details['failure_reason'] = $e->getMessage();
                }
            }
    
            // Log recipients in the database
            foreach ($recipientDetails as $details) {
                MessageRecipient::create($details);
            }
        }
    }

    private function processEmployees($messageLog, $messageContent)
    {
        // Fetch all employees based on the message log's campus
        $employees = Employee::where('campus_id', $messageLog->campus_id)->get();
    
        // Split the employees into batches
        $batches = $employees->chunk($this->batchSize);
    
        foreach ($batches as $batch) {
            $recipients = [];
            $recipientDetails = [];
    
            foreach ($batch as $employee) {
                $formattedNumber = $this->formatPhoneNumber($employee->emp_contact);
                $recipients[] = $formattedNumber;
    
                $recipientDetails[] = [
                    'message_log_id' => $messageLog->id,
                    'recipient_type' => 'employee',
                    'stud_id' => null,
                    'emp_id' => $employee->emp_id,
                    'fname' => $employee->emp_fname ?? 'Unknown',
                    'lname' => $employee->emp_lname ?? 'Unknown',
                    'mname' => $employee->emp_mname ?? '',
                    'c_num' => $formattedNumber,
                    'email' => $employee->emp_email ?? '',
                    'campus_id' => $employee->campus_id,
                    'office_id' => $employee->office_id,
                    'status_id' => $employee->status_id,
                    'type_id' => $employee->type_id,
                    'sent_status' => 'Pending',
                    'failure_reason' => '',
                ];
            }
    
            try {
                // Send bulk SMS for this batch
                app(MoviderService::class)->sendBulkSMS($recipients, $messageContent);
    
                // Mark the recipients as sent
                foreach ($recipientDetails as &$details) {
                    $details['sent_status'] = 'Sent';
                }
            } catch (\Exception $e) {
                // Log errors and mark as failed
                Log::error('Failed to send SMS: ' . $e->getMessage());
                foreach ($recipientDetails as &$details) {
                    $details['sent_status'] = 'Failed';
                    $details['failure_reason'] = $e->getMessage();
                }
            }
    
            // Log recipients in the database
            foreach ($recipientDetails as $details) {
                MessageRecipient::create($details);
            }
        }
    }    

    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Get the last 10 digits
        $phone = substr($phone, -10);

        // Prepend +63 country code
        return '+63' . $phone;
    }
}
