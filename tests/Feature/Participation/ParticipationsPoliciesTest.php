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

class ParticipationsPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_you_must_not_be_authenticated_to_see_the_participations(): void
    {
        $response = $this->getJson($this->getEndPoint().'participations');
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_as_admin_to_manage_the_participations(): void
    {
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $response = $this->postJson($this->getEndPoint().'participations');
        $response->assertStatus(403);

        $participation = $this->create_a_participation();

        $response = $this->deleteJson($this->getEndPoint()."participations/$participation->id");
        $response->assertStatus(403);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'participations');
        $response->assertStatus(422);

        $response = $this->deleteJson($this->getEndPoint()."participations/$participation->id");
        $response->assertStatus(204);
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
