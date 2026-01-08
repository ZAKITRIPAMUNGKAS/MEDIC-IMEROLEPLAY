<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$form = App\Models\MedicalForm::find(2312);

if (!$form) {
    echo "Form not found!\n";
    exit(1);
}

echo "Form ID: " . $form->id . "\n";
echo "Form Type: " . $form->form_type . "\n";
echo "Character Name: " . $form->character_name . "\n";
echo "Created At: " . $form->created_at . "\n\n";

echo "=== FORM DATA ===\n";
$formData = $form->form_data;

echo "Has suggestions: " . (isset($formData['suggestions']) ? 'YES' : 'NO') . "\n";
echo "Has pss_score: " . (isset($formData['pss_score']) ? 'YES (' . $formData['pss_score'] . ')' : 'NO') . "\n";
echo "Has rses_score: " . (isset($formData['rses_score']) ? 'YES (' . $formData['rses_score'] . ')' : 'NO') . "\n";
echo "Has bfi_scores: " . (isset($formData['bfi_scores']) ? 'YES' : 'NO') . "\n";

if (isset($formData['suggestions'])) {
    echo "\nSuggestions:\n";
    foreach ($formData['suggestions'] as $i => $sugg) {
        echo ($i + 1) . ". " . $sugg . "\n";
    }
} else {
    echo "\n⚠️ NO SUGGESTIONS FOUND - This is why results don't show!\n";

    // Check if we have the raw test data to recalculate
    $hasStressData = false;
    for ($i = 1; $i <= 10; $i++) {
        if (isset($formData['stress' . $i])) {
            $hasStressData = true;
            break;
        }
    }

    if ($hasStressData) {
        echo "✅ But has stress test data - can recalculate!\n";
    } else {
        echo "❌ No test data found either\n";
    }
}
