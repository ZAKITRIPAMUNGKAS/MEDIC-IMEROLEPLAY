<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalarySetting;

class SalarySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SISTEM LAMA (HARDCODED) - Tanpa sistem lembur, hanya hourly_rate
        $settings = [
            [
                'role_name' => 'trainee',
                'hourly_rate' => 0,
                'description' => 'Trainee atau magang dengan pembelajaran',
                'is_active' => true,
            ],
            [
                'role_name' => 'perawat',
                'hourly_rate' => 67375,
                'description' => 'Perawat dengan tanggung jawab perawatan pasien',
                'is_active' => true,
            ],
            [
                'role_name' => 'co_ass',
                'hourly_rate' => 74112,
                'description' => 'Co-assistant dengan tanggung jawab medis',
                'is_active' => true,
            ],
            [
                'role_name' => 'dokter_umum',
                'hourly_rate' => 85229,
                'description' => 'Dokter umum dengan tanggung jawab medis',
                'is_active' => true,
            ],
            [
                'role_name' => 'dokter_spesialis',
                'hourly_rate' => 98013,
                'description' => 'Dokter spesialis dengan keahlian khusus',
                'is_active' => true,
            ],
            [
                'role_name' => 'staff_manager',
                'hourly_rate' => 102174,
                'description' => 'Staff manager dengan tanggung jawab operasional',
                'is_active' => true,
            ],
            [
                'role_name' => 'manajer',
                'hourly_rate' => 112174,
                'description' => 'Manajer dengan tanggung jawab tim dan operasional',
                'is_active' => true,
            ],
            [
                'role_name' => 'executive',
                'hourly_rate' => 123986,
                'description' => 'Executive dengan tanggung jawab strategis',
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            SalarySetting::updateOrCreate(
                ['role_name' => $setting['role_name']],
                $setting
            );
        }
    }
}