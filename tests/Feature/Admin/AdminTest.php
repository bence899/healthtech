<?php

namespace Tests\Feature\Admin;

use Tests\Feature\Admin\AdminTestCase;

class AdminTest extends AdminTestCase
{
    public function test_admin_can_access_dashboard(): void
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $patient = $this->createPatient();
        $response = $this->actingAs($patient)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }
}