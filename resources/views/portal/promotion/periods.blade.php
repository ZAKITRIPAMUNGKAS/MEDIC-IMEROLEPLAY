@extends('layouts.app')
@section('title', 'Kelola Periode Kenaikan Jabatan — Divisi PND')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-5xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-toggle-on text-violet-400"></i> PND — Kelola Periode Kenaikan Jabatan</h1>
            <p class="text-white/50 text-sm mt-0.5">Buka dan tutup periode kenaikan jabatan untuk Alta Hospital</p>
        </div>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- Status Periode Aktif --}}
    <div class="mb-6 p-5 rounded-2xl border {{ $currentPeriod ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-red-500/10 border-red-500/20' }}">
        @if($currentPeriod)
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center"><i class="fas fa-door-open text-emerald-400"></i></div>
                <div>
                    <p class="text-emerald-300 font-semibold">Periode Aktif: {{ $currentPeriod->name }}</p>
                    <p class="text-white/50 text-sm">Dibuka sejak {{ $currentPeriod->start_date?->format('d M Y') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('portal.promotion.period.close', $currentPeriod) }}">
                @csrf
                <button type="submit" onclick="return confirm('Tutup periode kenaikan jabatan {{ addslashes($currentPeriod->name) }}?')"
                        class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-sm font-semibold rounded-xl border border-red-500/30 transition-all">
                    <i class="fas fa-door-closed mr-1.5"></i> Tutup Periode
                </button>
            </form>
        </div>
        @else
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center"><i class="fas fa-door-closed text-red-400"></i></div>
            <p class="text-red-300 font-semibold">Tidak ada periode kenaikan jabatan yang aktif saat ini.</p>
        </div>
        @endif
    </div>

    {{-- Form Buka Periode Baru --}}
    @if(!$currentPeriod)
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6">
        <h3 class="text-white font-semibold mb-4 flex items-center gap-2"><i class="fas fa-plus-circle text-violet-400"></i> Buka Periode Baru</h3>
        <form method="POST" action="{{ route('portal.promotion.period.open') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs text-white/50 mb-1">Nama Periode *</label>
                <input type="text" name="name" required maxlength="255" placeholder="Contoh: Periode Kenaikan Jabatan Oktober 2026"
                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-400">
            </div>
            <div>
                <label class="block text-xs text-white/50 mb-1">Batch</label>
                <input type="text" name="batch" maxlength="100" placeholder="Batch X"
                       class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-violet-400">
            </div>
            <div class="sm:col-span-3">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl text-sm hover:from-violet-400 hover:to-purple-500 transition-all">
                    <i class="fas fa-door-open mr-1.5"></i> Buka Periode Kenaikan Jabatan
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Riwayat Periode --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-white/10"><h3 class="text-white/70 text-sm font-semibold">Riwayat Periode</h3></div>
        @if($periods->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-white/40"><p class="text-sm">Belum ada periode yang pernah dibuat.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase"><tr><th class="text-left px-5 py-3">Nama Periode</th><th class="text-left px-5 py-3">Batch</th><th class="text-left px-5 py-3">Dibuka</th><th class="text-left px-5 py-3">Ditutup</th><th class="text-left px-5 py-3">Status</th></tr></thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($periods as $p)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5 text-white font-medium">{{ $p->name }}</td>
                        <td class="px-5 py-3.5 text-white/70">{{ $p->batch ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $p->start_date?->format('d M Y') }}<div class="text-white/30">oleh {{ $p->openedBy?->name }}</div></td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $p->end_date?->format('d M Y') ?? '—' }}<div class="text-white/30">{{ $p->closedBy?->name ?? '' }}</div></td>
                        <td class="px-5 py-3.5"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->is_open ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-gray-500/20 text-gray-400 border border-gray-500/30' }}">{{ $p->is_open ? '🔓 Aktif' : '🔒 Tutup' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-white/10">{{ $periods->links() }}</div>
        @endif
    </div>
</div>
</div>
@endsection
