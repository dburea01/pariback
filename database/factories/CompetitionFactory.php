<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'short_name' => $this->faker->word(),
            'name' => $this->faker->words(3, true),
            'icon' => 'competition_'.random_int(1, 10),
            'position' => random_int(1, 10),
            'status' => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}
