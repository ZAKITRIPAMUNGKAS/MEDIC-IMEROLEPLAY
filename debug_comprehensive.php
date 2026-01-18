<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\MedicalForm;

echo "=== COMPREHENSIVE DEBUG ===\n\n";

// Check which user is staff manager
echo "1. CHECKING STAFF MANAGER USERS:\n";
$managers = User::whereHas('role', function ($q) {
    $q->where('name', 'staff_manager');
})->with('role')->get();

if ($managers->isEmpty()) {
    echo "   ❌ NO STAFF MANAGER FOUND!\n";
} else {
    foreach ($managers as $mgr) {
        echo sprintf(
            "   - %s (ID: %d, Role: %s, Level: %d, Hospital: %s)\n",
            $mgr->name,
            $mgr->id,
            $mgr->role->name,
            $mgr->role->level,
            $mgr->hospital ?? 'alta'
        );
    }
}

echo "\n2. CHECKING DOKTER UMUM USERS:\n";
$doctors = User::whereHas('role', function ($q) {
    $q->where('name', 'dokter_umum');
})->with('role')->get();

foreach ($doctors as $doc) {
    echo sprintf(
        "   - %s (ID: %d, Role: %s, Level: %d, Hospital: %s)\n",
        $doc->name,
        $doc->id,
        $doc->role->name,
        $doc->role->level,
        $doc->hospital ?? 'alta'
    );
}

echo "\n3. CHECKING APPOINTMENTS BY HOSPITAL:\n";
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

$altaAppointments = MedicalForm::whereIn('form_type', $appointmentTypes)
    ->where(function ($q) {
        $q->where('hospital', 'alta')->orWhereNull('hospital');
    })
    ->count();

$roxwoodAppointments = MedicalForm::whereIn('form_type', $appointmentTypes)
    ->where('hospital', 'roxwood')
    ->count();

echo "   Alta/Null: $altaAppointments\n";
echo "   Roxwood: $roxwoodAppointments\n";

echo "\n4. SIMULATING STAFF MANAGER QUERY:\n";
if ($managers->isNotEmpty()) {
    $testManager = $managers->first();
    echo "   Testing with: {$testManager->name}\n";

    $user = $testManager;
    $isAdmin = $user->isAdmin();
    $userRole = $user->role->name ?? '';
    $userLevel = $user->role->level ?? 0;
    $userHospital = $isAdmin ? null : $user->getHospital();

    echo "   - isAdmin: " . ($isAdmin ? 'TRUE' : 'FALSE') . "\n";
    echo "   - userRole: $userRole\n";
    echo "   - userLevel: $userLevel\n";
    echo "   - userHospital: $userHospital\n";
    echo "   - Check (level >= 5): " . ($userLevel >= 5 ? 'TRUE ✅' : 'FALSE ❌') . "\n\n";

    // Simulate exact query from controller
    $recentAppointmentsQuery = MedicalForm::with('processedBy');

    // Apply hospital filter
    if (!$isAdmin) {
        $recentAppointmentsQuery->where('hospital', $userHospital);
    }

    // Apply role filter
    if ($userLevel >= 5) {
        $recentAppointmentsQuery->whereIn('form_type', $appointmentTypes);
    } else {
        $recentAppointmentsQuery->whereRaw('1 = 0');
    }

    $appointments = $recentAppointmentsQuery->orderBy('created_at', 'desc')->limit(5)->get();

    echo "   QUERY RESULT: {$appointments->count()} appointments\n";

    if ($appointments->count() > 0) {
        echo "   ✅ SUCCESS! Appointments:\n";
        foreach ($appointments as $apt) {
            echo sprintf(
                "      - %s: %s (Hospital: %s)\n",
                $apt->form_type,
                $apt->character_name,
                $apt->hospital ?? 'null'
            );
        }
    } else {
        echo "   ❌ FAILED - No appointments found!\n";
        echo "   Debugging:\n";
        echo "     - Total appointments in DB: " . MedicalForm::whereIn('form_type', $appointmentTypes)->count() . "\n";
        echo "     - For hospital '$userHospital': " . MedicalForm::whereIn('form_type', $appointmentTypes)->where('hospital', $userHospital)->count() . "\n";
    }
}

echo "\n5. CODE CHECK - VERIFYING CONTROLLER:\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/DashboardController.php';
$content = file_get_contents($controllerPath);

// Check if userLevel is defined
if (strpos($content, '$userLevel = $user->role->level ?? 0;') !== false) {
    echo "   ✅ \$userLevel extraction found\n";
} else {
    echo "   ❌ \$userLevel extraction NOT found\n";
}

// Check if level >= 5 condition exists
if (strpos($content, '$userLevel >= 5') !== false) {
    echo "   ✅ Level >= 5 condition found\n";
} else {
    echo "   ❌ Level >= 5 condition NOT found\n";
}

echo "\n=== END DEBUG ===\n";
