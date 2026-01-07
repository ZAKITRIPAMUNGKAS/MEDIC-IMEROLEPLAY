<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if staff_roles table exists before trying to update it
        if (!Schema::hasTable('staff_roles')) {
            return; // Skip if table doesn't exist yet
        }

        // Add manage_payroll permission to existing roles
        $roles = DB::table('staff_roles')->get();

        foreach ($roles as $role) {
            $permissions = json_decode($role->permissions, true) ?? [];

            // Add manage_payroll permission to admin and manager roles
            if (in_array($role->name, ['admin', 'manajer', 'executive'])) {
                if (!in_array('manage_payroll', $permissions)) {
                    $permissions[] = 'manage_payroll';
                }
            }

            // Update the role with new permissions
            DB::table('staff_roles')
                ->where('id', $role->id)
                ->update(['permissions' => json_encode($permissions)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if staff_roles table exists
        if (!Schema::hasTable('staff_roles')) {
            return; // Skip if table doesn't exist
        }

        // Remove manage_payroll permission from all roles
        $roles = DB::table('staff_roles')->get();

        foreach ($roles as $role) {
            $permissions = json_decode($role->permissions, true) ?? [];

            // Remove manage_payroll permission
            $permissions = array_filter($permissions, function ($permission) {
                return $permission !== 'manage_payroll';
            });

            // Update the role with updated permissions
            DB::table('staff_roles')
                ->where('id', $role->id)
                ->update(['permissions' => json_encode(array_values($permissions))]);
        }
    }
};
