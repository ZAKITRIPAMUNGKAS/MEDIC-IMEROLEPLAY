@extends('layouts.app')

@section('title', 'Daftar Cuti Medis Alta Hospital')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-rose-950/30 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-6xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('portal.leave.index') }}" class="text-xs text-rose-300 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Pengajuan Cuti Saya
                    </a>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-rose-400 text-lg">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    Jadwal Cuti Anggota Medis
                </h1>
                <p class="text-slate-300 text-sm mt-1">Daftar anggota medis Alta Hospital yang sedang atau akan menjalani cuti dinas.</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('portal.leave.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-400 hover:to-pink-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-rose-900/30 transition-all">
                    <i class="fas fa-plus"></i> Ajukan Cuti Baru
                </a>
            </div>
        </div>

        {{-- Sorting & Filter Controls --}}
        <div class="glass-effect rounded-2xl p-4 border border-white/10 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-300 font-semibold flex items-center gap-1.5">
                    <i class="fas fa-sort text-rose-400"></i> Urutkan Berdasarkan:
                </span>
                
                {{-- Sort Tanggal Mulai --}}
                <a href="{{ route('portal.leave.public-list', ['sort_by' => 'start_date', 'sort_dir' => ($sortBy === 'start_date' && $sortDir === 'asc') ? 'desc' : 'asc']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all flex items-center gap-1.5 {{ $sortBy === 'start_date' ? 'bg-rose-500/30 text-rose-200 border-rose-400' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' }}">
                    <span>Tanggal Mulai Cuti</span>
                    @if($sortBy === 'start_date')
                        <i class="fas fa-sort-amount-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                    @else
                        <i class="fas fa-sort text-slate-500 text-[10px]"></i>
                    @endif
                </a>

                {{-- Sort Tanggal Selesai --}}
                <a href="{{ route('portal.leave.public-list', ['sort_by' => 'end_date', 'sort_dir' => ($sortBy === 'end_date' && $sortDir === 'asc') ? 'desc' : 'asc']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all flex items-center gap-1.5 {{ $sortBy === 'end_date' ? 'bg-rose-500/30 text-rose-200 border-rose-400' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' }}">
                    <span>Tanggal Selesai Cuti</span>
                    @if($sortBy === 'end_date')
                        <i class="fas fa-sort-amount-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                    @else
                        <i class="fas fa-sort text-slate-500 text-[10px]"></i>
                    @endif
                </a>
            </div>

            <div class="text-xs text-slate-400">
                Total Medis Cuti Terdata: <span class="text-white font-bold">{{ $leaves->total() }}</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="glass-effect rounded-2xl overflow-hidden border border-white/10 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-5 py-3.5 text-left">Nama Medis</th>
                            <th class="px-5 py-3.5 text-left">Jabatan</th>
                            <th class="px-5 py-3.5 text-left">
                                <a href="{{ route('portal.leave.public-list', ['sort_by' => 'start_date', 'sort_dir' => ($sortBy === 'start_date' && $sortDir === 'asc') ? 'desc' : 'asc']) }}"
                                   class="hover:text-rose-300 flex items-center gap-1">
                                    Mulai Cuti
                                    @if($sortBy === 'start_date') <i class="fas fa-chevron-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-[10px] text-rose-400"></i> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-left">
                                <a href="{{ route('portal.leave.public-list', ['sort_by' => 'end_date', 'sort_dir' => ($sortBy === 'end_date' && $sortDir === 'asc') ? 'desc' : 'asc']) }}"
                                   class="hover:text-rose-300 flex items-center gap-1">
                                    Selesai Cuti
                                    @if($sortBy === 'end_date') <i class="fas fa-chevron-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-[10px] text-rose-400"></i> @endif
                                </a>
                            </th>
                            <th class="px-5 py-3.5 text-center">Durasi</th>
                            <th class="px-5 py-3.5 text-center">Status Dinas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        @forelse($leaves as $leave)
                        @php
                            $today = \Carbon\Carbon::today();
                            $startDate = \Carbon\Carbon::parse($leave->start_date);
                            $endDate = \Carbon\Carbon::parse($leave->end_date);
                            $isOngoing = $today->between($startDate, $endDate);
                            $isUpcoming = $today->lt($startDate);
                            $isEnded = $today->gt($endDate);
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-500/30 to-purple-500/30 border border-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($leave->user?->name ?? $leave->applicant_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">{{ $leave->user?->name ?? $leave->applicant_name }}</p>
                                        <p class="text-xs text-rose-300 font-mono">{{ $leave->user?->staff_id ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                    {{ $leave->user?->role?->display_name ?? $leave->position }}
                                </span>
                                @if($leave->user?->medicRole && $leave->user->medic_role_id !== $leave->user->role_id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 ml-1">
                                        🩺 {{ $leave->user->medicRole->display_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-white/90 font-medium">
                                <i class="far fa-calendar-alt text-rose-400 mr-1.5"></i>
                                {{ $startDate->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-white/90 font-medium">
                                <i class="far fa-calendar-check text-emerald-400 mr-1.5"></i>
                                {{ $endDate->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-white/10 text-white border border-white/15">
                                    {{ $leave->duration_days }} Hari
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($isOngoing)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse">
                                        <i class="fas fa-plane-departure text-[10px]"></i> Sedang Cuti
                                    </span>
                                @elseif($isUpcoming)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        <i class="fas fa-clock text-[10px]"></i> Akan Cuti
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-500/20 text-gray-300 border border-gray-500/30">
                                        <i class="fas fa-check text-[10px]"></i> Selesai
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-calendar-times text-3xl mb-2 text-slate-500 block"></i>
                                Tidak ada anggota medis yang sedang atau akan cuti pada periode saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaves->hasPages())
            <div class="px-5 py-3 border-t border-white/10">
                {{ $leaves->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
