@extends('layouts.app')
@section('title', 'Review Pengajuan Kenaikan Jabatan — Divisi PND')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-clipboard-list text-violet-400"></i> PND — Review Pengajuan Kenaikan Jabatan</h1>
            <p class="text-white/50 text-sm mt-0.5">Tinjau dan putuskan pengajuan kenaikan jabatan anggota</p>
        </div>
        <a href="{{ route('portal.promotion.period.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-500/20 hover:bg-violet-500/30 text-violet-300 text-sm font-medium rounded-xl border border-violet-500/30 transition-all">
            <i class="fas fa-toggle-on"></i> Kelola Periode
        </a>
    </div>
    @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4 flex gap-2 flex-wrap">
        <select name="period_id" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
            <option value="">Semua Periode</option>
            @foreach($periods as $p)<option value="{{ $p->id }}" {{ request('period_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} {{ $p->batch ? '('.$p->batch.')' : '' }}</option>@endforeach
        </select>
        <select name="status" class="px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-violet-500/20 hover:bg-violet-500/30 text-violet-300 rounded-xl border border-violet-500/30 text-sm">Filter</button>
    </form>

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-white/40"><i class="fas fa-inbox text-4xl mb-3"></i><p class="text-sm">Tidak ada pengajuan kenaikan jabatan.</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-white/10 text-xs text-white/50 uppercase tracking-wider">
                    <tr><th class="text-left px-5 py-3">Anggota</th><th class="text-left px-5 py-3">Dari → Ke</th><th class="text-left px-5 py-3">Periode</th><th class="text-left px-5 py-3">Credit Score</th><th class="text-left px-5 py-3">Status</th><th class="px-5 py-3 text-center">Aksi PND</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($applications as $app)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5"><div class="text-white font-medium">{{ $app->user?->name }}</div><div class="text-white/40 text-xs">{{ $app->user?->staff_id }}</div></td>
                        <td class="px-5 py-3.5">
                            <span class="text-white/60">{{ $app->currentRole?->display_name ?? '—' }}</span>
                            <i class="fas fa-arrow-right text-violet-400 mx-1 text-xs"></i>
                            <span class="text-violet-300 font-semibold">{{ $app->targetRole?->display_name ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-white/60 text-xs">{{ $app->period?->name }}</td>
                        <td class="px-5 py-3.5 text-white/80 font-semibold">{{ $app->credit_score_at_submission }} poin</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $app->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($app->status === 'rejected' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30') }}">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($app->status === 'pending')
                            <div class="flex flex-col gap-2">
                                {{-- Checklist preview --}}
                                @if($app->requirements_checklist)
                                <details class="text-xs text-white/50 cursor-pointer mb-1">
                                    <summary class="text-violet-300 hover:text-violet-200">Lihat checklist ({{ collect($app->requirements_checklist)->where('met',true)->count() }}/{{ count($app->requirements_checklist) }} terpenuhi)</summary>
                                    <div class="mt-1 space-y-1 pl-2">
                                        @foreach($app->requirements_checklist as $item)
                                        <div class="{{ $item['met'] ? 'text-emerald-300' : 'text-red-300' }}">
                                            {{ $item['met'] ? '✅' : '❌' }} {{ $item['label'] }}
                                        </div>
                                        @endforeach
                                    </div>
                                </details>
                                @endif
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('portal.promotion.applications.review', $app) }}" class="flex gap-1 items-center">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <input type="text" name="pnd_notes" placeholder="Catatan (opsional)" class="px-2 py-1 bg-white/10 border border-white/20 rounded-lg text-white text-xs focus:outline-none w-24">
                                        <button type="submit" onclick="return confirm('Setujui kenaikan jabatan {{ addslashes($app->user?->name) }} ke {{ addslashes($app->targetRole?->display_name) }}? Jabatan akan otomatis diperbarui!')"
                                                class="px-2 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-500/30 transition-all">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('portal.promotion.applications.review', $app) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button"
                                                onclick="var n=prompt('Alasan penolakan:'); if(n!==null){this.form.querySelector('[name=pnd_notes]').value=n;this.form.submit()}"
                                                class="px-2 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-semibold rounded-lg border border-red-500/30 transition-all">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        <input type="hidden" name="pnd_notes" value="">
                                    </form>
                                </div>
                            </div>
                            @else
                            <div class="text-center text-white/30 text-xs">
                                @if($app->pnd_notes)<span class="text-white/50">{{ Str::limit($app->pnd_notes, 40) }}</span>@else —@endif
                            </div>
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
