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
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->company(),
            'position' => $this->faker->jobTitle(),
            'occupation' => $this->faker->randomElement(['Developer', 'Designer', 'Manager', 'Analyst', 'Consultant']),
            'address' => $this->faker->address(),
            'ticket_code' => 'WS-' . strtoupper($this->faker->bothify('########')),
            'is_paid' => $this->faker->boolean(70), // 70% chance of being paid
            'is_checked_in' => $this->faker->boolean(30), // 30% chance of being checked in
            'checked_in_at' => $this->faker->optional(0.3)->dateTimeThisMonth(),
            'workshop_id' => \App\Models\Workshop::factory(),
            'ticket_type_id' => \App\Models\TicketType::factory(),
        ];
    }
}
