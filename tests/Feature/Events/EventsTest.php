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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_get_events_of_phase(): void
    {
        $this->insert_data();

        $phase = Phase::first();
        $countEvents = Event::where('phase_id', $phase->id)->count();
        $response = $this->getJson($this->getEndPoint()."phases/$phase->id/events");
        $response->assertStatus(200);

        $eventsReturned = json_decode($response->getContent(), true)['data'];

        $this->assertEquals($countEvents, count($eventsReturned));
        $this->assertGreaterThan($eventsReturned[0]['date'], $eventsReturned[1]['date']);
    }

    public function test_a_post_of_event_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->postJson($this->getEndPoint()."phases/$phase->id/events");
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['team1_id', 'team2_id', 'date', 'status']);
    }

    public function test_a_post_of_event_with_wrong_body_must_return_an_error(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $event = [
            'team1_id' => 'toto',
            'team2_id' => 'titi',
            'date' => '2022-07-1234',
            'status' => 'tutu',
        ];
        $response = $this->postJson($this->getEndPoint()."phases/$phase->id/events");
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['team1_id', 'team2_id', 'date', 'status']);
    }

    public function test_a_post_of_event_with_two_same_teams_must_return_an_error(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $participations = Participation::where('competition_id', $phase->competition_id)->get();

        $event = [
            'team1_id' => $participations[0]->team_id,
            'team2_id' => $participations[0]->team_id,
            'date' => '2022-07-12 21:00',
            'status' => 'PLANNED',
        ];

        $response = $this->postJson($this->getEndPoint()."phases/$phase->id/events", $event);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['team2_id']);
    }

    public function test_a_post_of_event_with_a_team_already_present_must_return_an_error(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $competition = Competition::find($phase->competition_id);
        $country = Country::find($competition->country_id);
        $sport = Sport::find($competition->sport_id);

        $team1 = Team::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
            'short_name' => 'TEAM1',
        ]);
        Participation::factory()->create(['competition_id' => $competition->id, 'team_id' => $team1->id]);

        $team2 = Team::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
            'short_name' => 'TEAM2',
        ]);
        Participation::factory()->create(['competition_id' => $competition->id, 'team_id' => $team2->id]);

        Event::factory()->create(['phase_id' => $phase->id, 'team1_id' => $team1->id, 'team2_id' => $team2->id]);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $event = [
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'date' => '2022-07-12 21:00',
            'status' => 'PLANNED',
            'location' => 'location',
        ];

        $response = $this->postJson($this->getEndPoint()."phases/$phase->id/events", $event);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['team1_id', 'team2_id']);
    }

    public function test_a_post_of_event_with_correct_body_must_create_the_event(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $competition = Competition::find($phase->competition_id);
        $country = Country::find($competition->country_id);
        $sport = Sport::find($competition->sport_id);

        $team1 = Team::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
            'short_name' => 'TEAM1',
        ]);

        $team2 = Team::factory()->create([
            'country_id' => $country->id,
            'sport_id' => $sport->id,
            'short_name' => 'TEAM2',
        ]);

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        Participation::factory()->create(['competition_id' => $competition->id, 'team_id' => $team1->id]);
        Participation::factory()->create(['competition_id' => $competition->id, 'team_id' => $team2->id]);

        $event = [
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'date' => '2022-07-12 21:00',
            'status' => 'PLANNED',
            'location' => 'location',
        ];

        $response = $this->postJson($this->getEndPoint()."phases/$phase->id/events", $event);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_event());

        $eventId = json_decode($response->getContent(), true)['data']['id'];
        $eventCreated = Event::find($eventId);

        $this->assertEquals($event['location'], $eventCreated->location);
        $this->assertEquals($event['date'], $eventCreated->date);
        $this->assertEquals($event['status'], $eventCreated->status);
        $this->assertEquals($event['team1_id'], $eventCreated->team1->id);
        $this->assertEquals($event['team2_id'], $eventCreated->team2->id);
    }

    public function test_a_put_of_event_with_correct_body_must_update_the_event(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $event = Event::where('phase_id', $phase->id)->first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $eventToUpdate = [
            'date' => '2022-07-12 21:00',
            'status' => 'TERMINATED',
            'location' => 'location modified',
            'score_team1' => 3,
            'score_team2' => 1,
        ];
        $response = $this->putJson($this->getEndPoint()."phases/$phase->id/events/$event->id", $eventToUpdate);
        $response->assertStatus(200)
        ->assertJsonStructure($this->return_structure_event());

        $eventId = json_decode($response->getContent(), true)['data']['id'];
        $eventUpdated = Event::find($eventId);

        $this->assertEquals($eventToUpdate['location'], $eventUpdated->location);
        $this->assertEquals($eventToUpdate['date'], $eventUpdated->date);
        $this->assertEquals($eventToUpdate['status'], $eventUpdated->status);
        $this->assertEquals($eventToUpdate['score_team1'], $eventUpdated->score_team1);
        $this->assertEquals($eventToUpdate['score_team2'], $eventUpdated->score_team2);
    }

    public function test_delete_an_event(): void
    {
        $this->insert_data();
        $phase = Phase::first();
        $event = Event::where('phase_id', $phase->id)->first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->deleteJson($this->getEndPoint()."phases/$phase->id/events/$event->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_access_to_an_unknown_event_must_return_a_404(): void
    {
        $this->insert_data();
        $phase = Phase::first();

        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $response = $this->getJson($this->getEndPoint()."phases/$phase->id/events/0200325d-1ccb-47fb-ae9d-a790569b1ec6");
        $response->assertStatus(404);
    }

    public function return_structure_event(): array
    {
        return [
            'data' => [
                'id',
                'team1',
                'team2',
                'date',
                'location',
                'status',
            ],
        ];
    }
}
