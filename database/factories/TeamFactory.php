<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'short_name' => strtoupper($this->faker->word()),
            'name' => $this->faker->sentence(rand(2, 5)),
            'city' => $this->faker->city(),
            'status' => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
            'icon' => 'icon.jpg',
        ];
    }
}
