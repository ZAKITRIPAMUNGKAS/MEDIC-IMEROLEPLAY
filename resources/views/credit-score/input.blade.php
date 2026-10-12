@extends('layouts.app')
@section('title', 'Input Credit Score — ' . $user->name)
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-2xl mx-auto px-4 sm:px-6">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('credit-score.show', $user) }}" class="inline-flex items-center gap-2 text-sm text-sky-400 hover:text-sky-300">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail
        </a>
        <a href="{{ route('credit-score.edit', $user) }}" class="text-xs text-purple-400 hover:text-purple-300 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-500/10 border border-purple-500/20">
            <i class="fas fa-edit"></i> Halaman Edit Skor Langsung &rarr;
        </a>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3">
        <i class="fas fa-exclamation-circle mt-0.5 text-base flex-shrink-0"></i>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-sm flex items-start gap-3">
        <i class="fas fa-check-circle mt-0.5 text-base flex-shrink-0"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl">
        <div class="flex items-center gap-3 pb-5 border-b border-white/10 mb-6">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Input / Edit Credit Score Comdis</h1>
                <p class="text-xs text-gray-400">Penyesuaian skor pelanggaran, penghargaan, atau edit langsung nilai staf Alta Hospital</p>
            </div>
        </div>

        {{-- Info Anggota --}}
        <div class="bg-white/5 rounded-xl p-4 mb-6 border border-white/5 flex items-center justify-between">
            <div>
                <div class="font-bold text-white">{{ $user->name }}</div>
                <div class="text-xs text-sky-300 font-mono">ID: {{ $user->staff_id ?? '-' }} &bull; {{ $user->role?->display_name }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400">Skor Saat Ini</div>
                <div class="text-2xl font-bold text-white">{{ $creditScore->score }}</div>
            </div>
        </div>

        <form action="{{ route('credit-score.input.store', $user) }}" method="POST" class="space-y-5">
            @csrf

            {{-- Jenis Tindakan --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-2">Jenis Penyesuaian</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="deduct" {{ old('type', 'deduct') === 'deduct' ? 'checked' : '' }} class="peer sr-only" onchange="onTypeChange('deduct')">
                        <div class="p-3.5 rounded-xl border border-white/10 bg-white/5 text-center peer-checked:border-rose-500 peer-checked:bg-rose-500/20 transition-all">
                            <i class="fas fa-minus-circle text-rose-400 text-lg mb-1 block"></i>
                            <div class="font-bold text-white text-sm">Pengurangan</div>
                            <div class="text-[11px] text-gray-400">Pelanggaran SOP / Etik</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="add" {{ old('type') === 'add' ? 'checked' : '' }} class="peer sr-only" onchange="onTypeChange('add')">
                        <div class="p-3.5 rounded-xl border border-white/10 bg-white/5 text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-500/20 transition-all">
                            <i class="fas fa-plus-circle text-emerald-400 text-lg mb-1 block"></i>
                            <div class="font-bold text-white text-sm">Penambahan</div>
                            <div class="text-[11px] text-gray-400">Prestasi / Pemulihan</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="set" {{ old('type') === 'set' ? 'checked' : '' }} class="peer sr-only" onchange="onTypeChange('set')">
                        <div class="p-3.5 rounded-xl border border-white/10 bg-white/5 text-center peer-checked:border-purple-500 peer-checked:bg-purple-500/20 transition-all">
                            <i class="fas fa-sliders-h text-purple-400 text-lg mb-1 block"></i>
                            <div class="font-bold text-white text-sm">Edit Langsung</div>
                            <div class="text-[11px] text-gray-400">Atur Nilai Skor Baru</div>
                        </div>
                    </label>
                </div>
                @error('type') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jumlah Poin / Nilai Skor --}}
            <div>
                <label id="amountLabel" for="amount" class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Jumlah Poin (1 - 100)</label>
                <input type="number" id="amount" name="amount" min="0" max="100" value="{{ old('amount', 5) }}" required
                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                @error('amount') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Alasan --}}
            <div>
                <label id="reasonLabel" for="reason" class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Alasan / Dasar Pelanggaran</label>
                <textarea id="reason" name="reason" rows="4" required placeholder="Jelaskan dasar pemberian sanksi atau penghargaan..."
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">{{ old('reason') }}</textarea>
                @error('reason') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('credit-score.show', $user) }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-gray-300 text-sm font-semibold hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-amber-500/20 transition-all">
                    <i class="fas fa-check mr-1.5"></i> Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>

</div>
</div>

<script>
function onTypeChange(type, retainValue = false) {
    const label = document.getElementById('amountLabel');
    const input = document.getElementById('amount');
    const reasonLabel = document.getElementById('reasonLabel');
    if (type === 'set') {
        label.textContent = 'Nilai Credit Score Baru (0 - 100)';
        if (!retainValue) input.value = {{ $creditScore->score }};
        reasonLabel.textContent = 'Alasan / Dasar Edit Nilai Skor';
    } else if (type === 'add') {
        label.textContent = 'Jumlah Poin Penambahan (1 - 100)';
        if (!retainValue) input.value = 5;
        reasonLabel.textContent = 'Alasan / Prestasi Penghargaan';
    } else {
        label.textContent = 'Jumlah Poin Pengurangan (1 - 100)';
        if (!retainValue) input.value = 5;
        reasonLabel.textContent = 'Alasan / Dasar Pelanggaran SOP';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const selected = document.querySelector('input[name="type"]:checked');
    if (selected) {
        onTypeChange(selected.value, true);
    }
});
</script>
@endsection
