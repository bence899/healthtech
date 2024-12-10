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
use App\Models\Patient;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_view_appointment_create_page(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        Patient::create(['user_id' => $patient->id]);

        $response = $this->actingAs($patient)->get('/appointments/create');
        $response->assertStatus(200);
    }

    public function test_patient_can_book_appointment(): void
    {
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('appointments.store'), [
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->addDays(1)->format('Y-m-d'),
                'appointment_time' => '14:00',
                'reason_for_visit' => 'Regular checkup'
            ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'pending'
        ]);
    }

    private function createPatientUser()
    {
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::factory()->create(['user_id' => $user->id]);
        return [$user, $patient];
    }

    public function test_patient_can_cancel_pending_appointment(): void
    {
        Notification::fake();
        
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::factory()->create();

        $appointment = Appointment::unguarded(function () use ($patient, $doctor) {
            return Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->addDays(1),
                'status' => 'pending',
                'reason_for_visit' => 'Regular checkup'
            ]);
        });

        $response = $this->actingAs($user)
            ->patch(route('appointments.cancel', $appointment));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled'
        ]);

        Notification::assertSentTo(
            $user,
            AppointmentStatusChanged::class
        );
    }

    public function test_patient_cannot_cancel_completed_appointment(): void
    {
        $this->seed(DoctorSeeder::class);
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->subDays(1),
            'status' => 'completed',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $response = $this->actingAs($user)
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
        $user1 = User::factory()->create(['role' => 'patient']);
        $patient1 = Patient::create(['user_id' => $user1->id]);
        $user2 = User::factory()->create(['role' => 'patient']);
        $patient2 = Patient::create(['user_id' => $user2->id]);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $response = $this->actingAs($user2)  // Login as user2
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
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $this->actingAs($user)
            ->patch("/appointments/{$appointment->id}/cancel");
        
        Notification::assertSentTo(
            $user,
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
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::first();
        $appointmentDate = now()->addDays(1);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $appointmentDate,
            'status' => 'pending',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $this->actingAs($user)
            ->patch("/appointments/{$appointment->id}/cancel");

        Notification::assertSentTo(
            $user,
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
        $user = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(1),
            'status' => 'completed',
            'reason_for_visit' => 'Regular checkup'
        ]);

        $this->actingAs($user)
            ->patch("/appointments/{$appointment->id}/cancel");

        Notification::assertNotSentTo(
            $user,
            AppointmentStatusChanged::class
        );
    }
}