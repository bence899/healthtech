<?php

namespace Tests\Feature\Auth;

use App\Models\Doctor;
use App\Models\User;
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

    public function test_new_patient_can_register(): void
    {
        $response = $this->post('/register', [
            'role' => 'patient',
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890',
            'address' => '123 Test Street',
            'date_of_birth' => '1999-01-01',
            'emergency_contact' => '0987654321'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'role' => 'patient',
        ]);
    }

    public function test_new_doctor_can_register(): void
    {
        $response = $this->post('/register', [
            'role' => 'doctor',
            'name' => 'Dr. John Smith',
            'email' => 'doctor@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890',
            'address' => '456 Medical Center',
            'date_of_birth' => '1980-01-01',
            'specialization' => 'Cardiology',
            'qualifications' => 'MD, MBBS',
            'experience' => '10 years of experience'
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'doctor@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('doctor', $user->role);

        $doctor = Doctor::where('user_id', $user->id)->first();
        $this->assertNotNull($doctor);
        $this->assertEquals('Cardiology', $doctor->specialization);
    }

    public function test_registration_validation_for_patient(): void
    {
        $response = $this->post('/register', [
            'role' => 'patient',
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'phone' => '',
            'date_of_birth' => '',
            'address' => '',
            'emergency_contact' => ''
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'phone',
            'date_of_birth',
            'emergency_contact'
        ]);
    }

    public function test_registration_validation_for_doctor(): void
    {
        $response = $this->post('/register', [
            'role' => 'doctor',
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'phone' => '',
            'date_of_birth' => '',
            'address' => '',
            'specialization' => '',
            'qualifications' => ''
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'phone',
            'date_of_birth',
            'address',
            'specialization',
            'qualifications'
        ]);
    }
}
