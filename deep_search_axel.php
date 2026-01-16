<?php

use App\Models\User;
use App\Models\Payroll;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== DEEP USER SEARCH ===\n";

// Search 1: "Axel"
echo "--- Searching 'Axel' ---\n";
$users = User::where('name', 'like', '%Axel%')->get();
foreach ($users as $u) {
    echo "Found: [{$u->id}] {$u->name} (Hosp: {$u->hospital}, Role: " . ($u->role ? $u->role->name : 'N/A') . ")\n";
}

// Search 2: "Wiliams" / "Williams"
echo "\n--- Searching 'Wiliams' / 'Williams' ---\n";
$users = User::where('name', 'like', '%Wiliams%')
    ->orWhere('name', 'like', '%Williams%')->get();
foreach ($users as $u) {
    echo "Found: [{$u->id}] {$u->name} (Hosp: {$u->hospital})\n";
}

// Search 3: "RH" prefix
echo "\n--- Searching 'RH -' (First 5) ---\n";
$users = User::where('name', 'like', 'RH -%')->limit(5)->get();
foreach ($users as $u) {
    echo "Sample: [{$u->id}] {$u->name}\n";
}


echo "\n=== PAYROLL DUMP (Week Start 2026-01-05) ===\n";
// Dump ALL payrolls for that week
$payrolls = Payroll::where('period_start', '2026-01-05')->get();
echo "Total Payrolls for week: " . $payrolls->count() . "\n";

foreach ($payrolls as $p) {
    // Only show if related to Axel or RH for brevity, OR list all names if small count
    if (stripos($p->user->name ?? '', 'Axel') !== false || stripos($p->user->name ?? '', 'Wil') !== false) {
        echo "!!! MATCH !!! -> Payroll ID: {$p->id} | User: {$p->user->name} | Amount: {$p->calculated_salary} | Status: {$p->status} | Hosp: {$p->user->hospital}\n";
    }
}
