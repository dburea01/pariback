<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['PLANNED', 'INPROGRESS', 'TERMINATED']);

        return [
            'date' => $this->faker->dateTime(),
            'location' => $this->faker->city(),
            'status' => $status,
            'score_team1' => $status !== 'PLANNED' ? rand(0, 10) : null,
            'score_team2' => $status !== 'PLANNED' ? rand(0, 10) : null,
        ];
    }
}
