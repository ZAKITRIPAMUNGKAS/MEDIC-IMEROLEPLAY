@extends('layouts.app')

@section('title', 'Role Interview — Antrean Calon Anggota Medis')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/40 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-7xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-semibold">
                        <i class="fas fa-id-badge mr-1"></i> Khusus PND, IE &amp; Petugas Interviewer
                    </span>
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-semibold">
                        <i class="fas fa-hourglass-half mr-1"></i> Tugas Sementara
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 text-lg">
                        <i class="fas fa-comments"></i>
                    </span>
                    Wawancara Calon Staf Medis
                </h1>
                <p class="text-slate-300 text-sm mt-1">
                    Kelola antrean wawancara calon anggota medis dari hasil formulir recruitment, input evaluasi interview, dan tetapkan rekomendasi jenjang jabatan awal.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                @if($canManageInterviewers)
                <button type="button" onclick="document.getElementById('interviewerManagementCard').classList.toggle('hidden')"
                        class="relative px-4 py-2 bg-indigo-600/30 hover:bg-indigo-600/50 border border-indigo-500/40 text-indigo-200 hover:text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition-all shadow-md">
                    <i class="fas fa-user-shield text-indigo-400"></i> Kelola Tim Interviewer ({{ $activeInterviewers->count() }})
                    @if($pendingApplications->isNotEmpty())
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping absolute -top-1 -right-1"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 absolute -top-1 -right-1"></span>
                    @endif
                </button>
                @endif
                <div class="text-right bg-white/5 border border-white/10 px-4 py-2 rounded-xl">
                    <span class="text-xs text-indigo-300 block">Antrean Siap Interview:</span>
                    <span class="text-xl font-bold text-white">{{ $candidates->total() }}</span>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-xl px-4 py-3 text-emerald-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-check-circle text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-500/20 border border-rose-500/40 rounded-xl px-4 py-3 text-rose-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-exclamation-circle text-rose-400"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Panel Kelola Petugas Interviewer Sementara (Khusus PND, IE, Admin) --}}
        @if($canManageInterviewers)
        <div id="interviewerManagementCard" class="glass-effect rounded-2xl p-6 border border-indigo-500/30 shadow-2xl space-y-6 {{ $pendingApplications->isNotEmpty() ? '' : ($activeInterviewers->isEmpty() ? '' : 'hidden') }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-white/10">
                <div>
                    <h3 class="font-bold text-white text-base flex items-center gap-2">
                        <i class="fas fa-users-cog text-indigo-400"></i> Manajemen Tim Interviewer (Tugas Sementara)
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Role Interviewer bersifat penugasan ad-hoc selama masa rekrutmen. Anggota staf dapat mengajukan diri dan Anda cukup klik <strong>ACC (Setujui)</strong> untuk mengaktifkannya secara otomatis.
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('interviewerManagementCard').classList.add('hidden')" class="text-slate-400 hover:text-white text-xs self-start sm:self-center">
                    <i class="fas fa-chevron-up mr-1"></i> Tutup Panel
                </button>
            </div>

            {{-- Bagian 1: Pengajuan Role Interviewer dari Anggota Staf (Menunggu ACC) --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-300 flex items-center gap-1.5">
                        <i class="fas fa-inbox text-amber-400"></i> Pengajuan Role Interviewer dari Anggota Staf
                    </h4>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $pendingApplications->isNotEmpty() ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse' : 'bg-white/10 text-slate-400' }}">
                        {{ $pendingApplications->count() }} Menunggu ACC
                    </span>
                </div>

                @if($pendingApplications->isEmpty())
                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-400 text-center">
                        <i class="fas fa-check-circle text-emerald-400 mr-1"></i> Tidak ada pengajuan baru yang menunggu persetujuan. Anggota staf dapat mengajukan diri melalui portal interview.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($pendingApplications as $app)
                        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 space-y-3 shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-500/30 to-indigo-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($app->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h5 class="text-white font-bold text-xs">{{ $app->user->name }}</h5>
                                        <p class="text-[11px] text-sky-300">{{ $app->user->medicRole?->display_name ?? $app->user->role?->display_name ?? 'Staf' }} {{ $app->user->subRole ? '('.$app->user->subRole->display_name.')' : '' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400">{{ $app->created_at->diffForHumans() }}</span>
                            </div>

                            @if($app->reason)
                            <div class="text-[11px] text-slate-300 bg-black/30 p-2.5 rounded-lg border border-white/5 italic">
                                "{{ $app->reason }}"
                            </div>
                            @endif

                            <div class="flex items-center justify-end gap-2 pt-1 border-t border-white/10">
                                <form method="POST" action="{{ route('portal.interview.reject-application', $app->id) }}" onsubmit="return confirm('Tolak permohonan role interviewer dari {{ addslashes($app->user->name) }}?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-rose-600/30 hover:bg-rose-600 text-rose-300 hover:text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('portal.interview.approve-application', $app->id) }}" onsubmit="return confirm('ACC dan aktifkan role Interviewer untuk {{ addslashes($app->user->name) }}?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-lg text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                                        <i class="fas fa-check-circle"></i> Setujui (ACC)
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Bagian 2: Form Tugaskan Interviewer Manual (Opsi Tambahan) --}}
            <details class="bg-white/5 rounded-xl border border-white/10 overflow-hidden">
                <summary class="px-4 py-3 text-xs font-semibold text-slate-300 cursor-pointer hover:bg-white/5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fas fa-user-plus text-indigo-400"></i> Opsi Manual: Tugaskan Staf Tanpa Pengajuan</span>
                    <span class="text-[11px] text-slate-400">Klik untuk buka form &darr;</span>
                </summary>
                <form method="POST" action="{{ route('portal.interview.assign') }}" class="p-4 pt-2 flex flex-col sm:flex-row gap-3 items-end border-t border-white/10">
                    @csrf
                    <div class="w-full sm:flex-1">
                        <select name="user_id" required
                                class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">-- Pilih Anggota Medis yang Ditugaskan --</option>
                            @foreach($availableStaff as $staf)
                            <option value="{{ $staf->id }}">
                                {{ $staf->name }} — {{ $staf->medicRole?->display_name ?? $staf->role?->display_name ?? 'Staf' }} {{ $staf->subRole ? '('.$staf->subRole->display_name.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl text-xs font-bold shadow-md transition-all whitespace-nowrap">
                        <i class="fas fa-check mr-1.5"></i> Tugaskan Langsung
                    </button>
                </form>
            </details>

            {{-- Tabel Daftar Interviewer Aktif Saat Ini --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-300 mb-2.5">
                    Petugas Interviewer Sementara Saat Ini ({{ $activeInterviewers->count() }})
                </h4>
                @if($activeInterviewers->isEmpty())
                    <p class="text-xs text-slate-400 italic bg-white/5 p-3 rounded-xl">
                        Belum ada staf yang ditugaskan sebagai interviewer sementara. (Anggota divisi IE &amp; PND tetap memiliki hak akses otomatis).
                    </p>
                @else
                    <div class="overflow-x-auto rounded-xl border border-white/10">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-left font-semibold">
                                    <th class="px-4 py-2.5">Nama Petugas</th>
                                    <th class="px-4 py-2.5">Jabatan Medis</th>
                                    <th class="px-4 py-2.5">Divisi Manajerial</th>
                                    <th class="px-4 py-2.5 text-center">Status Akses</th>
                                    <th class="px-4 py-2.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-slate-200">
                                @foreach($activeInterviewers as $ai)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-2.5 font-medium text-white">{{ $ai->name }}</td>
                                    <td class="px-4 py-2.5 text-sky-300">{{ $ai->medicRole?->display_name ?? $ai->role?->display_name ?? '-' }}</td>
                                    <td class="px-4 py-2.5 text-purple-300">{{ $ai->subRole?->display_name ?? 'Tidak Ada' }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            Aktif Sementara
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <form method="POST" action="{{ route('portal.interview.revoke', $ai->id) }}" onsubmit="return confirm('Cabut tugas interviewer sementara untuk {{ $ai->name }}?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-rose-600/30 hover:bg-rose-600 text-rose-300 hover:text-white rounded-lg text-[11px] font-medium transition-colors">
                                                <i class="fas fa-user-minus mr-1"></i> Cabut Tugas
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Search & Filter Form --}}
        <div class="glass-effect rounded-2xl p-4 border border-white/10">
            <form method="GET" action="{{ route('portal.interview.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <div class="sm:col-span-6 relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama calon medis (IC), CID, atau Discord..."
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-9 pr-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="sm:col-span-4">
                    <select name="status" onchange="this.form.submit()"
                            class="w-full bg-slate-800 text-white border border-white/20 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer">
                        <option value="">Semua Status Berkas</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Lolos Berkas</option>
                        <option value="interview" {{ request('status') === 'interview' ? 'selected' : '' }}>Tahap Wawancara</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all whitespace-nowrap text-center">
                        Filter Calon
                    </button>
                    @if(request('q') || request('status'))
                        <a href="{{ route('portal.interview.index') }}" class="px-3 py-2.5 bg-white/10 hover:bg-white/15 text-white/70 hover:text-white rounded-xl text-xs flex items-center justify-center transition-colors" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Antrean Calon Medis Dari Hasil Recruitment --}}
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <div class="px-5 py-4 border-b border-white/10 bg-white/5 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white text-sm">Antrean Calon Medis Baru (Hasil Recruitment)</h3>
                    <p class="text-xs text-slate-400">Daftar calon staf yang mendaftar via formulir recruitment Alta Hospital</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30">
                    <i class="fas fa-database mr-1"></i> Data Recruitment Form
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3.5 text-left">Nama Pendaftar (IC)</th>
                            <th class="px-5 py-3.5 text-left">Citizen ID (CID)</th>
                            <th class="px-5 py-3.5 text-left">Discord</th>
                            <th class="px-5 py-3.5 text-left">Status Berkas</th>
                            <th class="px-5 py-3.5 text-left">Tanggal Mendaftar</th>
                            <th class="px-5 py-3.5 text-center">Status Wawancara</th>
                            <th class="px-5 py-3.5 text-center">Aksi Interview</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        @forelse($candidates as $candidate)
                        @php
                            $latestInterview = $candidate->latestInterview ?? $candidate->candidateInterviews->last();
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500/30 to-purple-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($candidate->ic_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $candidate->ic_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $candidate->period?->batch_name ?? 'Batch Recruitment' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-emerald-300 font-semibold">
                                {{ $candidate->cid }}
                            </td>
                            <td class="px-5 py-3.5 text-indigo-300 text-xs font-mono">
                                {{ $candidate->discord_username ? '@' . $candidate->discord_username : '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                {!! $candidate->status_badge !!}
                            </td>
                            <td class="px-5 py-3.5 text-slate-300 text-xs">
                                {{ $candidate->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($latestInterview)
                                    @if($latestInterview->result === 'recommended')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <i class="fas fa-check-circle text-[10px]"></i> Recommended ({{ $latestInterview->recommended_role_label }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                            <i class="fas fa-times-circle text-[10px]"></i> Not Recommended
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <i class="fas fa-clock text-[10px]"></i> Belum Di-interview
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('portal.interview.form', $candidate->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all">
                                    <i class="fas fa-clipboard-check"></i> Form Evaluasi Interview
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 text-2xl">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h4 class="text-white font-semibold text-sm mb-1">Belum Ada Calon Pendaftar Recruitment</h4>
                                <p class="text-xs text-slate-400 max-w-md mx-auto">
                                    Data pada halaman ini bersumber langsung dari formulir pendaftaran recruitment Alta Hospital. Saat ada calon medis yang mendaftar via web recruitment, berkas mereka akan otomatis masuk ke dalam antrean ini.
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($candidates->hasPages())
            <div class="px-5 py-3 border-t border-white/10">
                {{ $candidates->links() }}
            </div>
            @endif
        </div>

        {{-- Tabel Riwayat Evaluasi Interview Terakhir --}}
        @if($completedInterviews->isNotEmpty())
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl mt-8">
            <div class="px-5 py-4 border-b border-white/10 bg-white/5">
                <h3 class="font-bold text-white text-sm">Riwayat Hasil Interview Terakhir</h3>
                <p class="text-xs text-slate-400">Catatan penilaian dan rekomendasi yang telah disimpan oleh tim interviewer</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-400 font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3 text-left">Nama Calon</th>
                            <th class="px-5 py-3 text-left">Interviewer</th>
                            <th class="px-5 py-3 text-center">Hasil Keputusan</th>
                            <th class="px-5 py-3 text-left">Rekomendasi Jabatan</th>
                            <th class="px-5 py-3 text-left">Catatan Interview</th>
                            <th class="px-5 py-3 text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-300">
                        @foreach($completedInterviews as $ci)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3 font-semibold text-white">
                                {{ $ci->application?->ic_name ?? $ci->candidate?->name ?? '-' }}
                                <span class="block text-[11px] text-slate-400 font-mono">CID: {{ $ci->application?->cid ?? $ci->candidate?->citizen_id ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3 text-indigo-300 font-medium">
                                {{ $ci->interviewer?->name ?? 'Interviewer' }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($ci->result === 'recommended')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        Recommended
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        Not Recommended
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-semibold text-sky-300">
                                {{ $ci->recommended_role_label }}
                            </td>
                            <td class="px-5 py-3 max-w-xs truncate" title="{{ $ci->notes }}">
                                {{ Str::limit($ci->notes, 60) }}
                            </td>
                            <td class="px-5 py-3 text-right text-slate-400">
                                {{ $ci->interviewed_at?->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($completedInterviews->hasPages())
            <div class="px-5 py-3 border-t border-white/10">
                {{ $completedInterviews->links() }}
            </div>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection
