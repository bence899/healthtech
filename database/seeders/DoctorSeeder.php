<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test doctor
        $doctor_user = User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'phone' => '1234567890',
            'address' => '123 Medical Center',
            'date_of_birth' => '1980-01-01',
        ]);

        Doctor::create([
            'user_id' => $doctor_user->id,
            'specialization' => 'General Medicine',
            'qualifications' => 'MD, MBBS',
            'experience' => '15 years of experience in general practice',
            'is_available' => true,
            'working_hours' => json_encode([
                'monday' => ['09:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-17:00'],
                'thursday' => ['09:00-17:00'],
                'friday' => ['09:00-17:00']
            ])
        ]);
    }
}
