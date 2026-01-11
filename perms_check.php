<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "--- PERMISSION CHECK RESULTS ---\n";
$roles = \App\Models\StaffRole::all();
foreach ($roles as $role) {
    if (in_array('manage_users', $role->permissions ?? [])) {
        $output .= "ROLE: " . $role->name . " | Display: " . $role->display_name . "\n";
    }
}

$users = \App\Models\User::whereNotNull('custom_permissions')->get();
foreach ($users as $user) {
    if (in_array('manage_users', $user->custom_permissions ?? [])) {
        $output .= "USER: " . $user->name . " | Email: " . $user->email . "\n";
    }
}
$output .= "--- END RESULTS ---\n";

file_put_contents('perms_output.txt', $output);
echo "Done.\n";
