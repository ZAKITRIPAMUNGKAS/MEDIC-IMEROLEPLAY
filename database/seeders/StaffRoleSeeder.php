<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StaffRole;

class StaffRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // Tetap pertahankan admin untuk kompatibilitas (tidak ditampilkan di public)
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'level' => 999, // Level tertinggi tapi tidak ditampilkan
                'description' => 'Akses penuh ke semua fitur sistem',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_users',
                    'view_reports',
                    'manage_attendance',
                    'manage_attendance_advanced', // Force checkout & manual attendance
                    'manage_settings',
                    'manage_payroll',
                    'manage_reimbursements',
                    'reply_livechat' // Can reply to live chat as admin
                ]
            ],

            // 7 EXECUTIVE - Level tertinggi yang ditampilkan
            [
                'name' => 'executive',
                'display_name' => 'Executive',
                'level' => 7,
                'description' => 'Level eksekutif dengan akses penuh ke sistem.',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_users',
                    'view_reports',
                    'manage_attendance',
                    'manage_attendance_advanced', // Force checkout & manual attendance
                    'manage_settings',
                    'manage_payroll',
                    'manage_reimbursements',
                    'reply_livechat' // Can reply to live chat as admin
                ]
            ],
            // 6 MANAJER
            [
                'name' => 'manajer',
                'display_name' => 'Manajer',
                'level' => 6,
                'description' => 'Manajer dengan akses penuh ke sistem.',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_users',
                    'view_reports',
                    'manage_attendance',
                    'manage_attendance_advanced', // Force checkout & manual attendance
                    'manage_settings',
                    'manage_payroll'
                ]
            ],
            // 5 STAFF MANAGER
            [
                'name' => 'staff_manager',
                'display_name' => 'Staff Manager',
                'level' => 5,
                'description' => 'Manajer staf dengan akses penuh ke sistem.',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_users',
                    'view_reports',
                    'manage_attendance',
                    'manage_attendance_advanced', // Force checkout & manual attendance
                    'manage_settings'
                ]
            ],
            // 4 DOKTER SPESIALIS
            [
                'name' => 'dokter_spesialis',
                'display_name' => 'Dokter Spesialis',
                'level' => 4,
                'description' => 'Dokter spesialis dengan akses luas',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_attendance',
                    'view_reports',
                    'view_leaderboard'
                ]
            ],
            // 3 DOKTER UMUM
            [
                'name' => 'dokter_umum',
                'display_name' => 'Dokter Umum',
                'level' => 3,
                'description' => 'Dokter umum dengan akses laporan',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_attendance',
                    'view_reports'
                ]
            ],
            // 2 Co-Ass
            [
                'name' => 'co_ass',
                'display_name' => 'Co-Ass',
                'level' => 2,
                'description' => 'Co-assistant dengan akses klinis dasar',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_attendance'
                ]
            ],
            // 1 PERAWAT
            [
                'name' => 'perawat',
                'display_name' => 'Perawat',
                'level' => 1,
                'description' => 'Perawat dengan akses pengelolaan formulir dasar',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'manage_attendance'
                ]
            ],
            // 0 Trainee - Level terendah
            [
                'name' => 'trainee',
                'display_name' => 'Trainee',
                'level' => 0,
                'description' => 'Staf pelatihan dengan akses terbatas',
                'permissions' => [
                    'view_dashboard'
                ]
            ],

            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'level' => 4, // Setara Dokter Spesialis
                'description' => 'Peran lama (kompatibilitas) setara Dokter Spesialis',
                'permissions' => [
                    'view_dashboard',
                    'manage_forms',
                    'view_reports',
                    'manage_attendance',
                    'view_leaderboard'
                ]
            ]
        ];

        foreach ($roles as $role) {
            StaffRole::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
