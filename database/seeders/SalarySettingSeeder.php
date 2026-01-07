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
        // Updated to use weekly_salary column
        $settings = [
            [
                'role_name' => 'trainee',
                'weekly_salary' => 61250,
                'description' => 'Trainee atau magang dengan pembelajaran',
                'is_active' => true,
            ],
            [
                'role_name' => 'nurse',
                'weekly_salary' => 67375,
                'description' => 'Perawat dengan tanggung jawab perawatan pasien',
                'is_active' => true,
            ],
            [
                'role_name' => 'co_ass',
                'weekly_salary' => 74124,
                'description' => 'Co-assistant dengan tanggung jawab medis',
                'is_active' => true,
            ],
            [
                'role_name' => 'general_doctor',
                'weekly_salary' => 85229,
                'description' => 'Dokter umum dengan tanggung jawab medis',
                'is_active' => true,
            ],
            [
                'role_name' => 'specialist_doctor',
                'weekly_salary' => 98013,
                'description' => 'Dokter spesialis dengan keahlian khusus',
                'is_active' => true,
            ],
            [
                'role_name' => 'staff_manager',
                'weekly_salary' => 102174,
                'description' => 'Staff manager dengan tanggung jawab operasional',
                'is_active' => true,
            ],
            [
                'role_name' => 'manager',
                'weekly_salary' => 112174,
                'description' => 'Manajer dengan tanggung jawab tim dan operasional',
                'is_active' => true,
            ],
            [
                'role_name' => 'executive',
                'weekly_salary' => 123986,
                'description' => 'Executive dengan tanggung jawab strategis',
                'is_active' => true,
            ],
            [
                'role_name' => 'ceo',
                'weekly_salary' => 253986,
                'description' => 'CEO PEMEGANG SAHAM RUMAH SAKIT',
                'is_active' => true,
            ],
            [
                'role_name' => 'it_support',
                'weekly_salary' => 150000,
                'description' => 'Web developer & Admin',
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