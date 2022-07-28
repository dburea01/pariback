<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Phase;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhasesPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_you_must_not_be_authenticated_to_see_the_phases(): void
    {
        $competition1 = $this->create_a_competition();
        $phase1 = Phase::factory()->create(['competition_id' => $competition1->id]);
        $phase2 = Phase::factory()->create(['competition_id' => $competition1->id]);

        $response = $this->getJson($this->getEndPoint()."competitions/$competition1->id/phases");
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_as_admin_to_manage_the_phases(): void
    {
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $competition = $this->create_a_competition();
        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases");
        $response->assertStatus(403);

        $phase = Phase::factory()->create(['competition_id' => $competition->id]);

        $response = $this->putJson($this->getEndPoint()."competitions/$competition->id/phases/$phase->id");
        $response->assertStatus(403);

        $response = $this->deleteJson($this->getEndPoint()."competitions/$competition->id/phases/$phase->id");
        $response->assertStatus(403);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();
        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases");
        $response->assertStatus(422);

        $response = $this->putJson($this->getEndPoint()."competitions/$competition->id/phases/$phase->id");
        $response->assertStatus(422);

        $response = $this->deleteJson($this->getEndPoint()."competitions/$competition->id/phases/$phase->id");
        $response->assertStatus(204);
    }

    public function create_a_competition(): Competition
    {
        $country = Country::factory()->create(['id' => random_int(10, 99), 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'SPORT'.random_int(1, 99)]);

        return Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);
    }
}
