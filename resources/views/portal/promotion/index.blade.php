@extends('layouts.app')
@section('title', 'Kenaikan Jabatan — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-4xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-level-up-alt text-violet-400"></i> Kenaikan Jabatan</h1>
            <p class="text-white/50 text-sm mt-0.5">Status pengajuan promosi Anda</p>
        </div>
        @if($period && (!$latestApp || $latestApp->status !== 'pending'))
        <a href="{{ route('portal.promotion.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white text-sm font-semibold rounded-xl shadow-lg transition-all">
            <i class="fas fa-plus"></i> Ajukan Kenaikan
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(session('info'))
    <div class="mb-4 p-4 bg-sky-500/20 border border-sky-500/40 rounded-xl text-sky-300 text-sm flex items-center gap-2"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
    @endif

    {{-- Status Periode --}}
    <div class="mb-6 p-5 rounded-2xl border {{ $period ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-white/5 border-white/10' }}">
        @if($period)
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center"><i class="fas fa-door-open text-emerald-400"></i></div>
            <div>
                <p class="text-emerald-300 font-semibold">Periode Kenaikan Jabatan Sedang Dibuka</p>
                <p class="text-white/60 text-sm">{{ $period->name }} @if($period->batch)({{ $period->batch }})@endif</p>
            </div>
        </div>
        @else
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center"><i class="fas fa-door-closed text-white/50"></i></div>
            <div>
                <p class="text-white/70 font-semibold">Periode Kenaikan Jabatan Sedang Ditutup</p>
                <p class="text-white/40 text-sm">Pantau pengumuman dari divisi PND untuk periode selanjutnya.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Jabatan Saat Ini --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-3">Jabatan Saat Ini</h3>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/30 flex items-center justify-center">
                <i class="fas fa-user-md text-violet-400 text-xl"></i>
            </div>
            <div>
                <p class="text-white text-lg font-bold">{{ $user->role?->display_name ?? 'Belum ada jabatan' }}</p>
                <p class="text-white/50 text-sm">{{ $user->staff_id }} — Level {{ $user->role?->level ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Pengajuan Terakhir --}}
    @if($latestApp)
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6">
        <h3 class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-4">Pengajuan Terakhir</h3>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-white font-semibold">{{ $latestApp->currentRole?->display_name }} → {{ $latestApp->targetRole?->display_name }}</p>
                <p class="text-white/50 text-sm">{{ $latestApp->period?->name }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $latestApp->status_color === 'green' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($latestApp->status_color === 'red' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30') }}">
                {{ $latestApp->status_label }}
            </span>
        </div>

        {{-- Checklist snapshot --}}
        @if($latestApp->requirements_checklist)
        <div class="space-y-2">
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Checklist Persyaratan saat Submit</p>
            @foreach($latestApp->requirements_checklist as $item)
            <div class="flex items-center gap-3 text-sm {{ $item['met'] ? 'text-emerald-300' : 'text-red-300' }}">
                <i class="fas {{ $item['met'] ? 'fa-check-circle' : 'fa-times-circle' }} text-sm shrink-0"></i>
                <span>{{ $item['label'] }}</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Berkas yang diunggah --}}
        @if($latestApp->case_study_file || $latestApp->recommendation_letter_1 || $latestApp->recommendation_letter_2 || $latestApp->supporting_document)
        <div class="mt-4 pt-3 border-t border-white/10">
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold mb-2">Berkas yang Anda Unggah</p>
            <div class="flex flex-wrap gap-2">
                @if($latestApp->case_study_file)
                    @php
                        $targetName = strtolower(($latestApp->targetRole?->name ?? '') . ' ' . ($latestApp->targetRole?->display_name ?? ''));
                        $isDokspel = str_contains($targetName, 'spesialis');
                    @endphp
                    <a href="{{ asset('storage/' . $latestApp->case_study_file) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $isDokspel ? 'bg-violet-500/20 text-violet-300 border-violet-500/30' : 'bg-sky-500/20 text-sky-300 border-sky-500/30' }} border rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas {{ $isDokspel ? 'fa-file-pdf text-rose-400' : 'fa-file-alt text-sky-400' }}"></i>
                        <span>{{ $isDokspel ? 'Studi Kasus Dokspel' : 'Berkas Laporan Kenaikan' }}</span>
                    </a>
                @endif
                @if($latestApp->recommendation_letter_1)
                    <a href="{{ asset('storage/' . $latestApp->recommendation_letter_1) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas fa-file-alt text-sky-400"></i>
                        <span>Rekomendasi Konsulen 1</span>
                    </a>
                @endif
                @if($latestApp->recommendation_letter_2)
                    <a href="{{ asset('storage/' . $latestApp->recommendation_letter_2) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas fa-file-alt text-sky-400"></i>
                        <span>Rekomendasi Konsulen 2</span>
                    </a>
                @endif
                @if($latestApp->supporting_document)
                    <a href="{{ asset('storage/' . $latestApp->supporting_document) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-xl text-xs font-medium hover:brightness-125 transition-all">
                        <i class="fas fa-folder-open text-amber-400"></i>
                        <span>Berkas Pendukung</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        @if($latestApp->pnd_notes)
        <div class="mt-4 p-3 bg-violet-500/10 rounded-xl border border-violet-500/20 text-xs text-violet-200">
            <span class="font-semibold">Catatan PND:</span> {{ $latestApp->pnd_notes }}
        </div>
        @endif
    </div>
    @endif
</div>
</div>
@endsection
