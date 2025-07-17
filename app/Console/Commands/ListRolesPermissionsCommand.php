<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListRolesPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all roles and their permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();

        $this->info('Roles and Permissions:');
        $this->line('');

        foreach ($roles as $role) {
            $this->info("Role: {$role->name}");
            $this->line("Permissions: " . $role->permissions->pluck('name')->implode(', '));
            $this->line('');
        }

        $this->info('All Permissions:');
        $permissions = \Spatie\Permission\Models\Permission::all();
        foreach ($permissions as $permission) {
            $this->line("- {$permission->name}");
        }

        return 0;
    }
}
