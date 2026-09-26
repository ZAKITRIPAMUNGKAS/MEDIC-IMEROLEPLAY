<?php
/**
 * Tailwind CSS Build Script - Run on server via SSH/CLI
 * This will rebuild app.css with proper purging using the updated tailwind.config.js
 * 
 * Usage (via SSH on server):
 *   cd /path/to/MEDIC-IMEROLEPLAY
 *   php build_css.php
 * 
 * Requirements: Node.js must be installed on server
 */

if (php_sapi_name() !== 'cli') {
    // Protect from web access
    $key = $_GET['key'] ?? '';
    if ($key !== 'ime_build_2024') {
        die('CLI use only. Or add ?key=ime_build_2024 for web access.');
    }
}

$basePath = __DIR__;
echo "Working directory: $basePath\n";

// Check if node is available
exec('which node 2>&1', $nodeOut, $nodeCode);
exec('which npm 2>&1', $npmOut, $npmCode);

if ($nodeCode !== 0) {
    die("ERROR: Node.js not found. Install Node.js first.\n");
}
if ($npmCode !== 0) {
    die("ERROR: npm not found.\n");
}

echo "Node: " . trim($nodeOut[0]) . "\n";
echo "npm: " . trim($npmOut[0]) . "\n\n";

// Install deps if needed
if (!is_dir($basePath . '/node_modules')) {
    echo "Installing node_modules...\n";
    exec("cd $basePath && npm install 2>&1", $out, $code);
    echo implode("\n", $out) . "\n";
}

// Run build
echo "Building CSS with --minify...\n";
exec("cd $basePath && npm run build 2>&1", $buildOut, $buildCode);
echo implode("\n", $buildOut) . "\n";

if ($buildCode === 0) {
    $size = round(filesize($basePath . '/public/css/app.css') / 1024, 1);
    echo "\n✅ SUCCESS! app.css rebuilt: {$size} KB\n";
} else {
    echo "\n❌ Build failed (exit code: $buildCode)\n";
}
