<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Competition;
use App\Models\Country;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Phase;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;

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

        $phase1 = Phase::factory()->create(['competition_id' => $competition->id, 'short_name' => 'PHASE1']);
        $phase2 = Phase::factory()->create(['competition_id' => $competition->id, 'short_name' => 'PHASE2']);

        foreach (Phase::all() as $phase) {
            foreach (Team::all() as $team) {
                $team2 = Team::where('id', '<>', $team->id)->first();
                Event::factory()->create(['phase_id' => $phase->id, 'team1_id' => $team->id, 'team2_id' => $team2->id]);
            }
        }

        /*
        $user1 = User::factory()->create(['is_admin' => false]);
        $user2 = User::factory()->create(['is_admin' => true]);

         Bet::factory()->count(3)->create(['user_id' => $user1->id, 'phase_id' => $phase1->id]);
        Bet::factory()->count(2)->create(['user_id' => $user2->id, 'phase_id' => $phase2->id]);
        */
    }
}
