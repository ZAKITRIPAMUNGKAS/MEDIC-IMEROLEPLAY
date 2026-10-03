@extends('layouts.app')
@section('title', 'Edit Credit Score — ' . $user->name)
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-2xl mx-auto px-4 sm:px-6">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('credit-score.show', $user) }}" class="inline-flex items-center gap-2 text-sm text-sky-400 hover:text-sky-300">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail
        </a>
        <a href="{{ route('credit-score.input', $user) }}" class="text-xs text-amber-400 hover:text-amber-300 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20">
            <i class="fas fa-plus-minus"></i> Mode Input Poin (+/-)
        </a>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-xl">
        <div class="flex items-center gap-3 pb-5 border-b border-white/10 mb-6">
            <div class="w-12 h-12 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 text-xl">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Edit Credit Score Comdis</h1>
                <p class="text-xs text-gray-400">Atur atau ubah nilai Credit Score anggota Alta Hospital secara langsung</p>
            </div>
        </div>

        {{-- Info Anggota --}}
        <div class="bg-white/5 rounded-xl p-4 mb-6 border border-white/5 flex items-center justify-between">
            <div>
                <div class="font-bold text-white text-base">{{ $user->name }}</div>
                <div class="text-xs text-sky-300 font-mono mt-0.5">ID: {{ $user->staff_id ?? '-' }} &bull; {{ $user->role?->display_name }}</div>
                @if($user->subRole)
                <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full font-bold" style="{{ $user->subRole->badge_style }}">{{ $user->subRole->short_name }}</span>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Skor Saat Ini</div>
                <div class="text-3xl font-extrabold text-white" id="currentScoreDisplay">{{ $creditScore->score }}</div>
            </div>
        </div>

        <form action="{{ route('credit-score.update', $user) }}" method="POST" class="space-y-5">
            @csrf

            {{-- Input Skor Baru --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="score" class="block text-xs font-semibold uppercase tracking-wider text-gray-300">
                        Nilai Credit Score Baru (0 - 100)
                    </label>
                    <span id="scoreDiffBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md hidden"></span>
                </div>
                <div class="relative">
                    <input type="number" id="score" name="score" min="0" max="100" value="{{ old('score', $creditScore->score) }}" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 text-lg font-bold">
                    <div class="absolute right-3 top-3 text-xs text-gray-400 font-medium">/ 100 Poin</div>
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="setPresetScore(100)" class="text-xs px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/30 transition-all">Set 100 (Penuh)</button>
                    <button type="button" onclick="setPresetScore(95)" class="text-xs px-2.5 py-1 rounded-lg bg-sky-500/20 text-sky-300 border border-sky-500/30 hover:bg-sky-500/30 transition-all">Set 95</button>
                    <button type="button" onclick="setPresetScore(85)" class="text-xs px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 hover:bg-amber-500/30 transition-all">Set 85 (Batas Min.)</button>
                    <button type="button" onclick="setPresetScore(75)" class="text-xs px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30 transition-all">Set 75</button>
                </div>
                @error('score') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Alasan Perubahan --}}
            <div>
                <label for="reason" class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">
                    Alasan / Keterangan Edit Skor <span class="text-rose-400">*</span>
                </label>
                <textarea id="reason" name="reason" rows="4" required placeholder="Contoh: Koreksi kekeliruan pencatatan sanksi oleh Comdis / Pemutihan berkala / Penyesuaian sidang disiplin..."
                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 text-sm">{{ old('reason') }}</textarea>
                @error('reason') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('credit-score.show', $user) }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-gray-300 text-sm font-semibold hover:bg-white/5 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-purple-500/20 transition-all">
                    <i class="fas fa-save mr-1.5"></i> Simpan Nilai Skor
                </button>
            </div>
        </form>
    </div>

</div>
</div>

<script>
const currentScore = {{ $creditScore->score }};
const scoreInput = document.getElementById('score');
const diffBadge = document.getElementById('scoreDiffBadge');

function updateDiff() {
    const val = parseInt(scoreInput.value) || 0;
    const diff = val - currentScore;
    if (diff === 0) {
        diffBadge.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-white/10 text-gray-300';
        diffBadge.textContent = 'Tidak ada perubahan';
    } else if (diff > 0) {
        diffBadge.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
        diffBadge.textContent = `+${diff} Poin`;
    } else {
        diffBadge.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-rose-500/20 text-rose-300 border border-rose-500/30';
        diffBadge.textContent = `${diff} Poin`;
    }
    diffBadge.classList.remove('hidden');
}

function setPresetScore(val) {
    scoreInput.value = val;
    updateDiff();
}

scoreInput.addEventListener('input', updateDiff);
updateDiff();
</script>
@endsection
