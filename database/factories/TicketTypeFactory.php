<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketType>
 */
class TicketTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Standard', 'Premium', 'VIP', 'Early Bird', 'Student']),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'workshop_id' => \App\Models\Workshop::factory(),
        ];
    }
}
