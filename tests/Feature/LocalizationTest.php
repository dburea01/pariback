<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;
    use Request;

    public function test_register_an_user_with_errors_in_french(): void
    {
        $response = $this->postJson($this->getEndPoint().'register', [], $this->setAcceptLanguageHeader('fr'));

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'password', 'email'])
        ->assertJsonFragment([
            'errors' => [
                'name' => [
                    'Le champ name est obligatoire.',
                ],
                'password' => [
                    'Le champ password est obligatoire.',
                ],
                'email' => [
                    'Le champ email est obligatoire.',
                ],
            ],
        ]);
    }

    public function test_register_an_user_with_errors_in_english(): void
    {
        $response = $this->postJson($this->getEndPoint().'register', [], $this->setAcceptLanguageHeader('en'));

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'password', 'email'])
        ->assertJsonFragment([
            'errors' => [
                'name' => [
                    'The name field is required.',
                ],
                'password' => [
                    'The password field is required.',
                ],
                'email' => [
                    'The email field is required.',
                ],
            ],
        ]);
    }

    public function test_register_an_user_with_errors_in_an_unknown_language(): void
    {
        $response = $this->postJson($this->getEndPoint().'register', [], $this->setAcceptLanguageHeader('toto'));

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'password', 'email'])
        ->assertJsonFragment([
            'errors' => [
                'name' => [
                    'The name field is required.',
                ],
                'password' => [
                    'The password field is required.',
                ],
                'email' => [
                    'The email field is required.',
                ],
            ],
        ]);
    }
}
