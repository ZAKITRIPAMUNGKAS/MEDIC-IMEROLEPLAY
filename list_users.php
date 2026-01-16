<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = DB::table('users')->where('name', 'like', '%Axel%')->get();

echo "Count: " . $users->count() . "\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Hosp: {$u->hospital}\n";
}
