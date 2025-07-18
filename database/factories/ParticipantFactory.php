<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $occupations = [
            'Software Developer', 'Frontend Developer', 'Backend Developer', 'Full Stack Developer',
            'UI/UX Designer', 'Product Designer', 'Graphic Designer',
            'Project Manager', 'Product Manager', 'Scrum Master',
            'Business Analyst', 'Data Analyst', 'System Analyst',
            'DevOps Engineer', 'QA Engineer', 'Technical Lead',
            'Marketing Specialist', 'Sales Representative', 'Consultant',
            'Student', 'Freelancer', 'Entrepreneur'
        ];

        $positions = [
            'Junior Developer', 'Senior Developer', 'Lead Developer', 'Principal Engineer',
            'Designer', 'Senior Designer', 'Design Lead',
            'Manager', 'Senior Manager', 'Director',
            'Analyst', 'Senior Analyst', 'Principal Analyst',
            'Consultant', 'Senior Consultant', 'Principal Consultant',
            'Intern', 'Associate', 'Specialist', 'Coordinator'
        ];

        $companies = [
            'Tech Solutions Inc.', 'Digital Innovations Ltd.', 'Creative Agency Co.',
            'StartUp Ventures', 'Enterprise Systems Corp.', 'Freelance',
            'Consulting Group LLC', 'Software House', 'Design Studio',
            'University of Technology', 'Community College', 'Self-Employed'
        ];

        $isCheckedIn = $this->faker->boolean(30);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->optional(0.8)->phoneNumber(),
            'company' => $this->faker->optional(0.9)->randomElement($companies),
            'position' => $this->faker->optional(0.8)->randomElement($positions),
            'occupation' => $this->faker->randomElement($occupations),
            'address' => $this->faker->optional(0.7)->address(),
            'ticket_code' => null, // Let the model generate this automatically
            'is_paid' => $this->faker->boolean(70), // 70% chance of being paid
            'is_checked_in' => $isCheckedIn,
            'checked_in_at' => $isCheckedIn ? $this->faker->dateTimeThisMonth() : null,
            'workshop_id' => \App\Models\Workshop::factory(),
            'ticket_type_id' => \App\Models\TicketType::factory(),
        ];
    }

    /**
     * Create a paid participant.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
        ]);
    }

    /**
     * Create an unpaid participant.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => false,
        ]);
    }

    /**
     * Create a checked-in participant.
     */
    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_checked_in' => true,
            'checked_in_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }

    /**
     * Create a participant who hasn't checked in.
     */
    public function notCheckedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_checked_in' => false,
            'checked_in_at' => null,
        ]);
    }

    /**
     * Create a student participant.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'occupation' => 'Student',
            'company' => $this->faker->randomElement([
                'University of Technology', 
                'Community College', 
                'State University',
                'Technical Institute'
            ]),
            'position' => null,
        ]);
    }

    /**
     * Create a developer participant.
     */
    public function developer(): static
    {
        return $this->state(fn (array $attributes) => [
            'occupation' => $this->faker->randomElement([
                'Software Developer', 
                'Frontend Developer', 
                'Backend Developer', 
                'Full Stack Developer'
            ]),
            'position' => $this->faker->randomElement([
                'Junior Developer', 
                'Senior Developer', 
                'Lead Developer'
            ]),
        ]);
    }
}
