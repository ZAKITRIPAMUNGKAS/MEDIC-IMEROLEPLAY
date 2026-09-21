@extends('layouts.app')

@section('title', 'Terbitkan Sertifikat Kendaraan GA — Portal Alta Hospital')

@section('content')
<div class="min-h-screen pt-20 pb-10" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="mb-6">
            <a href="{{ route('portal.ga.index') }}" class="text-white/50 hover:text-white text-sm flex items-center gap-1.5 mb-3 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar Sertifikat
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <i class="fas fa-car text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-wide">Terbitkan Sertifikat Kendaraan GA</h1>
                    <p class="text-white/50 text-sm mt-0.5">Sertifikat resmi berformat foto otomatis di-generate dan langsung masuk ke profil anggota</p>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-500/20 border border-red-500/40 rounded-xl text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Form Column --}}
            <div class="lg:col-span-7 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 sm:p-7 shadow-2xl">
                <form method="POST" action="{{ route('portal.ga.store') }}" class="space-y-4" id="certForm">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Pilih Anggota <span class="text-rose-400">*</span></label>
                        <select name="user_id" id="userIdSelect" required
                                class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                            <option value="" data-name="— Pilih Anggota —" data-staffid="ALTA-MED" class="bg-gray-800 text-white">— Pilih Anggota —</option>
                            @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" 
                                    data-name="{{ $staff->name }}" 
                                    data-staffid="{{ $staff->staff_id ?? 'ALTA-MED' }}"
                                    data-role="{{ $staff->role?->display_name ?? 'Staf' }}"
                                    {{ old('user_id') == $staff->id ? 'selected' : '' }}
                                    class="bg-gray-800 text-white">
                                {{ $staff->name }} ({{ $staff->staff_id }}) — {{ $staff->role?->display_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Tipe Sertifikat Kendaraan <span class="text-rose-400">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3 bg-white/10 border border-white/20 rounded-xl cursor-pointer hover:bg-amber-500/10 hover:border-amber-500/40 transition-all has-[:checked]:bg-amber-500/20 has-[:checked]:border-amber-400">
                                <input type="radio" name="type" value="vehicle_land" id="typeLand" {{ old('type','vehicle_land') === 'vehicle_land' ? 'checked' : '' }} class="accent-amber-400">
                                <span class="text-white text-xs sm:text-sm font-medium"><i class="fas fa-car text-amber-400 mr-1.5"></i> Kendaraan Darat</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-white/10 border border-white/20 rounded-xl cursor-pointer hover:bg-sky-500/10 hover:border-sky-500/40 transition-all has-[:checked]:bg-sky-500/20 has-[:checked]:border-sky-400">
                                <input type="radio" name="type" value="vehicle_heli" id="typeHeli" {{ old('type') === 'vehicle_heli' ? 'checked' : '' }} class="accent-sky-400">
                                <span class="text-white text-xs sm:text-sm font-medium"><i class="fas fa-helicopter text-sky-400 mr-1.5"></i> Helikopter</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Judul Sertifikat <span class="text-rose-400">*</span></label>
                        <input type="text" name="title" id="titleInput" value="{{ old('title', 'Sertifikat Kelayakan Kendaraan Darurat Darat') }}" required maxlength="255"
                               placeholder="Contoh: SIM / Sertifikat Kelayakan Mengemudi Kendaraan Darat"
                               class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Nomor Registrasi (Opsional)</label>
                            <input type="text" name="certificate_number" id="certNumberInput" value="{{ old('certificate_number') }}" maxlength="100"
                                   placeholder="Otomatis: ALTA/GA/2026/..."
                                   class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Tanggal Terbit <span class="text-rose-400">*</span></label>
                            <input type="date" name="issue_date" id="issueDateInput" value="{{ old('issue_date', date('Y-m-d')) }}" required
                                   class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white text-sm focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-white/70 uppercase mb-1.5">Catatan Khusus Kualifikasi (Opsional)</label>
                        <textarea name="notes" id="notesInput" rows="2" maxlength="500" placeholder="Keterangan kelayakan, unit kendaraan, dsb..."
                                  class="w-full px-3.5 py-2 bg-white/10 border border-white/20 rounded-xl text-white text-sm placeholder-white/30 focus:outline-none focus:border-amber-400 resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <div class="pt-3 flex gap-3">
                        <button type="submit"
                                class="flex-1 py-3 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-400 hover:to-yellow-500 text-white font-semibold rounded-xl shadow-lg shadow-amber-900/40 transition-all duration-200 text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-magic text-sm"></i> Terbitkan &amp; Generate Foto Sertifikat
                        </button>
                        <a href="{{ route('portal.ga.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 text-white/70 font-medium rounded-xl transition-all text-sm">Batal</a>
                    </div>
                </form>
            </div>

            {{-- Live Certificate Template Preview Column --}}
            <div class="lg:col-span-5 bg-gradient-to-b from-[#0b1633] to-[#070d1e] border-2 border-amber-400/40 rounded-2xl p-5 shadow-2xl relative overflow-hidden">
                <div class="flex items-center justify-between pb-3 border-b border-amber-400/20 mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-300 flex items-center gap-1.5">
                        <i class="fas fa-eye text-amber-400"></i> Pratinjau Template Foto
                    </span>
                    <span class="text-[10px] text-white/50 bg-white/10 px-2 py-0.5 rounded-full font-mono">
                        Auto-Generated SVG
                    </span>
                </div>

                {{-- Miniature Certificate Visual Display --}}
                <div class="relative bg-gradient-to-br from-[#080f24] via-[#0f224a] to-[#050a18] border-2 border-amber-400/60 rounded-xl p-4 sm:p-5 text-center shadow-inner space-y-3.5">
                    
                    {{-- Gold Header Emblem --}}
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full bg-slate-900 border border-amber-400/60 flex items-center justify-center text-emerald-400 text-sm shadow">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="text-[11px] font-serif font-black tracking-widest text-amber-300 uppercase mt-1">
                            ALTA HOSPITAL MEDICAL CENTER
                        </div>
                        <div class="text-[8px] text-sky-200 tracking-wider uppercase font-semibold">
                            DIVISI GENERAL AFFAIRS &amp; OPERASIONAL
                        </div>
                    </div>

                    {{-- Banner Badge --}}
                    <div class="inline-block px-3 py-0.5 rounded-full bg-gradient-to-r from-amber-500 to-yellow-600 text-slate-950 font-serif font-bold text-[9px] tracking-wider uppercase shadow">
                        SERTIFIKAT KELAYAKAN RESMI
                    </div>

                    {{-- Member Name --}}
                    <div>
                        <div class="text-[9px] text-slate-400 uppercase tracking-widest">Diberikan Kepada:</div>
                        <div id="previewName" class="text-base font-serif font-bold text-white tracking-wide mt-0.5 line-clamp-1">
                            [Pilih Anggota Medis]
                        </div>
                        <div id="previewStaffId" class="text-[9px] font-mono text-sky-300 font-semibold">
                            ID: ALTA-MED &bull; GA Operasional
                        </div>
                    </div>

                    {{-- Qualification Box --}}
                    <div class="bg-slate-950/70 border border-amber-400/40 rounded-lg p-2.5 shadow-sm">
                        <div class="text-[8px] text-slate-300 uppercase tracking-wider mb-0.5">Bidang / Kualifikasi:</div>
                        <div id="previewTitle" class="text-xs font-serif font-black text-amber-200 tracking-wide uppercase line-clamp-1">
                            Sertifikat Kelayakan Kendaraan Darat
                        </div>
                        <div id="previewTypeBadge" class="text-[9px] text-emerald-300 font-semibold mt-1">
                            <i class="fas fa-car mr-1"></i> Kategori: Kendaraan Darat
                        </div>
                    </div>

                    {{-- Footer: Seal & Details --}}
                    <div class="pt-2 border-t border-white/10 flex items-center justify-between text-left text-[9px]">
                        <div>
                            <div class="text-slate-400">No. Registrasi:</div>
                            <div id="previewNumber" class="font-mono text-white font-bold">ALTA/GA/2026/AUTO</div>
                            <div class="text-slate-400 mt-1">Tanggal Terbit:</div>
                            <div id="previewDate" class="text-sky-300 font-semibold">{{ date('d M Y') }}</div>
                        </div>

                        {{-- Official Seal Graphic Simulation --}}
                        <div class="w-14 h-14 rounded-full border-2 border-dashed border-amber-400/80 bg-slate-950/80 flex flex-col items-center justify-center text-center shadow-lg p-1">
                            <i class="fas fa-certificate text-amber-400 text-xs"></i>
                            <span class="text-[6px] font-black text-amber-300 tracking-tighter uppercase leading-none mt-0.5">ALTA SEAL</span>
                            <span class="text-[5px] text-emerald-400 font-bold uppercase leading-none">VERIFIED</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-200 text-xs flex items-start gap-2.5">
                    <i class="fas fa-info-circle text-amber-400 mt-0.5 shrink-0"></i>
                    <p class="leading-relaxed text-[11px]">
                        <strong>Cetak Foto Otomatis:</strong> Tidak perlu mengunggah foto manual. Sistem secara otomatis men-generate file sertifikat beresolusi tinggi dengan nomor seri resmi dan stempel digital Alta Hospital yang langsung tersimpan di profil anggota.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('userIdSelect');
    const titleInput = document.getElementById('titleInput');
    const certNumInput = document.getElementById('certNumberInput');
    const issueDateInput = document.getElementById('issueDateInput');
    const typeLand = document.getElementById('typeLand');
    const typeHeli = document.getElementById('typeHeli');

    const previewName = document.getElementById('previewName');
    const previewStaffId = document.getElementById('previewStaffId');
    const previewTitle = document.getElementById('previewTitle');
    const previewTypeBadge = document.getElementById('previewTypeBadge');
    const previewNumber = document.getElementById('previewNumber');
    const previewDate = document.getElementById('previewDate');

    function updatePreview() {
        const opt = userSelect.options[userSelect.selectedIndex];
        if (opt && opt.value) {
            previewName.textContent = opt.getAttribute('data-name') || opt.text;
            previewStaffId.textContent = 'ID: ' + (opt.getAttribute('data-staffid') || 'ALTA-MED') + ' • ' + (opt.getAttribute('data-role') || 'Staf');
        } else {
            previewName.textContent = '[Pilih Anggota Medis]';
            previewStaffId.textContent = 'ID: ALTA-MED • GA Operasional';
        }

        previewTitle.textContent = titleInput.value.trim() || 'Sertifikat Kelayakan Kendaraan';
        previewNumber.textContent = certNumInput.value.trim() || 'ALTA/GA/2026/AUTO';

        if (issueDateInput.value) {
            const d = new Date(issueDateInput.value);
            previewDate.textContent = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        if (typeHeli.checked) {
            previewTypeBadge.innerHTML = '<i class="fas fa-helicopter mr-1 text-sky-400"></i> Kategori: Helikopter';
        } else {
            previewTypeBadge.innerHTML = '<i class="fas fa-car mr-1 text-amber-400"></i> Kategori: Kendaraan Darat';
        }
    }

    userSelect.addEventListener('change', updatePreview);
    titleInput.addEventListener('input', updatePreview);
    certNumInput.addEventListener('input', updatePreview);
    issueDateInput.addEventListener('change', updatePreview);
    typeLand.addEventListener('change', updatePreview);
    typeHeli.addEventListener('change', updatePreview);

    updatePreview();
});
</script>
@endsection
