<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->create(['role' => 'patient'])->id,
            'doctor_id' => Doctor::factory(),
            'appointment_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'reason_for_visit' => $this->faker->sentence(),
            'notes' => $this->faker->paragraph()
        ];
    }
}