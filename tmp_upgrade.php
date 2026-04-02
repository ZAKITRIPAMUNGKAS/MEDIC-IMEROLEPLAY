<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::statement("ALTER TABLE medical_forms ADD COLUMN cancelled_at TIMESTAMP NULL AFTER notes");
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    Illuminate\Support\Facades\DB::statement("ALTER TABLE medical_forms MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending'");
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    Illuminate\Support\Facades\DB::statement("ALTER TABLE payrolls ADD COLUMN cancel_reason TEXT NULL AFTER status");
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

try {
    Illuminate\Support\Facades\DB::statement("ALTER TABLE payrolls MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
} catch (\Exception $e) { echo $e->getMessage() . "\n"; }

echo "DONE\n";
