<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Debug Telegram Multiple Chat IDs

use App\Models\TelegramSetting;

$settings = TelegramSetting::first();

if (!$settings) {
    echo "No Telegram settings found!\n";
    exit;
}

echo "=== TELEGRAM DEBUG ===\n\n";

echo "Raw chat_ids: " . $settings->chat_ids . "\n";
echo "Chat IDs array: " . print_r($settings->chat_ids_array, true) . "\n";
echo "Count: " . count($settings->chat_ids_array) . "\n";

echo "\n=== TESTING LOOP ===\n";
foreach ($settings->chat_ids_array as $index => $chatId) {
    $chatId = trim($chatId);
    echo "[$index] Chat ID: '$chatId' (length: " . strlen($chatId) . ")\n";
}
