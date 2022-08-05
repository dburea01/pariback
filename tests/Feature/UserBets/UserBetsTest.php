<?php
namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Event;
use App\Models\Phase;
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

        $response = $this->getJson($this->getEndPoint() . "bets/$bet->id/user-bets");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(UserBet::where('bet_id', $bet->id)->count(), count($returnedJson));

        $bettor = Bettor::where('bet_id', $bet->id)->first();
        $response = $this->getJson($this->getEndPoint() . "bets/$bet->id/user-bets?userId=$bettor->user_id");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(UserBet::where('bet_id', $bet->id)->where('user_id', $bettor->user_id)->count(), count($returnedJson));

        $event = Event::where('phase_id', $bet->phase_id)->first();
        $response = $this->getJson($this->getEndPoint() . "bets/$bet->id/user-bets?userId=$bettor->user_id&eventId=$event->id");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(UserBet::where('bet_id', $bet->id)->where('user_id', $bettor->user_id)->where('event_id', $event->id)->count(), count($returnedJson));
    }
}
