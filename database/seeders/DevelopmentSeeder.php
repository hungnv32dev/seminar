<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use App\Models\TicketType;
use App\Models\Participant;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds for development environment.
     * This creates a minimal set of data for quick testing.
     */
    public function run(): void
    {
        // Create basic users for testing
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $organizer = User::create([
            'name' => 'Organizer',
            'email' => 'organizer@test.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $organizer->assignRole('organizer');

        // Create a test workshop
        $workshop = Workshop::create([
            'name' => 'Test Workshop',
            'description' => 'This is a test workshop for development.',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(4),
            'location' => 'Test Location',
            'status' => 'published',
            'created_by' => $organizer->id,
        ]);

        $workshop->organizers()->attach($organizer->id);

        // Create ticket types
        $standardTicket = TicketType::create([
            'workshop_id' => $workshop->id,
            'name' => 'Standard',
            'price' => 100.00,
        ]);

        $studentTicket = TicketType::create([
            'workshop_id' => $workshop->id,
            'name' => 'Student',
            'price' => 50.00,
        ]);

        // Create some test participants
        for ($i = 1; $i <= 5; $i++) {
            Participant::create([
                'workshop_id' => $workshop->id,
                'ticket_type_id' => $i <= 3 ? $standardTicket->id : $studentTicket->id,
                'name' => "Test Participant $i",
                'email' => "participant$i@test.com",
                'phone' => "123-456-789$i",
                'company' => "Test Company $i",
                'position' => "Test Position $i",
                'occupation' => 'Developer',
                'is_paid' => $i <= 4, // 4 out of 5 paid
                'is_checked_in' => false,
            ]);
        }

        // Create basic email templates
        EmailTemplate::create([
            'workshop_id' => $workshop->id,
            'type' => 'ticket',
            'subject' => 'Your Ticket for {{ workshop_name }}',
            'content' => "Hello {{ name }},\n\nYour ticket code is: {{ ticket_code }}\n\nWorkshop: {{ workshop_name }}\nDate: {{ workshop_start_date_formatted }}\nLocation: {{ workshop_location }}\n\nSee you there!",
        ]);

        $this->command->info('Development data created successfully!');
        $this->command->info('Login: admin@test.com / 123456');
        $this->command->info('Login: organizer@test.com / 123456');
    }
}