<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Phase;
use App\Models\Team;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phases = Phase::with('competition')->get();
        $teams = Team::all();

        foreach ($phases as $phase) {
            for ($i = 0; $i < rand(1, 20); $i++) {
                $teamsBySport = $teams->filter(function ($value, $key) use ($phase) {
                    return $value->sport_id === $phase->competition->sport_id;
                })->random(2);

                Event::factory()->create([
                    'phase_id' => $phase->id,
                    'team1_id' => $teamsBySport->first()->id,
                    'team2_id' => $teamsBySport->last()->id,
                    'location' => $teamsBySport->first()->city,
                ]);
            }
        }
    }
}
