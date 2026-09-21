@extends('layouts.app')
@section('title', 'Kelola Stase — Divisi MSL')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-book-medical text-teal-400"></i> MSL — Kelola Pengajuan Stase</h1>
            <p class="text-white/50 text-sm mt-0.5">Setujui atau input kelulusan stase anggota yang diajukan konsulen</p>
        </div>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    <form method="GET" class="mb-4 flex gap-2">
        <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="pending_msl" {{ request('status') === 'pending_msl' ? 'selected' : '' }}>Menunggu MSL</option>
            <option value="approved_konsulen" {{ request('status') === 'approved_konsulen' ? 'selected' : '' }}>Disetujui Konsulen</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 rounded-xl border border-teal-500/30 text-sm">Filter</button>
    </form>
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-graduation-cap text-4xl mb-3"></i><p class="text-sm">Tidak ada pengajuan stase untuk ditinjau.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Nama Stase</th><th class="text-left px-5 py-3">Konsulen</th><th class="text-left px-5 py-3">Status</th><th class="px-5 py-3 text-center">Aksi MSL</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $app->user?->name }}</div><div class="text-white/40 text-xs">{{ $app->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5 text-white/80">{{ $app->stase_name }}</td>
                        <td class="px-5 py-3.5 text-white/60">{{ $app->konsulen?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                @if(in_array($app->status, ['pending_konsulen'])) bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                                @elseif(in_array($app->status, ['pending_msl','approved_konsulen'])) bg-sky-500/20 text-sky-300 border border-sky-500/30
                                @elseif($app->status === 'approved') bg-blue-500/20 text-blue-300 border border-blue-500/30
                                @elseif($app->status === 'completed') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @else bg-red-500/20 text-red-300 border border-red-500/30 @endif">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex gap-2 justify-center">
                                @if(in_array($app->status, ['pending_msl','approved_konsulen']))
                                <form method="POST" action="{{ route('portal.msl.stase.approve', $app) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 text-xs font-semibold rounded-lg border border-blue-500/30 transition-all">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                </form>
                                @endif
                                @if($app->status === 'approved')
                                <form method="POST" action="{{ route('portal.msl.stase.complete', $app) }}" class="flex gap-2 items-center">
                                    @csrf
                                    <select name="passed" required class="px-2 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-xs focus:outline-none">
                                        <option value="1">LULUS ✅</option>
                                        <option value="0">TIDAK LULUS ❌</option>
                                    </select>
                                    <input type="text" name="grade" placeholder="Grade" maxlength="5" class="w-16 px-2 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-xs focus:outline-none">
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition-all">
                                        <i class="fas fa-flag-checkered"></i> Selesaikan
                                    </button>
                                </form>
                                @endif
                            </div>
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
