<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create organizer user
        $organizer = User::factory()->create([
            'name' => 'Organizer User',
            'email' => 'organizer@example.com',
            'is_active' => true,
        ]);
        $organizer->assignRole('organizer');

        // Create staff user
        $staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');
    }
}
