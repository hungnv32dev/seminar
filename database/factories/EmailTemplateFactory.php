<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['ticket', 'invite', 'confirm', 'reminder', 'thank_you']);
        
        $templates = [
            'ticket' => [
                'subject' => 'Your Ticket for {{ workshop_name }}',
                'content' => "Dear {{ name }},\n\nThank you for registering for {{ workshop_name }}!\n\nYour ticket details:\n- Ticket Code: {{ ticket_code }}\n- Workshop: {{ workshop_name }}\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nPlease bring this email or show the QR code below for check-in:\n{{ qr_code_url }}\n\nWe look forward to seeing you!\n\nBest regards,\n{{ app_name }}"
            ],
            'invite' => [
                'subject' => 'You\'re Invited to {{ workshop_name }}',
                'content' => "Hello {{ name }},\n\nWe're excited to invite you to {{ workshop_name }}!\n\n{{ workshop_description }}\n\nWorkshop Details:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n- Duration: {{ workshop_date_range }}\n\nTo secure your spot, please register as soon as possible.\n\nBest regards,\n{{ app_name }}"
            ],
            'confirm' => [
                'subject' => 'Registration Confirmed for {{ workshop_name }}',
                'content' => "Dear {{ name }},\n\nYour registration for {{ workshop_name }} has been confirmed!\n\nRegistration Details:\n- Name: {{ name }}\n- Email: {{ email }}\n- Company: {{ company }}\n- Position: {{ position }}\n\nWorkshop Information:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nYou will receive your ticket with QR code shortly.\n\nThank you,\n{{ app_name }}"
            ],
            'reminder' => [
                'subject' => 'Reminder: {{ workshop_name }} is Tomorrow',
                'content' => "Hello {{ name }},\n\nThis is a friendly reminder that {{ workshop_name }} is scheduled for tomorrow!\n\nWorkshop Details:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n- Your Ticket Code: {{ ticket_code }}\n\nPlease arrive 15 minutes early for check-in. Don't forget to bring your ticket or have your QR code ready on your phone.\n\nSee you tomorrow!\n\n{{ app_name }}"
            ],
            'thank_you' => [
                'subject' => 'Thank You for Attending {{ workshop_name }}',
                'content' => "Dear {{ name }},\n\nThank you for attending {{ workshop_name }}! We hope you found it valuable and informative.\n\nWe'd love to hear your feedback about the workshop. Please take a moment to share your thoughts with us.\n\nStay tuned for upcoming workshops and events. We look forward to seeing you again!\n\nBest regards,\n{{ app_name }}"
            ]
        ];

        $selectedTemplate = $templates[$type] ?? $templates['ticket'];
        
        return [
            'type' => $type,
            'subject' => $selectedTemplate['subject'],
            'content' => $selectedTemplate['content'],
            'workshop_id' => \App\Models\Workshop::factory(),
        ];
    }

    /**
     * Create a ticket email template.
     */
    public function ticket(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'ticket',
            'subject' => 'Your Ticket for {{ workshop_name }}',
            'content' => "Dear {{ name }},\n\nThank you for registering for {{ workshop_name }}!\n\nYour ticket details:\n- Ticket Code: {{ ticket_code }}\n- Workshop: {{ workshop_name }}\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nPlease bring this email or show the QR code below for check-in:\n{{ qr_code_url }}\n\nWe look forward to seeing you!\n\nBest regards,\n{{ app_name }}"
        ]);
    }

    /**
     * Create an invitation email template.
     */
    public function invite(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'invite',
            'subject' => 'You\'re Invited to {{ workshop_name }}',
            'content' => "Hello {{ name }},\n\nWe're excited to invite you to {{ workshop_name }}!\n\n{{ workshop_description }}\n\nWorkshop Details:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n- Duration: {{ workshop_date_range }}\n\nTo secure your spot, please register as soon as possible.\n\nBest regards,\n{{ app_name }}"
        ]);
    }

    /**
     * Create a confirmation email template.
     */
    public function confirm(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'confirm',
            'subject' => 'Registration Confirmed for {{ workshop_name }}',
            'content' => "Dear {{ name }},\n\nYour registration for {{ workshop_name }} has been confirmed!\n\nRegistration Details:\n- Name: {{ name }}\n- Email: {{ email }}\n- Company: {{ company }}\n- Position: {{ position }}\n\nWorkshop Information:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nYou will receive your ticket with QR code shortly.\n\nThank you,\n{{ app_name }}"
        ]);
    }

    /**
     * Create a reminder email template.
     */
    public function reminder(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'reminder',
            'subject' => 'Reminder: {{ workshop_name }} is Tomorrow',
            'content' => "Hello {{ name }},\n\nThis is a friendly reminder that {{ workshop_name }} is scheduled for tomorrow!\n\nWorkshop Details:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n- Your Ticket Code: {{ ticket_code }}\n\nPlease arrive 15 minutes early for check-in. Don't forget to bring your ticket or have your QR code ready on your phone.\n\nSee you tomorrow!\n\n{{ app_name }}"
        ]);
    }

    /**
     * Create a thank you email template.
     */
    public function thankYou(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'thank_you',
            'subject' => 'Thank You for Attending {{ workshop_name }}',
            'content' => "Dear {{ name }},\n\nThank you for attending {{ workshop_name }}! We hope you found it valuable and informative.\n\nWe'd love to hear your feedback about the workshop. Please take a moment to share your thoughts with us.\n\nStay tuned for upcoming workshops and events. We look forward to seeing you again!\n\nBest regards,\n{{ app_name }}"
        ]);
    }
}
