@extends('layouts.app')

@section('title', 'Form Evaluasi Wawancara — ' . $candidate->name)

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/40 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-3xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div>
            <a href="{{ route('portal.interview.index') }}" class="text-xs text-indigo-300 hover:text-white flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Antrean Interview
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 text-lg">
                    <i class="fas fa-user-edit"></i>
                </span>
                Formulir Hasil Interview Calon Medis
            </h1>
            <p class="text-slate-300 text-sm mt-1">
                Lakukan penilaian kelayakan calon staf, berikan rekomendasi status, dan tentukan jenjang jabatan yang direkomendasikan.
            </p>
        </div>

        @if($errors->any())
            <div class="bg-rose-500/20 border border-rose-500/40 rounded-xl p-4 text-rose-300 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <div><i class="fas fa-exclamation-circle mr-1"></i> {{ $err }}</div>
                @endforeach
            </div>
        @endif

        {{-- Info Calon Medis --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl">
            <h3 class="text-xs uppercase font-bold text-indigo-300 tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-id-card"></i> Informasi Identitas Calon
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm pb-4 border-b border-white/10">
                <div>
                    <span class="text-xs text-slate-400 block mb-0.5">Nama Lengkap</span>
                    <span class="text-white font-bold text-base">{{ $candidate->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block mb-0.5">Citizen ID (CID) FiveM</span>
                    <span class="font-mono text-emerald-300 font-bold text-base">{{ $candidate->citizen_id ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block mb-0.5">Email</span>
                    <span class="text-slate-200">{{ $candidate->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block mb-0.5">Pilihan Role Awal</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                        {{ $candidate->role?->display_name ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Form Penilaian Interview --}}
            <form method="POST" action="{{ route('portal.interview.store', $candidate) }}" class="space-y-6 mt-6">
                @csrf

                {{-- Status Rekomendasi --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Hasil Keputusan Interview <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 rounded-xl border border-white/20 bg-white/5 cursor-pointer hover:bg-white/10 transition-all has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-500/15">
                            <input type="radio" name="result" value="recommended" id="res_rec" required
                                   {{ old('result') === 'recommended' ? 'checked' : '' }}
                                   class="text-emerald-500 focus:ring-emerald-400 mr-3"
                                   onchange="toggleRoleSelection(true)">
                            <div>
                                <span class="block text-sm font-bold text-emerald-300 flex items-center gap-1.5">
                                    <i class="fas fa-check-circle"></i> Recommended
                                </span>
                                <span class="text-xs text-slate-400">Calon memenuhi standar etika, attitude &amp; pemahaman medis.</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 rounded-xl border border-white/20 bg-white/5 cursor-pointer hover:bg-white/10 transition-all has-[:checked]:border-rose-400 has-[:checked]:bg-rose-500/15">
                            <input type="radio" name="result" value="not_recommended" id="res_not_rec" required
                                   {{ old('result') === 'not_recommended' ? 'checked' : '' }}
                                   class="text-rose-500 focus:ring-rose-400 mr-3"
                                   onchange="toggleRoleSelection(false)">
                            <div>
                                <span class="block text-sm font-bold text-rose-300 flex items-center gap-1.5">
                                    <i class="fas fa-times-circle"></i> Not Recommended
                                </span>
                                <span class="text-xs text-slate-400">Calon belum memenuhi kriteria kualifikasi rumah sakit.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Rekomendasi Jabatan (Jika Recommended) --}}
                <div id="recommendedRoleBox" class="{{ old('result') === 'not_recommended' ? 'hidden' : '' }}">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-emerald-300 mb-2">
                        <i class="fas fa-stethoscope mr-1"></i> Rekomendasi Jabatan Medis Awal <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($allowedRoles as $rSlug => $rLabel)
                        <label class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-white/20 bg-white/5 cursor-pointer hover:bg-white/10 transition-all has-[:checked]:border-sky-400 has-[:checked]:bg-sky-500/20 text-center">
                            <input type="radio" name="recommended_role" value="{{ $rSlug }}"
                                   {{ old('recommended_role') === $rSlug ? 'checked' : '' }}
                                   class="text-sky-500 focus:ring-sky-400 mb-1.5">
                            <span class="text-xs font-bold text-white">{{ $rLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">Pilih salah satu jabatan: Trainee, Perawat, Co-Ass, atau Dokter Umum.</p>
                </div>

                {{-- Catatan Interview --}}
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Catatan &amp; Evaluasi Wawancara <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="notes" name="notes" rows="4" required maxlength="2000"
                              placeholder="Tuliskan catatan hasil interview, pemahaman SOP, etika medis, attitude, dan alasan rekomendasi..."
                              class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl p-3.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-white/10 flex items-center justify-end gap-3">
                    <a href="{{ route('portal.interview.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-slate-300 rounded-xl text-xs font-semibold transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            onclick="return confirm('Apakah Anda yakin data evaluasi wawancara ini sudah benar dan siap disimpan?')"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-900/30 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Hasil Interview
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function toggleRoleSelection(isRecommended) {
    const box = document.getElementById('recommendedRoleBox');
    if (isRecommended) {
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
        // Uncheck all role radios
        document.querySelectorAll('input[name="recommended_role"]').forEach(el => el.checked = false);
    }
}
</script>
@endsection
