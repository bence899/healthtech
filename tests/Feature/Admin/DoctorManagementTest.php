<?php

namespace Tests\Feature\Admin;

use App\Models\Doctor;
use Tests\Feature\Admin\AdminTestCase;

class DoctorManagementTest extends AdminTestCase
{
    public function test_admin_can_view_doctors_list(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('admin.doctors.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_doctor(): void
    {
        $admin = $this->createAdmin();
        
        $response = $this->actingAs($admin)->post(route('admin.doctors.store'), [
            'name' => 'Dr. Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '1234567890',
            'address' => '456 Medical Center',
            'date_of_birth' => '1985-01-01',
            'specialization' => 'Cardiology',
            'qualifications' => 'MD, PhD',
            'experience' => '10 years of experience',
            'working_hours' => [
                'monday' => ['09:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-17:00'],
                'thursday' => ['09:00-17:00'],
                'friday' => ['09:00-17:00']
            ]
        ]);

        $response->assertRedirect(route('admin.doctors.index'));
        
        $this->assertDatabaseHas('users', [
            'name' => 'Dr. Jane Doe',
            'email' => 'jane.doe@example.com',
            'role' => 'doctor'
        ]);

        $doctor = Doctor::whereHas('user', function($query) {
            $query->where('email', 'jane.doe@example.com');
        })->first();

        $this->assertNotNull($doctor);
        $this->assertEquals('Cardiology', $doctor->specialization);
    }

    public function test_non_admin_cannot_create_doctor(): void
    {
        $patient = $this->createPatient();
        
        $response = $this->actingAs($patient)->post(route('admin.doctors.store'), [
            'name' => 'Dr. Jane Doe',
            'email' => 'jane.doe@example.com',
            // ... other fields
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_create_doctor_with_invalid_data(): void
    {
        $admin = $this->createAdmin();
        
        $response = $this->actingAs($admin)->post(route('admin.doctors.store'), [
            'name' => '', // Invalid: empty name
            'email' => 'not-an-email', // Invalid: wrong email format
            'password' => 'short', // Invalid: too short
            // Missing required fields
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }
} 