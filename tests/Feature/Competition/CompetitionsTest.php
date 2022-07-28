<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionsTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_a_post_of_competition_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint().'competitions');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['country_id', 'sport_id', 'short_name', 'english_name', 'french_name', 'position', 'start_date', 'end_date']);
    }

    public function test_a_post_of_competition_with_wrong_body_must_return_an_error(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = [
            'country_id' => 'TOTO',
            'sport_id' => 'TITI',
            'english_name' => 'competition test en',
            'french_name' => 'competition test fr',
            'position' => 'RR',
            'icon' => UploadedFile::fake()->image('fake_image.jpg', 400, 400),
        ];
        $response = $this->postJson($this->getEndPoint().'competitions', $competition);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['country_id', 'sport_id', 'position', 'icon', 'short_name', 'start_date', 'end_date']);
    }

    public function test_a_post_of_competition_with_correct_body_must_create_the_competition(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $competition = [
            'country_id' => $country->id,
            'sport_id' => $sport->id,
            'english_name' => 'competition test en',
            'french_name' => 'competition test fr',
            'position' => '10',
            'short_name' => $country->id.'_'.$sport->id,
            'icon' => UploadedFile::fake()->image('fake_image.jpg', 100, 100),
            'start_date' => '2022-07-27',
            'end_date' => '2022-07-31',
        ];

        $response = $this->postJson($this->getEndPoint().'competitions', $competition);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_competition());

        $competitionId = json_decode($response->getContent(), true)['data']['id'];
        $competitionCreated = Competition::find($competitionId);

        $this->assertEquals($competition['english_name'], $competitionCreated->getTranslation('name', 'en'));
        $this->assertEquals($competition['french_name'], $competitionCreated->getTranslation('name', 'fr'));
        $this->assertEquals($competition['position'], $competitionCreated->position);
        $this->assertEquals('competition_'.$competition['short_name'].'.jpg', $competitionCreated->icon);
        $this->assertEquals('INACTIVE', $competitionCreated->status);
        $this->assertEquals($competition['start_date'], $competitionCreated->start_date);
        $this->assertEquals($competition['end_date'], $competitionCreated->end_date);
    }

    public function test_a_put_of_sport_with_correct_body_must_update_the_sport(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $competition = Competition::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
        ]);

        $response = $this->putJson($this->getEndPoint()."competitions/$competition->id", ['french_name' => 'toto']);
        $response->assertStatus(200)
        ->assertJsonStructure($this->return_structure_competition());

        $competitionUpdated = Competition::find($competition->id);
        $this->assertEquals('toto', $competitionUpdated->getTranslation('name', 'fr'));
    }

    public function test_delete_a_competition(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $competition = Competition::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
        ]);

        $response = $this->deleteJson($this->getEndPoint()."competitions/$competition->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('competitions', ['id' => $competition->id]);
    }

    public function test_access_to_an_unknown_competition_must_return_a_404(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->getJson($this->getEndPoint().'competitions/TOTO');
        $response->assertStatus(404);
    }

    public function return_structure_competition(): array
    {
        return [
            'data' => [
                'id',
                'country_id',
                'sport_id',
                'sport',
                'short_name',
                'name',
                'position',
                'icon',
                'icon_url',
                'status',
                'start_date',
                'end_date',
            ],
        ];
    }
}
