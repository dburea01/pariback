<?php
namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Void_;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_a_post_of_team_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint() . 'teams');
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['country_id', 'sport_id', 'short_name', 'name', 'city', 'icon']);
    }

    public function test_a_post_of_team_with_wrong_body_must_return_an_error(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $team = [
            'country_id' => 'TOTO',
            'sport_id' => 'TITI',
            'short_name' => 'shortname',
            'name' => 'name',
            'city' => 'city',
            'icon' => UploadedFile::fake()->image('fake_image.jpg', 400, 400),
        ];
        $response = $this->postJson($this->getEndPoint() . 'teams', $team);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['country_id', 'sport_id', 'icon']);
    }

    public function test_the_short_name_of_a_team_must_be_unique(): void
    {
        // todo
    }

    public function test_a_post_of_team_with_correct_body_must_create_the_team(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $team = [
            'country_id' => $country->id,
            'sport_id' => $sport->id,
            'short_name' => 'TEAM',
            'name' => 'team name',
            'city' => 'City',
            'icon' => UploadedFile::fake()->image('fake_image.jpg', 100, 100),
        ];

        $response = $this->postJson($this->getEndPoint() . 'teams', $team);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_team());

        $teamId = json_decode($response->getContent(), true)['data']['id'];
        $teamCreated = Team::find($teamId);

        $this->assertEquals($team['short_name'], $teamCreated->short_name);
        $this->assertEquals($team['name'], $teamCreated->name);
        $this->assertEquals($team['city'], $teamCreated->city);
        $this->assertEquals($team['country_id'], $teamCreated->country_id);
        $this->assertEquals($team['sport_id'], $teamCreated->sport_id);
        $this->assertEquals('INACTIVE', $teamCreated->status);
    }

    public function test_a_put_of_team_with_correct_body_must_update_the_team(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $team = Team::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
        ]);

        $response = $this->putJson($this->getEndPoint() . "teams/$team->id", ['city' => 'City updated']);
        $response->assertStatus(200)
        ->assertJsonStructure($this->return_structure_team());

        $teamUpdated = Team::find($team->id);
        $this->assertEquals('City updated', $teamUpdated->city);
    }

    public function test_delete_a_team(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $country = Country::factory()->create(['id' => 'CC', 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'GOLF']);

        $team = Team::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
        ]);

        $response = $this->deleteJson($this->getEndPoint() . "teams/$team->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_access_to_an_unknown_team_must_return_a_404(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->getJson($this->getEndPoint() . 'teams/TOTO');
        $response->assertStatus(404);
    }

    public function return_structure_team(): array
    {
        return [
            'data' => [
                'id',
                'short_name',
                'name',
                'city',
                'icon_url',
                'status',
                'sport',
                'country'
            ],
        ];
    }
}
