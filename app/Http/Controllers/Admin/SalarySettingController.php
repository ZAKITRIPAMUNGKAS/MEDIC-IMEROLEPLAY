<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalarySetting;
use App\Models\StaffRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalarySettingController extends Controller
{

    /**
     * Display a listing of salary settings
     */
    public function index()
    {
        $settings = SalarySetting::orderBy('role_name')->get();
        $roles = StaffRole::all();
        
        return view('admin.salary-settings.index', compact('settings', 'roles'));
    }

    /**
     * Show the form for creating a new salary setting
     */
    public function create()
    {
        $roles = StaffRole::all();
        return view('admin.salary-settings.create', compact('roles'));
    }

    /**
     * Store a newly created salary setting
     */
    public function store(Request $request)
    {
        $request->validate(SalarySetting::getValidationRules());

        try {
            DB::beginTransaction();

            SalarySetting::create([
                'role_name' => $request->role_name,
                'weekly_salary' => $request->weekly_salary,
                'description' => $request->description,
                'is_active' => $request->has('is_active')
            ]);

            DB::commit();

            return redirect()->route('admin.salary-settings.index')
                ->with('success', 'Setting gaji berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat setting gaji: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a salary setting
     */
    public function edit(SalarySetting $salarySetting)
    {
        $roles = StaffRole::all();
        return view('admin.salary-settings.edit', compact('salarySetting', 'roles'));
    }

    /**
     * Update the specified salary setting
     */
    public function update(Request $request, SalarySetting $salarySetting)
    {
        $rules = SalarySetting::getValidationRules();
        $rules['role_name'] = 'required|string|max:255|unique:salary_settings,role_name,' . $salarySetting->id;
        $request->validate($rules);

        try {
            DB::beginTransaction();

            $salarySetting->update([
                'role_name' => $request->role_name,
                'weekly_salary' => $request->weekly_salary,
                'description' => $request->description,
                'is_active' => $request->has('is_active')
            ]);

            DB::commit();

            return redirect()->route('admin.salary-settings.index')
                ->with('success', 'Setting gaji berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui setting gaji: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified salary setting
     */
    public function destroy(SalarySetting $salarySetting)
    {
        try {
            $salarySetting->delete();

            return redirect()->route('admin.salary-settings.index')
                ->with('success', 'Setting gaji berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus setting gaji: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(SalarySetting $salarySetting)
    {
        try {
            $salarySetting->update([
                'is_active' => !$salarySetting->is_active
            ]);

            $status = $salarySetting->is_active ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('admin.salary-settings.index')
                ->with('success', "Setting gaji berhasil {$status}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengubah status setting gaji: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create settings for all roles
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'default_weekly_salary' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $roles = StaffRole::all();
            $created = 0;

            foreach ($roles as $role) {
                // Skip if setting already exists
                if (SalarySetting::where('role_name', $role->name)->exists()) {
                    continue;
                }

                SalarySetting::create([
                    'role_name' => $role->name,
                    'weekly_salary' => $request->default_weekly_salary,
                    'description' => "Setting gaji untuk role {$role->name}",
                    'is_active' => true
                ]);

                $created++;
            }

            DB::commit();

            return redirect()->route('admin.salary-settings.index')
                ->with('success', "Berhasil membuat {$created} setting gaji untuk role yang belum memiliki setting!");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal membuat setting gaji: ' . $e->getMessage());
        }
    }
}