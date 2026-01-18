<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\MedicalForm;

echo "=== SIDE-BY-SIDE COMPARISON ===\n\n";

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

// Get a staff manager
$staffManager = User::whereHas('role', function ($q) {
    $q->where('name', 'staff_manager');
})->first();

// Get a dokter umum  
$dokterUmum = User::whereHas('role', function ($q) {
    $q->where('name', 'dokter_umum');
})->first();

function testQuery($user, $appointmentTypes)
{
    $isAdmin = $user->isAdmin();
    $userRole = $user->role->name ?? '';
    $userLevel = $user->role->level ?? 0;
    $userHospital = $isAdmin ? null : $user->getHospital();

    echo "User: {$user->name}\n";
    echo "  Role: $userRole (Level $userLevel)\n";
    echo "  Hospital: $userHospital\n";
    echo "  isAdmin: " . ($isAdmin ? 'yes' : 'no') . "\n";

    // Build query like controller
    $recentAppointmentsQuery = MedicalForm::with('processedBy');

    // Apply hospital filter (line 110-114)
    if (!$isAdmin) {
        $recentAppointmentsQuery->where('hospital', $userHospital);
        echo "  Hospital filter: Applied (where hospital = $userHospital)\n";
    } else {
        echo "  Hospital filter: Not applied (admin)\n";
    }

    // Apply role filter - EXACT logic from controller
    if ($isAdmin) {
        $recentAppointmentsQuery->whereIn('form_type', $appointmentTypes);
        echo "  Role filter: Admin - show all appointments\n";
    } elseif ($userRole === 'trainee') {
        $recentAppointmentsQuery->whereRaw('1 = 0');
        echo "  Role filter: Trainee - show nothing\n";
    } elseif ($userRole === 'co_ass') {
        $recentAppointmentsQuery->whereRaw('1 = 0');
        echo "  Role filter: Co-Ass - show nothing\n";
    } elseif (in_array($userRole, ['dokter_umum', 'dokter_spesialis'])) {
        $recentAppointmentsQuery->whereIn('form_type', $appointmentTypes);
        echo "  Role filter: Doctor - show appointments\n";
    } elseif ($userLevel >= 5) {
        $recentAppointmentsQuery->whereIn('form_type', $appointmentTypes);
        echo "  Role filter: Manager (level >= 5) - show appointments\n";
    } else {
        $recentAppointmentsQuery->whereRaw('1 = 0');
        echo "  Role filter: Other - show nothing\n";
    }

    $appointments = $recentAppointmentsQuery->orderBy('created_at', 'desc')->limit(5)->get();

    echo "  RESULT: {$appointments->count()} appointments\n";

    if ($appointments->count() > 0) {
        foreach ($appointments as $apt) {
            echo sprintf("    - %s: %s\n", $apt->form_type, $apt->character_name);
        }
    }

    echo "\n";
}

if ($staffManager) {
    echo "=== STAFF MANAGER ===\n";
    testQuery($staffManager, $appointmentTypes);
} else {
    echo "❌ No staff manager found\n\n";
}

if ($dokterUmum) {
    echo "=== DOKTER UMUM ===\n";
    testQuery($dokterUmum, $appointmentTypes);
} else {
    echo "❌ No dokter umum found\n\n";
}

echo "=== END ===\n";
