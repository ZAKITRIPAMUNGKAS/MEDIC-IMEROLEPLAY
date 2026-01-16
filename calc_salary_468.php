<?php

use App\Models\User;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Helpers\PayrollHelper;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 468;
$start = '2026-01-05';
$end = '2026-01-11';
$payroll = Payroll::where('user_id', $id)->where('period_start', $start)->first();

if (!$payroll) {
    echo "Payroll still missing for 468.\n";
    exit;
}

echo "Calculating salary for user {$id}...\n";
$user = $payroll->user;

// Calculate Total Seconds
$totalSeconds = Attendance::where('user_id', $id)
    ->whereBetween('work_date', [$start, $end])
    ->whereIn('session_type', ['work', 'meeting']) // Assuming types
    ->whereNotNull('session_duration')
    ->where('session_duration', '>', 0)
    ->where('is_active', false)
    ->sum('session_duration');

echo "Total Seconds: $totalSeconds\n";

$totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
$roleName = $user->role ? $user->role->name : 'Nurse'; // Fallback
$customSalary = $user->custom_salary ?? 0;
$baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
$calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

echo "Role: $roleName\n";
echo "Base: $baseSalary\n";
echo "Calculated: $calculatedSalary\n";

$payroll->update([
    'total_hours' => $totalHours,
    'base_salary' => $baseSalary,
    'calculated_salary' => $calculatedSalary,
    'notes' => "Fixed by Agent"
]);

echo "Payroll Updated.\n";
