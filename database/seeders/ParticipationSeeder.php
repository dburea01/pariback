<?php
namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Participation;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ParticipationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $competitions = Competition::all();

        foreach ($competitions as $competition) {
            $teams = Team::where('country_id', $competition->country_id)
            ->where('sport_id', $competition->sport_id)
            ->get();

            $randomTeams = $teams->random(rand(1, $teams->count()));

            foreach ($randomTeams as $randomTeam) {
                Participation::factory()->create([
                    'competition_id' => $competition->id,
                    'team_id' => $randomTeam->id,
                ]);
            }
        }
    }
}
