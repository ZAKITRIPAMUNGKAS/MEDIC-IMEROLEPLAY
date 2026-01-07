<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\StaffRole;

class UpdateRolesToEnglish extends Command
{
    protected $signature = 'roles:update-to-english';
    protected $description = 'Update role names from Indonesian to English and add IT Support role';

    public function handle()
    {
        $this->info('Updating roles to English...');

        DB::beginTransaction();
        try {
            // Update Indonesian names to English
            $updates = [
                'staf' => 'staff',
                'manajer' => 'manager',
                'perawat' => 'nurse',
                'dokter_umum' => 'general_doctor',
                'dokter_spesialis' => 'specialist_doctor',
            ];

            foreach ($updates as $indonesian => $english) {
                // Update staff_roles table
                $updated = DB::table('staff_roles')
                    ->where('name', $indonesian)
                    ->update(['name' => $english]);

                if ($updated > 0) {
                    $this->info("✓ Updated role '{$indonesian}' to '{$english}'");
                }

                // Also update salary_settings table if it references role names
                $salaryUpdated = DB::table('salary_settings')
                    ->where('role_name', $indonesian)
                    ->update(['role_name' => $english]);

                if ($salaryUpdated > 0) {
                    $this->info("  ✓ Updated {$salaryUpdated} salary settings");
                }
            }

            // Add IT Support role if not exists
            $itSupport = StaffRole::where('name', 'it_support')->first();
            if (!$itSupport) {
                StaffRole::create([
                    'name' => 'it_support',
                    'display_name' => 'IT Support',
                    'level' => 50,
                    'description' => 'IT Support staff for technical assistance',
                ]);
                $this->info('✓ Added IT Support role');
            } else {
                $this->info('- IT Support role already exists');
            }

            DB::commit();

            $this->newLine();
            $this->info('All roles:');
            $roles = StaffRole::orderBy('level')->get(['name', 'display_name', 'level']);
            $this->table(['Name', 'Display Name', 'Level'], $roles->map(function ($role) {
                return [$role->name, $role->display_name, $role->level];
            }));

            $this->newLine();
            $this->info('✓ Roles updated successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to update roles: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
