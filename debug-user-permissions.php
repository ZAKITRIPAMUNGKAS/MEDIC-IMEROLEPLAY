<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::find(4);

if (!$user) {
    echo "User not found\n";
    exit(1);
}

echo "=== USER DEBUG INFO ===\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role ID: " . ($user->role_id ?? 'NULL') . "\n";

// Get raw database value
$rawValue = $user->getAttributes()['custom_permissions'] ?? null;
echo "\n=== RAW DATABASE VALUE ===\n";
echo "Type: " . gettype($rawValue) . "\n";
echo "Value: " . ($rawValue === null ? 'NULL' : $rawValue) . "\n";

// Get casted value
echo "\n=== CASTED VALUE (from model) ===\n";
$castedValue = $user->custom_permissions;
echo "Type: " . gettype($castedValue) . "\n";
echo "Value: " . json_encode($castedValue) . "\n";
echo "Is Array: " . (is_array($castedValue) ? 'YES' : 'NO') . "\n";
if (is_array($castedValue)) {
    echo "Array Count: " . count($castedValue) . "\n";
    echo "Contents: " . print_r($castedValue, true) . "\n";
}

// Test hasPermission method
echo "\n=== PERMISSION CHECKS ===\n";
echo "Has 'access_live_chat': " . ($user->hasPermission('access_live_chat') ? 'YES ✅' : 'NO ❌') . "\n";
echo "Has 'access_feedback': " . ($user->hasPermission('access_feedback') ? 'YES ✅' : 'NO ❌') . "\n";
echo "Has 'manage_users': " . ($user->hasPermission('manage_users') ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n=== DONE ===\n";
