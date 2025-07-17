<?php

namespace App\Jobs;

use App\Models\Participant;
use App\Mail\TicketMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SendTicketEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The participant instance.
     */
    public Participant $participant;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 120;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        
        // Set queue connection and queue name
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load relationships to ensure they're available
            $this->participant->load(['workshop', 'ticketType']);

            // Check if participant still exists and is valid
            if (!$this->participant->exists) {
                Log::warning('Attempted to send ticket email to non-existent participant', [
                    'participant_id' => $this->participant->id
                ]);
                return;
            }

            // Check if workshop is still active
            $workshop = $this->participant->workshop;
            if ($workshop->status === 'cancelled') {
                Log::info('Skipping ticket email for cancelled workshop', [
                    'participant_id' => $this->participant->id,
                    'workshop_id' => $workshop->id,
                    'workshop_status' => $workshop->status
                ]);
                return;
            }

            // Send the email
            Mail::to($this->participant->email, $this->participant->name)
                ->send(new TicketMailable($this->participant));

            Log::info('Ticket email sent successfully', [
                'participant_id' => $this->participant->id,
                'participant_email' => $this->participant->email,
                'workshop_id' => $workshop->id,
                'workshop_name' => $workshop->name
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send ticket email', [
                'participant_id' => $this->participant->id,
                'participant_email' => $this->participant->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('SendTicketEmailJob failed permanently', [
            'participant_id' => $this->participant->id,
            'participant_email' => $this->participant->email,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // You could send a notification to administrators here
        // or add the failed email to a retry queue for manual processing
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'email',
            'ticket',
            'participant:' . $this->participant->id,
            'workshop:' . $this->participant->workshop_id
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
