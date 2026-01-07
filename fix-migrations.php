<?php

/**
 * This script adds Schema::hasTable checks to all migrations that modify existing tables
 * Run this once to fix all migrations at once
 */

$migrationsPath = 'd:/website/EMS-IME/public_html/database/migrations';
$files = glob($migrationsPath . '/*.php');

$fixedCount = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);

    // Skip if already has hasTable check
    if (strpos($content, 'Schema::hasTable') !== false) {
        continue;
    }

    // Skip create migrations
    if (strpos($content, 'Schema::create') !== false) {
        continue;
    }

    // Only process migrations that use Schema::table
    if (strpos($content, 'Schema::table') === false) {
        continue;
    }

    // Extract table name from Schema::table('table_name'
    if (preg_match("/Schema::table\('([^']+)'/", $content, $matches)) {
        $tableName = $matches[1];

        // Add hasTable check in up() method
        $content = preg_replace(
            '/(public function up\(\): void\s*\{)/',
            "$1\n        // Check if table exists before modifying\n        if (!Schema::hasTable('{$tableName}')) {\n            return;\n        }\n",
            $content,
            1
        );

        // Add hasTable check in down() method  
        $content = preg_replace(
            '/(public function down\(\): void\s*\{)/',
            "$1\n        if (!Schema::hasTable('{$tableName}')) {\n            return;\n        }\n",
            $content,
            1
        );

        file_put_contents($file, $content);
        $fixedCount++;
        echo "Fixed: " . basename($file) . " (table: $tableName)\n";
    }
}

echo "\nTotal migrations fixed: $fixedCount\n";
