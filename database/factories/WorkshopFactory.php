<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 8) . ' hours');

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $this->faker->address(),
            'status' => $this->faker->randomElement(['draft', 'published', 'ongoing', 'completed', 'cancelled']),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
