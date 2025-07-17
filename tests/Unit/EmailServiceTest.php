<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EmailService;
use App\Services\QrCodeService;
use App\Models\Participant;
use App\Models\Workshop;
use App\Models\User;
use App\Models\TicketType;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class EmailServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmailService $emailService;
    protected QrCodeService $qrCodeService;
    protected Workshop $workshop;
    protected TicketType $ticketType;
    protected Participant $participant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock QrCodeService
        $this->qrCodeService = Mockery::mock(QrCodeService::class);
        $this->emailService = new EmailService($this->qrCodeService);
        
        $this->user = User::factory()->create();
        $this->workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        $this->ticketType = TicketType::factory()->create(['workshop_id' => $this->workshop->id]);
        $this->participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        Mail::fake();
        Queue::fake();
    }

    public function test_send_ticket_email_with_queue()
    {
        $result = $this->emailService->sendTicketEmail($this->participant, true);

        $this->assertTrue($result);
        Queue::assertPushed(\App\Jobs\SendTicketEmailJob::class);
    }

    public function test_send_ticket_email_immediately()
    {
        $result = $this->emailService->sendTicketEmailNow($this->participant);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\TicketMailable::class, function ($mail) {
            return $mail->participant->id === $this->participant->id;
        });
    }

    public function test_send_bulk_emails_with_queue()
    {
        $participants = Collection::make([$this->participant]);
        
        $result = $this->emailService->sendBulkEmails($participants, 'invite', true);

        $this->assertTrue($result);
        Queue::assertPushed(\App\Jobs\SendBulkEmailsJob::class);
    }

    public function test_send_bulk_emails_immediately()
    {
        $participant2 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);
        $participants = Collection::make([$this->participant, $participant2]);
        
        $result = $this->emailService->sendBulkEmailsNow($participants, 'invite');

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\WorkshopNotificationMailable::class, 2);
    }

    public function test_send_email_by_type_ticket()
    {
        $result = $this->emailService->sendEmailByType($this->participant, 'ticket', false);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\TicketMailable::class);
    }

    public function test_send_email_by_type_invite()
    {
        $result = $this->emailService->sendEmailByType($this->participant, 'invite', false);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\WorkshopNotificationMailable::class, function ($mail) {
            return $mail->templateType === 'invite';
        });
    }

    public function test_send_email_by_type_invalid_type()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid email template type: invalid_type');

        $this->emailService->sendEmailByType($this->participant, 'invalid_type', false);
    }

    public function test_prepare_template_variables()
    {
        $variables = $this->emailService->prepareTemplateVariables($this->participant);

        $this->assertIsArray($variables);
        $this->assertArrayHasKey('name', $variables);
        $this->assertArrayHasKey('email', $variables);
        $this->assertArrayHasKey('ticket_code', $variables);
        $this->assertArrayHasKey('workshop_name', $variables);
        $this->assertArrayHasKey('ticket_type_name', $variables);
        $this->assertArrayHasKey('app_name', $variables);
        
        $this->assertEquals($this->participant->name, $variables['name']);
        $this->assertEquals($this->participant->email, $variables['email']);
        $this->assertEquals($this->participant->ticket_code, $variables['ticket_code']);
        $this->assertEquals($this->workshop->name, $variables['workshop_name']);
        $this->assertEquals($this->ticketType->name, $variables['ticket_type_name']);
    }

    public function test_render_template()
    {
        $template = EmailTemplate::factory()->create([
            'workshop_id' => $this->workshop->id,
            'type' => 'ticket',
            'subject' => 'Hello {{ name }}',
            'content' => 'Your ticket code is {{ ticket_code }}',
        ]);

        $variables = ['name' => 'John Doe', 'ticket_code' => 'WS-12345678'];
        
        $rendered = $this->emailService->renderTemplate($template, $variables);

        $this->assertIsArray($rendered);
        $this->assertArrayHasKey('subject', $rendered);
        $this->assertArrayHasKey('content', $rendered);
        $this->assertEquals('Hello John Doe', $rendered['subject']);
        $this->assertEquals('Your ticket code is WS-12345678', $rendered['content']);
    }

    public function test_get_email_template()
    {
        $template = EmailTemplate::factory()->create([
            'workshop_id' => $this->workshop->id,
            'type' => 'ticket',
        ]);

        $foundTemplate = $this->emailService->getEmailTemplate($this->workshop, 'ticket');

        $this->assertInstanceOf(EmailTemplate::class, $foundTemplate);
        $this->assertEquals($template->id, $foundTemplate->id);
    }

    public function test_get_email_template_not_found()
    {
        $foundTemplate = $this->emailService->getEmailTemplate($this->workshop, 'nonexistent');

        $this->assertNull($foundTemplate);
    }

    public function test_get_or_create_email_template_existing()
    {
        $template = EmailTemplate::factory()->create([
            'workshop_id' => $this->workshop->id,
            'type' => 'ticket',
        ]);

        $foundTemplate = $this->emailService->getOrCreateEmailTemplate($this->workshop, 'ticket');

        $this->assertEquals($template->id, $foundTemplate->id);
    }

    public function test_get_or_create_email_template_create_new()
    {
        $template = $this->emailService->getOrCreateEmailTemplate($this->workshop, 'invite');

        $this->assertInstanceOf(EmailTemplate::class, $template);
        $this->assertEquals('invite', $template->type);
        $this->assertEquals($this->workshop->id, $template->workshop_id);
        $this->assertDatabaseHas('email_templates', [
            'workshop_id' => $this->workshop->id,
            'type' => 'invite',
        ]);
    }

    public function test_create_default_email_template()
    {
        $template = $this->emailService->createDefaultEmailTemplate($this->workshop, 'ticket');

        $this->assertInstanceOf(EmailTemplate::class, $template);
        $this->assertEquals('ticket', $template->type);
        $this->assertEquals($this->workshop->id, $template->workshop_id);
        $this->assertStringContains('{{ workshop_name }}', $template->subject);
        $this->assertStringContains('{{ name }}', $template->content);
        $this->assertStringContains('{{ ticket_code }}', $template->content);
    }

    public function test_test_email_configuration()
    {
        $result = $this->emailService->testEmailConfiguration('test@example.com');

        $this->assertTrue($result);
        Mail::assertSent(function ($mail) {
            return $mail->hasTo('test@example.com') && 
                   str_contains($mail->subject, 'Test Email');
        });
    }

    public function test_get_email_stats()
    {
        $stats = $this->emailService->getEmailStats($this->workshop);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_sent', $stats);
        $this->assertArrayHasKey('total_failed', $stats);
        $this->assertArrayHasKey('pending_queue', $stats);
        $this->assertArrayHasKey('by_type', $stats);
        $this->assertIsArray($stats['by_type']);
    }

    public function test_resend_failed_emails()
    {
        $participant2 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
        ]);

        $result = $this->emailService->resendFailedEmails(
            [$this->participant->id, $participant2->id],
            'ticket'
        );

        $this->assertTrue($result);
        Queue::assertPushed(\App\Jobs\SendBulkEmailsJob::class);
    }

    public function test_send_invite_email()
    {
        $result = $this->emailService->sendInviteEmail($this->participant, false);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\WorkshopNotificationMailable::class, function ($mail) {
            return $mail->templateType === 'invite';
        });
    }

    public function test_send_confirmation_email()
    {
        $result = $this->emailService->sendConfirmationEmail($this->participant, false);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\WorkshopNotificationMailable::class, function ($mail) {
            return $mail->templateType === 'confirm';
        });
    }

    public function test_send_reminder_email()
    {
        $result = $this->emailService->sendReminderEmail($this->participant, false);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\WorkshopNotificationMailable::class, function ($mail) {
            return $mail->templateType === 'reminder';
        });
    }

    public function test_send_thank_you_email()
    {
        $result = $this->emailService->sendThankYouEmail($this->participant, false);

        $this->assertTrue($result);
        Mail::assertSent(\App\Mail\WorkshopNotificationMailable::class, function ($mail) {
            return $mail->templateType === 'thank_you';
        });
    }

    public function test_send_email_with_template()
    {
        $template = EmailTemplate::factory()->create([
            'workshop_id' => $this->workshop->id,
            'type' => 'custom',
            'subject' => 'Custom {{ name }}',
            'content' => 'Hello {{ name }}, your code is {{ ticket_code }}',
        ]);

        $result = $this->emailService->sendEmailWithTemplate($this->participant, $template, false);

        $this->assertTrue($result);
        Queue::assertPushed(\App\Jobs\SendTemplateEmailJob::class);
    }

    public function test_format_workshop_date_range_same_day()
    {
        $workshop = Workshop::factory()->create([
            'created_by' => $this->user->id,
            'start_date' => '2024-12-01 09:00:00',
            'end_date' => '2024-12-01 17:00:00',
        ]);

        $variables = $this->emailService->prepareTemplateVariables(
            Participant::factory()->create([
                'workshop_id' => $workshop->id,
                'ticket_type_id' => $this->ticketType->id,
            ])
        );

        $this->assertEquals('December 1, 2024', $variables['workshop_date_range']);
    }

    public function test_format_workshop_date_range_multiple_days()
    {
        $workshop = Workshop::factory()->create([
            'created_by' => $this->user->id,
            'start_date' => '2024-12-01 09:00:00',
            'end_date' => '2024-12-03 17:00:00',
        ]);

        $variables = $this->emailService->prepareTemplateVariables(
            Participant::factory()->create([
                'workshop_id' => $workshop->id,
                'ticket_type_id' => $this->ticketType->id,
            ])
        );

        $this->assertEquals('December 1, 2024 - December 3, 2024', $variables['workshop_date_range']);
    }

    public function test_bulk_email_with_partial_failures()
    {
        $participant2 = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'email' => 'invalid-email', // This might cause issues
        ]);
        
        $participants = Collection::make([$this->participant, $participant2]);
        
        // The service should handle partial failures gracefully
        $result = $this->emailService->sendBulkEmailsNow($participants, 'invite');

        // Even with potential failures, the method should complete
        $this->assertIsBool($result);
    }

    public function test_create_default_templates_for_all_types()
    {
        $types = ['ticket', 'invite', 'confirm', 'reminder', 'thank_you'];
        
        foreach ($types as $type) {
            $template = $this->emailService->createDefaultEmailTemplate($this->workshop, $type);
            
            $this->assertInstanceOf(EmailTemplate::class, $template);
            $this->assertEquals($type, $template->type);
            $this->assertEquals($this->workshop->id, $template->workshop_id);
            $this->assertNotEmpty($template->subject);
            $this->assertNotEmpty($template->content);
        }
    }

    public function test_prepare_template_variables_with_null_values()
    {
        $participant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'phone' => null,
            'company' => null,
            'position' => null,
        ]);

        $variables = $this->emailService->prepareTemplateVariables($participant);

        $this->assertEquals('', $variables['phone']);
        $this->assertEquals('', $variables['company']);
        $this->assertEquals('', $variables['position']);
    }

    public function test_prepare_template_variables_payment_status()
    {
        $paidParticipant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => true,
        ]);

        $unpaidParticipant = Participant::factory()->create([
            'workshop_id' => $this->workshop->id,
            'ticket_type_id' => $this->ticketType->id,
            'is_paid' => false,
        ]);

        $paidVariables = $this->emailService->prepareTemplateVariables($paidParticipant);
        $unpaidVariables = $this->emailService->prepareTemplateVariables($unpaidParticipant);

        $this->assertEquals('Yes', $paidVariables['is_paid']);
        $this->assertEquals('Paid', $paidVariables['payment_status']);
        $this->assertEquals('No', $unpaidVariables['is_paid']);
        $this->assertEquals('Unpaid', $unpaidVariables['payment_status']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
