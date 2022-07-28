<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phase>
 */
class PhaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->sentence();

        return [
            'short_name' => 'Phase '.$this->faker->word(),
            'name' => [
                'en' => $name.'_en',
                'fr' => $name.'_fr',
            ],
            'start_date' => '2022-07-27',
            'end_date' => '2022-07-31',
            'status' => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
        ];
    }
}
