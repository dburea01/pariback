<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidateRegistrationTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_validate_a_registration_without_body_must_return_an_error(): void
    {
        $response = $this->postJson($this->getEndPoint().'validate-registration');

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'token_validation_registration']);
    }

    public function test_validate_a_registration_with_a_wrong_couple_email_token_must_return_an_error(): void
    {
        $user = User::factory()->create(['status' => 'CREATED']);

        $response = $this->postJson($this->getEndPoint().'validate-registration', [
            'email' => $user->email,
            'token_validation_registration' => 'azerty',
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['token_validation_registration']);

        $userInDatabase = User::find($user->id);
        $this->assertEquals($userInDatabase->status, 'CREATED');
    }

    public function test_validate_a_registration_with_a_correct_couple_email_token_must_validate_the_user(): void
    {
        $user = User::factory()->create(['status' => 'CREATED']);

        $response = $this->postJson($this->getEndPoint().'validate-registration', [
            'email' => $user->email,
            'token_validation_registration' => $user->token_validation_registration,
        ]);

        $response->assertStatus(200);

        $userInDatabase = User::find($user->id);
        $this->assertEquals($userInDatabase->status, 'VALIDATED');
        $this->assertNotNull($userInDatabase->email_verified_at);
    }
}
