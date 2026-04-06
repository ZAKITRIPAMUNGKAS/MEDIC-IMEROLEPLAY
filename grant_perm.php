<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StaffRole;

$role = StaffRole::where('name', 'admin')->first();
if ($role) {
    echo "Found Admin Role.\n";
    $permissions = $role->permissions ?? [];
    if (!in_array('force_checkout', $permissions)) {
        $permissions[] = 'force_checkout';
        $role->permissions = $permissions;
        $role->save();
        echo "Granted 'force_checkout' to Admin role.\n";
    } else {
        echo "Admin already has 'force_checkout'.\n";
    }

    // Also grant 'manage_attendance_advanced' just in case
    if (!in_array('manage_attendance_advanced', $permissions)) {
        $permissions[] = 'manage_attendance_advanced';
        $role->permissions = $permissions;
        $role->save();
        echo "Granted 'manage_attendance_advanced' to Admin role.\n";
    }
} else {
    echo "Admin role not found.\n";
}
