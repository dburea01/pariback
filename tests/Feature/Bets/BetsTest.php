<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BetsTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_get_the_bets(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($user);
        $bets = Bet::factory()->count(3)->create(['user_id' => $user->id, 'phase_id' => $phase->id]);
        $response = $this->getJson($this->getEndPoint().'bets');
        $response->assertStatus(200);

        $betsReturned = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(3, count($betsReturned));
    }

    public function test_a_post_of_bet_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $response = $this->postJson($this->getEndPoint().'bets');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['phase_id', 'title', 'points_good_score', 'points_good_1n2']);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'bets');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['user_id', 'phase_id', 'title', 'points_good_score', 'points_good_1n2', 'status']);
    }

    public function test_a_post_of_phase_with_wrong_body_must_return_an_error(): void
    {
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $bet = [
            'phase_id' => 'toto',
            'title' => '',
            'points_good_score' => 'titi',
            'points_good_1n2' => 'toto',
        ];

        $response = $this->postJson($this->getEndPoint().'bets', $bet);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['phase_id', 'title', 'points_good_score', 'points_good_1n2']);
    }

    public function test_a_post_of_bet_with_correct_body_must_create_the_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $bet = [
            'phase_id' => $phase->id,
            'title' => 'Test bet',
            'description' => 'description',
            'stake' => 'stake',
            'points_good_score' => '1',
            'points_good_1n2' => '2',
        ];

        $response = $this->postJson($this->getEndPoint().'bets', $bet);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_bet());

        $betId = json_decode($response->getContent(), true)['data']['id'];
        $betCreated = Bet::find($betId);

        $this->assertEquals($bet['title'], $betCreated->title);
        $this->assertEquals($bet['description'], $betCreated->description);
        $this->assertEquals($bet['stake'], $betCreated->stake);
        $this->assertEquals($bet['points_good_score'], $betCreated->points_good_score);
        $this->assertEquals($bet['points_good_1n2'], $betCreated->points_good_1n2);
    }

    public function test_a_put_of_bet_must_update_the_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $bet = Bet::factory()->create([
            'user_id' => $userNotAdmin->id,
            'phase_id' => $phase->id,
        ]);

        $betToUpdate = [
            'title' => 'Test bet updated',
            'description' => 'description updated',
            'stake' => 'stake updated',
            'points_good_score' => '10',
            'points_good_1n2' => '20',
        ];

        $response = $this->putJson($this->getEndPoint()."bets/$bet->id", $betToUpdate);
        $response->assertStatus(200)
        ->assertJsonStructure($this->return_structure_bet());

        $betUpdated = Bet::find($bet->id);

        $this->assertEquals($betToUpdate['title'], $betUpdated->title);
        $this->assertEquals($betToUpdate['description'], $betUpdated->description);
        $this->assertEquals($betToUpdate['stake'], $betUpdated->stake);
        $this->assertEquals($betToUpdate['points_good_score'], $betUpdated->points_good_score);
        $this->assertEquals($betToUpdate['points_good_1n2'], $betUpdated->points_good_1n2);
    }

    public function test_delete_a_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $bet = Bet::factory()->create([
            'user_id' => $userNotAdmin->id,
            'phase_id' => $phase->id,
        ]);

        $response = $this->deleteJson($this->getEndPoint()."bets/$bet->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('bets', ['id' => $bet->id]);
    }

    public function test_access_to_an_unknown_bet_must_return_a_404(): void
    {
        $response = $this->getJson($this->getEndPoint().'bets/TOTO');
        $response->assertStatus(404);
    }

    public function test_activate_an_unknow_bet_must_return_a_404(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($user);

        $response = $this->patchJson($this->getEndPoint().'bets/6c197903-3ba7-4d25-8f2d-d02ed28b2367/activate');
        $response->assertStatus(404);
    }

    public function test_activate_a_bet_already_in_progress_must_return_an_error()
    {
        $this->insert_data();
        $phase = Phase::first();

        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($user);

        $bet = Bet::factory()->create([
            'user_id' => $user->id,
            'phase_id' => $phase->id,
            'status' => 'INPROGRESS',
        ]);

        $response = $this->patchJson($this->getEndPoint()."bets/$bet->id/activate");
        $response->assertStatus(422);
    }

    public function test_activate_a_bet_in_a_draft_status_must_activate_the_bet()
    {
        $this->insert_data();
        $phase = Phase::first();

        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($user);

        $bet = Bet::factory()->create([
            'user_id' => $user->id,
            'phase_id' => $phase->id,
            'status' => 'DRAFT',
        ]);

        $response = $this->patchJson($this->getEndPoint()."bets/$bet->id/activate");
        $response->assertStatus(200);

        $betPatched = Bet::find($bet->id);
        $this->assertEquals($betPatched->status, 'INPROGRESS');
    }

    public function return_structure_bet(): array
    {
        return [
            'data' => [
                'id',
                'owner',
                'phase',
                'title',
                'description',
                'stake',
                'status',
                'points_good_score',
                'points_good_1n2',
            ],
        ];
    }
}
