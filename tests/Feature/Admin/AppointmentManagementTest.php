<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Patient;
use App\Notifications\AppointmentStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class AppointmentManagementTest extends AdminTestCase
{
    private $appointment;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = $this->createAdmin();
        
        // Create patient user and patient record
        $patientUser = User::factory()->create(['role' => 'patient']);
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'date_of_birth' => now()->subYears(30),
            'gender' => 'male',
            'phone' => '1234567890',
            'address' => '123 Test St'
        ]);
        
        $doctor = Doctor::factory()->create([
            'user_id' => User::factory()->create(['role' => 'doctor'])->id
        ]);
        
        $this->appointment = Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'pending'
        ]);
    }

    public function test_admin_can_view_appointments_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.appointments.index'));

        $response->assertStatus(200)
            ->assertViewHas('appointments')
            ->assertSee($this->appointment->patient->name)
            ->assertSee($this->appointment->doctor->user->name);
    }

    public function test_admin_can_filter_appointments_by_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.appointments.index', ['status' => 'pending']));

        $response->assertStatus(200)
            ->assertSee($this->appointment->patient->name);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.appointments.index', ['status' => 'completed']));

        $response->assertStatus(200)
            ->assertDontSee($this->appointment->patient->name);
    }

    public function test_admin_can_search_appointments(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.appointments.index', [
                'search' => $this->appointment->patient->name
            ]));

        $response->assertStatus(200)
            ->assertSee($this->appointment->patient->name);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.appointments.index', ['search' => 'nonexistent']));

        $response->assertStatus(200)
            ->assertDontSee($this->appointment->patient->name);
    }

    public function test_admin_can_update_appointment_status(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.appointments.update-status', $this->appointment), [
                'status' => 'confirmed'
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->appointment->refresh();
        $this->assertEquals('confirmed', $this->appointment->status);

        Notification::assertSentTo(
            $this->appointment->patient->user,
            AppointmentStatusChanged::class
        );
    }

    public function test_admin_cannot_update_appointment_with_invalid_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.appointments.update-status', $this->appointment), [
                'status' => 'invalid-status'
            ]);

        $response->assertSessionHasErrors('status');
        
        $this->appointment->refresh();
        $this->assertEquals('pending', $this->appointment->status);
    }
} 