<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CountriesTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_a_post_of_country_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'countries');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'french_name', 'english_name', 'position']);
    }

    public function test_a_post_of_country_with_wrong_body_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = [
            'id' => 'TOTO',
            'local_name' => 'test',
            'english_name' => 'test',
            'position' => 'toto',
        ];
        $response = $this->postJson($this->getEndPoint().'countries', $country);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'position']);
    }

    public function test_a_post_of_country_with_correct_body_must_create_the_country(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = [
            'id' => 'ES',
            'french_name' => 'Espagne',
            'english_name' => 'Spain',
            'position' => '10',
            'icon' => UploadedFile::fake()->image('fake_image.jpg', 100, 100),
        ];

        $response = $this->postJson($this->getEndPoint().'countries', $country);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_country());

        $countryCreated = Country::find($country['id']);

        $this->assertEquals($country['id'], $countryCreated->id);
        $this->assertEquals($country['english_name'], $countryCreated->name);
        $this->assertEquals($country['position'], $countryCreated->position);
        $this->assertEquals('country_ES.jpg', $countryCreated->icon);
        $this->assertEquals('INACTIVE', $countryCreated->status);
    }

    public function test_a_post_of_an_existing_country_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = [
            'id' => 'FR',
            'local_name' => 'France',
            'english_name' => 'France',
            'position' => '10',
        ];

        $response = $this->postJson($this->getEndPoint().'countries', $country);
        $response->assertStatus(422);
    }

    public function test_a_put_of_country_with_correct_body_must_update_the_country(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = [
            'local_name' => 'fr name modifié',
            'english_name' => 'english name modified',
            'position' => '123',
        ];
        $response = $this->putJson($this->getEndPoint().'countries/FR', $country);
        $response->assertStatus(200);

        //@todo : test with image uopdated
        // ->assertJsonStructure($this->return_structure_country());
    }

    public function test_delete_a_country(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->deleteJson($this->getEndPoint().'countries/FR');
        $response->assertStatus(204);

        $this->assertDatabaseMissing('countries', ['id' => 'FR']);
    }

    public function test_access_to_an_unknown_country_must_return_a_404(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->deleteJson($this->getEndPoint().'countries/TOTO');
        $response->assertStatus(404);
    }

    public function return_structure_country(): array
    {
        return [
            'data' => [
                'id',
                'name',
                'icon',
                'icon_url',
                'status',
                'position',
            ],
        ];
    }
}
