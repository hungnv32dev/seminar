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
        $ticketTypes = [
            ['name' => 'Standard', 'price' => $this->faker->randomFloat(2, 50, 150)],
            ['name' => 'Premium', 'price' => $this->faker->randomFloat(2, 150, 300)],
            ['name' => 'VIP', 'price' => $this->faker->randomFloat(2, 300, 500)],
            ['name' => 'Early Bird', 'price' => $this->faker->randomFloat(2, 30, 100)],
            ['name' => 'Student', 'price' => $this->faker->randomFloat(2, 20, 80)],
            ['name' => 'Corporate', 'price' => $this->faker->randomFloat(2, 200, 400)],
            ['name' => 'Group (5+)', 'price' => $this->faker->randomFloat(2, 40, 120)],
        ];

        $selectedType = $this->faker->randomElement($ticketTypes);

        return [
            'name' => $selectedType['name'],
            'price' => $selectedType['price'],
            'workshop_id' => \App\Models\Workshop::factory(),
        ];
    }

    /**
     * Create a free ticket type.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Free',
            'price' => 0.00,
        ]);
    }

    /**
     * Create a standard ticket type.
     */
    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Standard',
            'price' => $this->faker->randomFloat(2, 50, 150),
        ]);
    }

    /**
     * Create a premium ticket type.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium',
            'price' => $this->faker->randomFloat(2, 150, 300),
        ]);
    }

    /**
     * Create a student discount ticket type.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Student',
            'price' => $this->faker->randomFloat(2, 20, 80),
        ]);
    }
}
