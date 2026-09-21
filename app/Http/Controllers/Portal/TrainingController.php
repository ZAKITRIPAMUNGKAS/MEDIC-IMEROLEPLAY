<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    private function normalizeType(string $type): string
    {
        return match ($type) {
            'operasi'        => TrainingApplication::TYPE_OPERASI,
            'surat-menyurat', 'surat_menyurat' => TrainingApplication::TYPE_SURAT_MENYURAT,
            'visum-hidup', 'visum_hidup'       => TrainingApplication::TYPE_VISUM_HIDUP,
            default          => abort(404, 'Jenis pelatihan tidak ditemukan.'),
        };
    }

    /**
     * Hub page: shows available trainings & current user's registration history.
     */
    public function index()
    {
        $user = Auth::user();
        $myApplications = TrainingApplication::with(['reviewer:id,name'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $trainings = [
            [
                'key'         => 'operasi',
                'title'       => 'FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIII',
                'short_title' => 'Pelatihan Operasi',
                'organizer'   => 'Divisi PND (People & Development) - MOT',
                'desc'        => 'Pendaftaran Pelatihan Operasi dibuka pada 5–7 September 2026. Kegiatan pelatihan akan dilaksanakan pada 8 September 2026 oleh Department People & Development bagian MOT (Medical of Trainer) sebagai upaya meningkatkan pengetahuan dan keterampilan peserta terkait prosedur operasi dan keselamatan pasien.',
                'badge'       => 'PND - MOT',
                'badge_color' => 'emerald',
                'icon'        => 'fa-procedures',
                'route'       => route('portal.training.form', 'operasi'),
            ],
            [
                'key'         => 'surat-menyurat',
                'title'       => 'Formulir Pendaftaran Surat Menyurat',
                'short_title' => 'Pelatihan Surat Menyurat',
                'organizer'   => 'Divisi PND (People & Development)',
                'desc'        => 'Pelatihan administrasi dan penulisan surat menyurat resmi IME Medical Center. Persyaratan minimal jabatan adalah Co-Ass.',
                'requirement' => 'Minimal Co-Ass',
                'badge'       => 'Min. Co-Ass',
                'badge_color' => 'blue',
                'icon'        => 'fa-envelope-open-text',
                'route'       => route('portal.training.form', 'surat-menyurat'),
            ],
            [
                'key'         => 'visum-hidup',
                'title'       => 'PENDAFTARAN PELATIHAN VISUM HIDUP',
                'short_title' => 'Pelatihan Visum Hidup',
                'organizer'   => 'MSL bersama People & Development Department',
                'desc'        => 'PELATIHAN VISUM HIDUP yang diselenggarakan oleh Medical Science & Laboratory bersama People & Development Department – IME Medical Center. Semua Dokter Umum WAJIB mengikuti dan opsional bagi dokter spesialis. Peserta akan mendapatkan sertifikat.',
                'requirement' => 'Wajib Dokter Umum / Opsional Spesialis',
                'badge'       => 'MSL & PND',
                'badge_color' => 'purple',
                'icon'        => 'fa-notes-medical',
                'route'       => route('portal.training.form', 'visum-hidup'),
            ],
        ];

        return view('portal.training.index', compact('trainings', 'myApplications', 'user'));
    }

    /**
     * Show registration form for a specific training.
     */
    public function showForm(string $type)
    {
        $normalizedType = $this->normalizeType($type);
        $user = Auth::user();

        // Check if user already has an active pending submission for this training
        $existingPending = TrainingApplication::where('user_id', $user->id)
            ->where('training_type', $normalizedType)
            ->where('status', TrainingApplication::STATUS_PENDING)
            ->first();

        $batches = [
            'BATCH I', 'BATCH II', 'BATCH III', 'BATCH IV',
            'BATCH V', 'BATCH VI', 'BATCH VII', 'BATCH VIII',
            'BATCH IX', 'BATCH X', 'BATCH XI', 'BATCH XII',
            'BATCH XIII', 'BATCH XIV'
        ];

        // Default phone & role from user if available
        $defaultName = $user->name ?? '';
        $defaultPhone = $user->phone_number ?? '';
        $defaultRole = $user->role?->display_name ?? $user->role?->name ?? '';

        return view('portal.training.form', compact(
            'normalizedType',
            'type',
            'user',
            'existingPending',
            'batches',
            'defaultName',
            'defaultPhone',
            'defaultRole'
        ));
    }

    /**
     * Process submission for a specific training.
     */
    public function submitForm(Request $request, string $type)
    {
        $normalizedType = $this->normalizeType($type);
        $user = Auth::user();

        // Validation rules per training type
        if ($normalizedType === TrainingApplication::TYPE_OPERASI) {
            $validated = $request->validate([
                'nama_ic' => ['required', 'string', 'max:255'],
                'gender'  => ['required', 'string', 'in:Laki-laki,Perempuan,Laki-Laki'],
                'batch'   => ['required', 'string', 'max:50'],
            ], [
                'nama_ic.required' => 'NAMA IC wajib diisi.',
                'gender.required'  => 'JENIS KELAMIN wajib dipilih.',
                'batch.required'   => 'BATCH wajib dipilih.',
            ]);

            // Normalize gender to 'Laki-laki' or 'Perempuan'
            $gender = (strcasecmp($validated['gender'], 'laki-laki') === 0) ? 'Laki-laki' : 'Perempuan';

            TrainingApplication::create([
                'user_id'       => $user->id,
                'training_type' => $normalizedType,
                'nama_ic'       => trim($validated['nama_ic']),
                'gender'        => $gender,
                'batch'         => trim($validated['batch']),
                'status'        => TrainingApplication::STATUS_PENDING,
            ]);

            return redirect()->route('portal.training.index')
                ->with('success', 'Pendaftaran Pelatihan Operasi berhasil dikirim! Silakan menunggu verifikasi dari Divisi PND.');

        } elseif ($normalizedType === TrainingApplication::TYPE_SURAT_MENYURAT) {
            $validated = $request->validate([
                'nama_ic'  => ['required', 'string', 'max:255'],
                'gender'   => ['required', 'string', 'in:Laki-Laki,Laki-laki,Perempuan'],
                'phone_ic' => ['required', 'string', 'max:50'],
                'jabatan'  => ['required', 'string', 'in:Co-Ass,Dokter,Dokter Spesialis'],
                'batch'    => ['required', 'string', 'max:50'],
            ], [
                'nama_ic.required'  => 'Nama (IC) wajib diisi.',
                'gender.required'   => 'Jenis Kelamin wajib dipilih.',
                'phone_ic.required' => 'Nomor Telepon (IC) wajib diisi.',
                'jabatan.required'  => 'Jabatan Saat Ini wajib dipilih.',
                'batch.required'    => 'BATCH wajib dipilih.',
            ]);

            $gender = (strcasecmp($validated['gender'], 'laki-laki') === 0) ? 'Laki-Laki' : 'Perempuan';

            TrainingApplication::create([
                'user_id'       => $user->id,
                'training_type' => $normalizedType,
                'nama_ic'       => trim($validated['nama_ic']),
                'gender'        => $gender,
                'phone_ic'      => trim($validated['phone_ic']),
                'jabatan'       => trim($validated['jabatan']),
                'batch'         => trim($validated['batch']),
                'status'        => TrainingApplication::STATUS_PENDING,
            ]);

            return redirect()->route('portal.training.index')
                ->with('success', 'Pendaftaran Pelatihan Surat Menyurat berhasil dikirim! Divisi PND akan memverifikasi berkas Anda.');

        } elseif ($normalizedType === TrainingApplication::TYPE_VISUM_HIDUP) {
            $validated = $request->validate([
                'nama_ic'  => ['required', 'string', 'max:255'],
                'gender'   => ['required', 'string', 'in:Laki-Laki,Laki-laki,Perempuan'],
                'phone_ic' => ['required', 'string', 'max:50'],
                'jabatan'  => ['required', 'string', 'in:Dokter Umum,Dokter Spesialis'],
                'batch'    => ['required', 'string', 'max:50'],
            ], [
                'nama_ic.required'  => 'NAMA (IC) wajib diisi.',
                'gender.required'   => 'Jenis Kelamin wajib dipilih.',
                'phone_ic.required' => 'Nomor Telepon (IC) wajib diisi.',
                'jabatan.required'  => 'Jabatan Saat Ini wajib dipilih.',
                'batch.required'    => 'BATCH wajib dipilih.',
            ]);

            $gender = (strcasecmp($validated['gender'], 'laki-laki') === 0) ? 'Laki-Laki' : 'Perempuan';

            TrainingApplication::create([
                'user_id'       => $user->id,
                'training_type' => $normalizedType,
                'nama_ic'       => trim($validated['nama_ic']),
                'gender'        => $gender,
                'phone_ic'      => trim($validated['phone_ic']),
                'jabatan'       => trim($validated['jabatan']),
                'batch'         => trim($validated['batch']),
                'status'        => TrainingApplication::STATUS_PENDING,
            ]);

            return redirect()->route('portal.training.index')
                ->with('success', 'Pendaftaran Pelatihan Visum Hidup berhasil dikirim! Silakan menunggu konfirmasi dari Divisi PND.');
        }

        abort(400, 'Tipe pelatihan tidak valid.');
    }
}
