<?php

use App\Models\User;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== SIMULATING PAYROLL VIEW ===\n";

// Mock Input: Week = 2026-01-05, Hospital = Roxwood
$filters = [
    'week' => '2026-01-05',
    'hospital' => 'roxwood',
    'status' => '',
];

echo "Filters: " . json_encode($filters) . "\n";

$query = Payroll::query();

// 1. Filter by Hospital
$hospital = $filters['hospital'];
$query->whereHas('user', function ($sub) use ($hospital) {
    if ($hospital === 'roxwood') {
        $sub->where('hospital', 'roxwood');
    }
});

// 2. Filter by Week
$weekDate = Carbon::parse($filters['week']);
$startOfWeek = $weekDate->copy()->startOfWeek()->format('Y-m-d');
$endOfWeek = $weekDate->copy()->endOfWeek()->format('Y-m-d');
$query->whereBetween('period_start', [$startOfWeek, $endOfWeek]);

echo "Query: " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";

$payrolls = $query->get();
echo "Found " . $payrolls->count() . " payrolls.\n";

$axelFound = false;
foreach ($payrolls as $p) {
    if (str_contains($p->user->name, 'Axel')) {
        $axelFound = true;
        echo "Found Axel: {$p->user->name}, Amount: {$p->calculated_salary}, Status: {$p->status}\n";
    }
}

if (!$axelFound) {
    echo "AXEL NOT FOUND IN QUERY RESULTS.\n";
    // Check why
    $axel = User::where('name', 'like', '%Axel%')->first();
    if ($axel) {
        echo "Axel User Check: Hospital={$axel->hospital}\n";
    }
} else {
    echo "SUCCESS: Axel should be visible.\n";
}
