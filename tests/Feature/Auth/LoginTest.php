<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_login_without_body_must_return_an_error(): void
    {
        $response = $this->postJson($this->getEndPoint().'login');

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_with_unknown_couple_email_password_must_return_an_error(): void
    {
        $response = $this->postJson($this->getEndPoint().'login', [
            'email' => 'email',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_with_wrong_couple_email_password_must_return_an_error(): void
    {
        $user = User::factory()->create(['status' => 'VALIDATED']);

        $response = $this->postJson($this->getEndPoint().'login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_with_an_user_not_validated_must_return_an_error(): void
    {
        $user = User::factory()->create(['status' => 'CREATED', 'password' => Hash::make('password')]);

        $response = $this->postJson($this->getEndPoint().'login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_with_correct_couple_email_password_must_return_a_token(): void
    {
        $user = User::factory()->create(['status' => 'VALIDATED', 'password' => Hash::make('password')]);

        $response = $this->postJson($this->getEndPoint().'login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_logout_without_token_bearer_must_return_an_error(): void
    {
        $response = $this->postJson($this->getEndPoint().'logout');

        $response->assertStatus(401);
    }

    public function test_logout_with_token_bearer_must_return_a_confirmation_message(): void
    {
        $user = User::factory()->create(['status' => 'VALIDATED', 'password' => Hash::make('password')]);

        $response = $this->postJson($this->getEndPoint().'login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
            ],
        ]);

        $token = json_decode($response->getContent(), true)['token'];

        $response = $this->postJson($this->getEndPoint().'logout', [], $this->setAuthorizationHeader($token));

        $response->assertStatus(200);
        $response->assertExactJson([
            'message' => 'User deconnected',
        ]);
    }
}
