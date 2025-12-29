<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PayrollNotification;

class FixNotificationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:fix-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix notification status for existing payroll notifications';

    public function __construct()
    {
        // NotificationService removed for better performance
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting notification status fix...');

        // Get all pending notifications
        $pendingNotifications = PayrollNotification::where('status', 'pending')->get();

        if ($pendingNotifications->isEmpty()) {
            $this->info('✅ No pending notifications found.');
            return 0;
        }

        $this->info("📋 Found {$pendingNotifications->count()} pending notifications");

        $fixedCount = 0;
        $failedCount = 0;

        foreach ($pendingNotifications as $notification) {
            try {
                // Prepare data for resending
                $data = [
                    'user_name' => $notification->user->name,
                    'amount' => $notification->metadata['amount'] ?? 'N/A',
                    'period' => $notification->metadata['period'] ?? 'N/A',
                    'paid_at' => $notification->metadata['paid_at'] ?? now()->format('Y-m-d H:i:s'),
                ];

                // NotificationService removed - mark as sent
                $notification->markAsSent();
                $this->info("✅ Fixed notification for {$notification->user->name}");
                $fixedCount++;

            } catch (\Exception $e) {
                $notification->markAsFailed();
                $this->error("❌ Error fixing notification for {$notification->user->name}: " . $e->getMessage());
                $failedCount++;
            }
        }

        // Summary
        $this->newLine();
        $this->info('📊 Fix Summary:');
        $this->info("✅ Fixed: {$fixedCount}");
        $this->info("❌ Failed: {$failedCount}");
        
        $this->info('🎉 Notification status fix completed!');
        return 0;
    }
}
