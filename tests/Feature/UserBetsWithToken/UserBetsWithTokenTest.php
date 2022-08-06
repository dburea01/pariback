<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Event;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBetsWithTokenTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_get_the_details_of_bet_with_token(): void
    {
        $this->insert_data();
        $bet = Bet::where('status', 'INPROGRESS')->first();
        $bettor = Bettor::where('bet_id', $bet->id)->first();

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/$bettor->token/details");
        $response->assertStatus(200);
    }

    public function test_get_the_details_of_bet_with_token_must_return_the_details_of_bet(): void
    {
        $this->insert_data();
        $bet = Bet::where('status', 'INPROGRESS')->first();
        $bettor = Bettor::where('bet_id', $bet->id)->first();

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/$bettor->token/details");
        $response->assertStatus(200);
    }

    public function test_a_post_of_userBet_with_token_must_create_the_userBet(): void
    {
        $this->insert_data();
        $bet = Bet::where('status', 'INPROGRESS')->first();
        $bettor = Bettor::where('bet_id', $bet->id)->first();
        $event = Event::where('phase_id', $bet->phase_id)->first();

        $userBet = [
            'user_id' => $bettor->user_id,
            'event_id' => $event->id,
            'score_team1' => '5',
            'score_team2' => '2',
        ];

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/$bettor->token/user-bets", $userBet);
        $response->assertStatus(201);

        $userBetCreated = UserBet::where('bet_id', $bet->id)
        ->where('event_id', $event->id)
        ->where('user_id', $bettor->user_id)
        ->get();

        $this->assertEquals(count($userBetCreated), 1);
        $this->assertEquals($userBetCreated[0]->score_team1, 5);
        $this->assertEquals($userBetCreated[0]->score_team2, 2);
        $this->assertEquals($userBetCreated[0]->created_by, User::find($bettor->user_id)->name);
    }

    public function test_a_delete_of_userBet_with_token_must_delete_the_userBet(): void
    {
        $this->insert_data();
        $userBet = UserBet::first();
        $bettor = Bettor::where('bet_id', $userBet->bet_id)->first();
        $response = $this->deleteJson($this->getEndPoint()."bets/$userBet->bet_id/$bettor->token/user-bets/$userBet->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('user_bets', ['id' => $userBet->id]);
    }
}
