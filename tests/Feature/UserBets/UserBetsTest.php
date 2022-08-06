<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Event;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserBetsTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_get_the_user_bets(): void
    {
        $this->insert_data();
        $userAdmin = User::where('is_admin', true)->first();
        $this->actingAs($userAdmin);
        $bet = Bet::first();

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(UserBet::where('bet_id', $bet->id)->count(), count($returnedJson));

        $bettor = Bettor::where('bet_id', $bet->id)->first();
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets?userId=$bettor->user_id");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(UserBet::where('bet_id', $bet->id)->where('user_id', $bettor->user_id)->count(), count($returnedJson));

        $event = Event::where('phase_id', $bet->phase_id)->first();
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/user-bets?userId=$bettor->user_id&eventId=$event->id");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(UserBet::where('bet_id', $bet->id)->where('user_id', $bettor->user_id)->where('event_id', $event->id)->count(), count($returnedJson));
    }

    public function test_a_post_of_userBet_with_wrong_body_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::first();
        $this->actingAs(User::where('is_admin', true)->first());

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets");
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['score_team1', 'score_team2', 'user_id', 'event_id']);
    }

    public function test_a_post_of_userBet_with_wrong_scores_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::first();
        $this->actingAs(User::where('is_admin', true)->first());

        $userBet = [
            'score_team1' => 'a',
            'score_team2' => 'b',
        ];

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets", $userBet);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['score_team1', 'score_team2', 'user_id', 'event_id']);
    }

    public function test_a_post_of_userBet_with_wrong_user_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::first();
        $this->actingAs(User::where('is_admin', true)->first());
        $wrongUser = Bettor::where('bet_id', '<>', $bet->id)->first();
        $userBet = [
            'user_id' => $wrongUser->id,
        ];

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets", $userBet);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['user_id']);
    }

    public function test_a_post_of_userBet_with_wrong_event_must_return_an_error(): void
    {
        $this->insert_data();
        $bet = Bet::first();
        $this->actingAs(User::where('is_admin', true)->first());
        $wrongEvent = Event::where('phase_id', '<>', $bet->phase_id)->first();
        $userBet = [
            'event_id' => $wrongEvent->id,
        ];

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets", $userBet);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['event_id']);
    }

    public function test_a_post_of_correct_userBet_must_create_the_userBet(): void
    {
        $this->insert_data();
        $bet = Bet::first();
        $bettor = Bettor::where('bet_id', $bet->id)->first();
        $event = Event::where('phase_id', $bet->phase_id)->first();
        $this->actingAs(User::where('is_admin', true)->first());

        $userBet = [
            'user_id' => $bettor->user_id,
            'event_id' => $event->id,
            'score_team1' => '5',
            'score_team2' => '2',
        ];

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/user-bets", $userBet);
        $response->assertStatus(201);

        $userBetCreated = UserBet::where('bet_id', $bet->id)
        ->where('event_id', $event->id)
        ->where('user_id', $bettor->user_id)
        ->get();

        $this->assertEquals(count($userBetCreated), 1);
        $this->assertEquals($userBetCreated[0]->score_team1, 5);
        $this->assertEquals($userBetCreated[0]->score_team2, 2);
    }

    public function test_a_delete_of_userBet_must_delete_the_userBet(): void
    {
        $this->insert_data();
        $this->actingAs(User::where('is_admin', true)->first());
        $userBet = UserBet::first();

        $response = $this->deleteJson($this->getEndPoint()."bets/$userBet->bet_id/user-bets/$userBet->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('user_bets', ['id' => $userBet->id]);
    }
}
