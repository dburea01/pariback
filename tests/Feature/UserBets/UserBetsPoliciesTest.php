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

    public function test_you_must_be_authenticated_as_admin_or_be_the_owner_to_see_any_userBets(): void
    {
        $this->insert_data();
        $userNotAdmin = User::where('is_admin', false)->first();
        $userAdmin = User::where('is_admin', true)->first();
        $bet = Bet::where('user_id', '<>', $userNotAdmin->id)->first();

        $this->actingAs($userAdmin);
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(200);

        $this->actingAs($userNotAdmin);
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(403);
    }

    public function test_you_must_be_authenticated_as_admin_to_post_an_userBet(): void
    {
        $this->insert_data();
        $userNotAdmin = User::where('is_admin', false)->first();
        $userAdmin = User::where('is_admin', true)->first();
        $bet = Bet::where('user_id', '<>', $userNotAdmin->id)->first();
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

    public function test_you_must_be_the_owner_of_the_bet_to_post_an_userBet(): void
    {
        $this->insert_data();
        $users = User::where('is_admin', false)->take(2)->get();
        $userA = $users[0];
        $userB = $users[1];

        $betA = Bet::where('user_id', $userA->id)->first();

        $bettor = Bettor::where('bet_id', $betA->id)->first();
        $event = Event::where('phase_id', $betA->phase_id)->first();

        $userBet = [
            'user_id' => $bettor->user_id,
            'event_id' => $event->id,
            'score_team1' => '5',
            'score_team2' => '2',
        ];

        $this->actingAs($userB);
        $response = $this->postJson($this->getEndPoint()."bets/$betA->id/user-bets", $userBet);
        $response->assertStatus(403);

        $this->actingAs($userA);
        $response = $this->postJson($this->getEndPoint()."bets/$betA->id/user-bets", $userBet);
        $response->assertStatus(201);
    }

    public function test_you_must_be_authenticated_as_admin_to_delete_any_userBet(): void
    {
        $this->insert_data();

        $userAdmin = User::where('is_admin', true)->first();
        $this->actingAs($userAdmin);

        $bet = Bet::where('user_id', '<>', $userAdmin->id)->first();
        $userBet = UserBet::where('bet_id', $bet->id)->first();

        $response = $this->deleteJson($this->getEndPoint()."bets/$bet->id/user-bets/$userBet->id");
        $response->assertStatus(204);
    }

    public function test_you_must_be_the_owner_of_the_bet_to_delete_an_userBet(): void
    {
        $this->insert_data();
        $users = User::where('is_admin', false)->take(2)->get();
        $userA = $users[0];
        $userB = $users[1];

        $betA = Bet::where('user_id', $userA->id)->first();

        $userBet = UserBet::where('bet_id', $betA->id)->first();
        $this->actingAs($userB);
        $response = $this->deleteJson($this->getEndPoint()."bets/$betA->id/user-bets/$userBet->id");
        $response->assertStatus(403);

        $this->actingAs($userA);
        $response = $this->deleteJson($this->getEndPoint()."bets/$betA->id/user-bets/$userBet->id");
        $response->assertStatus(204);
    }
}
