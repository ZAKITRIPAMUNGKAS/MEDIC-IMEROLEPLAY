<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSubRoleSeeder extends Seeder
{
    public function run(): void
    {
        $subRoles = [
            [
                'name'         => 'ie',
                'display_name' => 'Industrial & Employee Relations',
                'short_name'   => 'IE',
                'hospital'     => 'alta',
                'description'  => 'Mengelola hubungan industri dan ketenagakerjaan, kalkulasi denda resign, dan surat perjanjian kontrak medis.',
                'color'        => '#ef4444', // red
                'is_active'    => true,
                'sort_order'   => 1,
            ],
            [
                'name'         => 'pnd',
                'display_name' => 'People & Development',
                'short_name'   => 'PND',
                'hospital'     => 'alta',
                'description'  => 'Mengelola pengembangan SDM, sertifikasi operasi, verifikasi resign, dan periode kenaikan jabatan.',
                'color'        => '#3b82f6', // blue
                'is_active'    => true,
                'sort_order'   => 2,
            ],
            [
                'name'         => 'msl',
                'display_name' => 'Dept. Head of Medical Science & Laboratory',
                'short_name'   => 'MSL',
                'hospital'     => 'alta',
                'description'  => 'Mengelola sertifikasi visum, pengajuan stase, dan kualifikasi laboratorium medis.',
                'color'        => '#10b981', // emerald
                'is_active'    => true,
                'sort_order'   => 3,
            ],
            [
                'name'         => 'ga',
                'display_name' => 'General Affairs',
                'short_name'   => 'GA',
                'hospital'     => 'alta',
                'description'  => 'Mengelola sertifikasi kendaraan (darat & heli) dan urusan umum operasional rumah sakit.',
                'color'        => '#f59e0b', // amber
                'is_active'    => true,
                'sort_order'   => 4,
            ],
            [
                'name'         => 'comdis',
                'display_name' => 'Disciplinary Committee',
                'short_name'   => 'Comdis',
                'hospital'     => 'alta',
                'description'  => 'Mengelola penegakan disiplin, input Credit Score, dan penanganan pelanggaran anggota.',
                'color'        => '#8b5cf6', // violet
                'is_active'    => true,
                'sort_order'   => 5,
            ],
        ];

        foreach ($subRoles as $subRole) {
            DB::table('staff_sub_roles')->updateOrInsert(
                ['name' => $subRole['name'], 'hospital' => $subRole['hospital']],
                array_merge($subRole, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
