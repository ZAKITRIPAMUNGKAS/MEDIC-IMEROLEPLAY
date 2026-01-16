<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DIRECT SQL UPDATE ---\n";

// 1. Find ID
$user = DB::table('users')->where('name', 'like', '%Axel%')->first();
if ($user) {
    echo "Found ID: {$user->id}, Name: {$user->name}, Hospital: {$user->hospital}\n";

    // 2. Update
    $affected = DB::table('users')
        ->where('id', $user->id)
        ->update(['hospital' => 'roxwood']);

    echo "Updated $affected rows.\n";

    // 3. Verify
    $user = DB::table('users')->where('id', $user->id)->first();
    echo "New Hospital: {$user->hospital}\n";
} else {
    echo "Axel not found via SQL.\n";
}
