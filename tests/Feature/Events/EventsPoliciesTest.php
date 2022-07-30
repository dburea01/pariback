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
use Tests\TestCase;

class EventsPoliciesTest extends TestCase
{
    // use RefreshDatabase;
    use DatabaseMigrations;
    use Request;

    public function test_you_must_not_be_authenticated_to_see_the_events(): void
    {
        $this->seed();
        $phase = Phase::first();
        $event = Event::where('phase_id', $phase->id)->first();

        $response = $this->getJson($this->getEndPoint() . "phases/$phase->id/events");
        $response->assertStatus(200);
    }

    public function test_you_must_be_authenticated_as_admin_to_manage_the_events(): void
    {
        $this->seed();
        $phase = Phase::first();
        $event = Event::where('phase_id', $phase->id)->first();

        $userNotAdmin = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $this->actingAs($userNotAdmin);

        $response = $this->postJson($this->getEndPoint() . "phases/$phase->id/events");
        $response->assertStatus(403);

        $response = $this->putJson($this->getEndPoint() . "phases/$phase->id/events/$event->id");
        $response->assertStatus(403);

        $response = $this->deleteJson($this->getEndPoint() . "phases/$phase->id/events/$event->id");
        $response->assertStatus(403);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint() . "phases/$phase->id/events");
        $response->assertStatus(422);

        $response = $this->putJson($this->getEndPoint() . "phases/$phase->id/events/$event->id");
        $response->assertStatus(200);

        $response = $this->deleteJson($this->getEndPoint() . "phases/$phase->id/events/$event->id");
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
