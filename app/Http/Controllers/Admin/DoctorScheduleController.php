<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DoctorScheduleController extends Controller
{
    /**
     * Daftar rekomendasi spesialisasi & poliklinik medis.
     * Dokter spesialis tetap dapat mendaftarkan spesialisasi kustom di luar daftar ini.
     */
    public const POLI_LIST = [
        '🩺 Spesialis Bedah (Sp.B)',
        '🩺 Spesialis Penyakit Dalam (Sp.PD)',
        '🩺 Spesialis Anak (Sp.A)',
        '🩺 Spesialis Obstetri & Ginekologi / Kandungan (Sp.OG)',
        '🩺 Spesialis Jantung & Pembuluh Darah (Sp.JP)',
        '🩺 Spesialis Saraf / Neurologi (Sp.S / Sp.N)',
        '🩺 Spesialis Mata (Sp.M)',
        '🩺 Spesialis Telinga, Hidung, Tenggorok (Sp.THT-KL)',
        '🩺 Spesialis Kulit & Kelamin / Dermatovenerologi (Sp.DV)',
        '🩺 Spesialis Ortopedi & Traumatologi (Sp.OT)',
        '🩺 Spesialis Kesehatan Jiwa / Psikiatri (Sp.KJ)',
        '🩺 Spesialis Paru / Pulmonologi (Sp.P)',
        '🩺 Spesialis Anestesiologi (Sp.An)',
        '🩺 Spesialis Radiologi (Sp.Rad)',
        '🩺 Spesialis Bedah Saraf (Sp.BS)',
        '🩺 Spesialis Urologi (Sp.U)',
        '🩺 Spesialis Onkologi',
        '🩺 Spesialis Bedah Plastik, Rekonstruksi, dan Estetik',
        '🩺 Spesialis Forensik dan Medikolegal',
        '🩺 Spesialis Kedokteran Gigi Anak',
        '🩺 Spesialis Konservasi Gigi',
        '🩺 Spesialis Bedah Mulut',
        '🩺 Spesialis Prostodontia',
        '🩺 Spesialis Gizi Klinis',
        '🩺 Poli Umum',
        '🩺 Poli Kejiwaan',
    ];

    public const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    /**
     * Dapatkan daftar rekomendasi poli digabung dengan spesialisasi yang sudah tersimpan di database.
     */
    public static function getPoliList(): array
    {
        try {
            $dbPolis = DoctorSchedule::distinct()->pluck('poli')->filter()->toArray();
        } catch (\Throwable $e) {
            $dbPolis = [];
        }

        $all = array_unique(array_merge(self::POLI_LIST, $dbPolis));
        sort($all);
        return array_values($all);
    }

    /**
     * Pastikan tabel dan kolom user_id aman (self-healing migration)
     */
    private function ensureTableSchema(): void
    {
        try {
            if (Schema::hasTable('doctor_schedules')) {
                if (!Schema::hasColumn('doctor_schedules', 'user_id')) {
                    Schema::table('doctor_schedules', function ($table) {
                        $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
                    });
                }
            }
        } catch (\Throwable $e) {
            Log::warning('DoctorSchedule ensureTableSchema: ' . $e->getMessage());
        }
    }

    /**
     * Cek apakah user berhak mengelola jadwal tertentu
     * - Admin & Manajer ke atas: dapat mengelola semua jadwal
     * - Dokter Spesialis / Dokter: dapat mengelola jadwal miliknya sendiri
     */
    public static function canManageSchedule(DoctorSchedule $schedule, $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return false;
        }

        if ($user->isAdmin() || $user->isManagerOrAbove()) {
            return true;
        }

        if (!empty($schedule->user_id) && (int)$schedule->user_id === (int)$user->id) {
            return true;
        }

        if (strcasecmp(trim((string)$schedule->doctor_name), trim((string)$user->name)) === 0) {
            return true;
        }

        return false;
    }

    public function index(Request $request)
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        $query = DoctorSchedule::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('doctor_name', 'like', "%{$q}%")
                    ->orWhere('poli', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%");
            });
        }

        if ($request->filled('poli')) {
            $query->where('poli', $request->input('poli'));
        }

        if ($request->filled('hospital') && in_array($request->input('hospital'), ['alta', 'roxwood'])) {
            $query->where('hospital', $request->input('hospital'));
        }

        // Filter jadwal milik dokter yang sedang login
        $filterMySchedules = $request->boolean('my_schedules');
        if ($filterMySchedules && $user) {
            $query->where(function ($sub) use ($user) {
                if (Schema::hasColumn('doctor_schedules', 'user_id')) {
                    $sub->where('user_id', $user->id)
                        ->orWhere('doctor_name', $user->name);
                } else {
                    $sub->where('doctor_name', $user->name);
                }
            });
        }

        $schedules = $query->orderBy('poli')->orderBy('doctor_name')->paginate(20)->withQueryString();
        $poliList = self::getPoliList();

        // Hitung total jadwal milik user login
        $mySchedulesCount = 0;
        if ($user) {
            try {
                $mySchedulesCount = DoctorSchedule::where(function ($sub) use ($user) {
                    if (Schema::hasColumn('doctor_schedules', 'user_id')) {
                        $sub->where('user_id', $user->id)->orWhere('doctor_name', $user->name);
                    } else {
                        $sub->where('doctor_name', $user->name);
                    }
                })->count();
            } catch (\Throwable $e) {
                $mySchedulesCount = 0;
            }
        }

        $totalCount = DoctorSchedule::count();

        return view('admin.doctor-schedules.index', compact('schedules', 'poliList', 'mySchedulesCount', 'totalCount', 'filterMySchedules'));
    }

    public function create()
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        $poliList = self::getPoliList();
        $days = self::DAYS;

        // Daftar dokter aktif untuk pilihan dropdown/datalist
        $doctors = User::where('is_active', true)
            ->where(function ($query) {
                $query->whereHas('role', function ($qr) {
                    $qr->where('level', '>=', 2)->where('name', '!=', 'admin');
                })->orWhereHas('medicRole', function ($qm) {
                    $qm->where('level', '>=', 2)->where('name', '!=', 'admin');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        // Default dokter & rumah sakit berdasarkan user yang sedang login
        $defaultDoctor = $user?->name ?? '';
        $defaultHospital = $user?->hospital ?? 'alta';

        // Cek apakah user adalah dokter/spesialis
        $isDoctor = $user && (
            ($user->role && $user->role->level >= 3) ||
            ($user->medicRole && $user->medicRole->level >= 3) ||
            $user->isDoctor()
        );

        return view('admin.doctor-schedules.create', compact(
            'poliList',
            'days',
            'doctors',
            'defaultDoctor',
            'defaultHospital',
            'isDoctor'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        // Validasi: 'poli' kini menerima string bebas (bebas mendaftarkan spesialisasi apapun)
        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'poli' => 'required|string|max:255',
            'day' => 'required|array|min:1',
            'day.*' => 'string|in:' . implode(',', self::DAYS),
            'start_time' => 'required',
            'end_time' => 'required',
            'hospital' => 'required|in:alta,roxwood',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Bersihkan dan format poli dengan icon 🩺 di depan agar seragam dan rapi di portal publik
        $poli = trim($validated['poli']);
        if (!str_starts_with($poli, '🩺')) {
            $poli = '🩺 ' . ltrim($poli);
        }

        // Tentukan user_id pemilik jadwal
        $userId = null;
        if ($user && strcasecmp(trim($validated['doctor_name']), trim($user->name)) === 0) {
            $userId = $user->id;
        } else {
            $matched = User::where('name', $validated['doctor_name'])->first();
            $userId = $matched?->id;
        }

        $payload = [
            'doctor_name' => $validated['doctor_name'],
            'poli' => $poli,
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hospital' => $validated['hospital'],
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ];

        if (Schema::hasColumn('doctor_schedules', 'user_id')) {
            $payload['user_id'] = $userId;
        }

        DoctorSchedule::create($payload);

        return redirect()->route('admin.doctor-schedules.index')
            ->with('success', 'Jadwal praktek spesialisasi "' . str_replace('🩺 ', '', $poli) . '" berhasil ditambahkan.');
    }

    public function edit(DoctorSchedule $doctorSchedule)
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        // Keamanan: Cek otorisasi
        if (!self::canManageSchedule($doctorSchedule, $user)) {
            return redirect()->route('admin.doctor-schedules.index')
                ->with('error', 'Anda hanya dapat mengubah jadwal praktek Anda sendiri.');
        }

        $poliList = self::getPoliList();
        $days = self::DAYS;
        $doctors = User::where('is_active', true)
            ->where(function ($query) {
                $query->whereHas('role', function ($qr) {
                    $qr->where('level', '>=', 2)->where('name', '!=', 'admin');
                })->orWhereHas('medicRole', function ($qm) {
                    $qm->where('level', '>=', 2)->where('name', '!=', 'admin');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.doctor-schedules.edit', compact('doctorSchedule', 'poliList', 'days', 'doctors'));
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        // Keamanan: Cek otorisasi
        if (!self::canManageSchedule($doctorSchedule, $user)) {
            return redirect()->route('admin.doctor-schedules.index')
                ->with('error', 'Anda hanya dapat memperbarui jadwal praktek Anda sendiri.');
        }

        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'poli' => 'required|string|max:255',
            'day' => 'required|array|min:1',
            'day.*' => 'string|in:' . implode(',', self::DAYS),
            'start_time' => 'required',
            'end_time' => 'required',
            'hospital' => 'required|in:alta,roxwood',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $poli = trim($validated['poli']);
        if (!str_starts_with($poli, '🩺')) {
            $poli = '🩺 ' . ltrim($poli);
        }

        // Tentukan user_id
        $userId = $doctorSchedule->user_id;
        if (empty($userId)) {
            if ($user && strcasecmp(trim($validated['doctor_name']), trim($user->name)) === 0) {
                $userId = $user->id;
            } else {
                $matched = User::where('name', $validated['doctor_name'])->first();
                $userId = $matched?->id;
            }
        }

        $payload = [
            'doctor_name' => $validated['doctor_name'],
            'poli' => $poli,
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hospital' => $validated['hospital'],
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ];

        if (Schema::hasColumn('doctor_schedules', 'user_id')) {
            $payload['user_id'] = $userId;
        }

        $doctorSchedule->update($payload);

        return redirect()->route('admin.doctor-schedules.index')
            ->with('success', 'Jadwal praktek spesialisasi "' . str_replace('🩺 ', '', $poli) . '" berhasil diperbarui.');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        if (!self::canManageSchedule($doctorSchedule, $user)) {
            return redirect()->route('admin.doctor-schedules.index')
                ->with('error', 'Anda hanya dapat menghapus jadwal praktek Anda sendiri.');
        }

        $doctorSchedule->delete();
        return redirect()->route('admin.doctor-schedules.index')->with('success', 'Jadwal praktek berhasil dihapus.');
    }

    public function toggleActive(DoctorSchedule $doctorSchedule)
    {
        $this->ensureTableSchema();
        $user = Auth::user();

        if (!self::canManageSchedule($doctorSchedule, $user)) {
            return back()->with('error', 'Anda hanya dapat mengubah status jadwal Anda sendiri.');
        }

        $doctorSchedule->is_active = !$doctorSchedule->is_active;
        $doctorSchedule->save();

        $status = $doctorSchedule->is_active ? 'diaktifkan (tampil di publik)' : 'dinonaktifkan';
        return back()->with('success', "Status jadwal dokter {$doctorSchedule->doctor_name} berhasil {$status}.");
    }
}
