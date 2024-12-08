<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890',
            'address' => '123 Test Street',
            'date_of_birth' => '1999-01-01',
            'emergency_contact' => '0987654321'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'phone' => '1234567890',
            'address' => '123 Test Street',
            'date_of_birth' => '1999-01-01',
            'role' => 'patient',
            'emergency_contact' => '0987654321'
        ]);
    }
}
