<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        
        // Assign permissions to admin
        $adminRole->givePermissionTo([
            'manage users', 'view users', 'create users', 'edit users', 'delete users'
        ]);
        
        // Create users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
    }

    public function test_index_displays_users_for_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertViewHas(['users', 'roles', 'filters']);
        $response->assertSee($this->user->name);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->get(route('users.index'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_index_requires_permission()
    {
        $response = $this->actingAs($this->user)->get(route('users.index'));
        
        $response->assertStatus(403);
    }

    public function test_index_filters_by_role()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index', ['role' => 'admin']));
        
        $response->assertStatus(200);
        $response->assertSee($this->admin->name);
        $response->assertDontSee($this->user->name);
    }

    public function test_index_filters_by_status()
    {
        $inactiveUser = User::factory()->create(['is_active' => false]);
        $inactiveUser->assignRole('user');
        
        $response = $this->actingAs($this->admin)->get(route('users.index', ['status' => 'inactive']));
        
        $response->assertStatus(200);
        $response->assertSee($inactiveUser->name);
        $response->assertDontSee($this->user->name);
    }

    public function test_index_searches_by_name()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index', ['search' => $this->user->name]));
        
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->admin)->get(route('users.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('users.create');
        $response->assertViewHas('roles');
    }

    public function test_store_creates_user_with_valid_data()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => true,
            'roles' => ['user'],
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'is_active' => true,
        ]);
        
        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($newUser->hasRole('user'));
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_store_validates_email_uniqueness()
    {
        $userData = [
            'name' => 'New User',
            'email' => $this->user->email, // Existing email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_store_validates_password_confirmation()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_show_displays_user()
    {
        $response = $this->actingAs($this->admin)->get(route('users.show', $this->user));
        
        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertViewHas(['user', 'stats']);
        $response->assertSee($this->user->name);
    }

    public function test_edit_displays_form()
    {
        $response = $this->actingAs($this->admin)->get(route('users.edit', $this->user));
        
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas(['user', 'roles']);
    }

    public function test_update_modifies_user_with_valid_data()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'is_active' => false,
            'roles' => ['admin'],
        ];

        $response = $this->actingAs($this->admin)->put(route('users.update', $this->user), $updateData);

        $response->assertRedirect(route('users.show', $this->user));
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'is_active' => false,
        ]);
        
        $this->user->refresh();
        $this->assertTrue($this->user->hasRole('admin'));
    }

    public function test_update_validates_email_uniqueness()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => $this->admin->email, // Existing email
        ];

        $response = $this->actingAs($this->admin)->put(route('users.update', $this->user), $updateData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_update_with_password()
    {
        $updateData = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->admin)->put(route('users.update', $this->user), $updateData);

        $response->assertRedirect();
        
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    public function test_destroy_deletes_user_without_workshops()
    {
        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('user');
        
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $userToDelete));
        
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_destroy_prevents_deletion_with_created_workshops()
    {
        Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->user));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    public function test_destroy_prevents_self_deletion()
    {
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_toggle_status_activates_user()
    {
        $this->user->update(['is_active' => false]);
        
        $response = $this->actingAs($this->admin)->post(route('users.toggle-status', $this->user));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->user->refresh();
        $this->assertTrue($this->user->is_active);
    }

    public function test_toggle_status_deactivates_user()
    {
        $response = $this->actingAs($this->admin)->post(route('users.toggle-status', $this->user));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->user->refresh();
        $this->assertFalse($this->user->is_active);
    }

    public function test_toggle_status_prevents_self_deactivation()
    {
        $response = $this->actingAs($this->admin)->post(route('users.toggle-status', $this->admin));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);
    }

    public function test_roles_permissions_displays_management_page()
    {
        $response = $this->actingAs($this->admin)->get(route('users.roles-permissions'));
        
        $response->assertStatus(200);
        $response->assertViewIs('users.roles-permissions');
        $response->assertViewHas(['roles', 'permissions']);
    }

    public function test_get_users_by_role_returns_json()
    {
        $response = $this->actingAs($this->admin)->get(route('users.by-role', ['role' => 'user']));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email']
        ]);
    }

    public function test_get_user_stats_returns_json()
    {
        Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->admin)->get(route('users.stats', $this->user));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'is_active',
            'roles',
            'created_workshops_count',
            'organized_workshops_count',
            'total_workshops',
            'created_at',
        ]);
    }

    public function test_bulk_update_status_activates_multiple_users()
    {
        $user1 = User::factory()->create(['is_active' => false]);
        $user2 = User::factory()->create(['is_active' => false]);
        $user1->assignRole('user');
        $user2->assignRole('user');
        
        $response = $this->actingAs($this->admin)->post(route('users.bulk-status'), [
            'user_ids' => [$user1->id, $user2->id],
            'is_active' => true,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $user1->refresh();
        $user2->refresh();
        $this->assertTrue($user1->is_active);
        $this->assertTrue($user2->is_active);
    }

    public function test_bulk_update_status_prevents_self_deactivation()
    {
        $response = $this->actingAs($this->admin)->post(route('users.bulk-status'), [
            'user_ids' => [$this->admin->id],
            'is_active' => false,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_bulk_assign_role()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $response = $this->actingAs($this->admin)->post(route('users.bulk-role'), [
            'user_ids' => [$user1->id, $user2->id],
            'role' => 'admin',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $user1->refresh();
        $user2->refresh();
        $this->assertTrue($user1->hasRole('admin'));
        $this->assertTrue($user2->hasRole('admin'));
    }

    public function test_unauthorized_user_cannot_access_user_management()
    {
        $response = $this->actingAs($this->user)->get(route('users.index'));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_create_user()
    {
        $response = $this->actingAs($this->user)->get(route('users.create'));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_edit_user()
    {
        $response = $this->actingAs($this->user)->get(route('users.edit', $this->admin));
        
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_delete_user()
    {
        $response = $this->actingAs($this->user)->delete(route('users.destroy', $this->admin));
        
        $response->assertStatus(403);
    }

    public function test_show_displays_user_statistics()
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->user->id]);
        
        $response = $this->actingAs($this->admin)->get(route('users.show', $this->user));
        
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['created_workshops']);
    }

    public function test_index_pagination()
    {
        // Create many users to test pagination
        User::factory()->count(20)->create()->each(function ($user) {
            $user->assignRole('user');
        });
        
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('users');
        
        $users = $response->viewData('users');
        $this->assertLessThanOrEqual(15, $users->count()); // Default pagination is 15
    }

    public function test_index_sorting()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index', [
            'sort_by' => 'name',
            'sort_order' => 'asc'
        ]));
        
        $response->assertStatus(200);
        $response->assertViewHas('filters');
        
        $filters = $response->viewData('filters');
        $this->assertEquals('name', $filters['sort_by']);
        $this->assertEquals('asc', $filters['sort_order']);
    }

    public function test_store_without_roles()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_update_without_password_change()
    {
        $originalPassword = $this->user->password;
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => $this->user->email,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->put(route('users.update', $this->user), $updateData);

        $response->assertRedirect();
        
        $this->user->refresh();
        $this->assertEquals($originalPassword, $this->user->password);
        $this->assertEquals('Updated Name', $this->user->name);
    }
}