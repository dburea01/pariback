<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SendEmailRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_register_an_user_without_body_must_return_an_error_with_the_list_of_errors(): void
    {
        $response = $this->postJson($this->getEndPoint().'register');

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'password', 'email']);
    }

    public function test_register_an_user_without_name_must_return_an_error(): void
    {
        $data = [
            'name' => '',
            'email' => 'email.email@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson($this->getEndPoint().'register', $data);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
    }

    public function test_register_an_user_with_a_wrong_email_must_return_an_error(): void
    {
        $data = [
            'name' => 'name',
            'email' => 'wrongemail',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson($this->getEndPoint().'register', $data);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }

    public function test_register_an_user_with_a_wrong_password_confirmation_must_return_an_error(): void
    {
        $data = [
            'name' => 'name',
            'email' => 'an.email@email.com',
            'password' => 'password',
            'password_confirmation' => 'passwordd',
        ];

        $response = $this->postJson($this->getEndPoint().'register', $data);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
    }

    public function test_register_an_user_with_a_password_too_short_must_return_an_error(): void
    {
        $data = [
            'name' => 'name',
            'email' => 'another.email@email.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ];

        $response = $this->postJson($this->getEndPoint().'register', $data);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
    }

    public function test_register_an_user_with_an_email_already_existing_must_return_an_error(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'name',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson($this->getEndPoint().'register', $data);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }

    public function test_register_an_user_with_correct_body_must_create_an_user(): void
    {
        Notification::fake();

        $data = [
            'name' => 'name',
            'email' => 'encoreunautre.email@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson($this->getEndPoint().'register', $data);

        $response->assertStatus(201);

        $user = User::where('email', $data['email'])->first();

        $this->assertEquals($user->name, $data['name']);
        $this->assertEquals($user->email, $data['email']);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->token_validation_registration);
        $this->assertEquals($user->status, 'CREATED');

        Notification::assertSentTo($user, SendEmailRegister::class);
    }
}
