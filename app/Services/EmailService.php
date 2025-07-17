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
            // Load relationships to ensure they're available
            $participant->load(['workshop', 'ticketType']);

            // Use the TicketMailable class which handles template integration
            Mail::to($participant->email, $participant->name)
                ->send(new \App\Mail\TicketMailable($participant));

            Log::info('Ticket email sent successfully', [
                'participant_id' => $participant->id,
                'participant_email' => $participant->email,
                'workshop_id' => $participant->workshop_id
            ]);

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
            // Load relationships to ensure they're available
            $participant->load(['workshop', 'ticketType']);

            // Use the WorkshopNotificationMailable class which handles template integration
            Mail::to($participant->email, $participant->name)
                ->send(new \App\Mail\WorkshopNotificationMailable($participant, $templateType));

            Log::info("Generic email sent successfully", [
                'participant_id' => $participant->id,
                'participant_email' => $participant->email,
                'template_type' => $templateType,
                'workshop_id' => $participant->workshop_id
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send {$templateType} email", [
                'participant_id' => $participant->id,
                'template_type' => $templateType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }    /**
 
    * Render email template with variables (delegated to EmailTemplate model).
     */
    public function renderTemplate(EmailTemplate $template, array $variables = []): array
    {
        return $template->render($variables);
    }

    /**
     * Prepare template variables for a participant.
     */
    public function prepareTemplateVariables(Participant $participant): array
    {
        $workshop = $participant->workshop;
        $ticketType = $participant->ticketType;

        return [
            // Participant Information
            'name' => $participant->name ?? '',
            'email' => $participant->email ?? '',
            'phone' => $participant->phone ?? '',
            'company' => $participant->company ?? '',
            'position' => $participant->position ?? '',
            'occupation' => $participant->occupation ?? '',
            'address' => $participant->address ?? '',
            
            // Ticket Information
            'ticket_code' => $participant->ticket_code ?? '',
            'qr_code_url' => route('qr-code.show', ['ticket_code' => $participant->ticket_code]),
            'is_paid' => $participant->is_paid ? 'Yes' : 'No',
            'payment_status' => $participant->is_paid ? 'Paid' : 'Unpaid',
            'check_in_status' => $participant->is_checked_in ? 'Checked In' : 'Not Checked In',
            'registration_date' => $participant->created_at ? $participant->created_at->format('F j, Y') : '',
            
            // Workshop Information
            'workshop_name' => $workshop->name ?? '',
            'workshop_description' => $workshop->description ?? '',
            'workshop_location' => $workshop->location ?? '',
            'workshop_start_date' => $workshop->start_date ? $workshop->start_date->format('Y-m-d H:i:s') : '',
            'workshop_end_date' => $workshop->end_date ? $workshop->end_date->format('Y-m-d H:i:s') : '',
            'workshop_start_date_formatted' => $workshop->start_date ? $workshop->start_date->format('F j, Y \a\t g:i A') : '',
            'workshop_end_date_formatted' => $workshop->end_date ? $workshop->end_date->format('F j, Y \a\t g:i A') : '',
            'workshop_date_range' => $this->formatWorkshopDateRange($workshop),
            
            // Ticket Type Information
            'ticket_type_name' => $ticketType->name ?? '',
            'ticket_type_price' => $ticketType ? number_format($ticketType->price, 2) : '0.00',
            'ticket_type_price_formatted' => $ticketType ? '$' . number_format($ticketType->price, 2) : '$0.00',
            
            // System Information
            'app_name' => config('app.name', 'Workshop Management System'),
            'app_url' => config('app.url', 'http://localhost'),
            'days_until_workshop' => $workshop->start_date ? now()->diffInDays($workshop->start_date, false) : 0,
        ];
    }

    /**
     * Format workshop date range.
     */
    protected function formatWorkshopDateRange($workshop): string
    {
        if (!$workshop->start_date) {
            return '';
        }

        $startDate = $workshop->start_date->format('F j, Y');
        
        if (!$workshop->end_date || $workshop->start_date->format('Y-m-d') === $workshop->end_date->format('Y-m-d')) {
            return $startDate;
        }
        
        return $startDate . ' - ' . $workshop->end_date->format('F j, Y');
    }

    /**
     * Get email template for workshop and type.
     */
    public function getEmailTemplate(Workshop $workshop, string $type): ?EmailTemplate
    {
        return $workshop->emailTemplates()
            ->where('type', $type)
            ->first();
    }

    /**
     * Get or create email template for workshop and type.
     */
    public function getOrCreateEmailTemplate(Workshop $workshop, string $type): EmailTemplate
    {
        $template = $this->getEmailTemplate($workshop, $type);
        
        if (!$template) {
            $template = $this->createDefaultEmailTemplate($workshop, $type);
        }
        
        return $template;
    }

    /**
     * Send email using specific template.
     */
    public function sendEmailWithTemplate(Participant $participant, EmailTemplate $template, bool $useQueue = true): bool
    {
        try {
            $variables = $this->prepareTemplateVariables($participant);
            $renderedContent = $template->render($variables);
            
            if ($useQueue) {
                // Create a custom job for template-based emails
                \App\Jobs\SendTemplateEmailJob::dispatch($participant, $template, $renderedContent);
                return true;
            } else {
                return $this->sendTemplateEmailNow($participant, $renderedContent);
            }
        } catch (Exception $e) {
            Log::error('Failed to send template email', [
                'participant_id' => $participant->id,
                'template_id' => $template->id,
                'template_type' => $template->type,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send template email immediately.
     */
    protected function sendTemplateEmailNow(Participant $participant, array $renderedContent): bool
    {
        try {
            Mail::send([], [], function ($message) use ($participant, $renderedContent) {
                $message->to($participant->email, $participant->name)
                    ->subject($renderedContent['subject'])
                    ->html($renderedContent['content']);
            });

            Log::info('Template email sent successfully', [
                'participant_id' => $participant->id,
                'participant_email' => $participant->email,
                'subject' => $renderedContent['subject']
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send template email immediately', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }   
 /**
     * Create default email template for a workshop and type.
     */
    public function createDefaultEmailTemplate(Workshop $workshop, string $type): EmailTemplate
    {
        $templates = [
            'ticket' => [
                'subject' => 'Your Ticket for {{ workshop_name }}',
                'content' => '
                    <h2>Workshop Ticket</h2>
                    <p>Dear {{ name }},</p>
                    <p>Thank you for registering for <strong>{{ workshop_name }}</strong>.</p>
                    
                    <h3>Workshop Details:</h3>
                    <ul>
                        <li><strong>Date:</strong> {{ workshop_start_date_formatted }}</li>
                        <li><strong>Location:</strong> {{ workshop_location }}</li>
                        <li><strong>Ticket Type:</strong> {{ ticket_type_name }}</li>
                        <li><strong>Price:</strong> {{ ticket_type_price_formatted }}</li>
                    </ul>
                    
                    <h3>Your Ticket Information:</h3>
                    <ul>
                        <li><strong>Ticket Code:</strong> {{ ticket_code }}</li>
                        <li><strong>Payment Status:</strong> {{ payment_status }}</li>
                    </ul>
                    
                    <p>Please bring this email or show your QR code at the workshop entrance for check-in.</p>
                    
                    <p>We look forward to seeing you at the workshop!</p>
                    
                    <p>Best regards,<br>{{ app_name }} Team</p>
                '
            ],
            'invite' => [
                'subject' => 'You\'re Invited to {{ workshop_name }}',
                'content' => '
                    <h2>Workshop Invitation</h2>
                    <p>Dear {{ name }},</p>
                    <p>You have been invited to participate in <strong>{{ workshop_name }}</strong>.</p>
                    <p>We would love to have you join us for this exciting workshop!</p>
                    
                    <h3>Workshop Details:</h3>
                    <ul>
                        <li><strong>Workshop:</strong> {{ workshop_name }}</li>
                        <li><strong>Date:</strong> {{ workshop_date_range }}</li>
                        <li><strong>Location:</strong> {{ workshop_location }}</li>
                    </ul>
                    
                    <p>Best regards,<br>{{ app_name }} Team</p>
                '
            ],
            'confirm' => [
                'subject' => 'Registration Confirmed for {{ workshop_name }}',
                'content' => '
                    <h2>Registration Confirmed</h2>
                    <p>Dear {{ name }},</p>
                    <p>Your registration for <strong>{{ workshop_name }}</strong> has been confirmed.</p>
                    <p>Thank you for registering! We look forward to seeing you at the workshop.</p>
                    
                    <h3>Workshop Details:</h3>
                    <ul>
                        <li><strong>Workshop:</strong> {{ workshop_name }}</li>
                        <li><strong>Date:</strong> {{ workshop_date_range }}</li>
                        <li><strong>Location:</strong> {{ workshop_location }}</li>
                        <li><strong>Ticket Type:</strong> {{ ticket_type_name }}</li>
                    </ul>
                    
                    <p>Best regards,<br>{{ app_name }} Team</p>
                '
            ],
            'reminder' => [
                'subject' => 'Reminder: {{ workshop_name }} is Coming Up',
                'content' => '
                    <h2>Workshop Reminder</h2>
                    <p>Dear {{ name }},</p>
                    <p>This is a friendly reminder that <strong>{{ workshop_name }}</strong> is coming up soon.</p>
                    <p>Don\'t forget to mark your calendar and prepare for the workshop!</p>
                    
                    <h3>Workshop Details:</h3>
                    <ul>
                        <li><strong>Workshop:</strong> {{ workshop_name }}</li>
                        <li><strong>Date:</strong> {{ workshop_date_range }}</li>
                        <li><strong>Location:</strong> {{ workshop_location }}</li>
                        <li><strong>Your Ticket:</strong> {{ ticket_code }}</li>
                    </ul>
                    
                    <p>Best regards,<br>{{ app_name }} Team</p>
                '
            ],
            'thank_you' => [
                'subject' => 'Thank You for Attending {{ workshop_name }}',
                'content' => '
                    <h2>Thank You!</h2>
                    <p>Dear {{ name }},</p>
                    <p>Thank you for attending <strong>{{ workshop_name }}</strong>.</p>
                    <p>We hope you found the workshop valuable and informative. We appreciate your participation!</p>
                    
                    <p>If you have any feedback or questions, please don\'t hesitate to contact us.</p>
                    
                    <p>Best regards,<br>{{ app_name }} Team</p>
                '
            ]
        ];

        $templateData = $templates[$type] ?? $templates['ticket'];

        return $workshop->emailTemplates()->create([
            'type' => $type,
            'subject' => $templateData['subject'],
            'content' => $templateData['content'],
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