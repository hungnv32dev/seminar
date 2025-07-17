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
        
        return [
            'type' => $type,
            'subject' => $this->faker->sentence() . ' {{ workshop_name }}',
            'content' => $this->faker->paragraphs(3, true) . "\n\nHello {{ name }},\n\nYour ticket code is {{ ticket_code }}.\n\nBest regards,\n{{ app_name }}",
            'workshop_id' => \App\Models\Workshop::factory(),
        ];
    }
}
