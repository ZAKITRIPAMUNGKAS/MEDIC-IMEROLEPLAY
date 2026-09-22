@extends('layouts.app')

@section('title', 'Direktori Sertifikat & Lisensi Resmi Seluruh Medic — Alta & Roxwood')

@section('content')
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ═══ HEADER SECTION ═══ --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-6 sm:p-8 shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500/30 to-yellow-500/30 border border-amber-400/40 text-amber-300 flex items-center justify-center text-2xl shadow-lg shrink-0">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                                Sertifikat &amp; Lisensi Resmi Medic
                            </h1>
                            <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                Seluruh RS (Alta &amp; Roxwood)
                            </span>
                        </div>
                        <p class="text-sky-200 text-sm mt-1">
                            Arsip dokumen kompetensi, sertifikasi pelatihan, dan lisensi medis terverifikasi yang dapat dipantau oleh seluruh anggota Medic.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="px-4 py-2 rounded-xl bg-black/20 border border-white/10 flex items-center gap-2.5">
                        <i class="fas fa-certificate text-amber-400 text-lg"></i>
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-sky-300/70 block font-semibold">Total Sertifikat</span>
                            <span class="text-base font-extrabold text-white">{{ $totalCerts }} Terdaftar</span>
                        </div>
                    </div>
                    <a href="{{ route('staff.members.index') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-all border border-white/20 flex items-center gap-2 shadow-sm">
                        <i class="fas fa-users text-sky-400"></i>
                        <span>Direktori Staf</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══ FILTER & SEARCH BAR ═══ --}}
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 p-5 shadow-xl space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                {{-- Hospital Tabs --}}
                <div class="flex items-center gap-2 bg-black/30 p-1 rounded-xl border border-white/10 self-start">
                    <a href="{{ route('staff.certificates.index', ['hospital' => 'all', 'division' => $division, 'batch' => $batch, 'search' => $search]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $hospital === 'all' ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow-md' : 'text-sky-200 hover:text-white' }}">
                        <i class="fas fa-globe mr-1"></i> Semua RS
                    </a>
                    <a href="{{ route('staff.certificates.index', ['hospital' => 'alta', 'division' => $division, 'batch' => $batch, 'search' => $search]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $hospital === 'alta' ? 'bg-cyan-600 text-white shadow-md' : 'text-sky-200 hover:text-white' }}">
                        <i class="fas fa-hospital mr-1 text-cyan-300"></i> Alta
                    </a>
                    <a href="{{ route('staff.certificates.index', ['hospital' => 'roxwood', 'division' => $division, 'batch' => $batch, 'search' => $search]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $hospital === 'roxwood' ? 'bg-emerald-600 text-white shadow-md' : 'text-sky-200 hover:text-white' }}">
                        <i class="fas fa-hospital-alt mr-1 text-emerald-300"></i> Roxwood
                    </a>
                </div>

                {{-- Search & Dropdowns --}}
                <form method="GET" action="{{ route('staff.certificates.index') }}" class="flex flex-wrap items-center gap-2 flex-1 lg:justify-end">
                    <input type="hidden" name="hospital" value="{{ $hospital }}">

                    {{-- Division Filter --}}
                    <div class="relative">
                        <select name="division" onchange="this.form.submit()"
                                class="bg-white/10 text-white border border-white/20 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-400 appearance-none pr-7">
                            <option value="all" class="bg-slate-800 text-slate-300">Semua Divisi</option>
                            <option value="pnd" @selected($division === 'pnd') class="bg-slate-800 text-slate-100">PND (Operasi &amp; Pelatihan)</option>
                            <option value="msl" @selected($division === 'msl') class="bg-slate-800 text-slate-100">MSL (Visum)</option>
                            <option value="ga"  @selected($division === 'ga') class="bg-slate-800 text-slate-100">GA (Kendaraan Darurat)</option>
                            <option value="ie"  @selected($division === 'ie') class="bg-slate-800 text-slate-100">IE (Kontrak Medis)</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-sky-300 text-[10px]">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>

                    {{-- Batch Filter --}}
                    <div class="relative">
                        <select name="batch" onchange="this.form.submit()"
                                class="bg-white/10 text-white border border-white/20 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-400 appearance-none pr-7">
                            <option value="" class="bg-slate-800 text-slate-300">Semua Batch</option>
                            @foreach($batches as $bKey => $bData)
                                <option value="{{ $bKey }}" @selected((string)$batch === (string)$bKey || (string)$batch === $bData['roman']) class="bg-slate-800 text-slate-100">
                                    {{ $bData['label'] }} ({{ $bData['roman'] }})
                                </option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-sky-300 text-[10px]">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-60">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari penerima / sertifikat..."
                               class="w-full bg-white/10 text-white placeholder-sky-300/60 text-xs rounded-xl pl-8 pr-3 py-2 border border-white/20 focus:outline-none focus:ring-2 focus:ring-sky-400 transition-all"
                        />
                        <div class="absolute left-2.5 top-2.5 text-sky-300/60 text-xs">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-semibold rounded-xl text-xs transition-all shadow-md">
                        Cari
                    </button>

                    @if(!empty($search) || $division !== 'all' || !empty($batch) || $hospital !== 'all')
                        <a href="{{ route('staff.certificates.index') }}" class="px-3 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs transition-all border border-white/20" title="Reset filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- ═══ CERTIFICATES GRID ═══ --}}
        @if($certifications->isEmpty())
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-2xl border border-white border-opacity-20 py-16 px-4 text-center">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/30 text-3xl">
                    <i class="fas fa-award"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Tidak Ada Sertifikat Ditemukan</h3>
                <p class="text-xs text-sky-200/60 mt-1 max-w-md mx-auto">
                    Tidak ada sertifikat resmi yang sesuai dengan filter pencarian saat ini.
                </p>
                @if(!empty($search) || $division !== 'all' || !empty($batch) || $hospital !== 'all')
                    <div class="mt-4">
                        <a href="{{ route('staff.certificates.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-500/20 text-sky-300 border border-sky-500/30 text-xs font-semibold hover:bg-sky-500/30 transition-all">
                            <i class="fas fa-sync-alt"></i> Reset Pencarian
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($certifications as $cert)
                    @php
                        $div = strtolower($cert->division ?? 'pnd');
                        $divColors = [
                            'ga'  => ['bg' => 'bg-amber-500/20', 'border' => 'border-amber-500/30', 'text' => 'text-amber-300', 'icon' => 'fa-car', 'label' => 'GA Kendaraan'],
                            'msl' => ['bg' => 'bg-teal-500/20', 'border' => 'border-teal-500/30', 'text' => 'text-teal-300', 'icon' => 'fa-stethoscope', 'label' => 'MSL Visum'],
                            'pnd' => ['bg' => 'bg-emerald-500/20', 'border' => 'border-emerald-500/30', 'text' => 'text-emerald-300', 'icon' => 'fa-award', 'label' => 'PND Operasi'],
                            'ie'  => ['bg' => 'bg-sky-500/20', 'border' => 'border-sky-500/30', 'text' => 'text-sky-300', 'icon' => 'fa-file-contract', 'label' => 'IE Kontrak'],
                        ];
                        $badge = $divColors[$div] ?? $divColors['pnd'];
                        $fileUrl = route('portal.cert.image', $cert->id);
                        $certUser = $cert->user;
                    @endphp
                    <div class="group bg-white/10 backdrop-blur-md hover:bg-white/15 border border-white/20 hover:border-amber-400/50 rounded-2xl overflow-hidden shadow-xl transition-all duration-300 flex flex-col justify-between">
                        
                        <div>
                            {{-- Image Preview with Lightbox trigger --}}
                            <div class="relative aspect-[1.41/1] bg-slate-950/80 overflow-hidden cursor-pointer"
                                 onclick="openCertPreview('{{ $fileUrl }}', '{{ addslashes($cert->title) }}', '{{ $cert->certificate_number ?? '-' }}', '{{ $cert->issue_date ? $cert->issue_date->translatedFormat('d F Y') : '-' }}', '{{ addslashes($cert->issuedBy?->name ?? 'Alta Hospital') }}')">
                                <img src="{{ $fileUrl }}" alt="{{ $cert->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2">
                                    <span class="px-3 py-1.5 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-semibold flex items-center gap-1.5 shadow-lg">
                                        <i class="fas fa-search-plus"></i> Lihat Foto Penuh
                                    </span>
                                </div>

                                {{-- Division Tag Pill --}}
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider backdrop-blur-md {{ $badge['bg'] }} {{ $badge['border'] }} {{ $badge['text'] }} border shadow-md">
                                        <i class="fas {{ $badge['icon'] }}"></i> {{ $badge['label'] }}
                                    </span>
                                </div>

                                {{-- Hospital Tag --}}
                                @if($certUser)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $certUser->hospital === 'roxwood' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' }} backdrop-blur-md">
                                        {{ $certUser->hospital === 'roxwood' ? 'Roxwood' : 'Alta' }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            {{-- Card Header & Recipient --}}
                            <div class="p-5 space-y-3">
                                <div>
                                    <h3 class="text-white font-bold text-base tracking-wide line-clamp-1 group-hover:text-amber-300 transition-colors" title="{{ $cert->title }}">
                                        {{ $cert->title }}
                                    </h3>
                                    <div class="flex items-center justify-between text-xs text-sky-200/60 mt-1 font-mono">
                                        <span>{{ $cert->certificate_number ?? '-' }}</span>
                                        <span>{{ $cert->issue_date ? $cert->issue_date->translatedFormat('d/m/Y') : '-' }}</span>
                                    </div>
                                </div>

                                @if($cert->notes)
                                    <p class="text-xs text-white/70 line-clamp-2 italic bg-black/20 p-2 rounded-lg border border-white/5">
                                        "{{ $cert->notes }}"
                                    </p>
                                @endif

                                {{-- Penerima Sertifikat (User Info) --}}
                                <div class="pt-2 border-t border-white/10 flex items-center justify-between gap-3">
                                    @if($certUser)
                                        <a href="{{ route('staff.members.show', $certUser->id) }}" class="flex items-center gap-2.5 min-w-0 group/u">
                                            <img src="{{ $certUser->profile_image_url }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('profile.jpg') }}';"
                                                 class="w-9 h-9 rounded-xl object-cover border border-white/20 shadow-sm shrink-0 group-hover/u:scale-105 transition-transform"
                                                 alt="{{ $certUser->name }}">
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-white truncate group-hover/u:text-sky-300 transition-colors">
                                                    {{ $certUser->name }}
                                                </div>
                                                <div class="text-[10px] text-sky-200/60 flex items-center gap-1.5">
                                                    <span>{{ $certUser->role->display_name ?? 'Staff' }}</span>
                                                    @if($certUser->batch)
                                                        <span>•</span>
                                                        <span class="font-bold text-amber-300">{{ $certUser->batch_info['roman'] ?? $certUser->batch }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <span class="text-xs text-white/40 italic">Anggota Medis</span>
                                    @endif

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button"
                                                onclick="openCertPreview('{{ $fileUrl }}', '{{ addslashes($cert->title) }}', '{{ $cert->certificate_number ?? '-' }}', '{{ $cert->issue_date ? $cert->issue_date->translatedFormat('d F Y') : '-' }}', '{{ addslashes($cert->issuedBy?->name ?? 'Alta Hospital') }}')"
                                                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-sky-300 flex items-center justify-center transition-all text-xs border border-white/10"
                                                title="Lihat Detail Sertifikat">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ $fileUrl }}" download="sertifikat_{{ $cert->id }}.svg"
                                           class="w-8 h-8 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 flex items-center justify-center transition-all text-xs border border-amber-500/30"
                                           title="Unduh Berkas Gambar (SVG)">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $certifications->links() }}
            </div>
        @endif

    </div>
</div>

{{-- ═══ LIGHTBOX MODAL PREVIEW FOTO SERTIFIKAT ═══ --}}
<div id="certModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md hidden opacity-0 transition-opacity duration-300">
    <div class="relative max-w-4xl w-full bg-slate-900 border border-white/20 rounded-2xl shadow-2xl p-4 sm:p-6 scale-95 transition-transform duration-300" id="certModalContent">
        
        {{-- Header Modal --}}
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/10">
            <div>
                <h3 id="modalCertTitle" class="text-base sm:text-lg font-bold text-white leading-tight">Sertifikat Resmi</h3>
                <p id="modalCertMeta" class="text-xs text-sky-200/70 mt-0.5">Memuat data sertifikat...</p>
            </div>
            <button type="button" onclick="closeCertPreview()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Preview Foto Sertifikat --}}
        <div class="relative aspect-[1.41/1] w-full max-h-[70vh] bg-slate-950 rounded-xl overflow-hidden border border-white/10 flex items-center justify-center">
            <img id="modalCertImage" src="" alt="Pratinjau Sertifikat" class="w-full h-full object-contain">
        </div>

        {{-- Footer Modal --}}
        <div class="flex items-center justify-between pt-4 mt-3 border-t border-white/10">
            <div class="text-[11px] text-white/50">
                <i class="fas fa-shield-alt text-amber-400 mr-1"></i> Terverifikasi Digital oleh Medic Center
            </div>
            <div class="flex items-center gap-2">
                <a id="modalDownloadBtn" href="#" download="sertifikat.svg"
                   class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-slate-950 text-xs font-bold transition-all flex items-center gap-1.5 shadow-lg">
                    <i class="fas fa-download"></i> Unduh Foto Asli
                </a>
                <button type="button" onclick="closeCertPreview()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-medium transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openCertPreview(imgUrl, title, number, issueDate, issuer) {
    document.getElementById('modalCertTitle').textContent = title || 'Sertifikat Resmi Medic';
    document.getElementById('modalCertMeta').textContent = 'No. ' + number + ' • Diterbitkan: ' + issueDate + ' • Oleh: ' + issuer;
    document.getElementById('modalCertImage').src = imgUrl;
    document.getElementById('modalDownloadBtn').href = imgUrl;

    const modal = document.getElementById('certModal');
    const content = document.getElementById('certModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeCertPreview() {
    const modal = document.getElementById('certModal');
    const content = document.getElementById('certModalContent');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('modalCertImage').src = '';
    }, 200);
}

document.getElementById('certModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCertPreview();
});
</script>
@endsection
