@extends('layouts.app')

@section('title', 'Pengajuan Role Interviewer — Alta Hospital')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/40 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-3xl mx-auto text-white space-y-6">

        {{-- Header Card --}}
        <div class="glass-effect rounded-2xl p-6 sm:p-8 border border-white/10 shadow-2xl space-y-3">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-semibold">
                    <i class="fas fa-id-badge mr-1"></i> Penugasan Sementara Khusus Rekrutmen
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 text-xl shadow-inner">
                    <i class="fas fa-user-plus"></i>
                </span>
                Pengajuan Diri Sebagai Petugas Interviewer
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed">
                Ingin membantu tim rumah sakit melakukan wawancara calon staf medis baru saat masa recruitment dibuka? Kirim pengajuan di bawah ini. Tim PND &amp; IE akan meninjau dan langsung menyetujui (ACC) pengajuan Anda.
            </p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-xl px-5 py-4 text-emerald-300 text-sm flex items-center gap-3 shadow-lg">
                <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
                <div>
                    <strong class="font-bold block">Berhasil!</strong>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-sky-500/20 border border-sky-500/40 rounded-xl px-5 py-4 text-sky-300 text-sm flex items-center gap-3 shadow-lg">
                <i class="fas fa-info-circle text-sky-400 text-lg"></i>
                <div>
                    <strong class="font-bold block">Informasi</strong>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-500/20 border border-rose-500/40 rounded-xl px-5 py-4 text-rose-300 text-sm flex items-center gap-3 shadow-lg">
                <i class="fas fa-exclamation-circle text-rose-400 text-lg"></i>
                <div>
                    <strong class="font-bold block">Perhatian</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Status Pengajuan Sebelumnya --}}
        @if($myApplication)
            @if($myApplication->status === 'pending')
                <div class="glass-effect rounded-2xl p-6 border border-amber-500/40 bg-amber-500/10 shadow-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400">
                                <i class="fas fa-clock"></i>
                            </span>
                            <h3 class="font-bold text-white text-sm">Status: Menunggu Persetujuan (ACC)</h3>
                        </div>
                        {!! $myApplication->status_badge !!}
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Pengajuan Anda yang dikirim pada <strong>{{ $myApplication->created_at->format('d M Y H:i') }}</strong> sedang dalam antrean review oleh tim PND atau IE. Begitu di-ACC, halaman ini akan otomatis membuka akses antrean calon medis untuk Anda.
                    </p>
                    @if($myApplication->reason)
                    <div class="text-xs text-slate-400 bg-white/5 p-3 rounded-xl border border-white/10 mt-2">
                        <span class="text-slate-500 block mb-0.5">Catatan/Alasan yang Anda kirimkan:</span>
                        <p class="text-slate-200 italic">"{{ $myApplication->reason }}"</p>
                    </div>
                    @endif
                </div>
            @elseif($myApplication->status === 'rejected')
                <div class="glass-effect rounded-2xl p-6 border border-rose-500/30 bg-rose-500/10 shadow-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-rose-300 text-sm flex items-center gap-2">
                            <i class="fas fa-times-circle"></i> Pengajuan Sebelumnya Belum Disetujui
                        </h3>
                        {!! $myApplication->status_badge !!}
                    </div>
                    <p class="text-xs text-slate-300">
                        Catatan peninjau: {{ $myApplication->action_notes ?: 'Kuota interviewer saat ini telah mencukupi.' }}
                    </p>
                    <p class="text-xs text-slate-400">Anda dapat mengajukan permohonan kembali dengan mengisi formulir di bawah ini.</p>
                </div>
            @endif
        @endif

        {{-- Form Pengajuan Baru (Tampil jika belum ada yang pending) --}}
        @if(!$myApplication || $myApplication->status !== 'pending')
        <div class="glass-effect rounded-2xl p-6 sm:p-8 border border-white/10 shadow-2xl space-y-6">
            <h3 class="text-xs uppercase font-bold text-indigo-300 tracking-wider flex items-center gap-2 pb-3 border-b border-white/10">
                <i class="fas fa-paper-plane"></i> Formulir Pengajuan Tugas Interviewer
            </h3>

            {{-- Identitas Pemohon --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-white/5 p-4 rounded-xl border border-white/10">
                <div>
                    <span class="text-slate-400 block mb-0.5">Nama Pemohon:</span>
                    <strong class="text-white text-sm">{{ $user->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Jabatan Medis Saat Ini:</span>
                    <span class="text-sky-300 font-semibold">{{ $user->medicRole?->display_name ?? $user->role?->display_name ?? 'Staf' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Divisi Manajerial:</span>
                    <span class="text-purple-300 font-semibold">{{ $user->subRole?->display_name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Citizen ID (CID):</span>
                    <span class="font-mono text-emerald-300 font-bold">{{ $user->citizen_id ?? 'Belum Diatur' }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('portal.interview.apply') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-2">
                        Alasan &amp; Kesiapan Waktu Menjadi Interviewer <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4" maxlength="1000"
                              placeholder="Ceritakan singkat motivasi Anda dan perkiraan jadwal jam online Anda yang siap digunakan untuk mewawancarai calon medis..."
                              class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl p-3.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ old('reason') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        Role Interviewer bersifat sementara (ad-hoc) untuk masa rekrutmen dan dapat dicabut kapan saja oleh PND &amp; IE saat masa penerimaan selesai.
                    </p>
                </div>

                <div class="pt-2">
                    <label class="flex items-start gap-3 p-3.5 rounded-xl bg-indigo-500/10 border border-indigo-500/30 cursor-pointer select-none">
                        <input type="checkbox" required class="w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800 mt-0.5">
                        <span class="text-xs text-slate-300 leading-relaxed">
                            Saya berkomitmen untuk menjalankan tugas wawancara secara objektif, menjunjung tinggi etika rumah sakit, dan mengikuti standar penilaian yang telah ditetapkan.
                        </span>
                    </label>
                </div>

                <div class="pt-4 border-t border-white/10 flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-slate-300 rounded-xl text-xs font-semibold transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-900/30 transition-all flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Pengajuan Role
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
