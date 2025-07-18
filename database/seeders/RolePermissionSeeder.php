<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
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
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            
            // Workshop management
            'view workshops',
            'create workshops',
            'edit workshops',
            'delete workshops',
            'manage workshop organizers',
            
            // Participant management
            'view participants',
            'create participants',
            'edit participants',
            'delete participants',
            'import participants',
            'export participants',
            
            // Ticket type management
            'view ticket types',
            'create ticket types',
            'edit ticket types',
            'delete ticket types',
            
            // Email template management
            'view email templates',
            'create email templates',
            'edit email templates',
            'delete email templates',
            'send emails',
            
            // Check-in management
            'view check-ins',
            'manage check-ins',
            'scan qr codes',
            
            // Dashboard and analytics
            'view dashboard',
            'view analytics',
            'view reports',
            
            // System administration
            'manage system settings',
            'view logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - has most permissions except system administration
        $admin = Role::create(['name' => 'admin']);
        $adminPermissions = [
            'view users', 'create users', 'edit users', 'delete users', 'manage user roles',
            'view workshops', 'create workshops', 'edit workshops', 'delete workshops', 'manage workshop organizers',
            'view participants', 'create participants', 'edit participants', 'delete participants', 'import participants', 'export participants',
            'view ticket types', 'create ticket types', 'edit ticket types', 'delete ticket types',
            'view email templates', 'create email templates', 'edit email templates', 'delete email templates', 'send emails',
            'view check-ins', 'manage check-ins', 'scan qr codes',
            'view dashboard', 'view analytics', 'view reports',
        ];
        $admin->givePermissionTo($adminPermissions);

        // Organizer - can manage workshops and participants
        $organizer = Role::create(['name' => 'organizer']);
        $organizerPermissions = [
            'view workshops', 'create workshops', 'edit workshops',
            'view participants', 'create participants', 'edit participants', 'import participants', 'export participants',
            'view ticket types', 'create ticket types', 'edit ticket types',
            'view email templates', 'create email templates', 'edit email templates', 'send emails',
            'view check-ins', 'manage check-ins', 'scan qr codes',
            'view dashboard', 'view analytics',
        ];
        $organizer->givePermissionTo($organizerPermissions);

        // Staff - can manage check-ins and view basic information
        $staff = Role::create(['name' => 'staff']);
        $staffPermissions = [
            'view workshops',
            'view participants',
            'view check-ins', 'manage check-ins', 'scan qr codes',
            'view dashboard',
        ];
        $staff->givePermissionTo($staffPermissions);

        // Viewer - read-only access
        $viewer = Role::create(['name' => 'viewer']);
        $viewerPermissions = [
            'view workshops',
            'view participants',
            'view ticket types',
            'view email templates',
            'view check-ins',
            'view dashboard', 'view analytics', 'view reports',
        ];
        $viewer->givePermissionTo($viewerPermissions);
    }
}