<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            // Workshop Management
            'view workshops',
            'create workshops',
            'edit workshops',
            'delete workshops',
            'manage workshop statistics',
            
            // Participant Management
            'view participants',
            'create participants',
            'edit participants',
            'delete participants',
            'import participants',
            'export participants',
            'manage participant payments',
            'send participant emails',
            
            // Ticket Type Management
            'view ticket types',
            'create ticket types',
            'edit ticket types',
            'delete ticket types',
            
            // Check-in Management
            'view check-ins',
            'manage check-ins',
            'scan qr codes',
            'manual check-in',
            'undo check-ins',
            'export check-in reports',
            
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            'activate users',
            'deactivate users',
            
            // Email Template Management
            'view email templates',
            'create email templates',
            'edit email templates',
            'delete email templates',
            'send test emails',
            
            // System Administration
            'view dashboard',
            'view system statistics',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $managerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'manager']);
        $organizerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer']);
        $assistantRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'assistant']);

        // Admin has all permissions
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        // Manager has most permissions except user management
        $managerPermissions = [
            'view workshops', 'create workshops', 'edit workshops', 'delete workshops', 'manage workshop statistics',
            'view participants', 'create participants', 'edit participants', 'delete participants', 
            'import participants', 'export participants', 'manage participant payments', 'send participant emails',
            'view ticket types', 'create ticket types', 'edit ticket types', 'delete ticket types',
            'view check-ins', 'manage check-ins', 'scan qr codes', 'manual check-in', 'undo check-ins', 'export check-in reports',
            'view email templates', 'create email templates', 'edit email templates', 'delete email templates', 'send test emails',
            'view dashboard', 'view system statistics',
        ];
        $managerRole->givePermissionTo($managerPermissions);

        // Organizer can manage workshops and participants
        $organizerPermissions = [
            'view workshops', 'create workshops', 'edit workshops', 'manage workshop statistics',
            'view participants', 'create participants', 'edit participants', 'import participants', 
            'export participants', 'manage participant payments', 'send participant emails',
            'view ticket types', 'create ticket types', 'edit ticket types',
            'view check-ins', 'manage check-ins', 'scan qr codes', 'manual check-in', 'export check-in reports',
            'view email templates', 'create email templates', 'edit email templates', 'send test emails',
            'view dashboard',
        ];
        $organizerRole->givePermissionTo($organizerPermissions);

        // Assistant has limited permissions
        $assistantPermissions = [
            'view workshops', 'view participants', 'view ticket types',
            'view check-ins', 'manage check-ins', 'scan qr codes', 'manual check-in',
            'view email templates', 'view dashboard',
        ];
        $assistantRole->givePermissionTo($assistantPermissions);
    }
}
