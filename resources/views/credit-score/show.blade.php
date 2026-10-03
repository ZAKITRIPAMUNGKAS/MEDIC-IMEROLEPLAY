@extends('layouts.app')
@section('title', 'Detail Credit Score — ' . $user->name)
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-5xl mx-auto px-4 sm:px-6">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('credit-score.index') }}" class="inline-flex items-center gap-2 text-sm text-sky-400 hover:text-sky-300">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        @if(auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove())
        <div class="flex items-center gap-2">
            <a href="{{ route('credit-score.input', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-amber-500/20">
                <i class="fas fa-plus-minus"></i> Input Poin Comdis
            </a>
            <a href="{{ route('credit-score.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-purple-500/20">
                <i class="fas fa-edit"></i> Edit Skor Langsung
            </a>
        </div>
        @endif
    </div>

    {{-- Member Score Card --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl mb-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-sky-500/30">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-sky-300 font-mono">ID: {{ $user->staff_id ?? '-' }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-white/10 text-white">{{ $user->role?->display_name ?? $user->role?->name }}</span>
                        @if($user->subRole)
                        <span class="text-xs px-2 py-0.5 rounded-full" style="{{ $user->subRole->badge_style }}">{{ $user->subRole->short_name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-center sm:text-right">
                <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Skor Saat Ini</div>
                <div class="text-4xl font-extrabold @if($creditScore->score >= 80) text-emerald-400 @elseif($creditScore->score >= 50) text-amber-400 @else text-rose-400 @endif">
                    {{ $creditScore->score }}
                </div>
                <div class="mt-1">
                    @if($creditScore->score >= 80)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-medium">Sangat Baik</span>
                    @elseif($creditScore->score >= 50)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-medium">Peringatan</span>
                    @else
                    <span class="text-xs px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-medium">Kritis (Sanksi)</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-history text-sky-400"></i> Riwayat Transaksi Credit Score
            </h2>
            <span class="text-xs text-gray-400">Total: {{ $logs->total() }} catatan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-white/5 text-gray-300 border-b border-white/10">
                    <tr>
                        <th class="px-5 py-3">Tanggal & Waktu</th>
                        <th class="px-5 py-3">Perubahan</th>
                        <th class="px-5 py-3">Alasan / Catatan</th>
                        <th class="px-5 py-3">Petugas Comdis</th>
                        @if(auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove())
                        <th class="px-5 py-3 text-center">Aksi Comdis</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3.5 text-xs text-gray-300 font-mono">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($log->type === 'add')
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400 px-2 py-0.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                <i class="fas fa-plus"></i> +{{ $log->amount }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-400 px-2 py-0.5 rounded-lg bg-rose-500/10 border border-rose-500/20">
                                <i class="fas fa-minus"></i> {{ $log->amount }}
                            </span>
                            @endif
                            <div class="text-[10px] text-gray-400 mt-0.5">Saldo: {{ $log->balance_after }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-200">
                            <span id="log-reason-{{ $log->id }}">{{ $log->reason }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-400">
                            {{ $log->issuedBy?->name ?? 'Sistem' }}
                        </td>
                        @if(auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove())
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button"
                                        onclick="openEditLogModal({{ $log->id }}, '{{ addslashes($log->reason) }}')"
                                        class="px-2 py-1 text-xs rounded bg-white/10 hover:bg-white/20 text-sky-300 transition-colors"
                                        title="Edit Catatan Alasan">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <form action="{{ route('credit-score.log.destroy', $log) }}" method="POST"
                                      onsubmit="return confirm('Batalkan catatan ini dan kembalikan saldo credit score anggota?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-colors"
                                            title="Hapus Catatan & Rollback Skor">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->isInDivision('comdis') || auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove()) ? 5 : 4 }}" class="px-5 py-8 text-center text-gray-400">
                            Belum ada riwayat perubahan credit score untuk anggota ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-white/10">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
</div>

{{-- Edit Log Modal --}}
<div id="editLogModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
    <div class="bg-[#0b1329] border border-white/20 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-white/5">
            <h3 class="text-white font-bold text-sm">Edit Catatan Alasan</h3>
            <button type="button" onclick="closeEditLogModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editLogForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Alasan / Catatan</label>
                <textarea id="editLogReason" name="reason" rows="3" required class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-white text-xs placeholder-gray-500 focus:outline-none focus:border-sky-500"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditLogModal()" class="px-4 py-2 rounded-xl border border-white/10 text-gray-300 text-xs font-semibold hover:bg-white/5">Batal</button>
                <button type="submit" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditLogModal(logId, currentReason) {
    document.getElementById('editLogForm').action = `/credit-score/log/${logId}`;
    document.getElementById('editLogReason').value = currentReason;
    const modal = document.getElementById('editLogModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeEditLogModal() {
    const modal = document.getElementById('editLogModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
