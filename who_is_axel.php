<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$axel = User::where('name', 'like', '%Axel%')->first();
echo "Name: '" . $axel->name . "'\n";
echo "Hospital: '" . $axel->hospital . "'\n";

// Force update him manually by ID
$axel->hospital = 'roxwood';
$axel->save();
echo "Forced update to roxwood.\n";
