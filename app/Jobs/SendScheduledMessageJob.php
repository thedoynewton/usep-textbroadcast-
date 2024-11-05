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
            // Refresh the message log instance to get the latest status from the database
            $this->messageLog->refresh();
    
            // Check if the message has been canceled
            if ($this->messageLog->status === 'cancelled') {
                Log::info("Scheduled message (ID: {$this->messageLog->id}) was canceled and will not be sent.");
                return; // Exit the job without sending
            }
    
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
        // Initialize a collection to store unique recipients by phone number
        $recipientsByPhone = collect();
        $recipientDetails = collect(); // To store all the recipients, even if they share the same phone number

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

            // Add students to the recipients collection
            $students->each(function ($student) use ($recipientsByPhone, $recipientDetails, $messageLog) {
                if (empty($student->stud_contact)) {
                    // Skip if contact number is empty
                    return;
                }
                
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
                    'sent_status' => 'pending',
                ]);
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

            // Add employees to the recipients collection
            $employees->each(function ($employee) use ($recipientsByPhone, $recipientDetails, $messageLog) {
                if (empty($employee->emp_contact)) {
                    // Skip if contact number is empty
                    return;
                }
                
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
                    'sent_status' => 'pending',
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
                    $moviderService->sendBulkSMS([$recipient['c_num']], $messageContent);
    
                    // Update sent status after successful sending
                    $recipient['sent_status'] = 'Sent'; // Mark as sent
                    $sentCount++; // Increment sent count
                } catch (Exception $e) {
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
