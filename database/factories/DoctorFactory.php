<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'doctor'])->id,
            'specialization' => $this->faker->randomElement(['Cardiology', 'Neurology', 'Pediatrics', 'Dermatology', 'Orthopedics', 'Gynecology', 'Psychiatry', 'Endocrinology', 'Gastroenterology', 'Hematology', 'Immunology', 'Nephrology', 'Pulmonology', 'Rheumatology', 'Urology', 'Anesthesiology', 'Dentistry', 'Pharmacy', 'Physical Therapy', 'Occupational Therapy', 'Speech Therapy', 'Nutrition Therapy', 'Rehabilitation Therapy', 'Occupational Therapy', 'Speech Therapy', 'Nutrition Therapy', 'Rehabilitation Therapy', 'Occupational Therapy', 'Speech Therapy', 'Nutrition Therapy', 'Rehabilitation Therapy', 'Occupational Therapy', 'Speech Therapy', 'Nutrition Therapy', 'Rehabilitation Therapy']),
            'qualifications' => 'MD, ' . $this->faker->randomElement(['PhD', 'MS', 'MBBS']),
            'experience' => $this->faker->numberBetween(1, 20) . 'years of experience',
            'is_available' => true,
            'working_hours' => [
                'moday' => ['09:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-17:00'],
                'thursday' => ['09:00-17:00'],
                'friday' => ['09:00-17:00'],
            ]
        ];
    }
}