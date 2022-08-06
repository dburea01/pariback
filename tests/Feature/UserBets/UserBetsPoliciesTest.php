<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Event;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBetsPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_you_must_be_authenticated_to_see_the_userBets(): void
    {
        $this->insert_data();
        $bet = Bet::first();
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(401);
    }

    public function test_you_must_be_authenticated_as_admin_to_see_the_userBets(): void
    {
        $this->insert_data();
        $userNotAdmin = User::where('is_admin', false)->first();
        $userAdmin = User::where('is_admin', true)->first();
        $bet = Bet::first();

        $this->actingAs($userNotAdmin);
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(403);

        $this->actingAs($userAdmin);
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_as_admin_to_post_an_userBet(): void
    {
        $this->insert_data();
        $userNotAdmin = User::where('is_admin', false)->first();
        $userAdmin = User::where('is_admin', true)->first();
        $bet = Bet::first();
        $bettor = Bettor::where('bet_id', $bet->id)->first();
        $event = Event::where('phase_id', $bet->phase_id)->first();

        $userBet = [
            'user_id' => $bettor->user_id,
            'event_id' => $event->id,
            'score_team1' => '5',
            'score_team2' => '2',
        ];

        $this->actingAs($userNotAdmin);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets", $userBet);
        $response->assertStatus(403);

        $this->actingAs($userAdmin);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets", $userBet);
        $response->assertStatus(201);
    }

    public function test_you_must_be_authenticated_as_admin_to_delete_of_userBet(): void
    {
        $this->insert_data();
        $userNotAdmin = User::where('is_admin', false)->first();
        $userAdmin = User::where('is_admin', true)->first();
        $userBet = UserBet::first();

        $this->actingAs($userNotAdmin);
        $response = $this->deleteJson($this->getEndPoint()."bets/$userBet->bet_id/user-bets/$userBet->id");
        $response->assertStatus(403);

        $this->actingAs($userAdmin);
        $response = $this->deleteJson($this->getEndPoint()."bets/$userBet->bet_id/user-bets/$userBet->id");
        $response->assertStatus(204);
    }
}
