<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CleanupProfileImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleanup-profiles 
                            {--dry-run : Show what would be cleaned without actually cleaning}
                            {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up database references to profile images that no longer exist on the server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting profile image cleanup...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get all users with profile images
        $users = User::whereNotNull('profile_image')
            ->where('profile_image', '!=', '')
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users with profile images found.');
            return 0;
        }

        $this->info("Found {$users->count()} users with profile images.");
        $this->newLine();

        $invalidImages = [];
        $validImages = [];

        // Check each user's profile image
        foreach ($users as $user) {
            if (!$user->hasValidProfileImage()) {
                $invalidImages[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_image' => $user->profile_image,
                ];
            } else {
                $validImages[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_image' => $user->profile_image,
                ];
            }
        }

        // Display results
        $this->info("✅ Valid images: " . count($validImages));
        $this->warn("❌ Invalid images: " . count($invalidImages));
        $this->newLine();

        if (empty($invalidImages)) {
            $this->info('🎉 All profile images are valid! No cleanup needed.');
            return 0;
        }

        // Display invalid images
        $this->table(
            ['ID', 'Name', 'Profile Image Path'],
            array_map(function($item) {
                return [
                    $item['id'],
                    $item['name'],
                    $item['profile_image'],
                ];
            }, $invalidImages)
        );

        $this->newLine();

        // Confirm cleanup
        if (!$dryRun && !$force) {
            if (!$this->confirm('Do you want to clean up these invalid profile image references?', true)) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        // Perform cleanup
        if (!$dryRun) {
            $this->info('🧹 Cleaning up invalid profile image references...');
            $this->newLine();

            $cleanedCount = 0;
            foreach ($invalidImages as $item) {
                $user = User::find($item['id']);
                if ($user) {
                    $this->line("  - Cleaning up: {$user->name} (ID: {$user->id})");
                    $user->profile_image = null;
                    $user->save();
                    $cleanedCount++;
                }
            }

            $this->newLine();
            $this->info("✅ Cleanup completed! {$cleanedCount} users updated.");
        } else {
            $this->info("🔍 DRY RUN: Would clean up " . count($invalidImages) . " users.");
        }

        return 0;
    }
}

