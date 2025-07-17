<?php

namespace App\Mail;

use App\Models\Participant;
use App\Models\EmailTemplate;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TicketMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The participant instance.
     */
    public Participant $participant;

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
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
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
                'workshop-ticket',
                'workshop-' . $this->participant->workshop_id,
                'participant-' . $this->participant->id
            ],
            metadata: [
                'workshop_id' => $this->participant->workshop_id,
                'participant_id' => $this->participant->id,
                'ticket_code' => $this->participant->ticket_code,
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
        $attachments = [];

        try {
            // Generate QR code for the ticket
            $qrCodeService = app(QrCodeService::class);
            $qrCodeData = $qrCodeService->generateQrCode($this->participant->ticket_code, 300);

            // Create attachment from QR code data
            $attachments[] = Attachment::fromData(
                fn () => $qrCodeData,
                'ticket-' . $this->participant->ticket_code . '.png'
            )->withMime('image/png');

        } catch (\Exception $e) {
            // Log error but don't fail the email
            \Log::error('Failed to generate QR code attachment for ticket email', [
                'participant_id' => $this->participant->id,
                'ticket_code' => $this->participant->ticket_code,
                'error' => $e->getMessage()
            ]);
        }

        return $attachments;
    }

    /**
     * Prepare template variables for email rendering.
     */
    protected function prepareTemplateVariables(): void
    {
        $workshop = $this->participant->workshop;
        $ticketType = $this->participant->ticketType;

        $this->templateVariables = [
            'name' => $this->participant->name,
            'email' => $this->participant->email,
            'phone' => $this->participant->phone,
            'company' => $this->participant->company,
            'position' => $this->participant->position,
            'occupation' => $this->participant->occupation,
            'address' => $this->participant->address,
            'ticket_code' => $this->participant->ticket_code,
            'qr_code_url' => route('qr-code.show', ['ticket_code' => $this->participant->ticket_code]),
            'workshop_name' => $workshop->name,
            'workshop_description' => $workshop->description,
            'workshop_location' => $workshop->location,
            'workshop_start_date' => $workshop->start_date->format('Y-m-d H:i:s'),
            'workshop_end_date' => $workshop->end_date->format('Y-m-d H:i:s'),
            'workshop_start_date_formatted' => $workshop->start_date->format('F j, Y \a\t g:i A'),
            'workshop_end_date_formatted' => $workshop->end_date->format('F j, Y \a\t g:i A'),
            'workshop_date_range' => $workshop->start_date->format('F j, Y') . 
                ($workshop->start_date->format('Y-m-d') !== $workshop->end_date->format('Y-m-d') 
                    ? ' - ' . $workshop->end_date->format('F j, Y') 
                    : ''),
            'ticket_type_name' => $ticketType->name,
            'ticket_type_price' => number_format($ticketType->price, 2),
            'ticket_type_price_formatted' => '$' . number_format($ticketType->price, 2),
            'is_paid' => $this->participant->is_paid ? 'Yes' : 'No',
            'payment_status' => $this->participant->is_paid ? 'Paid' : 'Unpaid',
            'check_in_status' => $this->participant->is_checked_in ? 'Checked In' : 'Not Checked In',
            'registration_date' => $this->participant->created_at->format('F j, Y'),
            'app_name' => config('app.name', 'Workshop Management System'),
            'app_url' => config('app.url', 'http://localhost'),
        ];
    }

    /**
     * Render email content using template or default.
     */
    protected function renderEmailContent(): void
    {
        $workshop = $this->participant->workshop;
        
        // Try to get custom email template
        $template = $workshop->emailTemplates()
            ->where('type', 'ticket')
            ->first();

        if ($template) {
            // Use custom template
            $this->renderedContent = $template->render($this->templateVariables);
        } else {
            // Use default template
            $this->renderedContent = $this->getDefaultTemplate();
        }
    }

    /**
     * Get default email template.
     */
    protected function getDefaultTemplate(): array
    {
        $subject = 'Your Ticket for {{ workshop_name }}';
        
        $content = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Workshop Ticket</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
                .ticket-info { background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .workshop-details { background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 15px 0; }
                .important { background-color: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; }
                .qr-note { text-align: center; margin: 20px 0; padding: 15px; background-color: #d4edda; border-radius: 8px; }
                ul { padding-left: 20px; }
                li { margin-bottom: 8px; }
                h1, h2, h3 { color: #2c3e50; }
                .ticket-code { font-size: 24px; font-weight: bold; color: #007bff; text-align: center; padding: 10px; background-color: #f8f9fa; border-radius: 4px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🎫 Workshop Ticket</h1>
                <p>Thank you for registering!</p>
            </div>

            <p>Dear <strong>{{ name }}</strong>,</p>
            
            <p>Thank you for registering for <strong>{{ workshop_name }}</strong>. Your ticket has been confirmed!</p>
            
            <div class="ticket-info">
                <h3>🎟️ Your Ticket Information</h3>
                <div class="ticket-code">{{ ticket_code }}</div>
                <ul>
                    <li><strong>Ticket Type:</strong> {{ ticket_type_name }}</li>
                    <li><strong>Price:</strong> {{ ticket_type_price_formatted }}</li>
                    <li><strong>Payment Status:</strong> {{ payment_status }}</li>
                    <li><strong>Registration Date:</strong> {{ registration_date }}</li>
                </ul>
            </div>

            <div class="workshop-details">
                <h3>📅 Workshop Details</h3>
                <ul>
                    <li><strong>Workshop:</strong> {{ workshop_name }}</li>
                    <li><strong>Date:</strong> {{ workshop_date_range }}</li>
                    <li><strong>Time:</strong> {{ workshop_start_date_formatted }}</li>
                    <li><strong>Location:</strong> {{ workshop_location }}</li>
                </ul>
            </div>

            <div class="qr-note">
                <h3>📱 QR Code Attached</h3>
                <p>Your QR code is attached to this email. Please save it to your phone or print it out to bring to the workshop for quick check-in.</p>
            </div>

            <div class="important">
                <h3>⚠️ Important Notes</h3>
                <ul>
                    <li>Please arrive 15 minutes before the workshop starts</li>
                    <li>Bring this email or show your QR code for check-in</li>
                    <li>If you have any questions, please contact our support team</li>
                </ul>
            </div>

            <p>We look forward to seeing you at the workshop!</p>

            <div class="footer">
                <p><strong>{{ app_name }}</strong></p>
                <p>This is an automated email. Please do not reply to this message.</p>
            </div>
        </body>
        </html>';

        // Replace variables in both subject and content
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
