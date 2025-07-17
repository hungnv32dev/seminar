<?php

namespace App\Jobs;

use App\Models\Participant;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTemplateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The participant instance.
     */
    public Participant $participant;

    /**
     * The email template instance.
     */
    public EmailTemplate $template;

    /**
     * The rendered email content.
     */
    public array $renderedContent;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Participant $participant, EmailTemplate $template, array $renderedContent)
    {
        $this->participant = $participant;
        $this->template = $template;
        $this->renderedContent = $renderedContent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send([], [], function ($message) {
                $message->to($this->participant->email, $this->participant->name)
                    ->subject($this->renderedContent['subject'])
                    ->html($this->renderedContent['content']);
            });

            Log::info('Template email sent successfully via job', [
                'participant_id' => $this->participant->id,
                'participant_email' => $this->participant->email,
                'template_id' => $this->template->id,
                'template_type' => $this->template->type,
                'subject' => $this->renderedContent['subject']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send template email via job', [
                'participant_id' => $this->participant->id,
                'template_id' => $this->template->id,
                'template_type' => $this->template->type,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Template email job failed permanently', [
            'participant_id' => $this->participant->id,
            'template_id' => $this->template->id,
            'template_type' => $this->template->type,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}