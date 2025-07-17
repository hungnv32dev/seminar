<?php

namespace App\Jobs;

use App\Models\Participant;
use App\Mail\TicketMailable;
use App\Mail\WorkshopNotificationMailable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SendBulkEmailsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The participants collection.
     */
    public Collection $participants;

    /**
     * The email template type.
     */
    public string $templateType;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300; // 5 minutes

    /**
     * The batch size for processing participants.
     */
    protected int $batchSize = 50;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [2, 5, 10];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $participants, string $templateType)
    {
        $this->participants = $participants;
        $this->templateType = $templateType;
        
        // Set queue connection and queue name
        $this->onQueue('bulk-emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $totalParticipants = $this->participants->count();
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        Log::info('Starting bulk email sending', [
            'template_type' => $this->templateType,
            'total_participants' => $totalParticipants,
            'batch_size' => $this->batchSize
        ]);

        try {
            // Process participants in batches to avoid memory issues
            $this->participants->chunk($this->batchSize)->each(function ($batch) use (&$successCount, &$errorCount, &$errors) {
                foreach ($batch as $participant) {
                    try {
                        // Ensure participant still exists and is valid
                        if (!$participant->exists) {
                            Log::warning('Skipping non-existent participant in bulk email', [
                                'participant_id' => $participant->id
                            ]);
                            continue;
                        }

                        // Load relationships
                        $participant->load(['workshop', 'ticketType']);

                        // Check if workshop is still active
                        if ($participant->workshop->status === 'cancelled') {
                            Log::info('Skipping email for cancelled workshop', [
                                'participant_id' => $participant->id,
                                'workshop_id' => $participant->workshop->id
                            ]);
                            continue;
                        }

                        // Send email based on template type
                        $this->sendEmailByType($participant, $this->templateType);
                        
                        $successCount++;

                        // Add small delay to avoid overwhelming the mail server
                        usleep(100000); // 0.1 second

                    } catch (Exception $e) {
                        $errorCount++;
                        $errors[] = [
                            'participant_id' => $participant->id,
                            'email' => $participant->email,
                            'error' => $e->getMessage()
                        ];

                        Log::error('Failed to send bulk email to participant', [
                            'participant_id' => $participant->id,
                            'participant_email' => $participant->email,
                            'template_type' => $this->templateType,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Log progress for large batches
                if ($totalParticipants > 100) {
                    Log::info('Bulk email progress', [
                        'template_type' => $this->templateType,
                        'processed' => $successCount + $errorCount,
                        'total' => $totalParticipants,
                        'success' => $successCount,
                        'errors' => $errorCount
                    ]);
                }
            });

            Log::info('Bulk email sending completed', [
                'template_type' => $this->templateType,
                'total_participants' => $totalParticipants,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'success_rate' => $totalParticipants > 0 ? round(($successCount / $totalParticipants) * 100, 2) : 0
            ]);

            // Log errors if any
            if (!empty($errors)) {
                Log::warning('Bulk email errors summary', [
                    'template_type' => $this->templateType,
                    'errors' => $errors
                ]);
            }

        } catch (Exception $e) {
            Log::error('Bulk email job failed', [
                'template_type' => $this->templateType,
                'total_participants' => $totalParticipants,
                'processed_success' => $successCount,
                'processed_errors' => $errorCount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Send email by template type.
     */
    protected function sendEmailByType(Participant $participant, string $templateType): void
    {
        switch ($templateType) {
            case 'ticket':
                Mail::to($participant->email, $participant->name)
                    ->send(new TicketMailable($participant));
                break;

            case 'invite':
            case 'confirm':
            case 'reminder':
            case 'thank_you':
                Mail::to($participant->email, $participant->name)
                    ->send(new WorkshopNotificationMailable($participant, $templateType));
                break;

            default:
                throw new Exception("Invalid email template type: {$templateType}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('SendBulkEmailsJob failed permanently', [
            'template_type' => $this->templateType,
            'total_participants' => $this->participants->count(),
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // You could send a notification to administrators here
        // or create a failed bulk email record for manual retry
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        $workshopIds = $this->participants->pluck('workshop_id')->unique()->toArray();
        
        $tags = [
            'bulk-email',
            'template:' . $this->templateType,
            'count:' . $this->participants->count()
        ];

        // Add workshop tags
        foreach ($workshopIds as $workshopId) {
            $tags[] = 'workshop:' . $workshopId;
        }

        return $tags;
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(30);
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            // You could add rate limiting middleware here
            // new \Illuminate\Queue\Middleware\RateLimited('bulk-emails')
        ];
    }
}
