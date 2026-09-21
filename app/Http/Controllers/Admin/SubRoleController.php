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

        // Fitur divisi/sub-jabatan ini khusus untuk Alta Hospital
        $hospital = 'alta';

        $subRoles = StaffSubRole::withCount('users')
            ->forHospital($hospital)
            ->orderBy('sort_order')
            ->get();

        // Staf Alta yang belum punya sub-jabatan
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
            ->where('sub_role_id', $subRole->id)
            ->where('hospital', 'alta');

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
     * Form assign sub-jabatan ke staf (Khusus Alta Hospital).
     */
    public function assignForm(Request $request)
    {
        $this->checkAccess();

        // Khusus Alta Hospital
        $hospital = 'alta';
        $search   = trim($request->get('q', ''));

        $subRoles = StaffSubRole::active()
            ->forHospital('alta')
            ->orderBy('sort_order')
            ->get();

        // Staf aktif khusus Alta Hospital, urutkan by level
        $staffQuery = User::with(['role:id,name,display_name,level', 'subRole:id,name,short_name,color'])
            ->where('is_active', true)
            ->whereNotNull('role_id')
            ->where('hospital', 'alta')
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'admin'));

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

        // Pastikan staf adalah anggota Alta Hospital
        if (strtolower(trim($user->hospital ?? '')) !== 'alta') {
            return back()->with('error', 'Penetapan sub-jabatan saat ini hanya diperuntukkan bagi anggota Alta Hospital.');
        }

        // Pastikan sub-jabatan adalah untuk Alta
        $newSubRole = $request->sub_role_id
            ? StaffSubRole::findOrFail($request->sub_role_id)
            : null;

        if ($newSubRole && $newSubRole->hospital !== 'alta') {
            return back()->with('error', 'Sub-jabatan ini bukan milik Alta Hospital.');
        }

        $oldSubRole = $user->subRole?->short_name ?? 'Tidak ada';
        $user->update(['sub_role_id' => $request->sub_role_id]);

        $newLabel = $newSubRole ? $newSubRole->short_name : 'Tidak ada';

        return back()->with('success', "Sub-jabatan {$user->name} berhasil diubah dari {$oldSubRole} menjadi {$newLabel}.");
    }

    /**
     * POST /admin/sub-roles/assign-bulk
     * Assign sub-jabatan ke banyak staf sekaligus (Khusus Alta Hospital).
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
            // Hanya perbarui jika anggota Alta
            User::where('id', $item['user_id'])
                ->where('hospital', 'alta')
                ->update(['sub_role_id' => $item['sub_role_id'] ?? null]);
            $count++;
        }

        return back()->with('success', "{$count} staf Alta berhasil diperbarui sub-jabatannya.");
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
