<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Phase;
use App\Models\Sport;
use App\Models\Team;

trait InsertData
{
    public function insert_data(): void
    {
        $country = Country::factory()->create(['id' => 'ZZ']);
        $sport = Sport::factory()->create(['id' => 'TEST_SPORT']);
        $competition = Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);

        Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id, 'short_name' => 'TEAM1_TEST']);
        Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id, 'short_name' => 'TEAM2_TEST']);

        foreach (Team::all() as $team) {
            Participation::factory()->create(['competition_id' => $competition->id, 'team_id' => $team->id]);
        }

        Phase::factory()->create(['competition_id' => $competition->id, 'short_name' => 'PHASE1']);
        Phase::factory()->create(['competition_id' => $competition->id, 'short_name' => 'PHASE2']);

        foreach (Phase::all() as $phase) {
            foreach (Team::all() as $team) {
                $team2 = Team::where('id', '<>', $team->id)->first();
                Event::factory()->create(['phase_id' => $phase->id, 'team1_id' => $team->id, 'team2_id' => $team2->id]);
            }
        }
    }
}
