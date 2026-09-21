@extends('layouts.app')

@section('title', 'Form Evaluasi Wawancara — ' . $candidate->ic_name)

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/40 to-slate-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative max-w-4xl mx-auto text-white space-y-6">

        {{-- Header --}}
        <div>
            <a href="{{ route('portal.interview.index') }}" class="text-xs text-indigo-300 hover:text-white flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Antrean Interview
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 text-lg">
                            <i class="fas fa-user-edit"></i>
                        </span>
                        Formulir Evaluasi Wawancara Calon
                    </h1>
                    <p class="text-slate-300 text-sm mt-1">
                        Tinjau profil berkas pendaftaran recruitment calon staf, berikan penilaian wawancara, dan tetapkan rekomendasi jabatan.
                    </p>
                </div>
                <div class="px-3.5 py-1.5 bg-indigo-500/20 border border-indigo-500/30 rounded-xl text-xs font-semibold text-indigo-300 self-start sm:self-center">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $candidate->period?->batch_name ?? 'Recruitment Alta' }}
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-rose-500/20 border border-rose-500/40 rounded-xl p-4 text-rose-300 text-xs space-y-1 shadow-lg">
                @foreach($errors->all() as $err)
                    <div><i class="fas fa-exclamation-circle mr-1"></i> {{ $err }}</div>
                @endforeach
            </div>
        @endif

        {{-- Ringkasan Profil Calon Dari Formulir Recruitment --}}
        <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-xs uppercase font-bold text-indigo-300 tracking-wider flex items-center gap-2">
                    <i class="fas fa-id-card"></i> Data Pendaftaran Calon (Dari Form Recruitment)
                </h3>
                {!! $candidate->status_badge !!}
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs pb-4 border-b border-white/10">
                <div>
                    <span class="text-slate-400 block mb-0.5">Nama Karakter (IC)</span>
                    <span class="text-white font-bold text-sm">{{ $candidate->ic_name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Citizen ID (CID)</span>
                    <span class="font-mono text-emerald-300 font-bold text-sm">{{ $candidate->cid }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Discord Username</span>
                    <span class="text-indigo-300 font-mono text-sm font-semibold">
                        {{ $candidate->discord_username ? '@' . $candidate->discord_username : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Jenis Kelamin / Lahir</span>
                    <span class="text-slate-200">
                        {{ $candidate->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }} • {{ $candidate->birth_date?->format('d/m/Y') ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Detail Jawaban Form --}}
            <div class="space-y-4 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold mb-1">
                        <i class="fas fa-briefcase-medical text-sky-400 mr-1"></i> Pengalaman Medis Sebelumnya:
                    </span>
                    <p class="text-slate-200 bg-white/5 p-3 rounded-xl border border-white/10 leading-relaxed">
                        <strong class="text-white block mb-1">
                            {{ $candidate->has_medical_exp ? 'Ada Pengalaman Medis' : 'Belum Ada Pengalaman Medis' }}
                        </strong>
                        {{ $candidate->medical_exp_desc ?: 'Tidak ada deskripsi tambahan.' }}
                    </p>
                </div>

                <div>
                    <span class="text-slate-400 block font-semibold mb-1">
                        <i class="fas fa-heart text-rose-400 mr-1"></i> Alasan Ingin Bergabung dengan Medic Alta:
                    </span>
                    <p class="text-slate-200 bg-white/5 p-3 rounded-xl border border-white/10 leading-relaxed whitespace-pre-line">
                        {{ $candidate->reason_joining }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 block font-semibold mb-1">
                            <i class="fas fa-gamepad text-purple-400 mr-1"></i> Pengalaman Roleplay (RP OOC):
                        </span>
                        <p class="text-slate-200 bg-white/5 p-3 rounded-xl border border-white/10 leading-relaxed">
                            {{ $candidate->rp_experience }}
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold mb-1">
                            <i class="fas fa-city text-amber-400 mr-1"></i> Tanggung Jawab di Kota Lain:
                        </span>
                        <p class="text-slate-200 bg-white/5 p-3 rounded-xl border border-white/10 leading-relaxed">
                            {{ $candidate->other_city_responsibility ?: 'Tidak ada tanggung jawab di kota lain.' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 block font-semibold mb-1">
                            <i class="fas fa-clock text-emerald-400 mr-1"></i> Jam Online Masuk Kota:
                        </span>
                        <div class="bg-white/5 p-2.5 rounded-xl border border-white/10 flex flex-wrap gap-1.5">
                            @if(is_array($candidate->online_hours))
                                @foreach($candidate->online_hours as $hr)
                                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-white font-mono text-[11px]">{{ $hr }}</span>
                                @endforeach
                            @else
                                <span class="text-slate-300 font-mono text-[11px]">{{ $candidate->online_hours ?? '-' }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold mb-1">
                            <i class="fas fa-calendar-check text-indigo-400 mr-1"></i> Hari Online Masuk Kota:
                        </span>
                        <div class="bg-white/5 p-2.5 rounded-xl border border-white/10 flex flex-wrap gap-1.5">
                            @if(is_array($candidate->online_days))
                                @foreach($candidate->online_days as $day)
                                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-white font-mono text-[11px]">{{ $day }}</span>
                                @endforeach
                            @else
                                <span class="text-slate-300 font-mono text-[11px]">{{ $candidate->online_days ?? '-' }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Berkas Lampiran --}}
                <div>
                    <span class="text-slate-400 block font-semibold mb-2">
                        <i class="fas fa-folder-open text-amber-300 mr-1"></i> Dokumen Lampiran Pendaftaran:
                    </span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @if($candidate->ktp_file)
                        <a href="{{ asset($candidate->ktp_file) }}" target="_blank"
                           class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 flex items-center gap-2 transition-colors">
                            <i class="fas fa-id-badge text-indigo-400 text-sm"></i>
                            <span class="text-white text-xs font-medium truncate">Foto KTP</span>
                        </a>
                        @endif
                        @if($candidate->skb_file)
                        <a href="{{ asset($candidate->skb_file) }}" target="_blank"
                           class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 flex items-center gap-2 transition-colors">
                            <i class="fas fa-file-contract text-emerald-400 text-sm"></i>
                            <span class="text-white text-xs font-medium truncate">Surat SKB</span>
                        </a>
                        @endif
                        @if($candidate->health_cert_file)
                        <a href="{{ asset($candidate->health_cert_file) }}" target="_blank"
                           class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 flex items-center gap-2 transition-colors">
                            <i class="fas fa-notes-medical text-rose-400 text-sm"></i>
                            <span class="text-white text-xs font-medium truncate">Surat Kesehatan</span>
                        </a>
                        @endif
                        @if($candidate->psychology_cert_file)
                        <a href="{{ asset($candidate->psychology_cert_file) }}" target="_blank"
                           class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 flex items-center gap-2 transition-colors">
                            <i class="fas fa-brain text-purple-400 text-sm"></i>
                            <span class="text-white text-xs font-medium truncate">Surat Psikologi</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Form Penilaian Interview --}}
            <form method="POST" action="{{ route('portal.interview.store', $candidate->id) }}" class="space-y-6 pt-6 border-t border-white/10">
                @csrf

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
                                    <i class="fas fa-check-circle"></i> Recommended (Lolos Wawancara)
                                </span>
                                <span class="text-xs text-slate-400">Calon memenuhi standar attitude, komunikasi, dan pemahaman SOP medis.</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 rounded-xl border border-white/20 bg-white/5 cursor-pointer hover:bg-white/10 transition-all has-[:checked]:border-rose-400 has-[:checked]:bg-rose-500/15">
                            <input type="radio" name="result" value="not_recommended" id="res_not_rec" required
                                   {{ old('result') === 'not_recommended' ? 'checked' : '' }}
                                   class="text-rose-500 focus:ring-rose-400 mr-3"
                                   onchange="toggleRoleSelection(false)">
                            <div>
                                <span class="block text-sm font-bold text-rose-300 flex items-center gap-1.5">
                                    <i class="fas fa-times-circle"></i> Not Recommended (Tidak Lolos)
                                </span>
                                <span class="text-xs text-slate-400">Calon belum memenuhi kriteria kualifikasi rumah sakit saat ini.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Rekomendasi Jabatan Medis Awal --}}
                <div id="recommendedRoleBox" class="{{ old('result') === 'not_recommended' ? 'hidden' : '' }}">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-emerald-300 mb-2">
                        <i class="fas fa-stethoscope mr-1"></i> Rekomendasi Jenjang Jabatan Medis Awal <span class="text-rose-400">*</span>
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
                    <p class="text-[11px] text-slate-400 mt-2">Pilih salah satu jabatan awal yang direkomendasikan berdasarkan hasil evaluasi interview.</p>
                </div>

                {{-- Catatan Evaluasi Wawancara --}}
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Catatan &amp; Evaluasi Wawancara <span class="text-rose-400">*</span>
                    </label>
                    <textarea id="notes" name="notes" rows="4" required maxlength="2000"
                              placeholder="Tuliskan catatan hasil wawancara, respon calon terhadap situasi medis, etika, attitude, dan pertimbangan rekomendasi..."
                              class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl p-3.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-white/10 flex items-center justify-end gap-3">
                    <a href="{{ route('portal.interview.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/15 text-slate-300 rounded-xl text-xs font-semibold transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            onclick="return confirm('Simpan hasil evaluasi interview untuk calon {{ addslashes($candidate->ic_name) }}?')"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-900/30 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Hasil Evaluasi
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
        const roleInputs = box.querySelectorAll('input[type="radio"]');
        roleInputs.forEach(input => input.checked = false);
    }
}
</script>
@endsection
