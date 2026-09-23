<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentApplication;
use App\Models\RecruitmentPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RecruitmentController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran jika recruitment buka, atau halaman ditutup jika tutup.
     */
    public function index()
    {
        $period = RecruitmentPeriod::currentOpen('alta');

        if (!$period) {
            return view('public.recruitment.closed');
        }

        return view('public.recruitment.form', compact('period'));
    }

    /**
     * Simpan pengajuan pendaftaran recruitment.
     */
    public function store(Request $request)
    {
        $period = RecruitmentPeriod::currentOpen('alta');

        if (!$period) {
            return redirect()->route('public.recruitment')
                ->with('error', 'Mohon maaf, periode pendaftaran rekrutmen saat ini sudah ditutup.');
        }

        // 1. Validasi Input Dasar
        $request->validate([
            'agree_general_req'          => 'accepted',
            'agree_special_req'          => 'accepted',
            'ic_name'                    => 'required|string|max:100',
            'cid'                        => 'required|string|max:50',
            'gender'                     => 'required|in:Laki-laki,Perempuan',
            'birth_date'                 => 'required|date',
            'has_medical_exp'            => 'required|in:Ada,Tidak',
            'medical_exp_desc'           => 'required|string',
            'reason_joining'             => 'required|string',
            'rp_experience'              => 'required|string',
            'ktp_file'                   => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'skb_file'                   => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'health_cert_file'           => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'psychology_cert_file'       => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'other_city_responsibility'  => 'nullable|string',
            'online_hours'               => 'required|array|min:1',
            'online_days'                => 'required|array|min:1',
            'discord_username'           => 'nullable|string|max:100',
            'email'                      => 'required|email|max:191',
            'password'                   => 'required|string|min:8|confirmed',
        ], [
            'agree_general_req.accepted' => 'Anda harus menyetujui seluruh persyaratan umum EMS.',
            'agree_special_req.accepted' => 'Anda harus menyetujui persyaratan khusus IC sebelum interview.',
            'ic_name.required'           => 'Nama Karakter IC wajib diisi.',
            'cid.required'               => 'CID (Citizen ID) wajib diisi.',
            'gender.required'            => 'Jenis kelamin wajib dipilih.',
            'birth_date.required'        => 'Tanggal lahir IC wajib diisi.',
            'has_medical_exp.required'   => 'Pilihan pengalaman medis wajib diisi.',
            'medical_exp_desc.required'  => 'Penjelasan pengalaman medis wajib diisi (isi 0 jika tidak ada).',
            'reason_joining.required'    => 'Alasan ingin bergabung wajib diisi.',
            'rp_experience.required'     => 'Pengalaman bermain RP (OOC) wajib diisi.',
            'ktp_file.required'          => 'Foto KTP IC wajib dilampirkan.',
            'skb_file.required'          => 'Foto SKB wajib dilampirkan.',
            'health_cert_file.required'  => 'Foto Surat Kesehatan wajib dilampirkan.',
            'ktp_file.max'               => 'Ukuran berkas KTP maksimal 10 MB.',
            'skb_file.max'               => 'Ukuran berkas SKB maksimal 10 MB.',
            'health_cert_file.max'       => 'Ukuran berkas Surat Kesehatan maksimal 10 MB.',
            'online_hours.required'      => 'Pilih minimal satu jam online / masuk kota.',
            'online_days.required'       => 'Pilih minimal satu hari online / masuk kota.',
            'email.required'             => 'Email wajib diisi untuk membuat akun portal jika diterima.',
            'email.email'                => 'Format email tidak valid.',
            'password.required'          => 'Password wajib diisi (minimal 8 karakter).',
            'password.min'               => 'Password minimal 8 karakter.',
            'password.confirmed'         => 'Konfirmasi password tidak sesuai.',
        ]);

        // 2. Validasi Minimal 50 Huruf/Karakter untuk Alasan Bergabung
        $charCount = mb_strlen(trim((string) $request->reason_joining));
        if ($charCount < 50) {
            return back()
                ->withErrors(['reason_joining' => "Alasan bergabung wajib minimal 50 huruf. Jawaban Anda saat ini baru {$charCount} huruf. Silakan jelaskan lebih detail."])
                ->withInput();
        }

        // 3. Simpan Berkas Upload
        $uploadDir = 'uploads/recruitment';
        $destinationPath = public_path($uploadDir);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $ktpPath = $this->uploadFile($request->file('ktp_file'), 'ktp');
        $skbPath = $this->uploadFile($request->file('skb_file'), 'skb');
        $healthCertPath = $this->uploadFile($request->file('health_cert_file'), 'health');
        $psychologyCertPath = $request->hasFile('psychology_cert_file')
            ? $this->uploadFile($request->file('psychology_cert_file'), 'psychology')
            : null;

        // 4. Buat Record Pengajuan
        $application = RecruitmentApplication::create([
            'period_id'                 => $period->id,
            'hospital'                  => 'alta',
            'agree_general_req'         => true,
            'agree_special_req'         => true,
            'ic_name'                   => strip_tags(trim($request->ic_name)),
            'cid'                       => ltrim(trim($request->cid), '#'),
            'gender'                    => $request->gender,
            'birth_date'                => $request->birth_date,
            'has_medical_exp'           => $request->has_medical_exp,
            'medical_exp_desc'          => strip_tags($request->medical_exp_desc),
            'reason_joining'            => strip_tags($request->reason_joining),
            'rp_experience'             => strip_tags($request->rp_experience),
            'ktp_file'                  => $ktpPath,
            'skb_file'                  => $skbPath,
            'health_cert_file'          => $healthCertPath,
            'psychology_cert_file'      => $psychologyCertPath,
            'other_city_responsibility' => $request->other_city_responsibility ? strip_tags($request->other_city_responsibility) : null,
            'online_hours'              => $request->online_hours,
            'online_days'               => $request->online_days,
            'discord_username'          => $request->discord_username ? strip_tags($request->discord_username) : null,
            'email'                     => strtolower(trim($request->email)),
            'password_temp'             => $request->password, // plain — akan di-hash saat akun dibuat
            'status'                    => 'pending',
        ]);

        // 5. Kirim Notifikasi Discord Webhook ke IE & PND
        $this->sendDiscordRecruitmentAlert($application);

        return redirect()->route('public.recruitment.success', $application->id);
    }

    /**
     * Halaman Sukses setelah berhasil mendaftar (sesuai Screenshot 5 Google Form).
     */
    public function success($id)
    {
        $application = RecruitmentApplication::findOrFail($id);
        return view('public.recruitment.success', compact('application'));
    }

    /**
     * Halaman Cek Status Pendaftaran Rekrutmen (Pelacakan Transparan via Citizen ID / CID)
     */
    public function statusCheck(Request $request)
    {
        $cid = trim((string) $request->input('cid', ''));
        $cidClean = preg_replace('/^(char\d+:|citizen:|cid:|id:|license:)/i', '', strtolower($cid));
        $cidClean = trim(str_replace(['#', ' ', '-', '.'], '', $cidClean));

        $applications = collect();
        $searched = false;

        if (!empty($cidClean)) {
            $searched = true;
            $applications = RecruitmentApplication::with(['period', 'user'])
                ->where(function ($q) use ($cid, $cidClean) {
                    $q->where('cid', $cid)
                      ->orWhere('cid', $cidClean)
                      ->orWhereRaw('LOWER(TRIM(cid)) = ?', [$cidClean])
                      ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(TRIM(cid), ' ', ''), '#', ''), '-', '')) = ?", [$cidClean]);
                })
                ->latest()
                ->get();
        }

        return view('public.recruitment.status', compact('applications', 'cid', 'searched'));
    }

    /**
     * Simpan email & password untuk pelamar yang sudah terlanjur daftar
     * sebelum kolom email tersedia di formulir.
     */
    public function setCredentials(Request $request)
    {
        $request->validate([
            'application_id' => 'required|integer|exists:recruitment_applications,id',
            'cid'            => 'required|string',
            'email'          => 'required|email|max:191',
            'password'       => 'required|string|min:8|confirmed',
        ], [
            'application_id.exists' => 'Data pendaftaran tidak ditemukan.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak sesuai.',
        ]);

        $app = RecruitmentApplication::find($request->application_id);

        if (!$app) {
            return back()->withErrors(['email' => 'Data pendaftaran tidak ditemukan.']);
        }

        // Cek apakah CID cocok (keamanan dasar agar orang lain tidak bisa ubah)
        $cidClean = strtoupper(trim(str_replace(['#', ' '], '', (string) $request->cid)));
        $appCid   = strtoupper(trim(str_replace(['#', ' '], '', (string) $app->cid)));

        if ($cidClean !== $appCid) {
            return back()->withErrors(['email' => 'Citizen ID tidak cocok dengan data pendaftaran.']);
        }

        // Jangan timpa jika email sudah ada (sudah di-set sebelumnya atau akun sudah aktif)
        if (!empty($app->email) && !empty($app->password_temp)) {
            return redirect()
                ->route('public.recruitment.status', ['cid' => $app->cid])
                ->with('info', 'Email dan password Anda sudah terdaftar sebelumnya. Tidak ada perubahan yang dilakukan.');
        }

        // Simpan email & password
        $app->update([
            'email'         => strtolower(trim($request->email)),
            'password_temp' => $request->password, // plain — akan di-hash saat akun dibuat
        ]);

        // Jika akun user sudah terbuat (user_id ada), update juga passwordnya
        if ($app->user_id && $app->user) {
            $app->user->update([
                'email'    => strtolower(trim($request->email)),
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
        }

        return redirect()
            ->route('public.recruitment.status', ['cid' => $app->cid])
            ->with('success', 'Email dan password berhasil disimpan! Simpan email dan password Anda baik-baik untuk login ke Portal Staf jika diterima.');
    }

    /**
     * Helper Upload File ke public/uploads/recruitment
     */
    private function uploadFile($file, string $prefix): string
    {
        $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/recruitment'), $filename);
        return 'uploads/recruitment/' . $filename;
    }

    /**
     * Kirim Webhook Discord saat ada pelamar baru
     */
    private function sendDiscordRecruitmentAlert(RecruitmentApplication $app): void
    {
        try {
            $webhookUrl = config('services.discord.recruitment_webhook')
                ?? config('services.discord.webhook_url')
                ?? env('DISCORD_WEBHOOK_URL');

            if (!$webhookUrl) {
                return;
            }

            $hours = is_array($app->online_hours) ? implode(', ', $app->online_hours) : '-';
            $days  = is_array($app->online_days) ? implode(', ', $app->online_days) : '-';

            $embed = [
                'title'       => '📋 Form Pendaftaran Medis Baru Masuk!',
                'description' => "Seorang calon paramedic telah mengirimkan formulir pendaftaran ke Industrial & Employee Relations (IE).",
                'color'       => 0x107c41, // Emerald Green
                'fields'      => [
                    ['name' => '👤 Nama Karakter IC', 'value' => $app->ic_name, 'inline' => true],
                    ['name' => '🆔 CID', 'value' => '#' . $app->cid, 'inline' => true],
                    ['name' => '⚧ Jenis Kelamin', 'value' => $app->gender, 'inline' => true],
                    ['name' => '🎂 Tgl Lahir IC', 'value' => $app->birth_date?->format('d/m/Y') ?? '-', 'inline' => true],
                    ['name' => '🩺 Pengalaman Medis', 'value' => $app->has_medical_exp, 'inline' => true],
                    ['name' => '💬 Discord / Kontak', 'value' => $app->discord_username ?? '-', 'inline' => true],
                    ['name' => '⏰ Jam Online', 'value' => $hours, 'inline' => false],
                    ['name' => '📅 Hari Online', 'value' => $days, 'inline' => false],
                ],
                'footer'      => [
                    'text' => 'IME Medical Center — Recruitment System Alta Hospital',
                ],
                'timestamp'   => now()->toISOString(),
            ];

            Http::timeout(5)->post($webhookUrl, [
                'content' => '📢 **[OPEN RECRUITMENT]** Formulir pendaftaran baru telah diterima! Mohon PND & IE memeriksa berkas pelamar.',
                'embeds'  => [$embed],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim Webhook Discord Recruitment: ' . $e->getMessage());
        }
    }
}
