<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffRole;
use App\Models\Attendance;
use App\Helpers\TimeHelper;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class StaffManagementController extends Controller
{
    public function index()
    {
        $query = User::with('role')->whereNotNull('role_id');

        if (request('q')) {
            $q = trim(request('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        if (request('role')) {
            $query->where('role_id', request('role'));
        }

        if (request('hospital')) {
            $query->where('hospital', request('hospital'));
        }

        // Explicit Status Filter
        if (request()->has('active') && request('active') !== null && request('active') !== '') {
            $isActive = request('active') == '1';
            $query->where('is_active', $isActive);
        }

        // Clone query for counts BEFORE pagination
        $countQuery = clone $query;
        // Remove ordering and select for faster counting
        $countQuery->getQuery()->orders = null;

        // Calculate dynamic counts based on current filters
        // Note: These counts reflect the CURRENT SEARCH/FILTER context
        $stats = [
            'total' => $query->count(),
            'active' => (clone $query)->where('is_active', 1)->count(),
            'inactive' => (clone $query)->where('is_active', 0)->count(),
            'admin' => (clone $query)->whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })->count(),
        ];

        $staff = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles = StaffRole::orderBy('display_name')->get();

        // Pass stats to view
        request()->merge(['stats' => $stats]);

        // Ringkas rekap absensi untuk dashboard admin (harian/mingguan/bulanan)
        // Filters: day (Y-m-d), week (YYYY-Www per input type=week), month (Y-m)
        $dayParam = request('day');
        $weekParam = request('week');
        $monthParam = request('month');

        // Day range
        $day = $dayParam ? Carbon::parse($dayParam) : now();
        $today = $day->toDateString();

        // Week range from HTML week input (e.g., 2025-W38)
        if ($weekParam) {
            [$y, $w] = explode('-W', $weekParam);
            $week = Carbon::now()->setISODate((int) $y, (int) $w);
        } else {
            $week = now();
        }
        $startOfWeek = $week->copy()->startOfWeek()->toDateString();
        $endOfWeek = $week->copy()->endOfWeek()->toDateString();

        // Month range from input month (YYYY-MM)
        $month = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam) : now();
        $startOfMonth = $month->copy()->startOfMonth()->toDateString();
        $endOfMonth = $month->copy()->endOfMonth()->toDateString();

        // Gunakan session_duration untuk perhitungan yang lebih akurat
        // total_hours dan session_duration seharusnya sama, tapi session_duration lebih konsisten
        $daily = Attendance::selectRaw('user_id, SUM(session_duration) as total')
            ->whereDate('work_date', $today)
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->whereIn('session_type', ['work', 'overtime'])
            ->groupBy('user_id')
            ->with('user')
            ->get();

        $weekly = Attendance::selectRaw('user_id, SUM(session_duration) as total')
            ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->whereIn('session_type', ['work', 'overtime'])
            ->groupBy('user_id')
            ->with('user')
            ->get();

        $monthly = Attendance::selectRaw('user_id, SUM(session_duration) as total')
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->whereIn('session_type', ['work', 'overtime'])
            ->groupBy('user_id')
            ->with('user')
            ->get();

        // Export CSV on demand (?export=daily|weekly|monthly)
        if (in_array(request('export'), ['daily', 'weekly', 'monthly'])) {
            $map = [
                'daily' => $daily,
                'weekly' => $weekly,
                'monthly' => $monthly,
            ];
            return $this->exportCsv($map[request('export')], request('export'));
        }

        // Pass selected filter values back to view
        $filters = [
            'day' => $today,
            'week' => $weekParam ?: $week->format('o-\WW'),
            'month' => $monthParam ?: $month->format('Y-m'),
            'week_label' => Carbon::parse($startOfWeek)->format('d M') . ' - ' . Carbon::parse($endOfWeek)->format('d M Y'),
            'month_label' => $month->format('M Y'),
        ];

        return view('admin.staff.index', compact('staff', 'roles', 'daily', 'weekly', 'monthly', 'filters'));
    }

    private function exportCsv($collection, string $range): StreamedResponse
    {
        $filename = 'rekap-' . $range . '-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($collection) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Staf', 'User ID', 'Total Detik', 'Total HH:MM:SS']);
            foreach ($collection as $row) {
                $seconds = (int) ($row->total ?? 0);
                $hhmmss = TimeHelper::formatDuration($seconds);
                fputcsv($out, [
                    optional($row->user)->name ?? ('#' . $row->user_id),
                    $row->user_id,
                    $seconds,
                    $hhmmss,
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function create()
    {
        $roles = StaffRole::orderBy('display_name')->get();
        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:staff_roles,id',
            'is_active' => 'nullable|boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            // Use public directory for hosting compatibility
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $request->file('profile_image')->getClientOriginalName());
            $publicPath = public_path('uploads/profile-images');
            $destinationPath = $publicPath . '/' . $fileName;

            // Create directory if it doesn't exist
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Compress and save image (max 500x500, quality 85)
            $compressed = ImageHelper::compressUploadedImage(
                $request->file('profile_image'),
                $destinationPath,
                500, // max width
                500, // max height
                85   // quality
            );

            // If compression failed, use original file
            if (!$compressed) {
                $request->file('profile_image')->move($publicPath, $fileName);
            }

            $profileImagePath = 'uploads/profile-images/' . $fileName;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
            'profile_image' => $profileImagePath,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Akun staf berhasil dibuat.');
    }

    public function show(User $user)
    {
        // Redirect to edit page or show detail page
        return redirect()->route('admin.staff.edit', $user);
    }

    public function edit(User $user)
    {
        $roles = StaffRole::orderBy('display_name')->get();
        return view('admin.staff.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'hospital' => 'required|in:alta,roxwood',
            'citizen_id' => 'nullable|string|max:50|unique:users,citizen_id,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:staff_roles,id',
            'is_active' => 'nullable|boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'custom_salary' => 'nullable|numeric|min:0|max:9999999999',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'hospital' => $validated['hospital'],
            'citizen_id' => $validated['citizen_id'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
        ];

        // Only admin can set custom salary
        if (auth()->user()->isAdmin()) {
            $data['custom_salary'] = $request->input('custom_salary') ?: null;
        }

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_image')) {
            // Hapus gambar lama jika ada
            if ($user->profile_image) {
                $oldImagePath = null;

                // Check if it's a storage path or public path
                if (str_starts_with($user->profile_image, 'uploads/')) {
                    // Public path
                    $oldImagePath = public_path($user->profile_image);
                } else {
                    // Storage path
                    $oldImagePath = storage_path('app/public/' . $user->profile_image);
                }

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Use public directory for hosting compatibility
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $request->file('profile_image')->getClientOriginalName());
            $publicPath = public_path('uploads/profile-images');
            $destinationPath = $publicPath . '/' . $fileName;

            // Create directory if it doesn't exist
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Compress and save image (max 500x500, quality 85)
            $compressed = ImageHelper::compressUploadedImage(
                $request->file('profile_image'),
                $destinationPath,
                500, // max width
                500, // max height
                85   // quality
            );

            // If compression failed, use original file
            if (!$compressed) {
                $request->file('profile_image')->move($publicPath, $fileName);
            }

            $data['profile_image'] = 'uploads/profile-images/' . $fileName;
        }

        // Handle custom permissions
        $customPermissions = $request->input('custom_permissions', []);

        // Security check: Only admin can toggle 'manage_users' permission
        if (!auth()->user()->isAdmin()) {
            // Find if user ALREADY has manage_users
            $alreadyHasPermission = in_array('manage_users', $user->custom_permissions ?? []);

            // If they had it, keep it. If they didn't, ensure they don't get it.
            if ($alreadyHasPermission) {
                if (!in_array('manage_users', $customPermissions)) {
                    $customPermissions[] = 'manage_users';
                }
            } else {
                // Remove if tried to add
                $customPermissions = array_values(array_diff($customPermissions, ['manage_users']));
            }
        }

        $data['custom_permissions'] = $customPermissions;

        $user->update($data);

        return redirect()->route('admin.staff.edit', $user)->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Hapus data attendance terlebih dahulu
        $user->attendances()->delete();

        // Hapus user
        $user->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staf berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        return back()->with('success', 'Status akun diperbarui.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = str()->random(10);
        $user->password = Hash::make($newPassword);
        $user->save();

        return back()->with('success', 'Password direset: ' . $newPassword);
    }

    /**
     * Export staff data to Excel (CSV format)
     */
    public function export(Request $request)
    {
        $query = User::with('role')->whereNotNull('role_id');

        // Apply same filters as index
        if ($request->get('q')) {
            $q = trim($request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        if ($request->get('role')) {
            $query->where('role_id', $request->get('role'));
        }

        if ($request->get('hospital')) {
            $query->where('hospital', $request->get('hospital'));
        }

        if ($request->has('active') && $request->get('active') !== null && $request->get('active') !== '') {
            $query->where('is_active', $request->get('active') ? 1 : 0);
        }

        $staff = $query->orderBy('name')->get();

        $filename = 'data_staf_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Add BOM for UTF-8 Excel compatibility
        return response()->stream(function () use ($staff) {
            $out = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($out, [
                'No',
                'Nama',
                'Email',
                'Staff ID',
                'Peran',
                'Level',
                'Status',
                'Rumah Sakit',
                'Tanggal Dibuat',
            ], ','); // Use comma for standard CSV

            // Data
            $no = 1;
            foreach ($staff as $user) {
                $hospital = $user->isRoxwood() ? 'Roxwood Hospital' : 'Alta Hospital';
                $status = $user->is_active ? 'Aktif' : 'Nonaktif';

                fputcsv($out, [
                    $no++,
                    $user->name,
                    $user->email,
                    $user->staff_id ?? '-',
                    $user->role->display_name ?? $user->role->name ?? 'Tidak ada',
                    $user->role->level ?? '-',
                    $status,
                    $hospital,
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-',
                ], ',');
            }

            fclose($out);
        }, 200, $headers);
    }
}


