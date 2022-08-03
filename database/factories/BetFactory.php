<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bet>
 */
class BetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'stake' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['DRAFT', 'INPROGRESS', 'CLOSED']),
            'points_good_score' => random_int(3, 5),
            'points_good_1n2' => random_int(0, 2),
        ];
    }
}
