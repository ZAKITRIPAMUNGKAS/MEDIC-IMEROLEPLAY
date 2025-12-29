<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payroll;

class PayrollReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:reminder 
                            {--days=7 : Number of days to look back for pending payrolls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for pending payrolls';

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
        $this->info('🔔 Starting payroll reminder process...');

        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        // Get pending payrolls
        $pendingPayrolls = Payroll::where('status', 'pending')
            ->where('created_at', '<=', $cutoffDate)
            ->with('user')
            ->get();

        if ($pendingPayrolls->isEmpty()) {
            $this->info('✅ No pending payrolls found that need reminders.');
            return 0;
        }

        $this->info("📋 Found {$pendingPayrolls->count()} pending payrolls older than {$days} days");

        $reminderCount = 0;
        $errors = [];

        foreach ($pendingPayrolls as $payroll) {
            try {
                // NotificationService removed - skip notification

                $this->info("📤 Sent reminder for {$payroll->user->name} - {$payroll->formatted_salary}");
                $reminderCount++;

            } catch (\Exception $e) {
                $error = "Error sending reminder for {$payroll->user->name}: " . $e->getMessage();
                $errors[] = $error;
                $this->error("❌ {$error}");
            }
        }

        // Summary
        $this->newLine();
        $this->info('📊 Reminder Summary:');
        $this->info("📤 Sent: {$reminderCount}");
        
        if (!empty($errors)) {
            $this->error("❌ Errors: " . count($errors));
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }

        $this->info('🎉 Payroll reminder process completed!');
        return 0;
    }
}
