<?php
namespace Tests\Feature;

use App\Models\Country;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SportsTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_a_post_of_sport_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint() . 'sports');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'french_name', 'english_name', 'position']);
    }

    public function test_a_post_of_sport_with_wrong_body_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = [
            'id' => 'FOOT',
            'english_name' => 'foot test en',
            'french_name' => 'foot test fr',
            'position' => 'toto'
        ];
        $response = $this->postJson($this->getEndPoint() . 'sports', $country);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'position']);
    }

    public function test_a_post_of_sport_with_correct_body_must_create_the_sport(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'id' => 'SPORT',
            'english_name' => 'foot test en',
            'french_name' => 'foot test fr',
            'position' => '123'
        ];

        $response = $this->postJson($this->getEndPoint() . 'sports', $sport);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_sport());

        //  $sportId = json_decode($response->getContent(), true)['data']['id'];
        $sportCreated = Sport::find($sport['id']);

        $this->assertEquals($sport['id'], $sportCreated->id);
        $this->assertEquals($sport['english_name'], $sportCreated->getTranslation('name', 'en'));
        $this->assertEquals($sport['french_name'], $sportCreated->getTranslation('name', 'fr'));
        $this->assertEquals($sport['position'], $sportCreated->position);
        $this->assertNull($sportCreated->icon);
        $this->assertEquals('INACTIVE', $sportCreated->status);
    }

    public function test_a_post_of_an_existing_sport_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'id' => 'FOOT',
            'english_name' => 'foot test en',
            'french_name' => 'foot test fr',
            'position' => '123'
        ];

        $response = $this->postJson($this->getEndPoint() . 'sports', $sport);
        $response->assertStatus(422);
    }

    public function test_a_put_of_sport_with_wrong_body_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'position' => 'toto'
        ];
        $response = $this->putJson($this->getEndPoint() . 'sports/FOOT', $sport);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['position']);
    }

    public function test_a_put_of_sport_with_correct_body_must_update_the_sport(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'id' => 'FOOT',
            'english_name' => 'foot test en',
            'french_name' => 'foot test fr',
            'position' => '123'
        ];
        $response = $this->putJson($this->getEndPoint() . 'sports/FOOT', $sport);
        $response->assertStatus(200)
        ->assertJsonStructure($this->return_structure_sport());
    }

    public function test_delete_a_sport(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->deleteJson($this->getEndPoint() . 'sports/FOOT');
        $response->assertStatus(204);

        $this->assertDatabaseMissing('sports', ['id' => 'FOOT']);
    }

    public function test_access_to_an_unknown_sport_must_return_a_404(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->getJson($this->getEndPoint() . 'sports/TOTO');
        $response->assertStatus(404);
    }

    public function return_structure_sport(): array
    {
        return [
            'data' => [
                'id',
                'name',
                'icon',
                'status',
                'position'
            ]
        ];
    }
}
