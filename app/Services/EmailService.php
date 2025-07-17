<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\EmailTemplate;
use App\Models\Workshop;
use App\Jobs\SendTicketEmailJob;
use App\Jobs\SendBulkEmailsJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Send ticket email to a participant.
     */
    public function sendTicketEmail(Participant $participant, bool $useQueue = true): bool
    {
        try {
            if ($useQueue) {
                SendTicketEmailJob::dispatch($participant);
                return true;
            } else {
                return $this->sendTicketEmailNow($participant);
            }
        } catch (Exception $e) {
            Log::error('Failed to send ticket email', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send ticket email immediately (without queue).
     */
    public function sendTicketEmailNow(Participant $participant): bool
    {
        try {
            $workshop = $participant->workshop;
            $template = $this->getEmailTemplate($workshop, 'ticket');
            
            if (!$template) {
                $template = $this->createDefaultTicketTemplate($workshop);
            }

            $variables = $this->prepareEmailVariables($participant);
            $renderedTemplate = $this->renderTemplate($template, $variables);

            // Generate QR code data URL for email
            $qrCodeDataUrl = $this->qrCodeService->generateQrCodeDataUrl($participant->ticket_code);

            Mail::send([], [], function ($message) use ($participant, $renderedTemplate, $qrCodeDataUrl) {
                $message->to($participant->email, $participant->name)
                    ->subject($renderedTemplate['subject'])
                    ->html($renderedTemplate['content'])
                    ->attachData(
                        base64_decode(str_replace('data:image/png;base64,', '', $qrCodeDataUrl)),
                        'ticket-' . $participant->ticket_code . '.png',
                        ['mime' => 'image/png']
                    );
            });

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send ticket email immediately', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send bulk emails to multiple participants.
     */
    public function sendBulkEmails(Collection $participants, string $templateType, bool $useQueue = true): bool
    {
        try {
            if ($useQueue) {
                SendBulkEmailsJob::dispatch($participants, $templateType);
                return true;
            } else {
                return $this->sendBulkEmailsNow($participants, $templateType);
            }
        } catch (Exception $e) {
            Log::error('Failed to send bulk emails', [
                'participant_count' => $participants->count(),
                'template_type' => $templateType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send bulk emails immediately (without queue).
     */
    public function sendBulkEmailsNow(Collection $participants, string $templateType): bool
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($participants as $participant) {
            try {
                $this->sendEmailByType($participant, $templateType, false);
                $successCount++;
            } catch (Exception $e) {
                $errorCount++;
                $errors[] = [
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                    'error' => $e->getMessage()
                ];
                
                Log::error('Failed to send email to participant', [
                    'participant_id' => $participant->id,
                    'template_type' => $templateType,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Bulk email sending completed', [
            'total' => $participants->count(),
            'success' => $successCount,
            'errors' => $errorCount,
            'template_type' => $templateType
        ]);

        return $errorCount === 0;
    }

    /**
     * Send email by template type.
     */
    public function sendEmailByType(Participant $participant, string $templateType, bool $useQueue = true): bool
    {
        switch ($templateType) {
            case 'ticket':
                return $this->sendTicketEmail($participant, $useQueue);
            case 'invite':
                return $this->sendInviteEmail($participant, $useQueue);
            case 'confirm':
                return $this->sendConfirmationEmail($participant, $useQueue);
            case 'reminder':
                return $this->sendReminderEmail($participant, $useQueue);
            case 'thank_you':
                return $this->sendThankYouEmail($participant, $useQueue);
            default:
                throw new Exception('Invalid email template type: ' . $templateType);
        }
    }

    /**
     * Send invitation email.
     */
    public function sendInviteEmail(Participant $participant, bool $useQueue = true): bool
    {
        return $this->sendGenericEmail($participant, 'invite', $useQueue);
    }

    /**
     * Send confirmation email.
     */
    public function sendConfirmationEmail(Participant $participant, bool $useQueue = true): bool
    {
        return $this->sendGenericEmail($participant, 'confirm', $useQueue);
    }

    /**
     * Send reminder email.
     */
    public function sendReminderEmail(Participant $participant, bool $useQueue = true): bool
    {
        return $this->sendGenericEmail($participant, 'reminder', $useQueue);
    }

    /**
     * Send thank you email.
     */
    public function sendThankYouEmail(Participant $participant, bool $useQueue = true): bool
    {
        return $this->sendGenericEmail($participant, 'thank_you', $useQueue);
    }

    /**
     * Send generic email by template type.
     */
    protected function sendGenericEmail(Participant $participant, string $templateType, bool $useQueue = true): bool
    {
        try {
            $workshop = $participant->workshop;
            $template = $this->getEmailTemplate($workshop, $templateType);
            
            if (!$template) {
                throw new Exception("No email template found for type: {$templateType}");
            }

            $variables = $this->prepareEmailVariables($participant);
            $renderedTemplate = $this->renderTemplate($template, $variables);

            Mail::send([], [], function ($message) use ($participant, $renderedTemplate) {
                $message->to($participant->email, $participant->name)
                    ->subject($renderedTemplate['subject'])
                    ->html($renderedTemplate['content']);
            });

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send {$templateType} email", [
                'participant_id' => $participant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Render email template with variables.
     */
    public function renderTemplate(EmailTemplate $template, array $variables = []): array
    {
        return $template->render($variables);
    }

    /**
     * Prepare email variables for a participant.
     */
    public function prepareEmailVariables(Participant $participant): array
    {
        $workshop = $participant->workshop;
        $ticketType = $participant->ticketType;

        return [
            'name' => $participant->name,
            'email' => $participant->email,
            'phone' => $participant->phone,
            'company' => $participant->company,
            'position' => $participant->position,
            'ticket_code' => $participant->ticket_code,
            'qr_code_url' => $this->qrCodeService->generateQrCodeUrl($participant->ticket_code),
            'workshop_name' => $workshop->name,
            'workshop_description' => $workshop->description,
            'workshop_location' => $workshop->location,
            'workshop_start_date' => $workshop->start_date->format('Y-m-d H:i:s'),
            'workshop_end_date' => $workshop->end_date->format('Y-m-d H:i:s'),
            'workshop_start_date_formatted' => $workshop->start_date->format('F j, Y \a\t g:i A'),
            'workshop_end_date_formatted' => $workshop->end_date->format('F j, Y \a\t g:i A'),
            'ticket_type_name' => $ticketType->name,
            'ticket_type_price' => number_format($ticketType->price, 2),
            'is_paid' => $participant->is_paid ? 'Yes' : 'No',
            'payment_status' => $participant->is_paid ? 'Paid' : 'Unpaid',
        ];
    }

    /**
     * Get email template for workshop and type.
     */
    protected function getEmailTemplate(Workshop $workshop, string $type): ?EmailTemplate
    {
        return $workshop->emailTemplates()
            ->where('type', $type)
            ->first();
    }

    /**
     * Create default ticket email template.
     */
    protected function createDefaultTicketTemplate(Workshop $workshop): EmailTemplate
    {
        $subject = 'Your Ticket for {{ workshop_name }}';
        $content = '
            <h2>Workshop Ticket</h2>
            <p>Dear {{ name }},</p>
            <p>Thank you for registering for <strong>{{ workshop_name }}</strong>.</p>
            
            <h3>Workshop Details:</h3>
            <ul>
                <li><strong>Date:</strong> {{ workshop_start_date_formatted }}</li>
                <li><strong>Location:</strong> {{ workshop_location }}</li>
                <li><strong>Ticket Type:</strong> {{ ticket_type_name }}</li>
                <li><strong>Price:</strong> ${{ ticket_type_price }}</li>
            </ul>
            
            <h3>Your Ticket Information:</h3>
            <ul>
                <li><strong>Ticket Code:</strong> {{ ticket_code }}</li>
                <li><strong>Payment Status:</strong> {{ payment_status }}</li>
            </ul>
            
            <p>Please bring this email or show your QR code at the workshop entrance for check-in.</p>
            
            <p>We look forward to seeing you at the workshop!</p>
            
            <p>Best regards,<br>Workshop Management Team</p>
        ';

        return $workshop->emailTemplates()->create([
            'type' => 'ticket',
            'subject' => $subject,
            'content' => $content,
        ]);
    }

    /**
     * Test email configuration.
     */
    public function testEmailConfiguration(string $testEmail): bool
    {
        try {
            Mail::raw('This is a test email from Workshop Management System.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('Test Email - Workshop Management System');
            });

            return true;
        } catch (Exception $e) {
            Log::error('Email configuration test failed', [
                'test_email' => $testEmail,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get email sending statistics.
     */
    public function getEmailStats(Workshop $workshop = null): array
    {
        // This would typically involve checking email logs or database records
        // For now, return basic structure
        return [
            'total_sent' => 0,
            'total_failed' => 0,
            'pending_queue' => Queue::size(),
            'by_type' => [
                'ticket' => 0,
                'invite' => 0,
                'confirm' => 0,
                'reminder' => 0,
                'thank_you' => 0,
            ],
        ];
    }

    /**
     * Resend failed emails.
     */
    public function resendFailedEmails(array $participantIds, string $templateType): bool
    {
        $participants = Participant::whereIn('id', $participantIds)->get();
        return $this->sendBulkEmails($participants, $templateType);
    }
}