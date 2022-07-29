<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Country;
use App\Models\Phase;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhasesTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_get_the_phases_of_a_competition(): void
    {
        $competition1 = $this->create_a_competition();
        $phase1 = Phase::factory()->create(['competition_id' => $competition1->id]);
        $phase2 = Phase::factory()->create(['competition_id' => $competition1->id]);

        $competition2 = $this->create_a_competition();
        $phase3 = Phase::factory()->create(['competition_id' => $competition2->id]);
        $phase4 = Phase::factory()->create(['competition_id' => $competition2->id]);

        $response = $this->getJson($this->getEndPoint()."competitions/$competition1->id/phases");
        $response->assertStatus(200);

        $returnedJson = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(2, count($returnedJson));
    }

    public function test_a_post_of_phase_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();
        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases");
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['short_name', 'english_name', 'french_name', 'start_date', 'end_date', 'dates']);
    }

    public function test_a_post_of_phase_with_wrong_body_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();
        $phase = [
            'short_name' => 'A',
            'english_name' => 'EN',
            'french_name' => 'FR',
            'start_date' => 'toto',
            'end_date' => 'titi',
            'status' => 'TUTU',
        ];

        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases", $phase);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['short_name', 'english_name', 'french_name', 'start_date', 'end_date', 'dates']);
    }

    public function test_a_post_of_phase_with_end_date_gt_start_date_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();

        $phase = [
            'short_name' => 'short',
            'english_name' => 'name EN',
            'french_name' => 'name FR',
            'start_date' => '2022-07-28',
            'end_date' => '2022-07-27',
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases", $phase);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['end_date']);
    }

    public function test_a_post_of_phase_with_wrong_start_date_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();

        $phase = [
            'short_name' => 'short',
            'english_name' => 'name EN',
            'french_name' => 'name FR',
            'start_date' => Carbon::createFromFormat('Y-m-d', $competition->start_date)->subDays(1),
            'end_date' => $competition->end_date,
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases", $phase);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['dates']);
    }

    public function test_a_post_of_phase_with_wrong_end_date_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();

        $phase = [
            'short_name' => 'short',
            'english_name' => 'name EN',
            'french_name' => 'name FR',
            'start_date' => $competition->start_date,
            'end_date' => '2022-12-31',
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases", $phase);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['dates']);
    }

    public function test_a_post_of_an_existing_phase_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();
        $phase = Phase::factory()->create([
            'competition_id' => $competition->id,
            'short_name' => 'SHORTNAME',
        ]);

        $phase = [
            'short_name' => 'SHORTNAME',
            'english_name' => 'name EN',
            'french_name' => 'name FR',
            'start_date' => $competition->start_date,
            'end_date' => '2022-12-31',
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases", $phase);
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['short_name']);
    }

    public function test_a_post_of_phase_with_correct_body_must_create_the_phase(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();

        $phase = [
            'short_name' => 'short',
            'english_name' => 'name EN',
            'french_name' => 'name FR',
            'start_date' => $competition->start_date,
            'end_date' => $competition->end_date,
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint()."competitions/$competition->id/phases", $phase);
        $response->assertStatus(201)
        ->assertJsonStructure($this->return_structure_phase());

        $phaseId = json_decode($response->getContent(), true)['data']['id'];
        $phaseCreated = Phase::find($phaseId);

        $this->assertEquals($phase['short_name'], $phaseCreated->short_name);
        // $this->assertEquals($phase['start_date'], $phaseCreated->start_date);
        // $this->assertEquals($phase['end_date'], $phaseCreated->end_date);
        $this->assertEquals($phase['status'], $phaseCreated->status);
        $this->assertEquals($phase['english_name'], $phaseCreated->getTranslation('name', 'en'));
        $this->assertEquals($phase['french_name'], $phaseCreated->getTranslation('name', 'fr'));
    }

    public function test_a_post_of_phase_for_an_unknown_competition_must_return_an_error(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $phase = [
            'short_name' => 'short',
            'english_name' => 'name EN',
            'french_name' => 'name FR',
            'start_date' => '2022-07-31',
            'end_date' => '2022-07-31',
            'status' => 'ACTIVE',
        ];

        $response = $this->postJson($this->getEndPoint().'competitions/toto/phases', $phase);

        $response->assertStatus(404);
    }

    public function test_delete_a_phase(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $phase = $this->create_a_phase();

        $response = $this->deleteJson($this->getEndPoint()."competitions/$phase->competition_id/phases/$phase->id");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('phases', ['id' => $phase->id]);
    }

    public function test_access_to_an_unknown_phase_must_return_a_404(): void
    {
        $userAdmin = User::factory()->create(['is_admin' => true, 'status' => 'VALIDATED']);
        $this->actingAs($userAdmin);

        $competition = $this->create_a_competition();
        $response = $this->getJson($this->getEndPoint()."competitions/$competition->id/phases/TOTO");
        $response->assertStatus(404);
    }

    public function return_structure_phase(): array
    {
        return [
            'data' => [
                'id',
                'competition_id',
                'short_name',
                'name',
                'start_date',
                'end_date',
                'status',
            ],
        ];
    }

    public function create_a_competition(): Competition
    {
        $country = Country::factory()->create(['id' => random_int(10, 99), 'name' => 'country CC', 'position' => 10]);
        $sport = Sport::factory()->create(['id' => 'SPORT'.random_int(1, 99)]);

        return Competition::factory()->create(['country_id' => $country->id, 'sport_id' => $sport->id]);
    }

    public function create_a_phase(): Phase
    {
        $competition = $this->create_a_competition();

        return Phase::factory()->create(['competition_id' => $competition->id]);
    }
}
