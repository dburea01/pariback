<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Participation;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipationsTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_a_post_of_participation_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'participations');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['team_id', 'competition_id']);
    }

    public function test_a_post_of_participation_with_wrong_body_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $participation = [
            'team_id' => 'b13932cd-3f1e-49c9-ba18-819b2556e3d2',
            'competition_id' => 'b13932cd-3f1e-49c9-ba18-819b2556e3d2',
        ];
        $response = $this->postJson($this->getEndPoint().'participations', $participation);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['team_id', 'competition_id']);
    }

    public function test_a_post_of_participation_with_correct_body_must_create_the_participation(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);
        $team = Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);
        $competition = Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);

        $participation = [
            'team_id' => $team->id,
            'competition_id' => $competition->id,
        ];

        $response = $this->postJson($this->getEndPoint().'participations', $participation);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_participation());

        $participationId = json_decode($response->getContent(), true)['data']['id'];
        $participationCreated = Participation::find($participationId);

        $this->assertEquals($participation['team_id'], $participationCreated->team_id);
        $this->assertEquals($participation['competition_id'], $participationCreated->competition_id);
    }

    public function test_a_post_of_an_existing_participation_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $participation = $this->create_a_participation();
        $participationToCreate = [
            'team_id' => $participation->team_id,
            'competition_id' => $participation->competition_id,
        ];

        $response = $this->postJson($this->getEndPoint().'participations', $participationToCreate);
        $response->assertStatus(422);
    }

    public function test_a_post_of_a_participation_of_a_team_with_incorrect_sport_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sportA = Sport::factory()->create(['id' => 'SPORTA']);
        $sportB = Sport::factory()->create(['id' => 'SPORTB']);
        $team = Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sportA->id]);
        $competition = Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sportB->id]);

        $participation = [
            'team_id' => $team->id,
            'competition_id' => $competition->id,
        ];

        $response = $this->postJson($this->getEndPoint().'participations', $participation);
        $response->assertStatus(422)->assertJsonValidationErrors(['team_id']);
    }

    public function test_delete_a_participation(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $participation = $this->create_a_participation();

        $response = $this->deleteJson($this->getEndPoint()."participations/$participation->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('participations', ['id' => $participation->id]);
    }

    public function test_access_to_an_unknown_participation_must_return_a_404(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->getJson($this->getEndPoint().'participations/TOTO');
        $response->assertStatus(404);
    }

    public function return_structure_participation(): array
    {
        return [
            'data' => [
                'id',
                'team',
                'competition',
            ],
        ];
    }

    public function create_a_participation(): Participation
    {
        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);
        $team = Team::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);
        $competition = Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);

        return Participation::factory()->create(['team_id' => $team->id, 'competition_id' => $competition->id]);
    }
}
