<?php
namespace Tests\Feature;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_a_request_to_reset_password_without_email_must_return_an_error(): void
    {
        $response = $this->postJson($this->getEndPoint() . 'forgot-password');

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }

    public function test_a_request_to_reset_password_with_an_incorrect_email_must_return_an_error(): void
    {
        $incorrectEmail = 'email';
        $response = $this->postJson($this->getEndPoint() . 'forgot-password', ['email' => $incorrectEmail]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }

    public function test_a_request_to_reset_password_with_unknown_email_must_not_generate_token(): void
    {
        $email = 'email.email@email.com';
        $response = $this->postJson($this->getEndPoint() . 'forgot-password', ['email' => $email]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('password_resets', ['email' => $email]);
    }

    public function test_a_request_to_reset_password_for_an_user_must_generate_token(): void
    {
        $user = User::factory()->create(['status' => 'VALIDATED']);
        $response = $this->postJson($this->getEndPoint() . 'forgot-password', ['email' => $user->email]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('password_resets', ['email' => $user->email]);
        // TODO : check an email has been sent
    }

    public function test_reset_password_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $response = $this->postJson($this->getEndPoint() . 'reset-password');

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password', 'token']);
    }

    public function test_reset_password_without_password_confirmation_must_return_an_error_with_the_list_of_errors(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->getEndPoint() . 'reset-password', [
            'email' => $user->email,
            'password' => 'azertyuiop',
            'password_confirmation' => 'toto',
            'token' => 'wrong token'
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors('token');
    }

    public function test_reset_password_for_an_unknow_token_must_return_an_error_with_the_list_of_errors(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson($this->getEndPoint() . 'reset-password', [
            'email' => $user->email,
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'token' => 'wrong token'
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors('token');
    }

    public function test_reset_password_for_a_known_user_must_return_a_correct_message_and_must_delete_the_used_token(): void
    {
        $user = User::factory()->create();
        $this->postJson($this->getEndPoint() . 'forgot-password', ['email' => $user->email]);

        $passwordReset = PasswordReset::where('email', $user->email)->first();

        $response = $this->postJson($this->getEndPoint() . 'reset-password', [
            'email' => $user->email,
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'token' => $passwordReset->token
        ]);

        $response->assertStatus(200)
        ->assertExactJson([
            'message' => 'The password has been modified.'
        ]);

        $this->assertDatabaseMissing('password_resets', ['email' => $user->email]);
    }
}
