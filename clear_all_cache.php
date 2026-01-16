<?php

/**
 * CLEAR ALL CACHE SCRIPT
 * Run this on production server to clear all Laravel caches
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== CLEARING ALL CACHES ===\n\n";

// Clear application cache
echo "1. Clearing application cache...\n";
Artisan::call('cache:clear');
echo "   ✅ Done\n\n";

// Clear config cache
echo "2. Clearing config cache...\n";
Artisan::call('config:clear');
echo "   ✅ Done\n\n";

// Clear route cache
echo "3. Clearing route cache...\n";
Artisan::call('route:clear');
echo "   ✅ Done\n\n";

// Clear view cache
echo "4. Clearing view cache...\n";
Artisan::call('view:clear');
echo "   ✅ Done\n\n";

// Clear compiled  
echo "5. Clearing compiled...\n";
Artisan::call('clear-compiled');
echo "   ✅ Done\n\n";

// Optimize if needed
echo "6. Re-caching config for performance...\n";
Artisan::call('config:cache');
echo "   ✅ Done\n\n";

echo "=== ALL CACHES CLEARED ===\n";
echo "Please refresh your browser (Ctrl+F5 for hard refresh)\n";
