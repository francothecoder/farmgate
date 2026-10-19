<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSee('Create your account')
            ->assertSee('Agricultural interest')
            ->assertSee('Phone / WhatsApp')
            ->assertSee('Confirm password');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'full_name' => 'Test User',
            'phone' => '+260955000111',
            'country' => 'Zambia',
            'province' => 'Lusaka',
            'district' => 'Lusaka Central',
            'primary_interest' => 'Vegetables',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone' => '+260955000111',
            'role' => 'registered_user',
        ]);
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
