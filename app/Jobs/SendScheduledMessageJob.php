<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\MessageRecipient;
use App\Models\Student;
use App\Services\MoviderService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendScheduledMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageLog;
    protected $recipientType;
    protected $messageContent;
    protected $filters;
    protected $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct($messageLog, $recipientType, $messageContent, $filters, $batchSize)
    {
        $this->messageLog = $messageLog;
        $this->recipientType = $recipientType;
        $this->messageContent = $messageContent;
        $this->filters = $filters;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     */
    public function handle(MoviderService $moviderService)
    {
        try {
            DB::beginTransaction();

            // Call the method that processes sending the message in batches
            $this->processScheduledMessageBatch(
                $this->messageLog,
                $this->recipientType,
                $this->messageContent,
                $this->filters,
                $this->batchSize,
                $moviderService
            );

            // Commit the transaction
            DB::commit();

            // After sending, update the message_log status to 'Sent'
            $this->messageLog->update([
                'status' => 'Sent',
                'sent_at' => now(), // Record the exact time the message was sent
            ]);

        } catch (Exception $e) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Log the error for troubleshooting
            Log::error('Failed to send scheduled message: ' . $e->getMessage());

            // Optionally, update the message log with a failure status
            $this->messageLog->update([
                'status' => 'Failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process the messages in batches and send individually within each batch.
     */
    private function processScheduledMessageBatch($messageLog, $recipientType, $messageContent, $filters, $batchSize, MoviderService $moviderService)
    {
        // Fetch recipients based on filters (students, employees, etc.)
        $recipients = collect();

        // Fetch students or employees based on the recipient type
        if ($recipientType === 'students' || $recipientType === 'all') {
            $students = Student::when($filters['campus_id'], function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            })->when($filters['college_id'], function ($query, $collegeId) {
                return $query->where('college_id', $collegeId);
            })->when($filters['program_id'], function ($query, $programId) {
                return $query->where('program_id', $programId);
            })->when($filters['major_id'], function ($query, $majorId) {
                return $query->where('major_id', $majorId);
            })->when($filters['year_id'], function ($query, $yearId) {
                return $query->where('year_id', $yearId);
            })->get();

            // Add students to the recipients collection with unique phone numbers
            $students->each(function ($student) use ($recipients, $messageLog) {
                $formattedNumber = $this->formatPhoneNumber($student->stud_contact);

                if (!$recipients->contains('c_num', $formattedNumber)) {
                    $recipients->push([
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
                        'sent_status' => 'Pending', // Default to pending
                        'failure_reason' => '',     // Initially blank
                    ]);
                }
            });
        }

        // Similar logic for employees
        if ($recipientType === 'employees' || $recipientType === 'all') {
            $employees = Employee::when($filters['campus_id'], function ($query, $campusId) {
                return $query->where('campus_id', $campusId);
            })->when($filters['office_id'], function ($query, $officeId) {
                return $query->where('office_id', $officeId);
            })->when($filters['status_id'], function ($query, $statusId) {
                return $query->where('status_id', $statusId);
            })->when($filters['type_id'], function ($query, $typeId) {
                return $query->where('type_id', $typeId);
            })->get();

            // Add employees to the recipients collection with unique phone numbers
            $employees->each(function ($employee) use ($recipients, $messageLog) {
                $formattedNumber = $this->formatPhoneNumber($employee->emp_contact);

                if (!$recipients->contains('c_num', $formattedNumber)) {
                    $recipients->push([
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
                        'sent_status' => 'Pending', // Default to pending
                        'failure_reason' => '',     // Initially blank
                    ]);
                }
            });
        }

        // Initialize counters for sent and failed messages
        $sentCount = 0;
        $failedCount = 0;

        // Split recipients into batches
        $batches = $recipients->chunk($batchSize);

        foreach ($batches as $batch) {
            foreach ($batch as $recipient) {
                try {
                    // Send the SMS for each recipient individually
                    $moviderService->sendBulkSMS([$recipient['c_num']], $messageContent);

                    // Update the recipient's status to Sent
                    $recipient['sent_status'] = 'Sent';
                    $sentCount++; // Increment sent count
                } catch (Exception $e) {
                    // Log error and mark status as Failed
                    Log::error("Failed to send SMS for Message Log ID: {$messageLog->id}. Error: " . $e->getMessage());
                    $recipient['sent_status'] = 'Failed';
                    $recipient['failure_reason'] = $e->getMessage();
                    $failedCount++; // Increment failed count
                }

                // Log the recipient in the database
                MessageRecipient::create($recipient);
            }
        }

        // **Ensure the message_log sent_count and failed_count are updated properly**
        $messageLog->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ]);

        // Log the final sent and failed count
        Log::info("Message Log ID: {$messageLog->id} - Sent: {$sentCount}, Failed: {$failedCount}");
    }

    /**
     * Format phone numbers to the required format.
     */
    private function formatPhoneNumber($number)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/\D/', '', $number);

        // Get the last 10 digits
        $number = substr($number, -10);

        // Prepend +63 country code
        return '+63' . $number;
    }
}
