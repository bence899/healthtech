<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function createAdmin()
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    protected function createPatient()
    {
        return User::factory()->create([
            'role' => 'patient',
            'email_verified_at' => now(),
        ]);
    }

    protected function createDoctor()
    {
        $user = User::factory()->create([
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);

        return $user->doctor()->create([
            'specialization' => 'General Practice',
            'qualifications' => 'MD',
        ]);
    }
} 