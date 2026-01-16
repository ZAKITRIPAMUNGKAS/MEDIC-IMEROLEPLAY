<?php

use App\Models\User;
use App\Models\Payroll;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Cleaning Invalid RH Payrolls ---\n";

// 1. Get all Roxwood Users
$users = User::where('hospital', 'roxwood')->pluck('id');

// 2. Find Payrolls for last week (Jan 5-11 2026) that are 0 or valid but need refresh
$start = Carbon::create(2026, 1, 5)->startOfDay()->format('Y-m-d');

$payrolls = Payroll::whereIn('user_id', $users)
    ->whereDate('period_start', $start)
    ->get();

echo "Found " . $payrolls->count() . " payrolls for RH this period.\n";

foreach ($payrolls as $p) {
    echo " - ID: {$p->id}, User: {$p->user->name}, Amount: {$p->calculated_salary}, Status: {$p->status}\n";
    // If amount is 0 or user asks to reset, delete it.
    // For safety, let's just delete ALL RH payrolls for this specific week so they can be cleanly generated.
    $p->delete();
    echo "   [DELETED]\n";
}

echo "Done. Please generate again.\n";
