<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBetsWithTokenPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_get_the_details_of_bet_not_inprogress_with_token_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::where('status', '<>', 'INPROGRESS')->first();
        $bettor = Bettor::where('bet_id', $bet->id)->first();

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/$bettor->token/details");
        $response->assertStatus(403);
    }

    public function test_get_the_details_of_bet_with_unknown_token_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::where('status', '<>', 'INPROGRESS')->first();

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/FAKETOKEN/details");
        $response->assertStatus(404);
    }

    public function test_get_the_details_of_bet_with_token_not_coherent_with_bet_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::where('status', '<>', 'INPROGRESS')->first();
        $bettor = Bettor::where('bet_id', '<>', $bet->id)->first();

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/$bettor->token/details");
        $response->assertStatus(404);
    }
}
