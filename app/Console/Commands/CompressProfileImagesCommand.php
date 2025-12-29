<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Helpers\ImageHelper;

class CompressProfileImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:compress-profiles 
                            {--max-width=500 : Maximum width in pixels}
                            {--max-height=500 : Maximum height in pixels}
                            {--quality=85 : JPEG quality (0-100)}
                            {--force : Force re-compress even if already compressed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress all existing profile images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Starting profile image compression...');
        $this->newLine();

        $maxWidth = (int) $this->option('max-width');
        $maxHeight = (int) $this->option('max-height');
        $quality = (int) $this->option('quality');
        $force = $this->option('force');

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

        $compressed = 0;
        $skipped = 0;
        $failed = 0;
        $totalSizeBefore = 0;
        $totalSizeAfter = 0;

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            try {
                $imagePath = $this->getImagePath($user->profile_image);
                
                if (!$imagePath || !file_exists($imagePath)) {
                    $this->newLine();
                    $this->warn("⚠️  Image not found for user {$user->name} (ID: {$user->id}): {$user->profile_image}");
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Check if already compressed (optional check)
                if (!$force) {
                    $imageInfo = getimagesize($imagePath);
                    if ($imageInfo && $imageInfo[0] <= $maxWidth && $imageInfo[1] <= $maxHeight) {
                        $this->newLine();
                        $this->line("✓ Image already compressed for {$user->name}");
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }
                }

                $originalSize = filesize($imagePath);
                $totalSizeBefore += $originalSize;

                // Create temporary compressed file
                $tempPath = $imagePath . '.compressed';
                
                // Compress image
                $success = ImageHelper::compressImage(
                    $imagePath,
                    $tempPath,
                    $maxWidth,
                    $maxHeight,
                    $quality
                );

                if ($success && file_exists($tempPath)) {
                    $newSize = filesize($tempPath);
                    $totalSizeAfter += $newSize;
                    
                    // Replace original with compressed version
                    if (copy($tempPath, $imagePath)) {
                        unlink($tempPath);
                        $compressed++;
                        
                        $sizeReduction = $originalSize - $newSize;
                        $reductionPercent = $originalSize > 0 ? round(($sizeReduction / $originalSize) * 100, 2) : 0;
                        
                        $this->newLine();
                        $this->info("✓ Compressed: {$user->name}");
                        $this->line("  Size: " . $this->formatBytes($originalSize) . " → " . $this->formatBytes($newSize) . " ({$reductionPercent}% reduction)");
                    } else {
                        unlink($tempPath);
                        $failed++;
                        $this->newLine();
                        $this->error("✗ Failed to replace image for {$user->name}");
                    }
                } else {
                    $failed++;
                    $this->newLine();
                    $this->error("✗ Compression failed for {$user->name}");
                }

            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("✗ Error processing {$user->name}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Compression Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Compressed', $compressed],
                ['Skipped', $skipped],
                ['Failed', $failed],
                ['Total', $users->count()],
            ]
        );

        if ($compressed > 0) {
            $totalReduction = $totalSizeBefore - $totalSizeAfter;
            $totalReductionPercent = $totalSizeBefore > 0 ? round(($totalReduction / $totalSizeBefore) * 100, 2) : 0;
            
            $this->newLine();
            $this->info('💾 Size Reduction:');
            $this->line("  Before: " . $this->formatBytes($totalSizeBefore));
            $this->line("  After:  " . $this->formatBytes($totalSizeAfter));
            $this->line("  Saved:  " . $this->formatBytes($totalReduction) . " ({$totalReductionPercent}%)");
        }

        $this->newLine();
        $this->info('✅ Profile image compression completed!');
        
        return 0;
    }

    /**
     * Get full path to image file
     */
    private function getImagePath($profileImage)
    {
        if (empty($profileImage)) {
            return null;
        }

        // Check if it's a public path
        if (str_starts_with($profileImage, 'uploads/')) {
            $path = public_path($profileImage);
            if (file_exists($path)) {
                return $path;
            }
        }

        // Check if it's a storage path
        $storagePath = storage_path('app/public/' . $profileImage);
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        // Try direct path
        if (file_exists($profileImage)) {
            return $profileImage;
        }

        return null;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

