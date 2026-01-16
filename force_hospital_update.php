<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- FORCING ROXWOOD HOSPITAL UPDATE ---\n";

// Case-insensitive search for 'rh' in name or staff_id
$users = User::where(function ($q) {
    $q->whereRaw('LOWER(name) LIKE ?', ['%rh%'])
        ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh%']);
})->get();

echo "Found " . $users->count() . " potential RH users.\n";

$count = 0;
foreach ($users as $user) {
    if ($user->hospital !== 'roxwood') {
        echo " [UPDATE] {$user->name} ({$user->hospital}) -> roxwood\n";
        $user->hospital = 'roxwood';
        $user->save();
        $count++;
    } else {
        // echo " [OK] {$user->name} is already roxwood.\n";
    }
}

echo "Updated $count users.\n";

// Verify Axel
$axel = User::where('name', 'like', '%Axel%')->first();
echo "\nVerifying Axel:\n";
echo "Name: {$axel->name}\n";
echo "Hospital: {$axel->hospital}\n";
