<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountriesPoliciesTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_you_must_not_be_authenticated_to_see_the_coutries(): void
    {
        $response = $this->getJson($this->getEndPoint().'countries');
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_as_admin_to_manage_the_countries(): void
    {
        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $response = $this->postJson($this->getEndPoint().'countries');
        $response->assertStatus(403);

        $response = $this->putJson($this->getEndPoint().'countries/FR');
        $response->assertStatus(403);

        $response = $this->deleteJson($this->getEndPoint().'countries/FR');
        $response->assertStatus(403);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'countries');
        $response->assertStatus(422);

        $response = $this->deleteJson($this->getEndPoint().'countries/FR');
        $response->assertStatus(204);
    }
}
