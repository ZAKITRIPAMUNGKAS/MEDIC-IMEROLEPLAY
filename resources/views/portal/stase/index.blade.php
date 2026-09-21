@extends('layouts.app')
@section('title', 'Pengajuan Stase Saya — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-5xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-graduation-cap text-blue-400"></i> Pengajuan Stase</h1>
            <p class="text-white/50 text-sm mt-0.5">Ajukan permohonan program stase ke konsulen</p>
        </div>
        <a href="{{ route('portal.stase.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg transition-all duration-200">
            <i class="fas fa-plus"></i> Ajukan Stase Baru
        </a>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- Cek apakah user adalah konsulen --}}
    @if($user->role?->level >= 4)
    <div class="mb-4">
        <a href="{{ route('portal.stase.konsulen.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 text-white text-sm rounded-xl border border-white/20 transition-all">
            <i class="fas fa-user-check text-blue-400"></i> Lihat Pengajuan Stase yang Ditujukan ke Saya (Konsulen)
        </a>
    </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40">
            <i class="fas fa-graduation-cap text-4xl mb-3"></i>
            <p class="text-sm">Belum ada pengajuan stase.</p>
            <a href="{{ route('portal.stase.create') }}" class="mt-4 text-blue-400 hover:text-blue-300 text-sm underline">Ajukan sekarang</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Nama Stase</th><th class="text-left px-5 py-3">Konsulen</th><th class="text-left px-5 py-3">Periode</th><th class="text-left px-5 py-3">Status</th><th class="text-left px-5 py-3">Hasil</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5 text-white font-medium">{{ $app->stase_name }}</td>
                        <td class="px-5 py-3.5 text-white/70">{{ $app->konsulen?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">
                            @if($app->start_date) {{ $app->start_date->format('d M Y') }} — {{ $app->end_date?->format('d M Y') ?? 'ongoing' }} @else — @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if(str_contains($app->status,'pending')) bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                @elseif($app->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @elseif($app->status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/30
                                @else bg-blue-500/20 text-blue-300 border border-blue-500/30 @endif">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'completed')
                            <span class="{{ $app->passed ? 'text-emerald-300' : 'text-red-300' }} font-semibold text-xs">
                                {{ $app->passed ? '✅ LULUS' : '❌ TIDAK LULUS' }}
                                @if($app->grade) ({{ $app->grade }}) @endif
                            </span>
                            @else — @endif
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
