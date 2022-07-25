<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class SportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => $this->faker->word(),
            'name' => [
                'en' => $this->faker->word.'_en',
                'fr' => $this->faker->word.'_fr',
            ],
            'icon' => 'icon.png',
            'status' => 'ACTIVE',
            'position' => random_int(10, 100),
        ];
    }
}
