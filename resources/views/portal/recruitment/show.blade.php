@extends('layouts.app')

@section('title', 'Tinjau Berkas: ' . $application->ic_name . ' - Portal Alta')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Top Header & Back Link -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
            <div>
                <a href="{{ route('portal.recruitment.index') }}" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-amber-300 font-semibold mb-2 transition">
                    &larr; Kembali ke Daftar Rekrutmen
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        {{ $application->ic_name }}
                    </h1>
                    <span class="font-mono text-amber-300 font-bold bg-amber-500/10 border border-amber-500/30 px-3 py-1 rounded-lg text-sm">
                        #{{ $application->cid }}
                    </span>
                    {!! $application->status_badge !!}
                </div>
            </div>

            <!-- Quick Action: Convert to Candidate Account -->
            <div class="flex items-center gap-2">
                @if(!$application->user_id)
                <form method="POST" action="{{ route('portal.recruitment.convert', $application) }}" onsubmit="return confirm('Buat akun calon medis untuk {{ $application->ic_name }} dan masukkan ke antrian interview?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-1.5">
                        <i class="fas fa-user-plus"></i> Buat Akun & Masukkan ke Interview
                    </button>
                </form>
                @else
                <span class="px-3.5 py-1.5 rounded-xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold flex items-center gap-1.5">
                    <i class="fas fa-check-double"></i> Terhubung ke Antrian Interview (Akun: {{ $application->user?->name }})
                </span>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm flex items-center gap-2">
                <i class="fas fa-check-circle text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT 2 COLS: DOSSIER CONTENT -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. INFORMASI IC CARD -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4 shadow-xl">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider border-b border-white/10 pb-3">
                        <i class="fas fa-id-card"></i> 1. Informasi & Riwayat Hidup IC
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block">Nama Karakter IC</span>
                            <span class="text-white font-bold text-sm">{{ $application->ic_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Citizen ID (CID)</span>
                            <span class="text-amber-300 font-mono font-bold text-sm">#{{ $application->cid }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Jenis Kelamin</span>
                            <span class="text-white font-semibold">{{ $application->gender }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Tanggal Lahir IC</span>
                            <span class="text-white font-semibold">{{ $application->birth_date?->format('d F Y') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Pengalaman Medis / EMS</span>
                            <span class="font-bold {{ $application->has_medical_exp === 'Ada' ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ $application->has_medical_exp }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Penjelasan Pengalaman</span>
                            <span class="text-slate-300">{{ $application->medical_exp_desc ?: '-' }}</span>
                        </div>
                    </div>

                    <!-- Alasan Bergabung (Check 50 Characters) -->
                    <div class="pt-3 border-t border-white/10 space-y-1.5">
                        @php
                            $charCount = mb_strlen(trim($application->reason_joining ?? ''));
                        @endphp
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Alasan Ingin Bergabung dengan IME Medical Center:</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $charCount >= 50 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                {{ $charCount }} huruf {{ $charCount >= 50 ? '(Lolos min. 50 huruf)' : '(Kurang dari 50 huruf)' }}
                            </span>
                        </div>
                        <div class="bg-slate-800/80 p-3.5 rounded-xl text-xs text-slate-200 leading-relaxed italic border border-white/5 whitespace-pre-line">
                            "{{ $application->reason_joining }}"
                        </div>
                    </div>

                    <!-- Pengalaman RP OOC -->
                    <div class="pt-3 border-t border-white/10 space-y-1.5 text-xs">
                        <span class="text-slate-400 font-semibold">Pengalaman Bermain RP (OOC):</span>
                        <div class="bg-slate-800/80 p-3.5 rounded-xl text-slate-200 leading-relaxed border border-white/5 whitespace-pre-line">
                            {{ $application->rp_experience }}
                        </div>
                    </div>
                </div>

                <!-- 2. INFORMASI OOC CARD -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4 shadow-xl">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider border-b border-white/10 pb-3">
                        <i class="fas fa-clock"></i> 2. Informasi OOC & Waktu Online
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">Jam Online / Masuk Kota</span>
                            <div class="flex flex-wrap gap-1.5">
                                @if(is_array($application->online_hours))
                                    @foreach($application->online_hours as $h)
                                        <span class="px-2.5 py-1 rounded-lg bg-sky-500/20 text-sky-300 font-semibold text-[11px] border border-sky-500/30">
                                            {{ $h }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 block mb-1">Hari Online / Masuk Kota</span>
                            <div class="flex flex-wrap gap-1.5">
                                @if(is_array($application->online_days))
                                    @foreach($application->online_days as $d)
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-semibold text-[11px] border border-emerald-500/30">
                                            {{ $d }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-400 block">Tanggung Jawab di Kota Lain</span>
                            <span class="text-slate-300">{{ $application->other_city_responsibility ?: 'Tidak ada' }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block">Username Discord</span>
                            <span class="text-white font-mono font-bold">{{ $application->discord_username ?: 'Tidak dicantumkan' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3. BERKAS LAMPIRAN DOKUMEN -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4 shadow-xl">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider border-b border-white/10 pb-3">
                        <i class="fas fa-file-medical-alt"></i> 3. Berkas Dokumen Pendukung
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- KTP File -->
                        <div class="p-3.5 rounded-xl bg-slate-800 border border-white/10 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white"><i class="fas fa-id-card text-amber-400 mr-1.5"></i> Foto KTP IC</span>
                                <a href="{{ asset($application->ktp_file) }}" target="_blank" class="text-amber-400 hover:underline text-[11px]">Buka Berkas &rarr;</a>
                            </div>
                            @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $application->ktp_file))
                                <a href="{{ asset($application->ktp_file) }}" target="_blank" class="block overflow-hidden rounded-lg border border-white/10 h-36 bg-black/40">
                                    <img src="{{ asset($application->ktp_file) }}" alt="KTP" class="w-full h-full object-cover hover:scale-105 transition">
                                </a>
                            @else
                                <div class="h-24 flex items-center justify-center text-xs text-slate-400 bg-white/5 rounded-lg">
                                    <i class="fas fa-file-pdf text-2xl text-rose-400 mr-2"></i> Dokumen PDF
                                </div>
                            @endif
                        </div>

                        <!-- SKB File -->
                        <div class="p-3.5 rounded-xl bg-slate-800 border border-white/10 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white"><i class="fas fa-file-contract text-blue-400 mr-1.5"></i> Foto SKB</span>
                                <a href="{{ asset($application->skb_file) }}" target="_blank" class="text-amber-400 hover:underline text-[11px]">Buka Berkas &rarr;</a>
                            </div>
                            @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $application->skb_file))
                                <a href="{{ asset($application->skb_file) }}" target="_blank" class="block overflow-hidden rounded-lg border border-white/10 h-36 bg-black/40">
                                    <img src="{{ asset($application->skb_file) }}" alt="SKB" class="w-full h-full object-cover hover:scale-105 transition">
                                </a>
                            @else
                                <div class="h-24 flex items-center justify-center text-xs text-slate-400 bg-white/5 rounded-lg">
                                    <i class="fas fa-file-pdf text-2xl text-rose-400 mr-2"></i> Dokumen PDF
                                </div>
                            @endif
                        </div>

                        <!-- Surat Kesehatan File -->
                        <div class="p-3.5 rounded-xl bg-slate-800 border border-white/10 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white"><i class="fas fa-heartbeat text-emerald-400 mr-1.5"></i> Surat Kesehatan</span>
                                <a href="{{ asset($application->health_cert_file) }}" target="_blank" class="text-amber-400 hover:underline text-[11px]">Buka Berkas &rarr;</a>
                            </div>
                            @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $application->health_cert_file))
                                <a href="{{ asset($application->health_cert_file) }}" target="_blank" class="block overflow-hidden rounded-lg border border-white/10 h-36 bg-black/40">
                                    <img src="{{ asset($application->health_cert_file) }}" alt="Surat Sehat" class="w-full h-full object-cover hover:scale-105 transition">
                                </a>
                            @else
                                <div class="h-24 flex items-center justify-center text-xs text-slate-400 bg-white/5 rounded-lg">
                                    <i class="fas fa-file-pdf text-2xl text-rose-400 mr-2"></i> Dokumen PDF
                                </div>
                            @endif
                        </div>

                        <!-- Surat Psikolog File (Optional) -->
                        <div class="p-3.5 rounded-xl bg-slate-800 border border-white/10 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white"><i class="fas fa-brain text-purple-400 mr-1.5"></i> Surat Psikolog</span>
                                @if($application->psychology_cert_file)
                                    <a href="{{ asset($application->psychology_cert_file) }}" target="_blank" class="text-amber-400 hover:underline text-[11px]">Buka Berkas &rarr;</a>
                                @endif
                            </div>
                            @if($application->psychology_cert_file)
                                @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $application->psychology_cert_file))
                                    <a href="{{ asset($application->psychology_cert_file) }}" target="_blank" class="block overflow-hidden rounded-lg border border-white/10 h-36 bg-black/40">
                                        <img src="{{ asset($application->psychology_cert_file) }}" alt="Surat Psikolog" class="w-full h-full object-cover hover:scale-105 transition">
                                    </a>
                                @else
                                    <div class="h-24 flex items-center justify-center text-xs text-slate-400 bg-white/5 rounded-lg">
                                        <i class="fas fa-file-pdf text-2xl text-rose-400 mr-2"></i> Dokumen PDF
                                    </div>
                                @endif
                            @else
                                <div class="h-36 flex flex-col items-center justify-center text-xs text-slate-500 bg-white/5 rounded-lg p-3 text-center">
                                    <i class="fas fa-minus-circle text-2xl mb-1 opacity-40"></i>
                                    <span>Tidak dilampirkan (Bisa menyusul)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT 1 COL: DECISION & VERIFICATION PANEL -->
            <div class="space-y-6">

                <!-- Status Update Form -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-4 shadow-xl">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                        <i class="fas fa-tasks text-amber-400"></i> Keputusan Seleksi Berkas
                    </h3>

                    <form method="POST" action="{{ route('portal.recruitment.status', $application) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Status Pelamar *</label>
                            <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-white/10 text-white text-xs focus:border-amber-400 outline-none" required>
                                <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                                <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Lolos Berkas</option>
                                <option value="interview" {{ $application->status === 'interview' ? 'selected' : '' }}>Panggil Wawancara (Interview)</option>
                                <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>Diterima Menjadi Anggota</option>
                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan Verifikator (IE / PND)</label>
                            <textarea name="reviewer_notes" rows="4" placeholder="Catatan hasil verifikasi berkas atau alasan penolakan/pemanggilan..." class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-white/10 text-white text-xs focus:border-amber-400 outline-none">{{ $application->reviewer_notes }}</textarea>
                        </div>

                        @if($application->reviewedBy)
                        <div class="text-[11px] text-slate-400 bg-white/5 p-3 rounded-xl space-y-0.5">
                            <div>Diverifikasi oleh: <strong class="text-white">{{ $application->reviewedBy->name }}</strong></div>
                            <div>Waktu: {{ $application->reviewed_at?->translatedFormat('d M Y, H:i') }} WIB</div>
                        </div>
                        @endif

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition shadow-lg">
                            Simpan Keputusan
                        </button>
                    </form>
                </div>

                <!-- Periode Info Card -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-2.5 text-xs text-slate-300 shadow-xl">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block">Informasi Batch</span>
                    <div class="font-bold text-white text-sm">{{ $application->period?->batch_name ?? 'Batch Umum' }}</div>
                    <div>Terkirim pada: {{ $application->created_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</div>
                    <div>Hospital: <span class="uppercase font-bold text-amber-400">{{ $application->hospital }}</span></div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
