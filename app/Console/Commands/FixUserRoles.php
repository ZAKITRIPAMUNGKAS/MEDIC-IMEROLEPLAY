<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\StaffRole;

class FixUserRoles extends Command
{
    protected $signature = 'fix:user-roles';
    protected $description = 'Fix user roles by assigning default role to users without role';

    public function handle()
    {
        $this->info('Fixing user roles...');

        // Get users without roles
        $usersWithoutRole = User::whereNull('role_id')->get();
        
        if ($usersWithoutRole->count() === 0) {
            $this->info('All users already have roles assigned.');
            return;
        }

        // Get or create perawat role
        $perawatRole = StaffRole::where('name', 'perawat')->first();
        
        if (!$perawatRole) {
            $this->info('Creating perawat role...');
            $perawatRole = StaffRole::create([
                'name' => 'perawat',
                'display_name' => 'Perawat',
                'description' => 'Perawat medis',
                'permissions' => json_encode(['view_reports', 'manage_forms'])
            ]);
        }

        // Assign role to users
        foreach ($usersWithoutRole as $user) {
            $user->role_id = $perawatRole->id;
            $user->save();
            $this->info("Assigned 'perawat' role to: {$user->name}");
        }

        $this->info('User roles fixed successfully!');
    }
}
