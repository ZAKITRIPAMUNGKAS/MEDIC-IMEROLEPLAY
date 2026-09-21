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
                        <i class="fas fa-id-badge mr-1"></i> Role Interviewer Alta Hospital
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 text-lg">
                        <i class="fas fa-comments"></i>
                    </span>
                    Wawancara Calon Staf Medis
                </h1>
                <p class="text-slate-300 text-sm mt-1">
                    Kelola antrean interview pendaftar baru, input hasil evaluasi wawancara, dan tetapkan rekomendasi jenjang jabatan medis awal.
                </p>
            </div>

            <div class="text-right">
                <span class="text-xs text-indigo-300">Antrean Siap Interview:</span>
                <span class="text-xl font-bold text-white ml-1.5">{{ $candidates->total() }}</span>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/40 rounded-xl px-4 py-3 text-emerald-300 text-sm flex items-center gap-2 shadow-lg">
                <i class="fas fa-check-circle text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Search Filter --}}
        <div class="glass-effect rounded-2xl p-4 border border-white/10">
            <form method="GET" action="{{ route('portal.interview.index') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama calon medis, email, atau Citizen ID (CID)..."
                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-9 pr-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all">
                    Cari Calon
                </button>
                @if(request('q'))
                    <a href="{{ route('portal.interview.index') }}" class="px-3 py-2.5 bg-white/10 hover:bg-white/15 text-white/70 rounded-xl text-xs flex items-center">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Antrean Calon Medis --}}
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <div class="px-5 py-4 border-b border-white/10 bg-white/5 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white text-sm">Antrean Calon Medis Baru</h3>
                    <p class="text-xs text-slate-400">Daftar pendaftar akun yang menunggu sesi wawancara</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3.5 text-left">Nama Pendaftar</th>
                            <th class="px-5 py-3.5 text-left">Citizen ID (CID)</th>
                            <th class="px-5 py-3.5 text-left">Pilihan Role Awal</th>
                            <th class="px-5 py-3.5 text-left">Tanggal Daftar</th>
                            <th class="px-5 py-3.5 text-center">Status Interview</th>
                            <th class="px-5 py-3.5 text-center">Aksi Interview</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        @forelse($candidates as $candidate)
                        @php
                            $latestInterview = $candidate->candidateInterviews->last();
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500/30 to-purple-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($candidate->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $candidate->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $candidate->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-emerald-300 font-semibold">
                                {{ $candidate->citizen_id ?? 'CID Belum Ada' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $candidate->role?->display_name ?? 'Pendaftar' }}
                                </span>
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
                                <a href="{{ route('portal.interview.form', $candidate) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl text-xs font-semibold shadow-md transition-all">
                                    <i class="fas fa-clipboard-check"></i> Form Evaluasi Interview
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 text-slate-500 block"></i>
                                Tidak ada pendaftar baru yang menunggu interview saat ini.
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
                                {{ $ci->candidate?->name ?? '-' }}
                                <span class="block text-[11px] text-slate-400 font-mono">CID: {{ $ci->candidate?->citizen_id ?? '-' }}</span>
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
