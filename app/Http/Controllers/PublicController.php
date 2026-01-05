<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function index()
    {
        // Hitung statistik untuk section "Keunggulan Kami"
        $totalStaff = User::where('is_active', true)
            ->whereNotNull('role_id')
            ->excludeAdmin()
            ->count();

        $totalForms = MedicalForm::count();

        $stats = [
            'total_staff' => $totalStaff,
            'total_forms' => $totalForms,
        ];

        // Ambil beberapa testimoni untuk carousel (maksimal 10 testimoni)
        // Prioritas: testimoni yang sudah di-approve, jika tidak ada ambil yang terbaru untuk testing
        // Use specific testimonials requested by user for the design refresh
        $testimonials = collect([
            (object) [
                'character_name' => 'Elmerz Ramirez',
                'testimoni' => 'gg abiezzzzzzzzzzzzzzzzz',
                'rating' => 5,
                'created_at' => now()->subDays(2)
            ],
            (object) [
                'character_name' => 'Thomas Andrew',
                'testimoni' => 'Keren sekali',
                'rating' => 5,
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'character_name' => 'Thomas Andrew',
                'testimoni' => 'Keren Sekali',
                'rating' => 5,
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'character_name' => 'Lil Hab',
                'testimoni' => 'terbaik pokoknya',
                'rating' => 5,
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'character_name' => 'Delvin Shironomi',
                'testimoni' => 'Nice discount pak Tan',
                'rating' => 5,
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'character_name' => 'Rangga Berto',
                'testimoni' => 'BUAHAHAHAHHAYUKKK',
                'rating' => 5,
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'character_name' => 'MY BAE',
                'testimoni' => 'good n comunicativ',
                'rating' => 5,
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'character_name' => 'Snapz Snapz',
                'testimoni' => 'mantap dah pokok nyaa',
                'rating' => 5,
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'character_name' => 'Om Black',
                'testimoni' => 'sangat ramah dan mau membantu warga baru',
                'rating' => 5,
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'character_name' => 'Om Black',
                'testimoni' => 'selalu membimbing',
                'rating' => 5,
                'created_at' => now()->subDays(5)
            ],
        ]);

        // Untuk backward compatibility, ambil testimoni pertama
        $testimoni = $testimonials->first();

        // Hitung jumlah staff on duty dari EMS (Alta Hospital) dan Roxwood Hospital
        // Ambil semua user RH untuk di-exclude dari EMS (sama seperti di strukturalEms)
        $rhUserIds = User::where('is_active', true)
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%rh%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%roxwood%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh -%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh-%'])
                    ->orWhere(function ($q) {
                        $q->whereNotNull('staff_id')
                            ->where(function ($sq) {
                                $sq->whereRaw('LOWER(staff_id) LIKE ?', ['%rh%'])
                                    ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh -%'])
                                    ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh-%']);
                            });
                    });
            })
            ->pluck('id')
            ->toArray();

        // Hitung staff on duty EMS (tanpa RH)
        // Ambil semua user EMS yang aktif dan punya role
        $emsUsers = User::where('is_active', true)
            ->whereNotNull('role_id')
            ->excludeAdmin()
            ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                return $query->whereNotIn('id', $rhUserIds);
            })
            ->get();

        // Filter hanya yang sedang on duty (clocked in)
        $emsOnDutyCount = $emsUsers->filter(function ($user) {
            return $user->isClockedIn();
        })->count();

        // Hitung staff on duty Roxwood Hospital (dengan RH)
        $roxwoodOnDutyCount = 0;
        if (!empty($rhUserIds)) {
            $roxwoodUsers = User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereNotNull('role_id')
                ->excludeAdmin()
                ->get();

            // Filter hanya yang sedang on duty (clocked in)
            $roxwoodOnDutyCount = $roxwoodUsers->filter(function ($user) {
                return $user->isClockedIn();
            })->count();
        }

        $onDutyStats = [
            'ems' => $emsOnDutyCount,
            'roxwood' => $roxwoodOnDutyCount,
        ];

        return view('public.index', compact('stats', 'testimoni', 'testimonials', 'onDutyStats'));
    }

    public function showForm($type = 'penyakit_dalam')
    {
        // Allow selecting form type via query string (?type=...)
        $type = request('type', $type);

        $formTypes = [
            'surat_kesehatan' => 'Surat Kesehatan',
            'operasi_plastik' => 'Operasi Plastik',
            'tes_psikologi' => 'Tes Psikologi',
            'surat_psikolog' => 'Surat Psikolog',
            'pendaftaran_karakter' => 'Karakter Kill',
            'konsultasi_medis' => 'Konsultasi Medis',
            'laporan_kecelakaan' => 'Laporan Kecelakaan',
            'permintaan_ambulans' => 'Permintaan Ambulans',
            'penyakit_dalam' => 'Poli Penyakit Dalam',
            'spesialis_anak' => 'Poli Spesialis Anak',
            'spesialis_bedah' => 'Poli Spesialis Bedah',
            'spesialis_mata' => 'Poli Spesialis Mata',
            'spesialis_saraf' => 'Poli Spesialis Saraf (Neurologi)',
            'spesialis_urologi' => 'Poli Spesialis Urologi',
            'spesialis_tht' => 'Poli Spesialis THT',
            'spesialis_ortopedi' => 'Poli Spesialis Ortopedi',
            'janji_temu' => 'Janji Temu'
        ];

        if (!array_key_exists($type, $formTypes)) {
            abort(404);
        }

        // Filter dokter berdasarkan form type dan level minimal yang dibutuhkan
        // Surat kesehatan, tes psikologi, surat psikolog: minimal Co-ass (level 2) ke atas
        // Operasi plastik: minimal Dokter Umum (level 3) ke atas
        $minLevel = 3; // Default untuk operasi plastik dan form lainnya
        if (in_array($type, ['surat_kesehatan', 'tes_psikologi', 'surat_psikolog'])) {
            $minLevel = 2; // Minimal Co-ass untuk surat kesehatan dan surat psikolog
        }

        // Ambil data user yang memiliki jabatan sesuai level minimal
        // Level 2 = co_ass, Level 3 = dokter_umum, Level 4 = dokter_spesialis, Level 5+ = manajer/executive/CEO
        // Kecualikan Administrator (role name = 'admin')
        $doctors = User::where('is_active', true)
            ->whereHas('role', function ($query) use ($minLevel) {
                $query->where('level', '>=', $minLevel)
                    ->where('name', '!=', 'admin');
            })
            ->with('role')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($doctor) {
                // Tambahkan informasi hospital pada setiap doctor
                $doctor->hospital = $doctor->isRoxwood() ? 'roxwood' : 'alta';
                return $doctor;
            });

        // For tes_psikologi, fetch pending surat_psikolog forms for droplist
        $availablePsychForms = collect();
        if ($type === 'tes_psikologi') {
            $availablePsychForms = MedicalForm::where('form_type', 'surat_psikolog')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(50) // Limit to last 50 for performance
                ->get(['id', 'character_name', 'created_at', 'hospital', 'form_data']);
        }

        return view('public.form', compact('type', 'formTypes', 'doctors', 'availablePsychForms'));
    }

    public function createAppointment(Request $request)
    {
        // Validasi untuk janji temu langsung
        $validator = Validator::make($request->all(), [
            'character_name' => 'required|string|max:255',
            'form_type' => 'required|string|in:penyakit_dalam,spesialis_anak,spesialis_bedah,spesialis_mata,spesialis_saraf,spesialis_urologi,spesialis_tht,spesialis_ortopedi',
            'hospital' => 'required|string|in:alta,roxwood',
            'appointment_date' => 'required|date|after_or_equal:today',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Mapping form type ke nama yang lebih user-friendly
        $formTypeNames = [
            'penyakit_dalam' => 'Poli Penyakit Dalam',
            'spesialis_anak' => 'Poli Spesialis Anak',
            'spesialis_bedah' => 'Poli Spesialis Bedah',
            'spesialis_mata' => 'Poli Spesialis Mata',
            'spesialis_saraf' => 'Poli Spesialis Saraf (Neurologi)',
            'spesialis_urologi' => 'Poli Spesialis Urologi',
            'spesialis_tht' => 'Poli Spesialis THT',
            'spesialis_ortopedi' => 'Poli Spesialis Ortopedi',
        ];

        // Parse datetime-local input
        $appointmentDateTime = \Carbon\Carbon::parse($request->appointment_date);
        $appointmentDate = $appointmentDateTime->format('Y-m-d');
        $appointmentTime = $appointmentDateTime->format('H:i');

        // Validasi: Cek apakah karakter dengan nama yang sama sudah mengisi form jenis yang sama hari ini (case-insensitive)
        // Hanya cek form dengan status 'pending' atau 'approved', form 'rejected' bisa diisi lagi
        $existingForm = MedicalForm::whereRaw('LOWER(character_name) = LOWER(?)', [$request->character_name])
            ->where('form_type', $request->form_type)
            ->whereDate('created_at', today())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingForm) {
            $hospitalName = $request->hospital === 'alta' ? 'Alta Hospital' : 'Roxwood Hospital';
            return response()->json([
                'success' => false,
                'message' => 'Karakter ' . $request->character_name . ' sudah mengisi form ' . ($formTypeNames[$request->form_type] ?? $request->form_type) . ' hari ini. Anda tidak dapat mengirim form yang sama lagi hari ini. Silakan hubungi staff medis di ' . $hospitalName . ' untuk bantuan lebih lanjut.',
                'errors' => ['character_name' => ['Karakter ini sudah mengisi form jenis ini hari ini. Silakan hubungi staff medis di rumah sakit untuk bantuan.']]
            ], 422);
        }

        // Buat data form untuk janji temu
        $formData = [
            'purpose' => 'Janji Temu',
            'doctor_name' => 'Dokter Spesialis',
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'phone_number' => $request->phone_number,
            'birth_date' => '1990-01-01', // Default value
            'gender' => 'Laki-laki', // Default value
            'age' => 30, // Default value
            'occupation' => 'Warga', // Default value
        ];

        // Simpan ke database
        $medicalForm = MedicalForm::create([
            'character_name' => $request->character_name,
            'citizen_id' => null,
            'form_type' => $request->form_type,
            'hospital' => $request->hospital,
            'description' => 'Janji Temu - ' . $formTypeNames[$request->form_type] . ' - ' . $request->character_name . ' - ' . $appointmentDate . ' ' . $appointmentTime,
            'form_data' => $formData,
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Janji temu berhasil dibuat! Tunggu dokter menghubungi Anda.',
            'form_id' => $medicalForm->id,
            'redirect_url' => route('public.appointment.success', $medicalForm->id)
        ]);
    }

    public function appointmentSuccess($id)
    {
        $appointment = MedicalForm::findOrFail($id);

        // Mapping form type ke nama yang lebih user-friendly
        $formTypeNames = [
            'penyakit_dalam' => 'Poli Penyakit Dalam',
            'spesialis_anak' => 'Poli Spesialis Anak',
            'spesialis_bedah' => 'Poli Spesialis Bedah',
            'spesialis_mata' => 'Poli Spesialis Mata',
            'spesialis_saraf' => 'Poli Spesialis Saraf (Neurologi)',
            'spesialis_urologi' => 'Poli Spesialis Urologi',
            'spesialis_tht' => 'Poli Spesialis THT',
            'spesialis_ortopedi' => 'Poli Spesialis Ortopedi',
        ];

        // Parse form data untuk mendapatkan detail appointment
        $formData = $appointment->form_data ?? [];
        $appointment->form_type = $formTypeNames[$appointment->form_type] ?? $appointment->form_type;
        $appointment->appointment_date = $formData['appointment_date'] ?? null;
        $appointment->appointment_time = $formData['appointment_time'] ?? null;

        return view('public.appointment-success', compact('appointment'));
    }

    public function submitForm(Request $request)
    {
        // Allowed form types should mirror showForm()
        $allowedTypes = [
            'surat_kesehatan',
            'operasi_plastik',
            'tes_psikologi',
            'surat_psikolog',
            'pendaftaran_karakter',
            'konsultasi_medis',
            'laporan_kecelakaan',
            'permintaan_ambulans',
            'penyakit_dalam',
            'spesialis_anak',
            'spesialis_bedah',
            'spesialis_mata',
            'spesialis_saraf',
            'spesialis_urologi',
            'spesialis_tht',
            'spesialis_ortopedi',
            'janji_temu',
        ];

        $validator = Validator::make($request->all(), [
            'character_name' => 'nullable|string|max:255',
            'citizen_id' => 'nullable|string|max:50',
            'form_type' => 'required|string|in:' . implode(',', $allowedTypes),
            'hospital' => 'nullable|string|in:alta,roxwood',
            // Banyak tipe form tidak memiliki kolom deskripsi, jadi buat opsional
            'description' => 'nullable|string|max:1000',
            'form_data' => 'required|array',
            // Field wajib untuk semua form (made optional since removed from frontend)
            'form_data.birth_date' => 'nullable|date',
            'form_data.gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'form_data.age' => 'nullable|integer|min:1|max:120',
            'form_data.occupation' => 'nullable|string|max:255',
            'form_data.phone_number' => 'nullable|string|max:20',
            // Validasi dokter_name wajib untuk surat kesehatan, tes psikologi, surat psikolog, dan operasi plastik
            'form_data.doctor_name' => 'required_if:form_type,surat_kesehatan,tes_psikologi,operasi_plastik|string|max:255|filled',
            // Validasi untuk tes psikologi
            'form_data.bigfive1' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive2' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive3' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive4' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive5' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive6' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive7' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive8' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive9' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.bigfive10' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:5',
            'form_data.stress1' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress2' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress3' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress4' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress5' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress6' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress7' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress8' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress9' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.stress10' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:0|max:4',
            'form_data.esteem1' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem2' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem3' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem4' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem5' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem6' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem7' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem8' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem9' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            'form_data.esteem10' => 'required_if:form_type,tes_psikologi,surat_psikolog|integer|min:1|max:4',
            // Validasi untuk tes psikologi: harus memilih surat psikolog yang sudah ada
            'linked_psych_form_id' => 'required_if:form_type,tes_psikologi|nullable|exists:medical_forms,id',
            // Field khusus untuk pendaftaran karakter
            'form_data.jenis_pemakaman' => 'required_if:form_type,pendaftaran_karakter|string|in:Penguburan,Kremasi',
            'form_data.tanggal_wafat' => 'required_if:form_type,pendaftaran_karakter|date',
            'form_data.kronologi_ck' => 'required_if:form_type,pendaftaran_karakter|string|max:1000',
            // Field untuk Penguburan
            'form_data.tempat_pemakaman' => 'required_if:form_data.jenis_pemakaman,Penguburan|string|max:255',
            'form_data.tanggal_pemakaman' => 'required_if:form_data.jenis_pemakaman,Penguburan|date',
            // Field untuk Kremasi
            'form_data.tanggal_kremasi' => 'required_if:form_data.jenis_pemakaman,Kremasi|date',
            'form_data.tempat_penyimpanan_abu' => 'required_if:form_data.jenis_pemakaman,Kremasi|string|max:255',
            // File upload untuk operasi_plastik (wajib)
            'form_data.photo_ktp' => 'required_if:form_type,operasi_plastik|file|image|max:4096',
            'form_data.photo_skb' => 'required_if:form_type,operasi_plastik|file|image|max:4096',
            // Checklist persetujuan input data
            'confirm_data' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validasi 1: Cek apakah surat masih status PENDING
        $pendingForm = MedicalForm::whereRaw('LOWER(character_name) = LOWER(?)', [$request->character_name])
            ->where('form_type', $request->form_type)
            ->where('status', 'pending')
            ->first();

        if ($pendingForm) {
            $errorMessage = "Surat atas nama karakter \"{$request->character_name}\" masih berstatus PENDING (Menunggu Persetujuan). Harap tunggu hingga surat diproses oleh staff kami sebelum mengajukan kembali.";
            return back()
                ->withErrors(['character_name' => $errorMessage])
                ->withInput()
                ->with('error', $errorMessage);
        }

        // Validasi 2: Cek apakah user sudah mengisi dalam 24 jam terakhir (Cooldown)
        // Kecuali jika statusnya 'rejected' (ditolak), maka boleh mengisi lagi
        $recentForm = MedicalForm::whereRaw('LOWER(character_name) = LOWER(?)', [$request->character_name])
            ->where('form_type', $request->form_type)
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', '!=', 'rejected')
            ->first();

        if ($recentForm) {
            $errorMessage = "Anda sudah membuat formulir ini dalam 24 jam terakhir. Mohon tunggu 24 jam sebelum membuat formulir baru.";
            return back()
                ->withErrors(['character_name' => $errorMessage])
                ->withInput()
                ->with('error', $errorMessage);
        }

        // reCAPTCHA dihapus, menggunakan checklist persetujuan sebagai pengganti

        // Ensure non-null description to satisfy DB NOT NULL constraint
        $fallbackDescription = function () use ($request) {
            $typeLabelMap = [
                'surat_kesehatan' => 'Pengajuan Surat Kesehatan',
                'operasi_plastik' => 'Permintaan Operasi Plastik',
                'tes_psikologi' => 'Pengisian Tes Psikologi',
                'surat_psikolog' => 'Permintaan Surat Psikolog',
                'pendaftaran_karakter' => 'Karakter Kill',
                'konsultasi_medis' => 'Konsultasi Medis',
                'laporan_kecelakaan' => 'Laporan Kecelakaan',
                'permintaan_ambulans' => 'Permintaan Ambulans',
            ];
            $label = $typeLabelMap[$request->form_type] ?? ucfirst(str_replace('_', ' ', $request->form_type));
            return $label . ' - otomatis (tanpa deskripsi tambahan)';
        };

        $description = $request->input('description');
        if (is_null($description) || $description === '') {
            $description = $fallbackDescription();
        }

        // Siapkan form_data yang sudah diproses (ganti file menjadi URL jika ada)
        $formData = $request->input('form_data', []);

        $ktpUrl = null;
        $skbUrl = null;
        $ktpStoredPath = null; // relative path in storage/app/public
        $skbStoredPath = null;

        if ($request->form_type === 'operasi_plastik') {
            if ($request->hasFile('form_data.photo_ktp')) {
                // For hosting compatibility, always use public directory
                $fileName = time() . '_' . $request->file('form_data.photo_ktp')->getClientOriginalName();
                $publicPath = public_path('uploads/operasi-plastik');

                // Create directory if it doesn't exist
                if (!is_dir($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }

                // Move file to public directory
                $request->file('form_data.photo_ktp')->move($publicPath, $fileName);
                $ktpStoredPath = 'uploads/operasi-plastik/' . $fileName;
                $ktpUrl = asset($ktpStoredPath);
                $formData['photo_ktp_url'] = $ktpUrl;
                unset($formData['photo_ktp']);
            }
            if ($request->hasFile('form_data.photo_skb')) {
                // For hosting compatibility, always use public directory
                $fileName = time() . '_' . $request->file('form_data.photo_skb')->getClientOriginalName();
                $publicPath = public_path('uploads/operasi-plastik');

                // Create directory if it doesn't exist
                if (!is_dir($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }

                // Move file to public directory
                $request->file('form_data.photo_skb')->move($publicPath, $fileName);
                $skbStoredPath = 'uploads/operasi-plastik/' . $fileName;
                $skbUrl = asset($skbStoredPath);
                $formData['photo_skb_url'] = $skbUrl;
                unset($formData['photo_skb']);
            }
        } elseif (in_array($request->form_type, ['tes_psikologi', 'surat_psikolog'])) {
            // Calculate Psychology Test Results

            // 1. Big Five Inventory (BFI-10)
            // Extraversion: 1R, 6 (1 is usually regular, 6 reversed. BFI scoring: R means Reverse-scored)
            // Check questions: 1=Extrovert, 6=Reserved/Quiet. So 1 is +, 6 is -.
            // Agreeableness: 2, 7 (2=Critical (-), 7=Warm (+)) -> 2 is -, 7 is +.
            // Conscientiousness: 3, 8 (3=Dependable (+), 8=Careless (-)) -> 3 is +, 8 is -.
            // Neuroticism: 4, 9 (4=Anxious (+), 9=Calm (-)) -> 4 is +, 9 is -.
            // Openness: 5, 10 (5=Open (+), 10=Conventional (-)) -> 5 is +, 10 is -.
            // Scale 1-5. Reverse score = 6 - x.

            $bfi_results = [];
            $bfi_scores = [];

            // Helper to safe get int
            $getVal = function ($key) use ($formData) {
                return intval($formData[$key] ?? 3);
            };

            // Extraversion
            $e_raw = $getVal('bigfive1') + (6 - $getVal('bigfive6'));
            $bfi_scores['extraversion'] = $e_raw / 2;

            // Agreeableness
            $a_raw = (6 - $getVal('bigfive2')) + $getVal('bigfive7');
            $bfi_scores['agreeableness'] = $a_raw / 2;

            // Conscientiousness
            $c_raw = $getVal('bigfive3') + (6 - $getVal('bigfive8'));
            $bfi_scores['conscientiousness'] = $c_raw / 2;

            // Neuroticism
            $n_raw = $getVal('bigfive4') + (6 - $getVal('bigfive9'));
            $bfi_scores['neuroticism'] = $n_raw / 2;

            // Openness
            $o_raw = $getVal('bigfive5') + (6 - $getVal('bigfive10'));
            $bfi_scores['openness'] = $o_raw / 2;

            $formData['bfi_scores'] = $bfi_scores;

            // 2. Perceived Stress Scale (PSS-10)
            // Scale 0-4.
            // Positive items (reversed): 4, 5, 7, 8. (Reverse: 4 - x)
            // Negative: 1, 2, 3, 6, 9, 10.
            $pss_score = 0;
            for ($i = 1; $i <= 10; $i++) {
                $val = intval($formData['stress' . $i] ?? 2);
                if (in_array($i, [4, 5, 7, 8])) {
                    $pss_score += (4 - $val);
                } else {
                    $pss_score += $val;
                }
            }
            $formData['pss_score'] = $pss_score;

            // 3. Rosenberg Self-Esteem Scale (RSES)
            // Scale 1-4.
            // Negative items (reversed): 3, 5, 8, 9, 10. (Reverse: 5 - x for 1-4 scale)
            // Positive: 1, 2, 4, 6, 7.
            $rses_score = 0;
            for ($i = 1; $i <= 10; $i++) {
                $val = intval($formData['esteem' . $i] ?? 2);
                if (in_array($i, [3, 5, 8, 9, 10])) {
                    $rses_score += (5 - $val);
                } else {
                    $rses_score += $val;
                }
            }
            $formData['rses_score'] = $rses_score;

            // Generate Suggestions
            $suggestions = [];

            // Stress Suggestions
            if ($pss_score >= 27) {
                $suggestions[] = "Skor stres Anda tergolong TINGGI. Disarankan untuk segera berkonsultasi dengan psikolog kami untuk manajemen stres, dan luangkan waktu untuk relaksasi atau aktivitas yang menyenangkan.";
            } elseif ($pss_score >= 14) {
                $suggestions[] = "Skor stres Anda tergolong SEDANG. Cobalah teknik pernapasan atau meditasi ringan, dan pastikan keseimbangan antara pekerjaan dan istirahat.";
            } else {
                $suggestions[] = "Skor stres Anda tergolong RENDAH. Pertahankan gaya hidup sehat Anda.";
            }

            // Self-Esteem Suggestions
            if ($rses_score < 15) {
                $suggestions[] = "Skor harga diri Anda tergolong RENDAH. Kami menyarankan sesi konseling untuk membantu membangun kepercayaan diri dan melihat potensi positif dalam diri Anda.";
            } elseif ($rses_score > 25) {
                $suggestions[] = "Skor harga diri Anda TINGGI/NORMAL. Anda memiliki pandangan positif terhadap diri sendiri.";
            } else {
                $suggestions[] = "Skor harga diri Anda dalam batas NORMAL. Terus kembangkan potensi diri Anda.";
            }

            // Personality specific suggestion (highest trait)
            $traits = $bfi_scores;
            arsort($traits);
            $top_trait = array_key_first($traits);
            $trait_names = [
                'extraversion' => 'Ekstroversi',
                'agreeableness' => 'Keramahan',
                'conscientiousness' => 'Ketekunan',
                'neuroticism' => 'Neurotisme (Sensitivitas Emosi)',
                'openness' => 'Keterbukaan'
            ];
            $suggestions[] = "Sifat dominan Anda adalah " . $trait_names[$top_trait] . ". Gunakan kekuatan ini dalam aktivitas Anda sehari-hari.";

            $formData['suggestions'] = $suggestions;

            // Append result summary to description for easy reading by staff
            $description .= "\n\nHASIL TES OTOMATIS:\n";
            $description .= "- PSS Score: $pss_score\n";
            $description .= "- RSES Score: $rses_score\n";
            $description .= "- BFI: E={$bfi_scores['extraversion']}, A={$bfi_scores['agreeableness']}, C={$bfi_scores['conscientiousness']}, N={$bfi_scores['neuroticism']}, O={$bfi_scores['openness']}";
        }

        // Provide default values for removed fields (fallback)
        $characterName = $request->character_name ?? 'Anonymous User';
        $hospital = $request->hospital ?? 'alta';

        // Ensure form_data has default values (fallback)
        if (!isset($formData['birth_date']))
            $formData['birth_date'] = '1990-01-01';
        if (!isset($formData['gender']))
            $formData['gender'] = 'Laki-laki';
        if (!isset($formData['age']))
            $formData['age'] = 25;
        if (!isset($formData['occupation']))
            $formData['occupation'] = 'Warga';
        if (!isset($formData['phone_number']))
            $formData['phone_number'] = '0000000000';

        $linkedFormId = null;

        // Handle linked psychology form for tes_psikologi (REQUIRED)
        if ($request->form_type === 'tes_psikologi') {
            if (!$request->filled('linked_psych_form_id')) {
                return back()
                    ->withErrors(['linked_psych_form_id' => 'Anda harus memilih Surat Psikolog yang sudah dibuat sebelumnya.'])
                    ->withInput()
                    ->with('error', 'Silakan pilih Surat Psikolog terlebih dahulu sebelum mengisi Tes Psikologi.');
            }

            $linkedId = $request->input('linked_psych_form_id');

            // Validate the linked form exists and is pending surat_psikolog
            $linkedForm = MedicalForm::where('id', $linkedId)
                ->where('form_type', 'surat_psikolog')
                ->where('status', 'pending')
                ->first();

            if (!$linkedForm) {
                return back()
                    ->withErrors(['linked_psych_form_id' => 'Surat Psikolog tidak ditemukan atau sudah diproses.'])
                    ->withInput()
                    ->with('error', 'Surat Psikolog yang dipilih tidak valid atau sudah diproses. Silakan pilih yang lain.');
            }

            // Auto-approve the linked psychology letter
            $linkedForm->update([
                'status' => 'approved',
                'processed_at' => now(),
                'notes' => 'Disetujui otomatis melalui pengisian Tes Psikologi'
            ]);

            // Link the form
            $linkedFormId = $linkedId;

            // INHERIT DATA FROM LINKED FORM
            // Prioritize linked form data over defaults/input
            $characterName = $linkedForm->character_name;
            $hospital = $linkedForm->hospital;

            // Inherit form_data fields if available in linked form
            if (isset($linkedForm->form_data['birth_date']))
                $formData['birth_date'] = $linkedForm->form_data['birth_date'];
            if (isset($linkedForm->form_data['gender']))
                $formData['gender'] = $linkedForm->form_data['gender'];
            if (isset($linkedForm->form_data['age']))
                $formData['age'] = $linkedForm->form_data['age'];
            if (isset($linkedForm->form_data['occupation']))
                $formData['occupation'] = $linkedForm->form_data['occupation'];
            if (isset($linkedForm->form_data['phone_number']))
                $formData['phone_number'] = $linkedForm->form_data['phone_number'];

            // Add note to description
            $description .= "\n\n[AUTO-LINKED] Terhubung dengan Surat Psikolog ID#{$linkedId} (sudah di-approve otomatis)";
        }

        // Prepare form creation data
        $formCreateData = [
            'character_name' => $characterName,
            'citizen_id' => $request->citizen_id,
            'form_type' => $request->form_type,
            'hospital' => $hospital,
            'description' => $description,
            'form_data' => $formData,
            'ip_address' => $request->ip(),
            'linked_form_id' => $linkedFormId
        ];

        $form = MedicalForm::create($formCreateData);

        // Webhook system removed for better performance

        return redirect()->route('public.form.success', $form->id)
            ->with('success', 'Formulir berhasil dikirim! Tim medis akan segera memproses permintaan Anda.');
    }

    public function formSuccess($id)
    {
        $form = MedicalForm::findOrFail($id);
        return view('public.form-success', compact('form'));
    }

    public function submitTestimoni(Request $request, $id)
    {
        $form = MedicalForm::findOrFail($id);

        // Validasi bahwa form belum memiliki testimoni
        if ($form->testimoni) {
            return redirect()->route('public.form.success', $id)
                ->with('error', 'Anda sudah mengirimkan testimoni sebelumnya.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'testimoni' => 'required|string|min:10|max:500',
        ]);

        $form->update([
            'rating' => $validated['rating'],
            'testimoni' => $validated['testimoni'],
            'testimoni_approved' => true, // Auto-approve untuk langsung muncul di beranda
        ]);

        return redirect()->route('public.form.success', $id)
            ->with('success', 'Terima kasih! Saran dan masukan Anda telah kami terima dan akan ditampilkan di halaman beranda.');
    }

    public function strukturalEms()
    {
        // Daftar nama yang harus ditampilkan di structural chart meskipun mereka admin
        // Ini untuk memastikan posisi penting seperti CEO muncul di chart
        $requiredNamesForHierarchy = [
            // High Command
            'Oliver Januari',
            'Joseph Preistley',
            'Jehan L. Keenan',
            // Department Heads
            'Oshee Khair',
            'Aurelya L. Keenan',
            'Abol Wangjanim',
            'Julian Rothschild',
            'Haruu Ravenscroft',
            'Valco Blanche',
            // People & Development
            'Kardus Smith',
            'Chris Wynlee',
            'Morgan Ackeric',
            'Cecilia Wynlee',
            'Witel Ivy',
            'Erga Shaka',
            'Udung Hayakawa',
            'Dilan Smith',
            'Mike Weston',
            'Nathan Ernesto',
            // Industrial & Employee Relation
            'Johns Ackeric',
            'Lemi Ackeric',
            'Mosawo Ackeric',
            'Darren Ackeric',
            'Suep Rahman',
            'Billy McCartney',
            'Nikola Charvi',
            // Clinical Education & Laboratory
            'Edel C. Zion',
            'Tan Ackeric',
            'Achmad Djayadinigrat',
            'Winnie A Honrado',
            'Joel Aldridge',
            // Forensic & Medico-Legal
            'Winther Sham Weasley',
            'Loen Sky',
            'Aiden Atmadja',
            'Ray Aldridge',
            'Rindu Winfield',
            // General Affair
            'Alicia L. Keenan',
            'Luffy Pielofi',
            'Wyda Cantik',
            'Claw Navida',
            'Queena Smith',
            'Jatmiko Tjokronugroho',
            'Jamal Shakur',
            'Keenanyohooo Fukushima',
            'Rikuni Aldridge',
            'Ousmane Sulaiman',
            'Jibil Dossman',
            'Kim Hayakawa',
            'Hansaga Honrado',
            'Bjorn Buchigiri',
            // Disciplinary Committee
            'Emir Rothschild',
            'Mayura Atmadja',
            'Rashid Jamal Ackeric',
            'Yuki Hayakawa',
            'Ochi Atmadja',
            'Satryo Greenboys',
            'Lucas C Blanche',
        ];

        // Normalize function untuk matching
        $normalizeName = function ($str) {
            return trim(strtolower(preg_replace('/\s+/', ' ', $str)));
        };

        // Ambil semua user RH untuk di-exclude dari EMS
        // Filter case-insensitive untuk menangkap variasi: "RH -", "RH-", "rh", dll
        // Juga menangkap variasi dengan spasi: "RH -", "RH - ", dll
        $rhUserIds = User::where('is_active', true)
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%rh%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%roxwood%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh -%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh-%'])
                    ->orWhere(function ($q) {
                        $q->whereNotNull('staff_id')
                            ->where(function ($sq) {
                                $sq->whereRaw('LOWER(staff_id) LIKE ?', ['%rh%'])
                                    ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh -%'])
                                    ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh-%']);
                            });
                    });
            })
            ->pluck('id')
            ->toArray();

        // Ambil semua user yang aktif dan memiliki role (bukan admin)
        // Juga include user tanpa role untuk menampilkan di organizational chart
        $users = User::where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('role', function ($roleQ) {
                    $roleQ->where('name', '!=', 'admin');
                })->orWhereNull('role_id');
            })
            ->with('role')
            ->orderByRoleLevel('desc')
            ->get();

        // Ambil user tambahan yang diperlukan untuk hierarchy meskipun mereka admin
        // Ini untuk memastikan posisi penting seperti CEO muncul di chart
        $additionalUsers = User::where('is_active', true)
            ->whereHas('role', function ($roleQ) {
                $roleQ->where('name', 'admin');
            })
            ->with('role')
            ->get()
            ->filter(function ($user) use ($requiredNamesForHierarchy, $normalizeName) {
                $userNameNormalized = $normalizeName($user->name);
                foreach ($requiredNamesForHierarchy as $requiredName) {
                    $requiredNameNormalized = $normalizeName($requiredName);

                    // Exact match (highest priority)
                    if ($userNameNormalized === $requiredNameNormalized) {
                        return true;
                    }

                    // Contains match (either direction)
                    if (
                        str_contains($userNameNormalized, $requiredNameNormalized)
                        || str_contains($requiredNameNormalized, $userNameNormalized)
                    ) {
                        return true;
                    }

                    // First word match (for cases like "Joseph Priestley" vs "JOSEPH GANTENG")
                    $userWords = array_filter(explode(' ', $userNameNormalized));
                    $requiredWords = array_filter(explode(' ', $requiredNameNormalized));
                    if (!empty($userWords) && !empty($requiredWords)) {
                        $userFirstWord = $userWords[0];
                        $requiredFirstWord = $requiredWords[0];
                        if ($userFirstWord === $requiredFirstWord && strlen($userFirstWord) > 2) {
                            return true;
                        }
                    }

                    // Last word match
                    if (!empty($userWords) && !empty($requiredWords)) {
                        $userLastWord = end($userWords);
                        $requiredLastWord = end($requiredWords);
                        if ($userLastWord === $requiredLastWord && strlen($userLastWord) > 2) {
                            return true;
                        }
                    }
                }
                return false;
            });

        // Merge users dengan additional users
        $users = $users->merge($additionalUsers)->unique('id');

        // Mapping hierarki berdasarkan data yang ada
        // Struktur hierarki akan dibuat berdasarkan role level dan nama user
        $hierarchy = $this->buildHierarchy($users);

        // Buat mapping user by name untuk akses cepat di view
        // Ini membantu view menemukan user dengan cepat tanpa perlu mencari lagi
        $userByNameMap = [];
        foreach ($users as $user) {
            $normalizedName = $normalizeName($user->name);
            $userByNameMap[$normalizedName] = $user;

            // Also map by first word for flexible matching
            $nameWords = array_filter(explode(' ', $normalizedName));
            if (!empty($nameWords)) {
                $firstWord = $nameWords[0];
                if (strlen($firstWord) > 2) {
                    // Store first user with this first word, but prefer exact name match
                    if (!isset($userByNameMap[$firstWord])) {
                        $userByNameMap[$firstWord] = $user;
                    } elseif ($userByNameMap[$firstWord]->id !== $user->id) {
                        // If different user, check if current user has exact name match
                        $existingNormalized = $normalizeName($userByNameMap[$firstWord]->name);
                        if ($existingNormalized === $normalizedName) {
                            // Keep exact match
                            continue;
                        } else {
                            // Check if current user is better match
                            $userByNameMap[$firstWord] = $user;
                        }
                    }
                }
            }
        }

        // Ambil staff berdasarkan role untuk EMS (tanpa RH) - Query langsung tanpa helper
        $staffByRoleEms = [
            'dokter_spesialis' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'dokter_spesialis');
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
            'dokter_umum' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'dokter_umum');
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
            'co_ass' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'co_ass');
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
            'perawat' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->whereIn('name', ['perawat', 'paramedic']);
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
            'trainee' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'trainee');
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
            'staff_manager' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'staff_manager');
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
            'manajer' => User::where('is_active', true)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'manajer');
                })
                ->when(!empty($rhUserIds), function ($query) use ($rhUserIds) {
                    return $query->whereNotIn('id', $rhUserIds);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get(),
        ];

        // Ambil staff berdasarkan role untuk Roxwood Hospital (dengan RH) - Query langsung
        $staffByRoleRoxwood = [
            'dokter_spesialis' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'dokter_spesialis');
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
            'dokter_umum' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'dokter_umum');
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
            'co_ass' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'co_ass');
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
            'perawat' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->whereIn('name', ['perawat', 'paramedic']);
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
            'trainee' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'trainee');
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
            'staff_manager' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'staff_manager');
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
            'manajer' => !empty($rhUserIds) ? User::where('is_active', true)
                ->whereIn('id', $rhUserIds)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'manajer');
                })
                ->with('role')
                ->orderBy('name', 'asc')
                ->get() : collect([]),
        ];

        return view('public.struktural-ems', compact('hierarchy', 'users', 'staffByRoleEms', 'staffByRoleRoxwood', 'userByNameMap'));
    }

    private function buildHierarchy($users)
    {
        // Mapping nama yang wajib digunakan (tidak akan diganti oleh matching database)
        // Format: 'nama yang dicari' => 'nama yang wajib ditampilkan'
        $mandatoryNames = [
            'dr. Tan Ackeric' => 'dr. Tan Ackeric',
            'Tan Ackeric' => 'dr. Tan Ackeric',
            'Dr Tan Ackeric' => 'dr. Tan Ackeric',
            'Dr. Tan Ackeric' => 'dr. Tan Ackeric',
        ];

        // Daftar nama yang harus di-exclude dari matching (untuk mencegah false positive)
        $excludedNames = [
            'tan noah rafael',
            'dr tan noah rafael',
            'dr. tan noah rafael',
            'dr tan noah',
            'tan noah',
        ];

        // Helper function untuk normalisasi nama
        $normalizeName = function ($str) {
            return trim(strtolower(preg_replace('/\s+/', ' ', $str)));
        };

        // Helper function untuk mencocokkan nama dari database
        $findUserByName = function ($searchName) use ($users, $normalizeName, $mandatoryNames, $excludedNames) {
            // Cek dulu apakah nama ini wajib digunakan
            $normalizedSearch = $normalizeName($searchName);
            foreach ($mandatoryNames as $key => $value) {
                if ($normalizeName($key) === $normalizedSearch) {
                    return $value;
                }
            }

            // Helper untuk check apakah nama harus di-exclude
            $shouldExclude = function ($userName) use ($normalizeName, $excludedNames, $normalizedSearch) {
                $normalizedUserName = $normalizeName($userName);

                // Jika mencari "tan ackeric", exclude semua yang mengandung "noah" atau "rafael"
                if (str_contains($normalizedSearch, 'tan') && str_contains($normalizedSearch, 'ackeric')) {
                    if (str_contains($normalizedUserName, 'noah') || str_contains($normalizedUserName, 'rafael')) {
                        return true;
                    }
                }

                // Check excluded names
                foreach ($excludedNames as $excluded) {
                    if ($normalizedUserName === $excluded || str_contains($normalizedUserName, $excluded)) {
                        return true;
                    }
                }

                return false;
            };

            // Split search name into words for flexible matching
            $searchWords = array_filter(explode(' ', $normalizedSearch));
            $firstWord = !empty($searchWords) ? $searchWords[0] : '';
            $lastWord = !empty($searchWords) ? end($searchWords) : '';

            // Try exact match first
            foreach ($users as $user) {
                // Skip excluded names
                if ($shouldExclude($user->name)) {
                    continue;
                }

                $normalizedUserName = $normalizeName($user->name);

                // Exact match
                if ($normalizedUserName === $normalizedSearch) {
                    return $user->name;
                }
            }

            // Try contains match
            foreach ($users as $user) {
                // Skip excluded names
                if ($shouldExclude($user->name)) {
                    continue;
                }

                $normalizedUserName = $normalizeName($user->name);

                // Contains match (either direction)
                if (
                    str_contains($normalizedUserName, $normalizedSearch)
                    || str_contains($normalizedSearch, $normalizedUserName)
                ) {
                    return $user->name;
                }
            }

            // Try first word match (for cases like "Joseph Priestley" vs "JOSEPH GANTENG")
            // This is important for flexible matching when names don't match exactly
            if (!empty($firstWord) && strlen($firstWord) > 2) {
                $matchedUsers = [];
                foreach ($users as $user) {
                    // Skip excluded names
                    if ($shouldExclude($user->name)) {
                        continue;
                    }

                    $normalizedUserName = $normalizeName($user->name);
                    $userWords = array_filter(explode(' ', $normalizedUserName));

                    // Check if first word matches
                    if (!empty($userWords) && $userWords[0] === $firstWord) {
                        $matchedUsers[] = $user;
                    }
                }

                // If multiple users match first word, prefer exact match or shortest name
                if (!empty($matchedUsers)) {
                    // Try to find exact match first
                    foreach ($matchedUsers as $matchedUser) {
                        $normalizedMatchedName = $normalizeName($matchedUser->name);
                        if ($normalizedMatchedName === $normalizedSearch) {
                            return $matchedUser->name;
                        }
                    }

                    // If no exact match, return the first match (usually the most relevant)
                    return $matchedUsers[0]->name;
                }
            }

            // Try last word match
            if (!empty($lastWord) && strlen($lastWord) > 2) {
                foreach ($users as $user) {
                    // Skip excluded names
                    if ($shouldExclude($user->name)) {
                        continue;
                    }

                    $normalizedUserName = $normalizeName($user->name);
                    $userWords = array_filter(explode(' ', $normalizedUserName));

                    // Check if last word matches
                    if (!empty($userWords) && end($userWords) === $lastWord) {
                        return $user->name;
                    }
                }
            }

            return null;
        };

        // Helper function untuk mendapatkan nama atau placeholder
        $getNameOrPlaceholder = function ($defaultName) use ($findUserByName, $mandatoryNames, $normalizeName) {
            // Cek dulu apakah nama ini wajib digunakan
            $normalizedDefault = $normalizeName($defaultName);
            foreach ($mandatoryNames as $key => $value) {
                if ($normalizeName($key) === $normalizedDefault) {
                    return $value;
                }
            }

            $matchedName = $findUserByName($defaultName);
            return $matchedName ?: ($defaultName ?: '[Belum diisi]');
        };

        // Helper function untuk memproses array staff dan mencocokkan dengan database
        $processStaffArray = function ($staffArray) use ($findUserByName, $mandatoryNames, $normalizeName) {
            $processed = [];
            foreach ($staffArray as $staffName) {
                // Cek dulu apakah nama ini wajib digunakan
                $normalizedStaff = $normalizeName($staffName);
                $isMandatory = false;
                $mandatoryValue = null;

                // PRIORITAS 1: Check khusus untuk Tan Ackeric - langsung return jika mengandung "tan" dan "ackeric" TANPA "noah" atau "rafael"
                if (
                    str_contains($normalizedStaff, 'tan') && str_contains($normalizedStaff, 'ackeric')
                    && !str_contains($normalizedStaff, 'noah') && !str_contains($normalizedStaff, 'rafael')
                ) {
                    $mandatoryValue = 'dr. Tan Ackeric';
                    $isMandatory = true;
                } else {
                    // PRIORITAS 2: Check mandatory names dengan exact match
                    foreach ($mandatoryNames as $key => $value) {
                        $normalizedKey = $normalizeName($key);

                        // Exact match
                        if ($normalizedKey === $normalizedStaff) {
                            $mandatoryValue = $value;
                            $isMandatory = true;
                            break;
                        }
                    }
                }

                // Jika mandatory, langsung return tanpa matching
                if ($isMandatory && $mandatoryValue) {
                    $processed[] = $mandatoryValue;
                } else {
                    // Hanya lakukan matching jika bukan mandatory
                    $matchedName = $findUserByName($staffName);
                    $processed[] = $matchedName ?: ($staffName ?: '[Belum diisi]');
                }
            }
            return $processed;
        };

        // Definisi hierarki lengkap sesuai permintaan
        $hierarchyStructure = [
            'level_0' => [
                'title' => 'High Command (Pimpinan Tertinggi)',
                'positions' => [
                    'Chief Executive Officer (CEO)' => $getNameOrPlaceholder('dr. Oliver Januari'),
                    'Hospital Director' => $getNameOrPlaceholder('dr. Joseph Preistley, Sp.B.'),
                    'Deputy Director' => $getNameOrPlaceholder('dr. Jehan L. Keenan, Sp.OT.')
                ]
            ],
            'level_1' => [
                'title' => 'Department of Human Capital',
                'departments' => [
                    'Department Head' => $getNameOrPlaceholder('dr. Oshee Khair, Sp.KJ., M.Sos.')
                ]
            ],
            'level_2' => [
                'title' => 'Department of Human Capital - Units',
                'departments' => [
                    'A. People & Development Unit' => [
                        'Head of People & Development' => $getNameOrPlaceholder('dr. Kardus Smith, Sp.KJ'),
                        'Staff' => $processStaffArray([
                            'dr. Chris Wynlee',
                            'dr. Morgan Ackeric',
                            'dr. Cecilia Wynlee',
                            'Witel Ivy, S.Ked',
                            'dr. Erga Shaka, Sp.An.',
                            'Udung Hayakawa, S.Ked',
                            'Dilan Smith, S.Ked',
                            'dr. Mike Weston',
                            'dr. Nathan Ernesto'
                        ])
                    ],
                    'B. Industrial & Employee Relation Unit' => [
                        'Head of Industrial & Employee Relation' => $getNameOrPlaceholder('dr. Johns Ackeric'),
                        'Deputy Head' => $getNameOrPlaceholder('dr. Lemi Ackeric'),
                        'Staff' => $processStaffArray([
                            'dr. Mosawo Ackeric',
                            'dr. Darren Ackeric',
                            'Suep Rahman, S.Ked',
                            'dr. Billy McCartney',
                            'Nikola Charvi, S.Ked'
                        ])
                    ]
                ]
            ],
            'level_3' => [
                'title' => 'Department of Medical Science & Laboratory',
                'departments' => [
                    'Department Head' => $getNameOrPlaceholder('dr. Aurelya L. Keenan, Sp.B., Sp.F.M.')
                ]
            ],
            'level_4' => [
                'title' => 'Department of Medical Science & Laboratory - Divisions',
                'departments' => [
                    'A. Clinical Education & Laboratory' => [
                        'Division Head' => $getNameOrPlaceholder('dr. Edel C. Zion, Sp.N'),
                        'Lead of Clinical Education & Laboratory' => $processStaffArray([
                            'dr. Tan Ackeric',
                            'dr. Achmad Djayadinigrat'
                        ]),
                        'Staff' => $processStaffArray([
                            'dr. Winnie A Honrado',
                            'Joel Aldridge, S.Ked'
                        ])
                    ],
                    'B. Forensic & Medico-Legal Laboratory' => [
                        'Division Head' => $getNameOrPlaceholder('dr. Winther Sham Weasley, Sp.KJ., Sp.F.M.'),
                        'Lead of Forensic & Medico-Legal' => $processStaffArray([
                            'dr. Loen Sky',
                            'dr. Aiden Atmadja'
                        ]),
                        'Staff' => $processStaffArray([
                            'dr. Ray Aldridge',
                            'dr. Rindu Winfield'
                        ])
                    ]
                ]
            ],
            'level_5' => [
                'title' => 'Department of General Affair',
                'departments' => [
                    'Department Head' => $getNameOrPlaceholder('drg. Abol Wangjanim, Sp.KGA'),
                    'Deputy of General Affair' => $getNameOrPlaceholder('dr. Haruu Ravenscroft')
                ]
            ],
            'level_6' => [
                'title' => 'Department of General Affair - Divisions',
                'departments' => [
                    'A. Logistics Division' => [
                        'Lead of Logistics Division' => $getNameOrPlaceholder('Wyda Cantik, S.Ked.'),
                        'Staff' => $processStaffArray([
                            'Claw Navida, S.Ked. (Pharmacy)',
                            'Queena Smith, S.Ked. (Consumption)',
                            'Jatmiko Tjokronugroho, A.Md. Kep. (Pharmacy)'
                        ])
                    ],
                    'B. Mobility Division' => [
                        'Lead of Mobility Division' => $getNameOrPlaceholder('dr. Alicia L. Keenan'),
                        'Assistant Mobility Division' => $getNameOrPlaceholder('dr. Luffy Pielofi'),
                        'Staff (Dispatchers)' => $processStaffArray([
                            'dr. Jamal Shakur',
                            'Keenanyohooo Fukushima, S.Ked.',
                            'Rikuni Aldridge, S.Ked.',
                            'Ousmane Sulaiman, S.Ked.'
                        ]),
                        'Staff (Vehicle Instructors)' => $processStaffArray([
                            'dr. Jibil Dossman',
                            'Kim Hayakawa, S.Ked.',
                            'Hansaga Honrado, S.Ked.',
                            'Bjorn Buchigiri, S.Ked.'
                        ])
                    ]
                ]
            ],
            'level_7' => [
                'title' => 'Department of Disciplinary Committee',
                'departments' => [
                    'Department Head' => $getNameOrPlaceholder('dr. Julian Rothschild'),
                    'Deputy Head' => $getNameOrPlaceholder('dr. Valco Blanche, Sp.BP-RE'),
                    'Staff' => $processStaffArray([
                        'Emir Rothschild, S.Ked.',
                        'Mayura Atmadja, S.Ked.',
                        'Rashid Jamal Ackeric, S.Ked.',
                        'Yuki Hayakawa, S.Ked.',
                        'Ochi Atmadja, S.Ked.',
                        'Satryo Greenboys, S.Ked.',
                        'Lucas C Blanche, S.Ked.'
                    ])
                ]
            ]
        ];

        return $hierarchyStructure;
    }

    /**
     * Generate sitemap.xml for SEO
     */
    public function sitemap()
    {
        $baseUrl = config('app.url');
        $currentDate = now()->toAtomString();

        // Define all public pages with their priority and change frequency
        $pages = [
            [
                'url' => route('public.index'),
                'priority' => '1.0',
                'changefreq' => 'daily',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.struktural-ems'),
                'priority' => '0.9',
                'changefreq' => 'weekly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.cek-kesehatan'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.operasi-plastik'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.tes-psikologi'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.surat-psikolog'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.pendaftaran-karakter'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            // Direct form routes
            [
                'url' => route('public.form', ['type' => 'surat_kesehatan']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.form', ['type' => 'operasi_plastik']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.form', ['type' => 'tes_psikologi']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.form', ['type' => 'surat_psikolog']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.form', ['type' => 'pendaftaran_karakter']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
            [
                'url' => route('public.form', ['type' => 'konsultasi_medis']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => now()->toAtomString()
            ],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
        $xml .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($pages as $page) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($page['url'], ENT_XML1, 'UTF-8') . '</loc>' . "\n";
            $xml .= '    <lastmod>' . htmlspecialchars($page['lastmod'], ENT_XML1, 'UTF-8') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . htmlspecialchars($page['changefreq'], ENT_XML1, 'UTF-8') . '</changefreq>' . "\n";
            $xml .= '    <priority>' . htmlspecialchars($page['priority'], ENT_XML1, 'UTF-8') . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Show feedback submission form
     */
    public function showFeedbackForm()
    {
        return view('feedback.index');
    }

    /**
     * Submit feedback
     */
    public function submitFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:laporan,masukan',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'name' => 'nullable|string|max:100',
        ], [
            'type.required' => 'Silakan pilih jenis laporan atau masukan.',
            'type.in' => 'Jenis yang dipilih tidak valid.',
            'subject.required' => 'Subjek harus diisi.',
            'subject.max' => 'Subjek terlalu panjang (maksimal 255 karakter).',
            'message.required' => 'Pesan harus diisi.',
            'message.max' => 'Pesan terlalu panjang (maksimal 5000 karakter).',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus: JPEG, PNG, JPG, atau GIF.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
            'name.max' => 'Nama terlalu panjang (maksimal 100 karakter).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['type', 'subject', 'message', 'name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('feedback', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        // Generate anonymous name if not provided
        if (empty($data['name'])) {
            $latestTicket = \App\Models\Feedback::max('id') ?? 0;
            $ticketNumber = str_pad($latestTicket + 1, 4, '0', STR_PAD_LEFT);
            $data['name'] = "Ticket #" . $ticketNumber;
        }

        // Create feedback
        \App\Models\Feedback::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'image' => $data['image'] ?? null,
            'status' => 'new',
        ]);

        return redirect()->route('feedback.success');
    }

    /**
     * Show feedback success page
     */
    public function feedbackSuccess()
    {
        return view('feedback.success');
    }
}
