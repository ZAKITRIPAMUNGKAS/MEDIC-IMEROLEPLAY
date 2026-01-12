<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\StaffRole;

class UpdateStaffRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-paramedic-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all users with Paramedic role to Perawat role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting role update process...');

        // Find the Paramedic role
        $paramedicRole = StaffRole::where('name', 'Paramedic')
            ->orWhere('display_name', 'Paramedic')
            ->orWhere('name', 'paramedic')
            ->first();

        if (!$paramedicRole) {
            $this->error('Role "Paramedic" not found.');
            return 1;
        }

        // Find the Perawat role
        $perawatRole = StaffRole::where('name', 'perawat')
            ->orWhere('display_name', 'Perawat')
            ->first();

        if (!$perawatRole) {
            $this->error('Role "Perawat" not found.');
            return 1;
        }

        $this->info("Found Paramedic Role ID: {$paramedicRole->id}");
        $this->info("Found Perawat Role ID: {$perawatRole->id}");

        if ($paramedicRole->id === $perawatRole->id) {
            $this->info('Paramedic and Perawat roles are the same. No action needed.');
            return 0;
        }

        // Get users with Paramedic role
        $users = User::where('role_id', $paramedicRole->id)->get();
        $count = $users->count();

        if ($count === 0) {
            $this->info('No users found with Paramedic role.');
            return 0;
        }

        if ($this->confirm("Are you sure you want to update {$count} users from Paramedic to Perawat?", true)) {
            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($users as $user) {
                $user->role_id = $perawatRole->id;
                $user->save();
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully updated {$count} users.");
        } else {
            $this->info('Operation cancelled.');
        }

        return 0;
    }
}
