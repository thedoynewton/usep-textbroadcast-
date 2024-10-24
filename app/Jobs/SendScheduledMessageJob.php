<?php

namespace App\Jobs;

use App\Http\Controllers\MessagesController;
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

    protected $messageLog;
    protected $recipientType;
    protected $messageContent;
    protected $filters; // Other filters (campus, program, etc.)
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
            // Call the method that processes sending the message
            (new MessagesController($moviderService))->processImmediateSending(
                $this->messageLog,
                $this->recipientType,
                $this->messageContent,
                $this->filters['campus_id'],
                $this->filters['college_id'],
                $this->filters['program_id'],
                $this->filters['major_id'],
                $this->filters['year_id'],
                $this->filters['office_id'],
                $this->filters['status_id'],
                $this->filters['type_id'],
                $this->batchSize
            );

            // After sending, update the message_log status to 'Sent'
            $this->messageLog->update([
                'status' => 'Sent',
                'sent_at' => now(), // Record the exact time the message was sent
            ]);

        } catch (\Exception $e) {
            // Log the error for troubleshooting
            Log::error('Failed to send scheduled message: ' . $e->getMessage());

            // Optionally, update the message log with a failure status
            $this->messageLog->update([
                'status' => 'Failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
