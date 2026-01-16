<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('hospital', 'roxwood')->first();
if ($user) {
    echo "Found User: " . $user->name . "\n";
    if ($user->role) {
        echo "Role Name: '" . $user->role->name . "'\n";
    } else {
        echo "Role is NULL\n";
    }
} else {
    echo "No Roxwood user found\n";
}
