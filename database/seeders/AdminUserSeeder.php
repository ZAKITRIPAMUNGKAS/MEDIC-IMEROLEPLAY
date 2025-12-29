<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin role
        $adminRole = StaffRole::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run StaffRoleSeeder first.');
            return;
        }

        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@mpk-ba.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'staff_id' => 'ADM001',
            'is_active' => true
        ]);

        // Create paramedic user
        $paramedicRole = StaffRole::where('name', 'paramedic')->first();
        if ($paramedicRole) {
            User::create([
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah@mpk-ba.com',
                'password' => Hash::make('paramedic123'),
                'role_id' => $paramedicRole->id,
                'staff_id' => 'PAR001',
                'is_active' => true
            ]);
        }

        // Create supervisor user
        $supervisorRole = StaffRole::where('name', 'supervisor')->first();
        if ($supervisorRole) {
            User::create([
                'name' => 'Michael Chen',
                'email' => 'michael@mpk-ba.com',
                'password' => Hash::make('supervisor123'),
                'role_id' => $supervisorRole->id,
                'staff_id' => 'SUP001',
                'is_active' => true
            ]);
        }

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin: admin@mpk-ba.com / admin123');
        $this->command->info('Paramedic: sarah@mpk-ba.com / paramedic123');
        $this->command->info('Supervisor: michael@mpk-ba.com / supervisor123');
    }
}
