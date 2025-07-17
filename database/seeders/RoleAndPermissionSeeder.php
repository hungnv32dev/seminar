<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Workshop management
            'manage workshops',
            'view workshops',
            'create workshops',
            'edit workshops',
            'delete workshops',
            
            // Participant management
            'manage participants',
            'view participants',
            'create participants',
            'edit participants',
            'delete participants',
            'import participants',
            
            // Ticket management
            'manage ticket types',
            'view ticket types',
            'create ticket types',
            'edit ticket types',
            'delete ticket types',
            
            // Check-in management
            'manage check-in',
            'view check-in',
            'check-in participants',
            
            // Email management
            'manage email templates',
            'view email templates',
            'create email templates',
            'edit email templates',
            'delete email templates',
            'send emails',
            
            // Dashboard and analytics
            'view dashboard',
            'view analytics',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $organizerRole = Role::create(['name' => 'organizer']);
        $organizerRole->givePermissionTo([
            'view workshops',
            'create workshops',
            'edit workshops',
            'manage participants',
            'view participants',
            'create participants',
            'edit participants',
            'delete participants',
            'import participants',
            'manage ticket types',
            'view ticket types',
            'create ticket types',
            'edit ticket types',
            'delete ticket types',
            'manage email templates',
            'view email templates',
            'create email templates',
            'edit email templates',
            'delete email templates',
            'send emails',
            'view dashboard',
            'view analytics',
        ]);

        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view workshops',
            'view participants',
            'manage check-in',
            'view check-in',
            'check-in participants',
        ]);

        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view workshops',
            'view participants',
            'view dashboard',
        ]);
    }
}