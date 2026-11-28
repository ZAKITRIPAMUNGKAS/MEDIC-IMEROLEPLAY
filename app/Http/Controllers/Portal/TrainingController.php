<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrainingController extends Controller
{
    private function normalizeType(string $type): string
    {
        return match ($type) {
            'operasi'        => TrainingApplication::TYPE_OPERASI,
            'surat-menyurat', 'surat_menyurat'            => TrainingApplication::TYPE_SURAT_MENYURAT,
            'visum-hidup', 'visum_hidup'                  => TrainingApplication::TYPE_VISUM_HIDUP,
            'rekam-medis', 'rekam_medis'                  => TrainingApplication::TYPE_REKAM_MEDIS,
            'pemulsaran-jenazah', 'pemulsaran_jenazah'    => TrainingApplication::TYPE_PEMULSARAN_JENAZAH,
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

        $trainings = TrainingProgram::getAllPrograms();

        $canManagePrograms = $user->isAdmin() || $user->isExecutiveOrAbove() || $user->isInDivision('pnd');

        return view('portal.training.index', compact('trainings', 'myApplications', 'user', 'canManagePrograms'));
    }

    /**
     * Update dynamic training schedule / details (PND / Admin only)
     */
    public function updateProgram(Request $request, string $key)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('pnd')) {
            abort(403, 'Hanya divisi PND atau Administrator yang berwenang mengubah jadwal pelatihan.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'organizer'   => 'required|string|max:255',
            'desc'        => 'required|string|max:2000',
            'requirement' => 'nullable|string|max:255',
            'badge'       => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|in:emerald,blue,purple,cyan,amber',
            'is_active'   => 'nullable|boolean',
        ], [
            'title.required'     => 'Judul formulir / pelatihan wajib diisi.',
            'organizer.required' => 'Divisi penyelenggara wajib diisi.',
            'desc.required'      => 'Deskripsi dan tanggal pelaksanaan wajib diisi.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $saved = TrainingProgram::saveProgram($key, $validated);

        if (!$saved) {
            return back()->with('error', 'Program pelatihan tidak ditemukan atau gagal diperbarui.');
        }

        return back()->with('success', "Jadwal dan informasi pelatihan \"{$validated['title']}\" berhasil diperbarui!");
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
            'BATCH XIII', 'BATCH XIV', 'BATCH XV', 'BATCH XVI',
            'BATCH XVII', 'BATCH XVIII', 'BATCH XIX', 'BATCH XX',
        ];

        // Default phone & role from user if available
        $defaultName = $user->name ?? '';
        $defaultPhone = $user->phone_number ?? '';
        $defaultRole = $user->role?->display_name ?? $user->role?->name ?? '';
        $program = TrainingProgram::getProgram($normalizedType);

        return view('portal.training.form', compact(
            'normalizedType',
            'type',
            'user',
            'existingPending',
            'batches',
            'defaultName',
            'defaultPhone',
            'defaultRole',
            'program'
        ));
    }

    /**
     * Process submission for a specific training.
     */
    public function submitForm(Request $request, string $type)
    {
        $normalizedType = $this->normalizeType($type);
        $user = Auth::user();
        $program = TrainingProgram::getProgram($normalizedType);

        if ($program && !($program['is_active'] ?? true)) {
            return redirect()->route('portal.training.index')
                ->with('error', 'Pendaftaran untuk pelatihan ini sedang ditutup.');
        }

        $config = $this->formConfigForType($normalizedType);
        $validated = $request->validate($config['rules'], $config['messages']);

        $gender = (strcasecmp($validated['gender'], 'laki-laki') === 0)
            ? $config['male_label']
            : 'Perempuan';
        $batch = trim($validated['batch']);

        $lock = Cache::lock("training_submit_{$user->id}_{$normalizedType}", 10);

        try {
            if (!$lock->get()) {
                return $this->duplicateSubmissionResponse(
                    $request,
                    'Pengiriman sedang diproses. Mohon tunggu sebentar dan tidak menekan tombol kirim berulang kali.'
                );
            }

            // Prevent duplicate pending registration for the same training + batch or rapid double submission
            $existingDuplicate = TrainingApplication::where('user_id', $user->id)
                ->where('training_type', $normalizedType)
                ->where(function ($q) use ($batch) {
                    $q->where(function ($pendingQ) use ($batch) {
                        $pendingQ->where('status', TrainingApplication::STATUS_PENDING)
                            ->whereRaw('LOWER(TRIM(batch)) = ?', [mb_strtolower($batch)]);
                    })->orWhere('created_at', '>=', now()->subSeconds(15));
                })
                ->exists();

            if ($existingDuplicate) {
                return $this->duplicateSubmissionResponse(
                    $request,
                    'Anda sudah memiliki pendaftaran yang masih menunggu review untuk pelatihan dan batch ini. Tidak perlu mengirim ulang.'
                );
            }

            $payload = [
                'user_id'       => $user->id,
                'training_type' => $normalizedType,
                'nama_ic'       => trim($validated['nama_ic']),
                'gender'        => $gender,
                'batch'         => $batch,
                'status'        => TrainingApplication::STATUS_PENDING,
            ];

            if (array_key_exists('phone_ic', $validated)) {
                $payload['phone_ic'] = trim($validated['phone_ic']);
            }
            if (array_key_exists('jabatan', $validated)) {
                $payload['jabatan'] = trim($validated['jabatan']);
            }

            TrainingApplication::create($payload);
        } finally {
            optional($lock)->release();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $config['success_message'],
                'redirect_url' => route('portal.training.index'),
            ]);
        }

        return redirect()->route('portal.training.index')
            ->with('success', $config['success_message']);
    }

    private function duplicateSubmissionResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return redirect()->route('portal.training.index')
            ->with('error', $message);
    }

    private function formConfigForType(string $normalizedType): array
    {
        $configs = [
            TrainingApplication::TYPE_OPERASI => [
                'rules' => [
                    'nama_ic' => ['required', 'string', 'max:255'],
                    'gender'  => ['required', 'string', 'in:Laki-laki,Perempuan,Laki-Laki'],
                    'batch'   => ['required', 'string', 'max:50'],
                ],
                'messages' => [
                    'nama_ic.required' => 'NAMA IC wajib diisi.',
                    'gender.required'  => 'JENIS KELAMIN wajib dipilih.',
                    'batch.required'   => 'BATCH wajib dipilih.',
                ],
                'male_label' => 'Laki-laki',
                'success_message' => 'Pendaftaran Pelatihan Operasi berhasil dikirim! Silakan menunggu verifikasi dari Divisi PND.',
            ],
            TrainingApplication::TYPE_SURAT_MENYURAT => [
                'rules' => [
                    'nama_ic'  => ['required', 'string', 'max:255'],
                    'gender'   => ['required', 'string', 'in:Laki-Laki,Laki-laki,Perempuan'],
                    'phone_ic' => ['required', 'string', 'max:50'],
                    'jabatan'  => ['required', 'string', 'in:Co-Ass,Dokter,Dokter Spesialis'],
                    'batch'    => ['required', 'string', 'max:50'],
                ],
                'messages' => [
                    'nama_ic.required'  => 'Nama (IC) wajib diisi.',
                    'gender.required'   => 'Jenis Kelamin wajib dipilih.',
                    'phone_ic.required' => 'Nomor Telepon (IC) wajib diisi.',
                    'jabatan.required'  => 'Jabatan Saat Ini wajib dipilih.',
                    'batch.required'    => 'BATCH wajib dipilih.',
                ],
                'male_label' => 'Laki-Laki',
                'success_message' => 'Pendaftaran Pelatihan Surat Menyurat berhasil dikirim! Divisi PND akan memverifikasi berkas Anda.',
            ],
            TrainingApplication::TYPE_VISUM_HIDUP => [
                'rules' => [
                    'nama_ic'  => ['required', 'string', 'max:255'],
                    'gender'   => ['required', 'string', 'in:Laki-Laki,Laki-laki,Perempuan'],
                    'phone_ic' => ['required', 'string', 'max:50'],
                    'jabatan'  => ['required', 'string', 'in:Dokter Umum,Dokter Spesialis'],
                    'batch'    => ['required', 'string', 'max:50'],
                ],
                'messages' => [
                    'nama_ic.required'  => 'NAMA (IC) wajib diisi.',
                    'gender.required'   => 'Jenis Kelamin wajib dipilih.',
                    'phone_ic.required' => 'Nomor Telepon (IC) wajib diisi.',
                    'jabatan.required'  => 'Jabatan Saat Ini wajib dipilih.',
                    'batch.required'    => 'BATCH wajib dipilih.',
                ],
                'male_label' => 'Laki-Laki',
                'success_message' => 'Pendaftaran Pelatihan Visum Hidup berhasil dikirim! Silakan menunggu konfirmasi dari Divisi PND.',
            ],
            TrainingApplication::TYPE_REKAM_MEDIS => [
                'rules' => [
                    'nama_ic'  => ['required', 'string', 'max:255'],
                    'gender'   => ['required', 'string', 'in:Laki-Laki,Laki-laki,Perempuan'],
                    'phone_ic' => ['required', 'string', 'max:50'],
                    'batch'    => ['required', 'string', 'max:50'],
                ],
                'messages' => [
                    'nama_ic.required'  => 'NAMA (IC) wajib diisi.',
                    'gender.required'   => 'Jenis Kelamin wajib dipilih.',
                    'phone_ic.required' => 'Nomor Telepon (IC) wajib diisi.',
                    'batch.required'    => 'BATCH wajib dipilih.',
                ],
                'male_label' => 'Laki-Laki',
                'success_message' => 'Pendaftaran Pelatihan Rekam Medis berhasil dikirim! Silakan menunggu konfirmasi dari Divisi PND.',
            ],
            TrainingApplication::TYPE_PEMULSARAN_JENAZAH => [
                'rules' => [
                    'nama_ic'  => ['required', 'string', 'max:255'],
                    'gender'   => ['required', 'string', 'in:Laki-Laki,Laki-laki,Perempuan'],
                    'phone_ic' => ['required', 'string', 'max:50'],
                    'batch'    => ['required', 'string', 'max:50'],
                ],
                'messages' => [
                    'nama_ic.required'  => 'NAMA (IC) wajib diisi.',
                    'gender.required'   => 'Jenis Kelamin wajib dipilih.',
                    'phone_ic.required' => 'Nomor Telepon (IC) wajib diisi.',
                    'batch.required'    => 'BATCH wajib dipilih.',
                ],
                'male_label' => 'Laki-Laki',
                'success_message' => 'Pendaftaran Pelatihan Pemulsaran Jenazah berhasil dikirim! Silakan menunggu konfirmasi dari Divisi PND.',
            ],
        ];

        if (!isset($configs[$normalizedType])) {
            abort(400, 'Tipe pelatihan tidak valid.');
        }

        return $configs[$normalizedType];
    }
}
