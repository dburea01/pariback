<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BettorsTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_get_the_bettors_of_a_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);
        $this->create_bettors($bet);

        $this->actingAs($user);
        $response = $this->getJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(200);

        $betsReturned = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(5, count($betsReturned));
    }

    public function test_a_post_of_bettor_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);
        $this->create_bettors($bet);

        $this->actingAs($user);

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors");
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_a_post_of_bettor_already_existing_must_return_an_error(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);

        $this->create_bettors($bet);
        $existingBettor = Bettor::where('bet_id', $bet->id)->first();
        $existingUser = User::find($existingBettor->user_id);
        $this->actingAs($user);

        $bettorToPost = [
            'name' => 'name',
            'email' => $existingUser->email,
        ];
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors", $bettorToPost);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }

    public function test_a_post_of_bet_with_correct_body_must_create_the_bet(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($user);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);

        $this->create_bettors($bet);

        $bettorToPost = [
            'name' => 'name',
            'email' => 'test.test@test.fr',
        ];

        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors", $bettorToPost);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_bettor());

        $bettorCreated = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($bettorToPost['name'], $bettorCreated['user']['name']);
        $this->assertEquals($bettorToPost['email'], $bettorCreated['user']['email']);
    }

    public function test_delete_a_bettor(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($user);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id]);

        $this->create_bettors($bet);
        $bettorToDelete = Bettor::where('bet_id', $bet->id)->first();

        $response = $this->deleteJson($this->getEndPoint()."bets/$bet->id/bettors/$bettorToDelete->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('bettors', ['id' => $bettorToDelete->id]);
    }

    public function return_structure_bettor(): array
    {
        return [
            'data' => [
                'id',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ];
    }

    public function create_bettors(Bet $bet)
    {
        $users = User::factory()->count(5)->create();
        foreach ($users as $user) {
            Bettor::factory()->create(['bet_id' => $bet->id, 'user_id' => $user->id]);
        }
    }
}
