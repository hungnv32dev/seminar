<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Role {$roleName} not found.");
            return 1;
        }

        if ($user->hasRole($roleName)) {
            $this->info("User {$email} already has the {$roleName} role.");
            return 0;
        }

        $user->assignRole($roleName);
        $this->info("Successfully assigned {$roleName} role to {$email}.");
        
        return 0;
    }
}
