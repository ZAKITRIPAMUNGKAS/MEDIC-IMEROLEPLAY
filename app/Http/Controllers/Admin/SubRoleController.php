<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffSubRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubRoleController extends Controller
{
    /**
     * Pastikan user adalah Executive ke atas atau Admin.
     */
    private function checkAccess(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isExecutiveOrAbove()) {
            abort(403, 'Akses ditolak. Hanya Executive atau Admin yang dapat mengelola sub-jabatan.');
        }
    }

    /**
     * GET /admin/sub-roles
     * Daftar semua sub-jabatan beserta jumlah anggota.
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $hospital = $request->get('hospital', 'alta');

        $subRoles = StaffSubRole::withCount('users')
            ->forHospital($hospital)
            ->orderBy('sort_order')
            ->get();

        // Staf yang belum punya sub-jabatan (untuk info)
        $unassigned = User::where('is_active', true)
            ->whereNotNull('role_id')
            ->whereNull('sub_role_id')
            ->where('hospital', $hospital)
            ->count();

        return view('admin.sub-roles.index', compact('subRoles', 'hospital', 'unassigned'));
    }

    /**
     * GET /admin/sub-roles/{subRole}/members
     * Lihat anggota dalam satu divisi.
     */
    public function members(StaffSubRole $subRole, Request $request)
    {
        $this->checkAccess();

        $search = trim($request->get('q', ''));

        $query = User::with('role:id,name,display_name,level')
            ->where('sub_role_id', $subRole->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.sub-roles.members', compact('subRole', 'members', 'search'));
    }

    /**
     * GET /admin/sub-roles/assign
     * Form assign sub-jabatan ke staf.
     */
    public function assignForm(Request $request)
    {
        $this->checkAccess();

        $hospital = $request->get('hospital', 'alta');
        $search   = trim($request->get('q', ''));

        $subRolesQuery = StaffSubRole::active()->orderBy('sort_order');
        if ($hospital && $hospital !== 'all') {
            $subRolesQuery->forHospital($hospital);
        }
        $subRoles = $subRolesQuery->get();

        // Staf aktif sesuai hospital, urutkan by level
        $staffQuery = User::with(['role:id,name,display_name,level', 'subRole:id,name,short_name,color'])
            ->where('is_active', true)
            ->whereNotNull('role_id')
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'admin'));

        if ($hospital && $hospital !== 'all') {
            $staffQuery->where('hospital', $hospital);
        }

        if ($search) {
            $staffQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $staffList = $staffQuery->orderByRoleLevel()->get();

        return view('admin.sub-roles.assign', compact('subRoles', 'staffList', 'hospital', 'search'));
    }

    /**
     * POST /admin/sub-roles/assign
     * Simpan assignment sub-jabatan ke staf.
     */
    public function assignStore(Request $request)
    {
        $this->checkAccess();

        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'sub_role_id' => 'nullable|exists:staff_sub_roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($request->user_id);

        // Pastikan user dalam hospital yang sama
        $newSubRole = $request->sub_role_id
            ? StaffSubRole::findOrFail($request->sub_role_id)
            : null;

        if ($newSubRole && $newSubRole->hospital !== $user->hospital) {
            return back()->with('error', 'Sub-jabatan tidak tersedia untuk hospital staf ini.');
        }

        $oldSubRole = $user->subRole?->short_name ?? 'Tidak ada';
        $user->update(['sub_role_id' => $request->sub_role_id]);

        $newLabel = $newSubRole ? $newSubRole->short_name : 'Tidak ada';

        return back()->with('success', "Sub-jabatan {$user->name} berhasil diubah dari {$oldSubRole} menjadi {$newLabel}.");
    }

    /**
     * POST /admin/sub-roles/assign-bulk
     * Assign sub-jabatan ke banyak staf sekaligus.
     */
    public function assignBulk(Request $request)
    {
        $this->checkAccess();

        $validator = Validator::make($request->all(), [
            'assignments'           => 'required|array|min:1',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.sub_role_id' => 'nullable|exists:staff_sub_roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $count = 0;
        foreach ($request->assignments as $item) {
            User::where('id', $item['user_id'])
                ->update(['sub_role_id' => $item['sub_role_id'] ?? null]);
            $count++;
        }

        return back()->with('success', "{$count} staf berhasil diperbarui sub-jabatannya.");
    }

    /**
     * POST /admin/sub-roles/{subRole}/toggle-active
     * Aktifkan / nonaktifkan sub-jabatan.
     */
    public function toggleActive(StaffSubRole $subRole)
    {
        $this->checkAccess();

        $subRole->update(['is_active' => !$subRole->is_active]);
        $status = $subRole->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Divisi {$subRole->short_name} berhasil {$status}.");
    }
}
