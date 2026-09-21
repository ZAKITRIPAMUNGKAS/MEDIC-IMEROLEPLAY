@extends('layouts.app')
@section('title', 'Ajukan Kenaikan Jabatan — Portal Alta Hospital')
@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
<div class="max-w-3xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('portal.promotion.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors"><i class="fas fa-arrow-left text-xs"></i> Kembali</a>
        <h1 class="text-2xl font-bold text-white flex items-center gap-2"><i class="fas fa-level-up-alt text-violet-400"></i> Formulir Kenaikan Jabatan</h1>
        <p class="text-white/50 text-sm mt-1">Periode: <span class="text-violet-300 font-medium">{{ $period->name }}</span></p>
    </div>
    @if($errors->any())
    <div class="mb-4 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm"><ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl space-y-6">
        {{-- Info Jabatan Saat Ini --}}
        <div class="flex items-center gap-3 p-4 bg-white/5 rounded-xl border border-white/10">
            <i class="fas fa-user-md text-violet-400 text-lg w-8"></i>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider">Jabatan Saat Ini</p>
                <p class="text-white font-bold">{{ $user->role?->display_name ?? 'Belum ada jabatan' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('portal.promotion.store') }}" enctype="multipart/form-data" class="space-y-6" id="promotionForm">
            @csrf

            {{-- Pilih Target Jabatan --}}
            <div>
                <label class="block text-sm text-white/70 font-medium mb-2">Target Jabatan <span class="text-rose-400">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($targetRoles as $role)
                    <label class="flex items-center gap-3 p-3.5 bg-white/5 border border-white/15 rounded-xl cursor-pointer hover:bg-violet-500/10 hover:border-violet-500/40 transition-all has-[:checked]:bg-violet-500/15 has-[:checked]:border-violet-400">
                        <input type="radio" name="target_role_id" value="{{ $role->id }}"
                               {{ old('target_role_id', $defaultTarget?->id) == $role->id ? 'checked' : '' }}
                               class="accent-violet-400"
                               onchange="loadChecklist({{ $role->id }})">
                        <div>
                            <p class="text-white font-medium text-sm">{{ $role->display_name }}</p>
                            <p class="text-white/40 text-xs">Level {{ $role->level }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Checklist Persyaratan (Live Update) --}}
            <div id="checklist-container" class="space-y-2">
                <p class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-2">Checklist Persyaratan</p>
                @foreach($checklist as $item)
                <div class="flex items-center gap-3 text-sm {{ $item['met'] ? 'text-emerald-300' : 'text-red-300' }} checklist-item" id="check_{{ $item['key'] }}">
                    <i class="fas {{ $item['met'] ? 'fa-check-circle' : 'fa-times-circle' }} text-sm shrink-0"></i>
                    <span>{{ $item['label'] }}</span>
                </div>
                @endforeach
                <div id="all-met-indicator" class="hidden mt-2 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-300 text-sm flex items-center gap-2">
                    <i class="fas fa-trophy text-emerald-400"></i>
                    <span>Semua persyaratan telah terpenuhi! Anda siap untuk mendaftar.</span>
                </div>
            </div>

            @php
                $defaultRoleName = strtolower(($defaultTarget?->name ?? '') . ' ' . ($defaultTarget?->display_name ?? ''));
                $defaultIsDokspel = str_contains($defaultRoleName, 'spesialis');
                $defaultIsDokterUmum = str_contains($defaultRoleName, 'dokter_umum') || str_contains($defaultRoleName, 'dokter umum');
            @endphp

            {{-- Catatan Khusus Pengajuan Dokspel (Hanya tampil jika memilih Dokter Spesialis) --}}
            <div id="dokspel-notice" class="{{ $defaultIsDokspel ? '' : 'hidden' }} p-4 bg-gradient-to-r from-amber-500/15 via-violet-500/15 to-amber-500/15 border border-amber-500/30 rounded-2xl shadow-lg">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0 mt-0.5">
                        <i class="fas fa-stethoscope text-base"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-amber-300">Catatan Khusus Pengajuan Dokter Spesialis (Dokspel)</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-200 border border-amber-500/30 uppercase tracking-wider">Perhatian</span>
                        </div>
                        <p class="text-xs text-white/80 mt-1 leading-relaxed">
                            Bagi anggota yang mengajukan kenaikan ke jenjang <strong>Dokter Spesialis (Dokspel)</strong>, harap memperhatikan ketentuan berkas berikut:
                        </p>
                        <ul class="mt-2 space-y-1 text-xs text-white/70 list-disc list-inside">
                            <li><strong class="text-white">Laporan Studi Kasus (Wajib PDF):</strong> Berkas telaah ilmiah kasus bedah / penanganan klinis spesialistik.</li>
                            <li><strong class="text-white">Surat Rekomendasi Konsulen:</strong> Minimal 2 surat rekomendasi resmi dari Konsulen/Dokter Spesialis senior.</li>
                            <li><strong class="text-white">Berkas Pendukung / Portofolio:</strong> Lampirkan bukti sertifikat stase kelulusan MSL, sertifikat operasi PND, atau rekap logbook pelayanan pasien.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Upload Berkas --}}
            <div class="space-y-4 pt-2 border-t border-white/10">
                <div class="flex items-center justify-between">
                    <p class="text-white/80 text-xs uppercase tracking-wider font-bold">Unggah Berkas Pendukung (Format PDF / Dokumen Resmi)</p>
                    <span class="text-[11px] text-white/40"><i class="fas fa-file-pdf text-rose-400 mr-1"></i> Format PDF diutamakan</span>
                </div>

                <div id="case-study-container">
                    <label id="case-study-label" class="block text-sm text-white/70 font-medium mb-1.5">
                        @if($defaultIsDokspel)
                            Laporan Studi Kasus Spesialisasi <span class="text-xs text-rose-400 font-bold">*Wajib Dokspel (PDF, max 10MB)</span>
                        @elseif($defaultIsDokterUmum)
                            Berkas Laporan Kenaikan Jabatan / Rekap Stase <span class="text-xs text-white/40">(PDF/DOC, max 10MB)</span>
                        @else
                            Berkas Laporan Kenaikan Jabatan <span class="text-xs text-white/40">(Opsional, max 10MB)</span>
                        @endif
                    </label>
                    <input type="file" name="case_study_file" accept=".pdf,.doc,.docx"
                           class="w-full px-3 py-2.5 bg-white/10 border border-white/20 border-dashed rounded-xl text-white/70 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-violet-500/20 file:text-violet-300 hover:file:bg-violet-500/30">
                    <p id="case-study-help" class="text-[11px] text-white/40 mt-1">
                        @if($defaultIsDokspel)
                            Wajib untuk calon Dokter Spesialis: Berkas telaah ilmiah kasus bedah / penanganan klinis spesialistik.
                        @elseif($defaultIsDokterUmum)
                            Lampirkan berkas laporan rekap tindakan / stase Co-Ass atau berkas kenaikan jabatan.
                        @else
                            Laporan evaluasi masa training atau rekap kegiatan (jika ada).
                        @endif
                    </p>
                </div>

                <div id="reco-section" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">
                            Surat Rekomendasi Konsulen 1 <span class="text-xs text-white/40">(PDF/Foto)</span>
                        </label>
                        <input type="file" name="recommendation_letter_1" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2.5 bg-white/10 border border-white/20 border-dashed rounded-xl text-white/70 text-sm file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-violet-500/20 file:text-violet-300">
                    </div>
                    <div>
                        <label class="block text-sm text-white/70 font-medium mb-1.5">
                            Surat Rekomendasi Konsulen 2 <span class="text-xs text-white/40">(PDF/Foto)</span>
                        </label>
                        <input type="file" name="recommendation_letter_2" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2.5 bg-white/10 border border-white/20 border-dashed rounded-xl text-white/70 text-sm file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-violet-500/20 file:text-violet-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-white/70 font-medium mb-1.5">
                        Berkas Pendukung Tambahan / Portofolio / Sertifikat <span class="text-xs text-white/40">(PDF/DOC/Gambar, max 10MB)</span>
                    </label>
                    <input type="file" name="supporting_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2.5 bg-white/10 border border-white/20 border-dashed rounded-xl text-white/70 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-amber-500/20 file:text-amber-300 hover:file:bg-amber-500/30">
                    <p class="text-[11px] text-white/40 mt-1">Lampirkan sertifikat kelulusan stase, sertifikat operasi, logbook tindakan, atau berkas pendukung kualifikasi lainnya.</p>
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" id="submitBtn"
                        class="flex-1 py-3 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-400 hover:to-purple-500 text-white font-semibold rounded-xl shadow-lg transition-all text-sm">
                    <i class="fas fa-paper-plane mr-1.5"></i> Kirim Pengajuan Kenaikan Jabatan
                </button>
                <a href="{{ route('portal.promotion.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
</div>
<script>
const rolesMeta = {
    @foreach($targetRoles as $r)
        "{{ $r->id }}": {
            id: {{ $r->id }},
            name: "{{ strtolower($r->name) }}",
            display_name: "{{ addslashes($r->display_name) }}",
            is_dokspel: {{ (str_contains(strtolower($r->name), 'spesialis') || str_contains(strtolower($r->display_name), 'spesialis')) ? 'true' : 'false' }},
            is_dokter_umum: {{ (str_contains(strtolower($r->name), 'dokter_umum') || str_contains(strtolower($r->display_name), 'dokter umum')) ? 'true' : 'false' }}
        },
    @endforeach
};

function updateRoleUI(roleId) {
    const meta = rolesMeta[roleId] || {};
    const dokspelNotice = document.getElementById('dokspel-notice');
    const caseStudyLabel = document.getElementById('case-study-label');
    const caseStudyHelp = document.getElementById('case-study-help');
    const recoSection = document.getElementById('reco-section');

    if (meta.is_dokspel) {
        dokspelNotice?.classList?.remove('hidden');
        if (caseStudyLabel) caseStudyLabel.innerHTML = 'Laporan Studi Kasus Spesialisasi <span class="text-xs text-rose-400 font-bold">*Wajib Dokspel (PDF, max 10MB)</span>';
        if (caseStudyHelp) caseStudyHelp.innerText = 'Wajib untuk calon Dokter Spesialis: Berkas telaah ilmiah kasus bedah / penanganan klinis spesialistik.';
        recoSection?.classList?.remove('hidden');
    } else if (meta.is_dokter_umum) {
        dokspelNotice?.classList?.add('hidden');
        if (caseStudyLabel) caseStudyLabel.innerHTML = 'Berkas Laporan Kenaikan Jabatan / Rekap Stase <span class="text-xs text-white/40">(PDF/DOC, max 10MB)</span>';
        if (caseStudyHelp) caseStudyHelp.innerText = 'Lampirkan berkas laporan rekap tindakan / stase Co-Ass atau berkas kenaikan jabatan.';
        recoSection?.classList?.remove('hidden');
    } else {
        dokspelNotice?.classList?.add('hidden');
        if (caseStudyLabel) caseStudyLabel.innerHTML = 'Berkas Laporan Kenaikan Jabatan <span class="text-xs text-white/40">(Opsional, max 10MB)</span>';
        if (caseStudyHelp) caseStudyHelp.innerText = 'Laporan evaluasi masa training atau rekap kegiatan (jika ada).';
        recoSection?.classList?.add('hidden');
    }
}

async function loadChecklist(roleId) {
    updateRoleUI(roleId);
    const container = document.getElementById('checklist-container');
    const allMetDiv = document.getElementById('all-met-indicator');
    container.innerHTML = '<p class="text-white/40 text-sm text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat persyaratan...</p>';
    try {
        const res = await fetch(`{{ route('portal.promotion.checklist') }}?target_role_id=${roleId}`);
        const data = await res.json();
        let html = '<p class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-2">Checklist Persyaratan</p>';
        data.checklist.forEach(item => {
            html += `<div class="flex items-center gap-3 text-sm ${item.met ? 'text-emerald-300' : 'text-red-300'}">
                <i class="fas ${item.met ? 'fa-check-circle' : 'fa-times-circle'} text-sm shrink-0"></i>
                <span>${item.label}</span>
            </div>`;
        });
        container.innerHTML = html;
        if (data.all_met) {
            allMetDiv?.classList?.remove('hidden');
        } else {
            allMetDiv?.classList?.add('hidden');
        }
    } catch(e) {
        container.innerHTML = '<p class="text-red-300 text-sm">Gagal memuat checklist.</p>';
    }
}
</script>
@endsection
