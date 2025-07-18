<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create basic permissions and roles for testing
        Permission::create(['name' => 'view dashboard']);
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo('view dashboard');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_authenticated_users_are_redirected_from_login(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_throttling_after_multiple_failed_attempts(): void
    {
        $user = User::factory()->create();

        // Make multiple failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // Next attempt should be throttled
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));
    }

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
        
        // Check that remember token is set
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    public function test_login_redirects_to_intended_url(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // Try to access a protected route
        $this->get('/workshops');
        
        // Login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Should redirect to the intended URL
        $response->assertRedirect('/workshops');
    }

    public function test_case_insensitive_email_login(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $user->assignRole('user');

        $response = $this->post('/login', [
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_with_whitespace_in_email(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $user->assignRole('user');

        $response = $this->post('/login', [
            'email' => ' test@example.com ',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_logout_clears_session(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // Login and set some session data
        $this->actingAs($user);
        session(['test_key' => 'test_value']);

        // Logout
        $response = $this->post('/logout');

        $this->assertGuest();
        $this->assertNull(session('test_key'));
        $response->assertRedirect('/');
    }

    public function test_logout_invalidates_remember_token(): void
    {
        $user = User::factory()->create(['remember_token' => 'old_token']);
        $user->assignRole('user');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $user->refresh();
        $this->assertNotEquals('old_token', $user->remember_token);
    }
}
