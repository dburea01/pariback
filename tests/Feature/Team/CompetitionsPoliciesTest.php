<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionsPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_you_must_not_be_authenticated_to_see_the_competitions(): void
    {
        $response = $this->getJson($this->getEndPoint().'competitions');
        $response->assertStatus(200);
    }

    public function test_you_must_not_be_authenticated_to_see_a_competition(): void
    {
        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $competition = Competition::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
        ]);

        $response = $this->getJson($this->getEndPoint()."competitions/$competition->id");
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_as_admin_to_manage_the_competitions(): void
    {
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $response = $this->postJson($this->getEndPoint().'competitions');
        $response->assertStatus(403);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $competition = Competition::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
        ]);

        $response = $this->putJson($this->getEndPoint()."competitions/$competition->id");
        $response->assertStatus(403);

        $response = $this->deleteJson($this->getEndPoint()."competitions/$competition->id");
        $response->assertStatus(403);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'competitions');
        $response->assertStatus(422);

        $response = $this->putJson($this->getEndPoint()."competitions/$competition->id");
        $response->assertStatus(200);

        $response = $this->deleteJson($this->getEndPoint()."competitions/$competition->id");
        $response->assertStatus(204);
    }
}
