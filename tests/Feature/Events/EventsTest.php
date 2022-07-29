<?php
namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Phase;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventsTest extends TestCase
{
    // use RefreshDatabase;
    use DatabaseMigrations;
    use Request;

    public function test_a_get_of_events_of_phase(): void
    {
        $this->seed();
        $phase = Phase::first();
        $events = Event::where('phase_id', $phase->id)->get();

        $response = $this->getJson($this->getEndPoint() . "phases/$phase->id/events");
        $response->assertStatus(200)->dump();

        $eventsReturned = json_decode($response->getContent(), true)['data'];

        $this->assertEquals(count($events), count($eventsReturned));
        $this->assertGreaterThan($eventsReturned[0]['date'], $eventsReturned[1]['date']);
    }

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
            'position' => 'toto',
        ];
        $response = $this->postJson($this->getEndPoint() . 'sports', $country);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['id', 'position']);
    }

    public function test_a_post_of_sport_with_correct_body_must_create_the_sport(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'id' => 'SPORT',
            'english_name' => 'foot test en',
            'french_name' => 'foot test fr',
            'position' => '123',
            'icon' => UploadedFile::fake()->image('fake_image.jpg'),
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
        $this->assertEquals('sport_SPORT.jpg', $sportCreated->icon);
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
            'position' => '123',
        ];

        $response = $this->postJson($this->getEndPoint() . 'sports', $sport);
        $response->assertStatus(422);
    }

    public function test_a_put_of_sport_with_wrong_body_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'position' => 'toto',
        ];
        $response = $this->putJson($this->getEndPoint() . 'sports/FOOT', $sport);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['position']);
    }

    public function test_a_put_of_sport_with_correct_body_must_update_the_sport(): void
    {
        Storage::fake('local');

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $sport = [
            'id' => 'FOOT',
            'english_name' => 'foot test en',
            'french_name' => 'foot test fr',
            'position' => '123',
            'icon' => UploadedFile::fake()->image('fake_sport.jpg'),
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
                'position',
            ],
        ];
    }
}
