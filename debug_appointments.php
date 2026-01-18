<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\StaffRole;
use App\Models\MedicalForm;

echo "=== DEBUG APPOINTMENT ACCESS ===\n\n";

// 1. Check all roles and their levels
echo "1. STAFF ROLES:\n";
$roles = StaffRole::orderBy('level', 'desc')->get();
foreach ($roles as $role) {
    echo sprintf(
        "   - %s (level %d): %s\n",
        $role->name,
        $role->level,
        $role->display_name
    );
}

echo "\n2. STAFF MANAGER USERS:\n";
$staffManagers = User::whereHas('role', function ($q) {
    $q->where('name', 'staff_manager');
})->get();

foreach ($staffManagers as $user) {
    echo sprintf(
        "   - %s (ID: %d, Role: %s, Level: %d)\n",
        $user->name,
        $user->id,
        $user->role->name ?? 'none',
        $user->role->level ?? 0
    );
}

echo "\n3. APPOINTMENT TYPES:\n";
$appointmentTypes = [
    'penyakit_dalam',
    'spesialis_anak',
    'spesialis_bedah',
    'spesialis_mata',
    'spesialis_saraf',
    'spesialis_urologi',
    'spesialis_tht',
    'spesialis_ortopedi',
    'janji_temu'
];

echo "   Total types: " . count($appointmentTypes) . "\n";
foreach ($appointmentTypes as $type) {
    echo "   - $type\n";
}

echo "\n4. TOTAL APPOINTMENTS IN DATABASE:\n";
$totalAppointments = MedicalForm::whereIn('form_type', $appointmentTypes)->count();
echo "   Total: $totalAppointments\n";

echo "\n5. RECENT APPOINTMENTS:\n";
$recentAppointments = MedicalForm::whereIn('form_type', $appointmentTypes)
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

foreach ($recentAppointments as $apt) {
    echo sprintf(
        "   - ID: %d, Type: %s, Character: %s, Created: %s\n",
        $apt->id,
        $apt->form_type,
        $apt->character_name,
        $apt->created_at->format('Y-m-d H:i')
    );
}

echo "\n6. TESTING QUERY FOR LEVEL >= 5:\n";
$testUser = $staffManagers->first();
if ($testUser) {
    echo "   Testing with user: {$testUser->name}\n";
    echo "   User role level: " . ($testUser->role->level ?? 'null') . "\n";
    echo "   Check (level >= 5): " . (($testUser->role->level ?? 0) >= 5 ? 'TRUE' : 'FALSE') . "\n";

    // Test the actual query
    $query = MedicalForm::query();
    $query->whereIn('form_type', $appointmentTypes);

    $results = $query->orderBy('created_at', 'desc')->limit(5)->get();
    echo "   Query results count: " . $results->count() . "\n";
} else {
    echo "   No staff manager user found!\n";
}

echo "\n=== END DEBUG ===\n";
