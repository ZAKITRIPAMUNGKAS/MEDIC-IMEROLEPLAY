@extends('layouts.app')
@section('title', 'Setujui Stase — Panel Konsulen')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-5xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('portal.stase.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-user-check text-blue-400"></i> Panel Konsulen — Pengajuan Stase</h1>
        <p class="text-white/50 text-sm mt-0.5">Pengajuan stase yang ditujukan kepada Anda sebagai konsulen pembimbing</p>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-inbox text-4xl mb-3"></i><p class="text-sm">Tidak ada pengajuan stase untuk Anda.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Nama Stase</th><th class="text-left px-5 py-3">Catatan</th><th class="text-left px-5 py-3">Status</th><th class="px-5 py-3 text-center">Aksi Konsulen</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $app->user?->name }}</div><div class="text-white/40 text-xs">{{ $app->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5 text-white/80">{{ $app->stase_name }}<div class="text-white/40 text-xs">{{ $app->department }}</div></td>
                        <td class="px-5 py-3.5 text-white/60 text-xs max-w-[200px] truncate">{{ $app->notes ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $app->status === 'pending_konsulen' ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' : ($app->status === 'rejected' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30') }}">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'pending_konsulen')
                            <div class="flex gap-2 justify-center">
                                <form method="POST" action="{{ route('portal.stase.konsulen.approve', $app) }}" class="flex gap-1 items-center">
                                    @csrf
                                    <input type="text" name="konsulen_notes" placeholder="Catatan (opsional)" class="px-2 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-xs focus:outline-none w-28">
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition-all">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('portal.stase.konsulen.reject', $app) }}">
                                    @csrf
                                    <input type="hidden" name="konsulen_notes" value="Ditolak oleh konsulen.">
                                    <button type="submit" onclick="return confirm('Tolak pengajuan stase ini?')" class="px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold rounded-lg border border-red-500/30 transition-all">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="text-white/30 text-center text-xs">—</div>
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
