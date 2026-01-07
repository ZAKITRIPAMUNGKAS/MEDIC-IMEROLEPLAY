<?php

/**
 * Script untuk membuat struktur organisasi EMS default di database
 * Run dengan: php create-default-structure.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OrganizationalStructure;

// Data struktur EMS default
$structureData = [
    'high_command' => [
        'name' => 'N/A',
        'title' => 'High Command (Pimpinan Tertinggi)'
    ],
    'departments' => [
        [
            'title' => 'Emergency Department',
            'name' => 'N/A',
            'members' => [
                ['role' => 'Emergency Coordinator', 'name' => 'N/A'],
                ['role' => 'Emergency Deputy', 'name' => 'N/A'],
                ['role' => 'Paramedic I', 'name' => 'N/A'],
                ['role' => 'Paramedic II', 'name' => 'N/A'],
                ['role' => 'Paramedic III', 'name' => 'N/A'],
            ]
        ],
        [
            'title' => 'Medical Department',
            'name' => 'N/A',
            'members' => [
                ['role' => 'Medical Coordinator', 'name' => 'N/A'],
                ['role' => 'Medical Deputy', 'name' => 'N/A'],
                ['role' => 'Dokter Umum I', 'name' => 'N/A'],
                ['role' => 'Dokter Umum II', 'name' => 'N/A'],
                ['role' => 'Perawat I', 'name' => 'N/A'],
                ['role' => 'Perawat II', 'name' => 'N/A'],
            ]
        ],
        [
            'title' => 'Surgery Department',
            'name' => 'N/A',
            'members' => [
                ['role' => 'Surgery Coordinator', 'name' => 'N/A'],
                ['role' => 'Surgery Deputy', 'name' => 'N/A'],
                ['role' => 'Dokter Bedah', 'name' => 'N/A'],
                ['role' => 'Anestesi', 'name' => 'N/A'],
            ]
        ],
        [
            'title' => 'Psychology Department',
            'name' => 'N/A',
            'members' => [
                ['role' => 'Psychology Coordinator', 'name' => 'N/A'],
                ['role' => 'Psikolog I', 'name' => 'N/A'],
                ['role' => 'Psikolog II', 'name' => 'N/A'],
            ]
        ],
        [
            'title' => 'Training Department',
            'name' => 'N/A',
            'members' => [
                ['role' => 'Training Coordinator', 'name' => 'N/A'],
                ['role' => 'Instruktur I', 'name' => 'N/A'],
                ['role' => 'Instruktur II', 'name' => 'N/A'],
            ]
        ],
    ]
];

try {
    // Cek apakah sudah ada struktur EMS yang aktif
    $existing = OrganizationalStructure::where('hospital_type', 'ems')
        ->where('is_active', true)
        ->first();

    if ($existing) {
        echo "✓ Struktur EMS sudah ada (ID: {$existing->id})\n";
        echo "  Apakah ingin update? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) != 'y') {
            echo "Dibatalkan.\n";
            exit;
        }

        $existing->structure_data = $structureData;
        $existing->save();
        echo "✓ Struktur EMS berhasil diupdate!\n";
    } else {
        // Buat struktur baru
        $structure = OrganizationalStructure::create([
            'name' => 'Struktur EMS Default',
            'hospital_type' => 'ems',
            'structure_data' => $structureData,
            'required_names' => [],
            'is_active' => true
        ]);

        echo "✓ Struktur EMS default berhasil dibuat!\n";
        echo "  ID: {$structure->id}\n";
        echo "  Status: Aktif\n";
    }

    echo "\nSekarang buka: http://127.0.0.1:8000/admin/organizational-structure\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
