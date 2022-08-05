<?php
namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BetsPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_you_must_be_authenticated_to_see_the_bets(): void
    {
        $this->insert_data();

        $response = $this->getJson($this->getEndPoint() . 'bets');
        $response->assertStatus(401);
    }

    public function test_a_not_admin_user_can_see_only_his_bets(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        Bet::factory()->count(2)->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        Bet::factory()->count(3)->create(['user_id' => $userBNotAdmin, 'phase_id' => $phase->id]);

        $this->actingAs($userANotAdmin);
        $response = $this->getJson($this->getEndPoint() . 'bets');

        $betsReturned = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(2, count($betsReturned));

        $this->actingAs($userBNotAdmin);
        $response = $this->getJson($this->getEndPoint() . 'bets');

        $betsReturned = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(3, count($betsReturned));
    }

    public function test_an_admin_user_can_see_all_the_bets(): void
    {
        // todo : surement à améliorer compte tenu d'une future pagination qui ne manquera pas d'arriver.
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        Bet::factory()->count(2)->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        Bet::factory()->count(3)->create(['user_id' => $userBNotAdmin, 'phase_id' => $phase->id]);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);
        $response = $this->getJson($this->getEndPoint() . 'bets');

        $betsReturned = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(Bet::all()->count(), count($betsReturned));
    }

    public function test_a_not_admin_user_can_see_only_his_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betB = Bet::factory()->create(['user_id' => $userBNotAdmin, 'phase_id' => $phase->id]);

        $this->actingAs($userANotAdmin);
        $response = $this->getJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(200);

        $this->actingAs($userBNotAdmin);
        $response = $this->getJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(403);
    }

    public function test_an_admin_user_can_see_any_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);

        $this->actingAs($userBAdmin);
        $response = $this->getJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(200);
    }

    public function test_you_must_be_authenticated_to_create_a_bet(): void
    {
        $response = $this->postJson($this->getEndPoint() . 'bets');
        $response->assertStatus(401);

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userANotAdmin);

        $response = $this->postJson($this->getEndPoint() . 'bets');
        $response->assertStatus(422);
    }

    public function test_a_not_admin_user_can_update_only_his_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);

        $this->actingAs($userANotAdmin);
        $response = $this->putJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(200);

        $this->actingAs($userBNotAdmin);
        $response = $this->putJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(403);
    }

    public function test_an_admin_user_can_update_any_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);

        $this->actingAs($userBAdmin);
        $response = $this->putJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(200);
    }

    public function test_a_not_admin_user_can_delete_only_his_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);

        $this->actingAs($userBNotAdmin);
        $response = $this->deleteJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(403);

        $this->actingAs($userANotAdmin);
        $response = $this->deleteJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(204);
    }

    public function test_an_admin_user_can_delete_any_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id]);

        $userBAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);

        $this->actingAs($userBAdmin);
        $response = $this->deleteJson($this->getEndPoint() . "bets/$betA->id")
        ->assertStatus(204);
    }

    public function test_an_authenticated_user_can_activate_only_his_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id, 'status' => 'DRAFT']);

        $userBNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);

        $this->actingAs($userBNotAdmin);
        $this->patchJson($this->getEndPoint() . "bets/$betA->id/activate")
        ->assertStatus(403);

        $this->actingAs($userANotAdmin);
        $this->patchJson($this->getEndPoint() . "bets/$betA->id/activate")
        ->assertStatus(200);
    }

    public function test_an_admin_can_activate_any_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userANotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $betA = Bet::factory()->create(['user_id' => $userANotAdmin, 'phase_id' => $phase->id, 'status' => 'DRAFT']);

        $userBAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);

        $this->actingAs($userBAdmin);
        $this->patchJson($this->getEndPoint() . "bets/$betA->id/activate")
        ->assertStatus(200);
    }
}
