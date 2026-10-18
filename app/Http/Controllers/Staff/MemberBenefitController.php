<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MedicalForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MemberBenefitController extends Controller
{
    /**
     * Tampilkan halaman Benefit Oplas Gratis untuk Anggota EMS
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // Cek pengajuan bulan berjalan (Tahun & Bulan saat ini)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $cid = $user->citizen_id ?: $user->staff_id;

        // Cari riwayat pengajuan benefit oplas gratis oleh user ini di bulan berjalan
        $thisMonthRequest = MedicalForm::query()
            ->where('form_type', 'operasi_plastik')
            ->where(function ($q) use ($user, $cid) {
                $q->where('form_data->user_id', $user->id)
                  ->orWhere('citizen_id', $cid)
                  ->orWhere('character_name', $user->name);
            })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->latest()
            ->first();

        // Riwayat semua pengajuan benefit oplas anggota ini
        $history = MedicalForm::query()
            ->where('form_type', 'operasi_plastik')
            ->where(function ($q) use ($user, $cid) {
                $q->where('form_data->user_id', $user->id)
                  ->orWhere('citizen_id', $cid)
                  ->orWhere('character_name', $user->name);
            })
            ->latest()
            ->take(10)
            ->get();

        // Hitung status kuota
        $canClaim = ($thisMonthRequest === null);
        $nextAvailableDate = now()->addMonthNoOverflow()->startOfMonth()->translatedFormat('d F Y');

        return view('staff.benefits.oplas', compact('user', 'thisMonthRequest', 'history', 'canClaim', 'nextAvailableDate'));
    }

    /**
     * Proses klaim 1-Klik Oplas Gratis
     */
    public function claimOplas(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $cid = $user->citizen_id ?: $user->staff_id;

        // Validasi apakah sudah pernah mengajukan bulan ini
        $alreadyClaimed = MedicalForm::query()
            ->where('form_type', 'operasi_plastik')
            ->where(function ($q) use ($user, $cid) {
                $q->where('form_data->user_id', $user->id)
                  ->orWhere('citizen_id', $cid)
                  ->orWhere('character_name', $user->name);
            })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->exists();

        if ($alreadyClaimed) {
            return back()->with('error', 'Anda sudah menggunakan kuota Oplas Gratis untuk bulan ini. Kuota akan otomatis di-reset pada awal bulan depan.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $hospital = strtolower(trim($user->hospital ?? 'alta'));

        // Buat record MedicalForm khusus Oplas Benefit EMS
        $form = MedicalForm::create([
            'character_name' => $user->name,
            'citizen_id'     => $cid,
            'form_type'      => 'operasi_plastik',
            'hospital'       => $hospital,
            'description'    => 'Klaim Oplas Gratis Anggota EMS (Benefit Bulanan)',
            'status'         => 'pending',
            'ip_address'     => $request->ip(),
            'notes'          => $request->filled('notes') ? 'Catatan Anggota: ' . $request->notes : 'Benefit Bulanan Anggota Medis/EMS (Gratis)',
            'form_data'      => [
                'user_id'          => $user->id,
                'staff_id'         => $user->staff_id,
                'role'             => $user->role?->display_name ?? $user->role?->name ?? 'Anggota Medic',
                'batch'            => $user->batch ?? '-',
                'phone'            => $user->phone_number ?? $user->contact ?? '-',
                'is_ems_benefit'   => true,
                'fasilitas_ems'    => true,
                'biaya'            => 'GRATIS (Benefit EMS)',
                'bulan_klaim'      => now()->translatedFormat('F Y'),
                'catatan_tambahan' => $request->notes,
            ],
        ]);

        return redirect()->route('staff.benefits.oplas')
            ->with('success', '🎉 Berhasil! Permintaan Oplas Gratis bulan ' . now()->translatedFormat('F Y') . ' telah dikirimkan ke dokter bedah. Silakan hubungi dokter bertugas saat in-game/kota!');
    }
}
