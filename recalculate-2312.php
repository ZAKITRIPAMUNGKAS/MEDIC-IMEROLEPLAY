<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update form #2312 with psychology test results
$form = App\Models\MedicalForm::find(2312);

if (!$form) {
    echo "Form not found!\n";
    exit(1);
}

if ($form->form_type !== 'surat_psikolog') {
    echo "Form is not surat_psikolog type!\n";
    exit(1);
}

echo "Updating form #{$form->id} - {$form->character_name}\n";
echo "Current form_type: {$form->form_type}\n\n";

$formData = $form->form_data;

// Helper to safe get int
$getVal = function ($key) use ($formData) {
    return intval($formData[$key] ?? 3);
};

// 1. Calculate BFI scores
$bfi_scores = [];
$bfi_scores['extraversion'] = ($getVal('bigfive1') + (6 - $getVal('bigfive6'))) / 2;
$bfi_scores['agreeableness'] = ((6 - $getVal('bigfive2')) + $getVal('bigfive7')) / 2;
$bfi_scores['conscientiousness'] = ($getVal('bigfive3') + (6 - $getVal('bigfive8'))) / 2;
$bfi_scores['neuroticism'] = ($getVal('bigfive4') + (6 - $getVal('bigfive9'))) / 2;
$bfi_scores['openness'] = ($getVal('bigfive5') + (6 - $getVal('bigfive10'))) / 2;

// 2. Calculate PSS score
$pss_score = 0;
for ($i = 1; $i <= 10; $i++) {
    $val = intval($formData['stress' . $i] ?? 2);
    if (in_array($i, [4, 5, 7, 8])) {
        $pss_score += (4 - $val);
    } else {
        $pss_score += $val;
    }
}

// 3. Calculate RSES score
$rses_score = 0;
for ($i = 1; $i <= 10; $i++) {
    $val = intval($formData['esteem' . $i] ?? 2);
    if (in_array($i, [3, 5, 8, 9, 10])) {
        $rses_score += (5 - $val);
    } else {
        $rses_score += $val;
    }
}

// 4. Generate suggestions
$suggestions = [];

if ($pss_score >= 27) {
    $suggestions[] = "Skor stres Anda tergolong TINGGI. Disarankan untuk segera berkonsultasi dengan psikolog kami untuk manajemen stres, dan luangkan waktu untuk relaksasi atau aktivitas yang menyenangkan.";
} elseif ($pss_score >= 14) {
    $suggestions[] = "Skor stres Anda tergolong SEDANG. Cobalah teknik pernapasan atau meditasi ringan, dan pastikan keseimbangan antara pekerjaan dan istirahat.";
} else {
    $suggestions[] = "Skor stres Anda tergolong RENDAH. Pertahankan gaya hidup sehat Anda.";
}

if ($rses_score < 15) {
    $suggestions[] = "Skor harga diri Anda tergolong RENDAH. Kami menyarankan sesi konseling untuk membantu membangun kepercayaan diri dan melihat potensi positif dalam diri Anda.";
} elseif ($rses_score > 25) {
    $suggestions[] = "Skor harga diri Anda TINGGI/NORMAL. Anda memiliki pandangan positif terhadap diri sendiri.";
} else {
    $suggestions[] = "Skor harga diri Anda dalam batas NORMAL. Terus kembangkan potensi diri Anda.";
}

$traits = $bfi_scores;
arsort($traits);
$top_trait = array_key_first($traits);
$trait_names = [
    'extraversion' => 'Ekstroversi',
    'agreeableness' => 'Keramahan',
    'conscientiousness' => 'Ketekunan',
    'neuroticism' => 'Neurotisme (Sensitivitas Emosi)',
    'openness' => 'Keterbukaan'
];
$suggestions[] = "Sifat dominan Anda adalah " . $trait_names[$top_trait] . ". Gunakan kekuatan ini dalam aktivitas Anda sehari-hari.";

// Update form_data
$formData['bfi_scores'] = $bfi_scores;
$formData['pss_score'] = $pss_score;
$formData['rses_score'] = $rses_score;
$formData['suggestions'] = $suggestions;

$form->form_data = $formData;
$form->save();

echo "✅ Successfully updated form #{$form->id}\n\n";
echo "=== RESULTS ===\n";
echo "PSS Score: $pss_score\n";
echo "RSES Score: $rses_score\n";
echo "BFI Scores:\n";
foreach ($bfi_scores as $trait => $score) {
    echo "  - {$trait_names[$trait]}: " . number_format($score, 2) . "\n";
}
echo "\nSuggestions:\n";
foreach ($suggestions as $i => $sugg) {
    echo ($i + 1) . ". " . $sugg . "\n";
}
