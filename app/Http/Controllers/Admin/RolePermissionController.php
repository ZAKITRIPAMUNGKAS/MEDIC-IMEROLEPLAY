<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // ... existing index and togglePermission ...

    /**
     * Toggle reply_livechat permission for a specific user
     */
    public function toggleUserChatPermission(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $permissions = $user->custom_permissions ?? [];
        $permission = 'reply_livechat';

        if (in_array($permission, $permissions)) {
            $permissions = array_values(array_diff($permissions, [$permission]));
            $action = 'removed';
            $message = "Izin reply chat dicabut dari {$user->name}";
        } else {
            $permissions[] = $permission;
            $action = 'added';
            $message = "Izin reply chat diberikan ke {$user->name}";
        }

        $user->custom_permissions = $permissions;
        $user->save();

        return back()->with('success', $message);
    }

    /**
     * Show all roles with their permissions
     */
    public function index()
    {
        $roles = StaffRole::orderBy('level', 'desc')->get();

        // List of all available permissions
        $allPermissions = [
            'view_dashboard' => 'Lihat Dashboard (Ringkasan statistik & informasi utama)',
            'manage_forms' => 'Kelola Formulir (Menyetujui/menolak formulir masuk)',
            'manage_users' => 'Kelola Staff (Tambah, edit, & kontrol akun staff)',
            'view_reports' => 'Lihat Laporan (Akses laporan umum di dashboard)',
            'manage_attendance' => 'Kelola Absensi (Lihat dan kelola data absensi harian)',
            'manage_attendance_advanced' => 'Kelola Absensi Manual (Input absensi manual secara mandiri)',
            'force_checkout' => 'Force Checkout (Hanya untuk mengakhiri sesi yang macet)',
            'view_attendance_reports' => 'Lihat Laporan Absensi (Rekapan absensi - Read Only)',
            'manage_settings' => 'Pengaturan Sistem (Konfigurasi dasar aplikasi)',
            'manage_payroll' => 'Daftar Gaji (Kelola dan proses gaji mingguan)',
            'manage_salary_settings' => 'Atur Gaji (Konfigurasi gaji dasar & bonus per jabatan)',
            'manage_reimbursements' => 'Pelacakan Reimbursement (Kelola klaim pengembalian dana)',
            'manage_meeting_requests' => 'Kelola Pertemuan (Setujui/tolak permintaan meeting)',
            'access_live_chat' => 'Akses Live Chat (Melihat riwayat pesan bantuan)',
            'reply_livechat' => 'Balas Live Chat (Membalas pesan bantuan warga)',
            'access_feedback' => 'Akses Kritik & Saran (Melihat laporan & masukan warga)'
        ];

        return view('admin.roles.permissions', compact('roles', 'allPermissions'));
    }

    /**
     * Toggle a specific permission for a role
     */
    public function togglePermission(Request $request, $roleId)
    {
        $request->validate([
            'permission' => 'required|string'
        ]);

        $role = StaffRole::findOrFail($roleId);
        $permission = $request->permission;

        $permissions = $role->permissions ?? [];

        // Toggle permission
        if (in_array($permission, $permissions)) {
            // Remove permission
            $permissions = array_values(array_diff($permissions, [$permission]));
            $action = 'removed';
        } else {
            // Add permission
            $permissions[] = $permission;
            $action = 'added';
        }

        $role->permissions = $permissions;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => "Permission {$action} successfully",
            'action' => $action,
            'has_permission' => $action === 'added'
        ]);
    }
}
