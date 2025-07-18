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

        $workshopNames = [
            'Laravel Advanced Techniques',
            'Vue.js Fundamentals',
            'Database Design Workshop',
            'API Development Masterclass',
            'Frontend Performance Optimization',
            'DevOps Best Practices',
            'Mobile App Development',
            'Machine Learning Basics',
            'Cybersecurity Essentials',
            'Project Management for Developers'
        ];

        $locations = [
            'Conference Room A, Main Building',
            'Training Center, Floor 2',
            'Auditorium, Tech Hub',
            'Meeting Room 101',
            'Innovation Lab, Building B',
            'Virtual (Online)',
            'Workshop Space, Creative Center'
        ];

        return [
            'name' => $this->faker->randomElement($workshopNames),
            'description' => $this->faker->paragraphs(2, true),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $this->faker->randomElement($locations),
            'status' => $this->faker->randomElement(['draft', 'published', 'ongoing', 'completed']),
            'created_by' => \App\Models\User::factory(),
        ];
    }

    /**
     * Indicate that the workshop is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the workshop is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the workshop is ongoing.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ongoing',
            'start_date' => now()->subHours(2),
            'end_date' => now()->addHours(2),
        ]);
    }

    /**
     * Indicate that the workshop is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => now()->subDays(7),
            'end_date' => now()->subDays(7)->addHours(6),
        ]);
    }

    /**
     * Indicate that the workshop is upcoming.
     */
    public function upcoming(): static
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+2 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(2, 6) . ' hours');

        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
