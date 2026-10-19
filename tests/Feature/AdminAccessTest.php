<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'registered_user']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(403);
    }

    public function test_admin_users_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertStatus(200);
    }
}
