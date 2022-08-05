<?php
namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Competition;
use App\Models\Country;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Phase;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\UserBet;

trait InsertData
{
    public function insert_data(): void
    {
        $country = Country::factory()->create(['id' => 'ZZ']);
        $sport = Sport::factory()->create(['id' => 'TEST_SPORT']);
        $competition = Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);

        $team1 = Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id, 'short_name' => 'TEAM1_TEST']);
        $team2 = Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id, 'short_name' => 'TEAM2_TEST']);

        foreach (Team::all() as $team) {
            Participation::factory()->create(['competition_id' => $competition->id, 'team_id' => $team->id]);
        }

        $phase1 = Phase::factory()->create(['competition_id' => $competition->id, 'short_name' => 'PHASE1_TEST']);
        $phase2 = Phase::factory()->create(['competition_id' => $competition->id, 'short_name' => 'PHASE2_TEST']);

        foreach (Phase::all() as $phase) {
            foreach (Team::all() as $team) {
                $team2 = Team::where('id', '<>', $team->id)->first();
                Event::factory()->create(['phase_id' => $phase->id, 'team1_id' => $team->id, 'team2_id' => $team2->id]);
            }
        }

        $users = User::factory()->count(5)->create(['is_admin' => false]);
        $userAdmin = User::factory()->create(['is_admin' => true]);
        $event1 = Event::factory()->count(5)->create([
            'phase_id' => $phase1->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id
        ]);

        $bet1 = Bet::factory()->create(['user_id' => $users->random()->id, 'phase_id' => $phase1->id]);
        $bet2 = Bet::factory()->create(['user_id' => $users->random()->id, 'phase_id' => $phase2->id]);

        foreach (Bet::all() as $bet) {
            foreach (User::all() as $user) {
                Bettor::factory()->create([
                    'bet_id' => $bet->id,
                    'user_id' => $user->id
                ]);
            }
        }

        foreach (Bet::all() as $bet) {
            foreach (Bettor::where('bet_id', $bet->id)->get() as $bettor) {
                foreach (Event::all() as $event) {
                    UserBet::factory()->create([
                        'bet_id' => $bet->id,
                        'user_id' => $bettor->user_id,
                        'event_id' => $event->id
                    ]);
                }
            }
        }
    }
}
