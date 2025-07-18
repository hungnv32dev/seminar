<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_disabled(): void
    {
        // Registration should be disabled for internal workshop management system
        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_registration_endpoint_is_disabled(): void
    {
        // Registration endpoint should also be disabled
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(404);
        $this->assertGuest();
    }

    public function test_registration_routes_are_not_accessible(): void
    {
        // Test that registration-related routes return 404
        $registrationRoutes = [
            '/register',
            '/register/confirm',
        ];

        foreach ($registrationRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(404);
        }
    }

    public function test_login_page_does_not_show_registration_link(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        // Should not contain registration links since it's an internal system
        $response->assertDontSee('Register');
        $response->assertDontSee('Sign up');
        $response->assertDontSee('Create account');
    }
}
