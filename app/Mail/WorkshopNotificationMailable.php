<?php

namespace App\Mail;

use App\Models\Participant;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopNotificationMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The participant instance.
     */
    public Participant $participant;

    /**
     * The email template type.
     */
    public string $templateType;

    /**
     * The email template variables.
     */
    public array $templateVariables;

    /**
     * The rendered email content.
     */
    public array $renderedContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Participant $participant, string $templateType)
    {
        $this->participant = $participant;
        $this->templateType = $templateType;
        $this->prepareTemplateVariables();
        $this->renderEmailContent();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->renderedContent['subject'],
            from: config('mail.from.address', 'hello@example.com'),
            replyTo: config('mail.from.address', 'hello@example.com'),
            tags: [
                'workshop-notification',
                'type-' . $this->templateType,
                'workshop-' . $this->participant->workshop_id,
                'participant-' . $this->participant->id
            ],
            metadata: [
                'template_type' => $this->templateType,
                'workshop_id' => $this->participant->workshop_id,
                'participant_id' => $this->participant->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->renderedContent['content'],
            with: [
                'participant' => $this->participant,
                'workshop' => $this->participant->workshop,
                'ticketType' => $this->participant->ticketType,
                'templateType' => $this->templateType,
                'variables' => $this->templateVariables,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Prepare template variables for email rendering.
     */
    protected function prepareTemplateVariables(): void
    {
        $emailService = app(\App\Services\EmailService::class);
        $this->templateVariables = $emailService->prepareTemplateVariables($this->participant);
    }

    /**
     * Render email content using template or default.
     */
    protected function renderEmailContent(): void
    {
        $workshop = $this->participant->workshop;
        
        // Try to get custom email template
        $template = $workshop->emailTemplates()
            ->where('type', $this->templateType)
            ->first();

        if ($template) {
            // Use custom template
            $this->renderedContent = $template->render($this->templateVariables);
        } else {
            // Use default template without creating database record
            $this->renderedContent = $this->getDefaultTemplate();
        }
    }

    /**
     * Get default email template based on type.
     */
    protected function getDefaultTemplate(): array
    {
        switch ($this->templateType) {
            case 'invite':
                return $this->getInviteTemplate();
            case 'confirm':
                return $this->getConfirmTemplate();
            case 'reminder':
                return $this->getReminderTemplate();
            case 'thank_you':
                return $this->getThankYouTemplate();
            default:
                return $this->getGenericTemplate();
        }
    }

    /**
     * Get invitation email template.
     */
    protected function getInviteTemplate(): array
    {
        $subject = 'You\'re Invited to {{ workshop_name }}';
        $content = $this->getBaseTemplate('🎉 Workshop Invitation', 
            'You have been invited to participate in <strong>{{ workshop_name }}</strong>.',
            'We would love to have you join us for this exciting workshop!'
        );

        return $this->renderTemplate($subject, $content);
    }

    /**
     * Get confirmation email template.
     */
    protected function getConfirmTemplate(): array
    {
        $subject = 'Registration Confirmed for {{ workshop_name }}';
        $content = $this->getBaseTemplate('✅ Registration Confirmed', 
            'Your registration for <strong>{{ workshop_name }}</strong> has been confirmed.',
            'Thank you for registering! We look forward to seeing you at the workshop.'
        );

        return $this->renderTemplate($subject, $content);
    }

    /**
     * Get reminder email template.
     */
    protected function getReminderTemplate(): array
    {
        $subject = 'Reminder: {{ workshop_name }} is Coming Up';
        $content = $this->getBaseTemplate('⏰ Workshop Reminder', 
            'This is a friendly reminder that <strong>{{ workshop_name }}</strong> is coming up soon.',
            'Don\'t forget to mark your calendar and prepare for the workshop!'
        );

        return $this->renderTemplate($subject, $content);
    }

    /**
     * Get thank you email template.
     */
    protected function getThankYouTemplate(): array
    {
        $subject = 'Thank You for Attending {{ workshop_name }}';
        $content = $this->getBaseTemplate('🙏 Thank You!', 
            'Thank you for attending <strong>{{ workshop_name }}</strong>.',
            'We hope you found the workshop valuable and informative. We appreciate your participation!'
        );

        return $this->renderTemplate($subject, $content);
    }

    /**
     * Get generic email template.
     */
    protected function getGenericTemplate(): array
    {
        $subject = 'Workshop Notification: {{ workshop_name }}';
        $content = $this->getBaseTemplate('📢 Workshop Notification', 
            'This is a notification regarding <strong>{{ workshop_name }}</strong>.',
            'Please check the workshop details below for more information.'
        );

        return $this->renderTemplate($subject, $content);
    }

    /**
     * Get base HTML template.
     */
    protected function getBaseTemplate(string $title, string $message, string $subtitle): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Workshop Notification</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
                .workshop-details { background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 15px 0; }
                .participant-info { background-color: #e3f2fd; padding: 15px; border-radius: 8px; margin: 15px 0; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
                ul { padding-left: 20px; }
                li { margin-bottom: 8px; }
                h1, h2, h3 { color: #2c3e50; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>' . $title . '</h1>
                <p>' . $subtitle . '</p>
            </div>

            <p>Dear <strong>{{ name }}</strong>,</p>
            
            <p>' . $message . '</p>
            
            <div class="workshop-details">
                <h3>📅 Workshop Details</h3>
                <ul>
                    <li><strong>Workshop:</strong> {{ workshop_name }}</li>
                    <li><strong>Date:</strong> {{ workshop_date_range }}</li>
                    <li><strong>Time:</strong> {{ workshop_start_date_formatted }}</li>
                    <li><strong>Location:</strong> {{ workshop_location }}</li>
                </ul>
            </div>

            <div class="participant-info">
                <h3>🎟️ Your Registration</h3>
                <ul>
                    <li><strong>Ticket Code:</strong> {{ ticket_code }}</li>
                    <li><strong>Ticket Type:</strong> {{ ticket_type_name }}</li>
                    <li><strong>Payment Status:</strong> {{ payment_status }}</li>
                </ul>
            </div>

            <p>If you have any questions, please don\'t hesitate to contact us.</p>

            <div class="footer">
                <p><strong>{{ app_name }}</strong></p>
                <p>This is an automated email. Please do not reply to this message.</p>
            </div>
        </body>
        </html>';
    }

    /**
     * Render template with variables.
     */
    protected function renderTemplate(string $subject, string $content): array
    {
        $renderedSubject = $subject;
        $renderedContent = $content;

        foreach ($this->templateVariables as $key => $value) {
            $placeholder = '{{ ' . $key . ' }}';
            $renderedSubject = str_replace($placeholder, $value, $renderedSubject);
            $renderedContent = str_replace($placeholder, $value, $renderedContent);
        }

        return [
            'subject' => $renderedSubject,
            'content' => $renderedContent,
        ];
    }

    /**
     * Build the message (for backward compatibility).
     */
    public function build()
    {
        return $this->subject($this->renderedContent['subject'])
                    ->html($this->renderedContent['content']);
    }
}
