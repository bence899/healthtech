<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_view_appointment_create_page(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($patient)->get('/appointments/create');
        $response->assertStatus(200);
    }

    public function test_patient_can_book_appointment(): void
    {
        $this->seed(DoctorSeeder::class);
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();

        $response = $this->actingAs($patient)->post('/appointments', [
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1)->setHour(10)->setMinute(0),
            'reason_for_visit' => 'Regular checkup'
        ]);

        $response->assertRedirect('/appointments');
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'pending'
        ]);
    }
} 