<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@healthtech.com'
        ]);
    }

    protected function createPatient(): User
    {
        return User::factory()->create([
            'role' => 'patient'
        ]);
    }
} 