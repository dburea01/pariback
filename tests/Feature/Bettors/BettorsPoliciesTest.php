<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BettorsPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    //TODO : pas certain de cette regle de gestion, à réflechir
    public function test_you_must_not_be_authenticated_to_see_the_bettors_of_a_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);
        $this->create_bettors($bet);

        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_to_add_bettor(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(401);

        $this->actingAs($user);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(422);
    }

    public function test_an_authenticated_user_cannot_add_bettor_to_a_bet_which_is_not_his(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(401);

        $otherUser = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($otherUser);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(403);
    }

    public function test_an_authenticated_admin_can_add_bettor_to_any_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(422);
    }

    public function test_a_not_admin_user_can_delete_only_his_bettors(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);
        $this->create_bettors($bet);

        $bettorToDelete = Bettor::where('bet_id', $bet->id)->first();

        $anotherUser = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($anotherUser);

        $response = $this->deleteJson($this->getEndPoint()."bets/$bet->id/bettors/$bettorToDelete->id");
        $response->assertStatus(403);

        $this->actingAs($user);

        $response = $this->deleteJson($this->getEndPoint()."bets/$bet->id/bettors/$bettorToDelete->id");
        $response->assertStatus(204);
    }

    public function test_an_admin_user_can_delete_any_bettors(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);
        $this->create_bettors($bet);

        $bettorToDelete = Bettor::where('bet_id', $bet->id)->first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->deleteJson($this->getEndPoint()."bets/$bet->id/bettors/$bettorToDelete->id");
        $response->assertStatus(204);
    }

    public function create_bettors(Bet $bet)
    {
        $users = User::factory()->count(5)->create();
        foreach ($users as $user) {
            Bettor::factory()->create(['bet_id' => $bet->id, 'user_id' => $user->id]);
        }
    }
}
