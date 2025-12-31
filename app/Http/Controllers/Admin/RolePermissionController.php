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
            'view_dashboard' => 'View Dashboard',
            'manage_forms' => 'Manage Forms',
            'manage_users' => 'Manage Users',
            'view_reports' => 'View Reports',
            'manage_attendance' => 'Manage Attendance',
            'manage_attendance_advanced' => 'Manage Attendance Advanced (Force Checkout & Manual)',
            'manage_settings' => 'Manage Settings',
            'manage_payroll' => 'Manage Payroll',
            'reply_livechat' => 'Reply to Live Chat'
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
