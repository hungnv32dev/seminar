<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use App\Models\TicketType;
use App\Models\Participant;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users with different roles
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $organizer1 = User::create([
            'name' => 'John Organizer',
            'email' => 'organizer1@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $organizer1->assignRole('organizer');

        $organizer2 = User::create([
            'name' => 'Jane Organizer',
            'email' => 'organizer2@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $organizer2->assignRole('organizer');

        $staff1 = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $staff1->assignRole('staff');

        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $viewer->assignRole('viewer');

        // Create additional organizers
        $organizers = User::factory()->count(3)->create();
        foreach ($organizers as $organizer) {
            $organizer->assignRole('organizer');
        }

        // Create sample workshops
        $workshop1 = Workshop::create([
            'name' => 'Laravel Advanced Techniques',
            'description' => 'Deep dive into advanced Laravel concepts including Eloquent relationships, query optimization, and design patterns.',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(30)->addHours(6),
            'location' => 'Conference Room A, Tech Hub',
            'status' => 'published',
            'created_by' => $organizer1->id,
        ]);

        $workshop2 = Workshop::create([
            'name' => 'Vue.js Fundamentals',
            'description' => 'Learn the basics of Vue.js framework, component architecture, and state management.',
            'start_date' => now()->addDays(45),
            'end_date' => now()->addDays(45)->addHours(4),
            'location' => 'Training Center, Floor 2',
            'status' => 'published',
            'created_by' => $organizer2->id,
        ]);

        $workshop3 = Workshop::create([
            'name' => 'Database Design Workshop',
            'description' => 'Master database design principles, normalization, and performance optimization techniques.',
            'start_date' => now()->addDays(60),
            'end_date' => now()->addDays(60)->addHours(8),
            'location' => 'Innovation Lab, Building B',
            'status' => 'draft',
            'created_by' => $organizer1->id,
        ]);

        $workshop4 = Workshop::create([
            'name' => 'API Development Masterclass',
            'description' => 'Build robust RESTful APIs with authentication, rate limiting, and comprehensive documentation.',
            'start_date' => now()->subDays(7),
            'end_date' => now()->subDays(7)->addHours(6),
            'location' => 'Auditorium, Tech Hub',
            'status' => 'completed',
            'created_by' => $organizer2->id,
        ]);

        $workshop5 = Workshop::create([
            'name' => 'DevOps Best Practices',
            'description' => 'Learn CI/CD pipelines, containerization with Docker, and cloud deployment strategies.',
            'start_date' => now()->subHours(2),
            'end_date' => now()->addHours(4),
            'location' => 'Virtual (Online)',
            'status' => 'ongoing',
            'created_by' => $organizer1->id,
        ]);

        // Assign organizers to workshops
        $workshop1->organizers()->attach([$organizer1->id, $organizer2->id]);
        $workshop2->organizers()->attach([$organizer2->id]);
        $workshop3->organizers()->attach([$organizer1->id, $organizers[0]->id]);
        $workshop4->organizers()->attach([$organizer2->id, $organizers[1]->id]);
        $workshop5->organizers()->attach([$organizer1->id]);

        // Create ticket types for each workshop
        $workshops = [$workshop1, $workshop2, $workshop3, $workshop4, $workshop5];
        
        foreach ($workshops as $workshop) {
            // Standard ticket
            $standardTicket = TicketType::create([
                'workshop_id' => $workshop->id,
                'name' => 'Standard',
                'price' => fake()->randomFloat(2, 50, 150),
            ]);

            // Premium ticket
            $premiumTicket = TicketType::create([
                'workshop_id' => $workshop->id,
                'name' => 'Premium',
                'price' => fake()->randomFloat(2, 150, 300),
            ]);

            // Student ticket (discounted)
            $studentTicket = TicketType::create([
                'workshop_id' => $workshop->id,
                'name' => 'Student',
                'price' => fake()->randomFloat(2, 20, 80),
            ]);

            // Create participants for each workshop
            $ticketTypes = [$standardTicket, $premiumTicket, $studentTicket];
            
            // Create 15-25 participants per workshop
            $participantCount = fake()->numberBetween(15, 25);
            
            for ($i = 0; $i < $participantCount; $i++) {
                $ticketType = fake()->randomElement($ticketTypes);
                $isCheckedIn = $workshop->status === 'completed' ? fake()->boolean(85) : 
                              ($workshop->status === 'ongoing' ? fake()->boolean(60) : false);
                
                Participant::create([
                    'workshop_id' => $workshop->id,
                    'ticket_type_id' => $ticketType->id,
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => fake()->optional(0.8)->phoneNumber(),
                    'company' => fake()->optional(0.9)->company(),
                    'position' => fake()->optional(0.8)->jobTitle(),
                    'occupation' => fake()->randomElement([
                        'Software Developer', 'Frontend Developer', 'Backend Developer',
                        'UI/UX Designer', 'Product Manager', 'Business Analyst',
                        'DevOps Engineer', 'QA Engineer', 'Student', 'Freelancer'
                    ]),
                    'address' => fake()->optional(0.7)->address(),
                    'is_paid' => fake()->boolean(80), // 80% paid
                    'is_checked_in' => $isCheckedIn,
                    'checked_in_at' => $isCheckedIn ? fake()->dateTimeThisMonth() : null,
                ]);
            }

            // Create email templates for each workshop
            $templateTypes = ['ticket', 'invite', 'confirm', 'reminder', 'thank_you'];
            
            foreach ($templateTypes as $type) {
                EmailTemplate::create([
                    'workshop_id' => $workshop->id,
                    'type' => $type,
                    'subject' => $this->getTemplateSubject($type),
                    'content' => $this->getTemplateContent($type),
                ]);
            }
        }

        $this->command->info('Sample data created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Super Admin: superadmin@example.com / password');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Organizer: organizer1@example.com / password');
        $this->command->info('Staff: staff@example.com / password');
        $this->command->info('Viewer: viewer@example.com / password');
    }

    private function getTemplateSubject(string $type): string
    {
        return match($type) {
            'ticket' => 'Your Ticket for {{ workshop_name }}',
            'invite' => 'You\'re Invited to {{ workshop_name }}',
            'confirm' => 'Registration Confirmed for {{ workshop_name }}',
            'reminder' => 'Reminder: {{ workshop_name }} is Tomorrow',
            'thank_you' => 'Thank You for Attending {{ workshop_name }}',
            default => 'Workshop Notification - {{ workshop_name }}',
        };
    }

    private function getTemplateContent(string $type): string
    {
        return match($type) {
            'ticket' => "Dear {{ name }},\n\nThank you for registering for {{ workshop_name }}!\n\nYour ticket details:\n- Ticket Code: {{ ticket_code }}\n- Workshop: {{ workshop_name }}\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nPlease bring this email or show the QR code for check-in.\n\nBest regards,\n{{ app_name }}",
            
            'invite' => "Hello {{ name }},\n\nWe're excited to invite you to {{ workshop_name }}!\n\n{{ workshop_description }}\n\nWorkshop Details:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nTo secure your spot, please register as soon as possible.\n\nBest regards,\n{{ app_name }}",
            
            'confirm' => "Dear {{ name }},\n\nYour registration for {{ workshop_name }} has been confirmed!\n\nRegistration Details:\n- Name: {{ name }}\n- Email: {{ email }}\n- Company: {{ company }}\n\nWorkshop Information:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n\nYou will receive your ticket shortly.\n\nThank you,\n{{ app_name }}",
            
            'reminder' => "Hello {{ name }},\n\nThis is a friendly reminder that {{ workshop_name }} is scheduled for tomorrow!\n\nWorkshop Details:\n- Date: {{ workshop_start_date_formatted }}\n- Location: {{ workshop_location }}\n- Your Ticket Code: {{ ticket_code }}\n\nPlease arrive 15 minutes early for check-in.\n\nSee you tomorrow!\n\n{{ app_name }}",
            
            'thank_you' => "Dear {{ name }},\n\nThank you for attending {{ workshop_name }}! We hope you found it valuable and informative.\n\nWe'd love to hear your feedback about the workshop.\n\nStay tuned for upcoming workshops and events!\n\nBest regards,\n{{ app_name }}",
            
            default => "Hello {{ name }},\n\nThis is a notification regarding {{ workshop_name }}.\n\nBest regards,\n{{ app_name }}",
        };
    }
}