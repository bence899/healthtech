<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'phone' => '1234567890',
            'address' => '123 Test Street',
            'date_of_birth' => '1999-01-01',
            'emergency_contact' => '0987654321'
        ]);
    }
}