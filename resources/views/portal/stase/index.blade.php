@extends('layouts.app')
@section('title', 'Pengajuan Stase Saya — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);">
<div class="max-w-5xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-graduation-cap text-blue-400"></i> Pengajuan Stase Kedokteran
            </h1>
            <p class="text-white/60 text-sm mt-0.5">Pantau status bimbingan stase, laporan konsulen, dan sertifikat kelulusan Anda.</p>
        </div>
        <a href="{{ route('portal.stase.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg transition-all duration-200">
            <i class="fas fa-plus"></i> Ajukan Stase Baru
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Akses Cepat untuk Konsulen Terdaftar --}}
    @if($user->isKonsulen() || $user->role?->level >= 4)
    <div class="mb-4">
        <a href="{{ route('portal.stase.konsulen.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/20 transition-all shadow-md">
            <i class="fas fa-user-check text-blue-300"></i> Buka Panel Konsulen (Input Laporan / Evaluasi Stase Bimbingan)
        </a>
    </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40">
            <i class="fas fa-graduation-cap text-4xl mb-3"></i>
            <p class="text-sm">Belum ada pengajuan stase.</p>
            <a href="{{ route('portal.stase.create') }}" class="mt-4 px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl border border-blue-500/30 text-sm">
                Ajukan Stase Sekarang
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3">Nama Bidang Stase</th>
                        <th class="text-left px-5 py-3">Konsulen Pembimbing</th>
                        <th class="text-left px-5 py-3">Tgl Diajukan</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Laporan &amp; Sertifikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="text-white font-medium">{{ $app->stase_name }}</div>
                            @if($app->notes)
                                <div class="text-white/40 text-xs italic line-clamp-1">"{{ $app->notes }}"</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-white/80">
                            <div class="font-medium flex items-center gap-1.5">
                                <i class="fas fa-user-md text-blue-400 text-xs"></i>
                                <span>{{ $app->konsulen?->name ?? '—' }}</span>
                            </div>
                            <div class="text-white/40 text-xs">
                                <span>{{ $app->konsulen?->staff_id ?? '-' }}</span>
                                @if($app->konsulen?->doctor_title)
                                    <span class="text-white/20">&bull;</span>
                                    <span class="text-blue-300/80">{{ $app->konsulen->doctor_title }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">
                            {{ $app->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($app->status === 'pending_konsulen') bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                @elseif($app->status === 'approved_konsulen' || $app->status === 'pending_msl') bg-sky-500/20 text-sky-300 border border-sky-500/30
                                @elseif($app->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @elseif($app->status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/30
                                @else bg-blue-500/20 text-blue-300 border border-blue-500/30 @endif">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'completed')
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-300 font-semibold text-xs flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> LULUS ({{ $app->grade ?? 'A' }})
                                    </span>
                                    @if($app->certification_id)
                                    <a href="{{ route('portal.cert.image', $app->certification_id) }}" target="_blank"
                                       class="px-2 py-0.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-lg text-xs font-medium flex items-center gap-1">
                                        <i class="fas fa-award text-[10px]"></i> Sertifikat
                                    </a>
                                    @endif
                                </div>
                            @elseif($app->status === 'approved_konsulen' || $app->status === 'pending_msl')
                                <div class="text-xs text-sky-300">
                                    <i class="fas fa-file-medical-alt mr-1"></i> Laporan Konsulen Masuk
                                </div>
                                <div class="text-[11px] text-white/40 italic">Menunggu verifikasi MSL</div>
                            @elseif($app->status === 'rejected')
                                <span class="text-xs text-rose-300 italic">Ditolak: {{ $app->konsulen_notes ?? $app->msl_notes ?? 'Tidak memenuhi syarat' }}</span>
                            @else
                                <span class="text-xs text-white/40 italic">Menunggu bimbingan konsulen</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/10">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
</div>
@endsection
