<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Appointment;
use Database\Seeders\DoctorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentStatusChanged;

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

    public function test_patient_can_cancel_pending_appointment(): void
    {
        Notification::fake();
        
        $this->seed(DoctorSeeder::class);
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $response = $this->actingAs($patient)
            ->patch("/appointments/{$appointment->id}/cancel");

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled'
        ]);

        Notification::assertSentTo(
            $patient,
            AppointmentStatusChanged::class,
            function ($notification) use ($appointment) {
                return $notification->getAppointment()->id === $appointment->id;
            }
        );
    }

    public function test_patient_cannot_cancel_completed_appointment(): void
    {
        $this->seed(DoctorSeeder::class);
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->subDays(1),
            'status' => 'completed',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $response = $this->actingAs($patient)
            ->patch("/appointments/{$appointment->id}/cancel");

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed'
        ]);
    }

    public function test_patient_cannot_cancel_other_patients_appointment(): void
    {
        $this->seed(DoctorSeeder::class);
        $patient1 = User::factory()->create(['role' => 'patient']);
        $patient2 = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $response = $this->actingAs($patient2)
            ->patch("/appointments/{$appointment->id}/cancel");

        $response->assertStatus(403);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'pending'
        ]);
    }

    public function test_notification_sent_when_appointment_cancelled(): void
    {
        Notification::fake();

        $this->seed(DoctorSeeder::class);
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $this->actingAs($patient)
            ->patch("/appointments/{$appointment->id}/cancel");
        
        Notification::assertSentTo(
            $patient,
            AppointmentStatusChanged::class,
            function ($notification) use ($appointment) {
                return $notification->getAppointment()->id === $appointment->id;
            }
        );
    }

    public function test_notification_contains_correct_appointment_details(): void
    {
        Notification::fake();
        
        $this->seed(DoctorSeeder::class);
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();
        $appointmentDate = now()->addDays(1);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $appointmentDate,
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $this->actingAs($patient)
            ->patch("/appointments/{$appointment->id}/cancel");

        Notification::assertSentTo(
            $patient,
            AppointmentStatusChanged::class,
            function ($notification) use ($doctor, $appointmentDate) {
                $mailMessage = $notification->toMail($notification->getAppointment()->patient);
                
                return $mailMessage->subject === 'Appointment Status Updated' &&
                       collect($mailMessage->introLines)->contains(fn($line) => 
                           str_contains($line, $doctor->user->name)
                       ) &&
                       collect($mailMessage->introLines)->contains(fn($line) => 
                           str_contains($line, $appointmentDate->format('M d, Y'))
                       );
            }
        );
    }

    public function test_notification_not_sent_for_invalid_cancellation(): void
    {
        Notification::fake();
        
        $this->seed(DoctorSeeder::class);
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->subDays(1),
            'status' => 'completed', // Already completed appointment
            'reason_for_visit' => 'Regular checkup'
        ]);

        $this->actingAs($patient)
            ->patch("/appointments/{$appointment->id}/cancel");

        // Assert no notification was sent
        Notification::assertNothingSent();
    }
} 