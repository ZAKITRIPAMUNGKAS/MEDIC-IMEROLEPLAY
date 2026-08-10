@extends('layouts.app')

@section('title', 'Detail Rekam Operasi Medis - Portal Medis')

@push('styles')
<style>
@media print {
    /* === PAGE SETUP === */
    @page {
        size: A4 portrait;
        margin: 12mm 15mm 12mm 15mm;
    }

    /* === HIDE NON-ESSENTIAL ELEMENTS === */
    header, footer, nav, .navbar, .sidebar, button,
    .print\:hidden, a[href*="index"], #chat-widget, .chat-widget,
    [id*="chat"], [class*="chat"], [id*="widget"], [class*="widget"],
    .fixed, .sticky, [class*="fixed"], [class*="floating"],
    [id*="live"], [class*="live-chat"] {
        display: none !important;
    }

    /* === BASE RESET === */
    *, *::before, *::after {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    html, body {
        background: #ffffff !important;
        color: #1e293b !important;
        font-family: 'Segoe UI', Arial, sans-serif !important;
        font-size: 8.5pt !important;
        line-height: 1.35 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .min-h-screen {
        min-height: auto !important;
        padding: 0 !important;
        background: transparent !important;
    }

    .max-w-7xl {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* === GRID LAYOUT === */
    .grid {
        display: grid !important;
    }

    .grid-cols-1.lg\:grid-cols-3 {
        grid-template-columns: 2fr 1fr !important;
        gap: 8px !important;
    }

    .lg\:col-span-2 {
        grid-column: span 1 !important;
    }

    .grid-cols-2, .sm\:grid-cols-2 {
        grid-template-columns: 1fr 1fr !important;
        gap: 4px !important;
    }

    .grid-cols-2.sm\:grid-cols-3, .sm\:grid-cols-3 {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 4px !important;
    }

    /* === SPACING === */
    .space-y-6 > * + * { margin-top: 7px !important; }
    .space-y-4 > * + * { margin-top: 5px !important; }
    .space-y-3 > * + * { margin-top: 4px !important; }
    .space-y-2 > * + * { margin-top: 3px !important; }
    .mb-8, .mb-6 { margin-bottom: 8px !important; }
    .mb-5, .mb-4 { margin-bottom: 5px !important; }
    .p-5 { padding: 7px 8px !important; }
    .p-4 { padding: 5px 7px !important; }
    .p-3, .p-3\.5 { padding: 4px 6px !important; }
    .px-6 { padding-left: 8px !important; padding-right: 8px !important; }
    .py-3\.5 { padding-top: 4px !important; padding-bottom: 4px !important; }
    .py-4, .py-3 { padding-top: 5px !important; padding-bottom: 5px !important; }
    .gap-6 { gap: 8px !important; }
    .gap-4 { gap: 5px !important; }
    .gap-3 { gap: 4px !important; }
    .gap-2 { gap: 3px !important; }

    /* === CARD STYLES (glassmorphism → clean white) === */
    [class*="bg-white\/"], [class*="bg-white\\/"] {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    [class*="bg-white\/5"], [class*="bg-black\/"] {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    [class*="bg-sky-"], [class*="bg-emerald-"], [class*="bg-amber-"],
    [class*="bg-red-"], [class*="bg-blue-"], [class*="bg-indigo-"],
    [class*="bg-purple-"] {
        background: #f1f5f9 !important;
        border-color: #94a3b8 !important;
    }

    /* Card header rows */
    [style*="rgba(14,165,233"] {
        background: #e0f2fe !important;
        border-bottom: 1px solid #bae6fd !important;
    }

    /* === TEXT COLOR OVERRIDES === */
    .text-white, .text-sky-100, .text-sky-200,
    .text-emerald-200, .text-amber-200 {
        color: #1e293b !important;
    }

    .text-sky-300, .text-sky-400, .text-sky-500 {
        color: #0369a1 !important;
        font-weight: 600 !important;
    }

    .text-emerald-300, .text-emerald-400 {
        color: #065f46 !important;
        font-weight: 600 !important;
    }

    .text-red-300, .text-red-400 { color: #991b1b !important; }
    .text-amber-200, .text-amber-300 { color: #92400e !important; }

    h1, h2, h3, h4, strong, b {
        color: #0f172a !important;
    }

    /* === BORDERS & RADIUS === */
    .rounded-2xl, .rounded-xl, .rounded-lg, .rounded-md {
        border-radius: 4px !important;
    }

    /* === BADGE / TAG === */
    [class*="inline-flex"][class*="rounded"] {
        background: #f1f5f9 !important;
        color: #1e293b !important;
        border: 1px solid #94a3b8 !important;
    }

    /* === PHOTO GRID (small thumbnails for print) === */
    .grid.grid-cols-2.sm\:grid-cols-3 a img {
        height: 60px !important;
        object-fit: cover !important;
    }

    /* === PAGE BREAK CONTROL === */
    .bg-white\/10 {
        page-break-inside: avoid !important;
    }

    /* === FOOTER WATERMARK === */
    body::after {
        content: "Dokumen Rekam Medis Resmi — Dihasilkan oleh Sistem Portal Medis";
        display: block;
        position: fixed;
        bottom: 8mm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 7pt;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 3px;
    }
}

</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        {{-- Back & Print Bar --}}
        <div class="mb-6 flex justify-between items-center print:hidden">
            <a href="{{ route('staff.operations.index') }}"
               class="inline-flex items-center text-sky-300 hover:text-white transition-colors duration-200 font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Rekam Operasi
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white border border-sky-400/30 rounded-xl text-sm font-bold flex items-center gap-2 transition shadow-lg">
                <i class="fas fa-file-pdf"></i> Cetak / Export PDF Laporan
            </button>
        </div>

        {{-- Header --}}
        @php
            $opHospital = strtolower(trim($operation->hospital ?? 'roxwood'));
            $hospitalName = ($opHospital === 'alta') ? 'ALTA MEDICAL CENTER' : 'ROXWOOD MEDICAL CENTER';
            $hospitalLogo = ($opHospital === 'alta') ? asset('images/logo_ems.webp') : asset('images/motionlife-logo.png');
        @endphp

        {{-- Print Kop Surat / Logo RS (Hanya tampil saat Print/Export PDF) --}}
        <div class="hidden print:block mb-6 border-b-2 border-slate-800 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="{{ $hospitalLogo }}" alt="Logo {{ $hospitalName }}" class="h-16 w-16 object-contain">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-wide uppercase">{{ $hospitalName }}</h1>
                        <p class="text-xs text-slate-600 font-medium">Laporan Rekam Operasi & Pelayanan Medis Terpadu</p>
                        <p class="text-[10px] text-slate-500">Official Medical Record Document</p>
                    </div>
                </div>
                <div class="text-right text-xs text-slate-600">
                    <span class="inline-block px-3 py-1 bg-slate-100 border border-slate-300 font-bold text-slate-800 rounded">
                        {{ $operation->jenis_operasi }}
                    </span>
                    <div class="mt-1 text-[11px] font-semibold text-slate-700">{{ $operation->tanggal_waktu->format('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 print:hidden">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-file-medical-alt mr-3 text-sky-400"></i>Laporan Rekam Operasi Medis
                </h1>
                <p class="text-sky-200">{{ $operation->tanggal_waktu->format('l, d F Y - H:i') }} WIB | Ruang: {{ $operation->lokasi }}</p>
            </div>
            @php
                $badgeColors = [
                    'Operasi Mayor'           => 'from-red-500 to-pink-500',
                    'Operasi Minor'           => 'from-yellow-500 to-orange-500',
                    'Emergency'               => 'from-red-600 to-red-700',
                    'Konsultasi Spesialisasi' => 'from-blue-500 to-indigo-500',
                ];
                $badgeColor = $badgeColors[$operation->jenis_operasi] ?? 'from-slate-500 to-gray-600';
                $med = $operation->medical_details ?? [];
            @endphp
            <div class="flex items-center gap-2 flex-wrap">
                @if(isset($canEdit) && $canEdit)
                    <a href="{{ route('staff.operations.edit', $operation->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 transition-all shadow-lg shadow-amber-950/30">
                        <i class="fas fa-edit mr-1.5"></i>Lengkapi / Edit
                    </a>
                @endif

                {{-- Copy Text Template Button --}}
                <button type="button" onclick="openWordModal()" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-sky-100 bg-sky-600/40 hover:bg-sky-500/50 border border-sky-400/40 transition-all shadow-md">
                    <i class="fas fa-copy mr-1.5 text-sky-300"></i>Format Teks Word
                </button>

                {{-- Export Word Button --}}
                <button type="button" onclick="exportToWord()" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-emerald-100 bg-emerald-600/40 hover:bg-emerald-500/50 border border-emerald-400/40 transition-all shadow-md">
                    <i class="fas fa-file-word mr-1.5 text-emerald-300"></i>Export Word (.doc)
                </button>

                {{-- Print Button --}}
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-600 transition-all shadow-md">
                    <i class="fas fa-print mr-1.5"></i>Cetak
                </button>

                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r {{ $badgeColor }} shadow-lg">
                    <i class="fas fa-tag mr-1.5"></i>{{ $operation->jenis_operasi }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column: Complete Medical Details --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card 1: Identitas Pasien --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10 flex items-center justify-between" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-user-injured text-sky-400"></i> IDENTITAS PASIEN
                        </h2>
                    </div>
                    <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-xs text-sky-300 block font-semibold">Nama Pasien</span>
                            <span class="text-white font-bold text-base">{{ $operation->nama_pasien }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-xs text-sky-300 block font-semibold">Date of Birth (DOB)</span>
                            <span class="text-white font-semibold">{{ $med['pasien']['dob'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-xs text-sky-300 block font-semibold">Jenis Kelamin</span>
                            <span class="text-white font-semibold">{{ $med['pasien']['jenis_kelamin'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-xs text-sky-300 block font-semibold">Golongan Darah</span>
                            <span class="text-red-300 font-bold">{{ $med['pasien']['gol_darah'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-xs text-sky-300 block font-semibold">No. HP / Telepon</span>
                            <span class="text-white font-semibold">{{ $med['pasien']['no_hp'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-xs text-sky-300 block font-semibold">Citizen ID / KTP</span>
                            <span class="text-sky-300 font-mono font-bold">{{ $med['pasien']['citizen_id'] ?? ($med['pasien']['alamat'] ?? '-') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Anamnesis & Riwayat Kesehatan --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-notes-medical text-sky-400"></i> ANAMNESIS & RIWAYAT KESEHATAN
                        </h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <span class="text-xs text-sky-300 font-bold uppercase tracking-wider block mb-1">Anamnesis / Diagnosa Utama</span>
                            <div class="bg-white/5 p-3.5 rounded-xl text-white text-sm leading-relaxed border border-white/10">
                                {!! nl2br(e($operation->diagnosa)) !!}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Riwayat Penyakit Dahulu</span>
                                <span class="text-white">{!! nl2br(e($med['anamnesis']['riwayat_penyakit_dahulu'] ?? '-')) !!}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Riwayat Penyakit Keluarga</span>
                                <span class="text-white">{!! nl2br(e($med['anamnesis']['riwayat_penyakit_keluarga'] ?? '-')) !!}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Riwayat Alergi</span>
                                <span class="text-amber-200">{!! nl2br(e($med['anamnesis']['riwayat_alergi'] ?? '-')) !!}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Riwayat Pengobatan</span>
                                <span class="text-white">{!! nl2br(e($med['anamnesis']['riwayat_pengobatan'] ?? '-')) !!}</span>
                            </div>
                        </div>

                        @if(!empty($med['obstetri']) && array_filter($med['obstetri']))
                        <div class="pt-3 border-t border-white/10">
                            <span class="text-xs text-sky-300 font-bold uppercase tracking-wider block mb-2">
                                <i class="fas fa-baby mr-1"></i> Data Obstetri / Kebidanan
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                                <div class="bg-white/5 p-2.5 rounded-xl">
                                    <span class="text-sky-300 text-[11px] block">HPHT</span>
                                    <span class="text-white font-semibold">{{ $med['obstetri']['hpht'] ?? '-' }}</span>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl">
                                    <span class="text-sky-300 text-[11px] block">Usia Kehamilan</span>
                                    <span class="text-white font-semibold">{{ $med['obstetri']['usia_kehamilan'] ?? '-' }}</span>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl">
                                    <span class="text-sky-300 text-[11px] block">Status G/P/A</span>
                                    <span class="text-white font-semibold">{{ $med['obstetri']['gpa'] ?? '-' }}</span>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl">
                                    <span class="text-sky-300 text-[11px] block">DJJ (Denyut Jantung Janin)</span>
                                    <span class="text-white font-semibold">{{ $med['obstetri']['djj'] ?? '-' }}</span>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl">
                                    <span class="text-sky-300 text-[11px] block">Presentasi Janin</span>
                                    <span class="text-white font-semibold">{{ $med['obstetri']['presentasi'] ?? '-' }}</span>
                                </div>
                                <div class="bg-white/5 p-2.5 rounded-xl">
                                    <span class="text-sky-300 text-[11px] block">TFU</span>
                                    <span class="text-white font-semibold">{{ $med['obstetri']['tfu'] ?? '-' }}</span>
                                </div>
                                @if(!empty($med['obstetri']['catatan_obstetri']))
                                <div class="bg-white/5 p-2.5 rounded-xl col-span-2 sm:col-span-3">
                                    <span class="text-sky-300 text-[11px] block">Catatan Obstetri</span>
                                    <span class="text-white">{!! nl2br(e($med['obstetri']['catatan_obstetri'])) !!}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Card 3: Pemeriksaan Fisik & TTV --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-heartbeat text-sky-400"></i> PEMERIKSAAN FISIK & TTV (TANDA VITAL)
                        </h2>
                    </div>
                    <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Keadaan Umum</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['keadaan_umum'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Kesadaran (GCS)</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['gcs'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Tekanan Darah</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['tekanan_darah'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Nadi</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['nadi'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Respirasi (RR)</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['respirasi'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Suhu Body</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['suhu'] ?? '-' }}</span>
                        </div>
                        <div class="bg-white/5 p-3 rounded-xl">
                            <span class="text-sky-300 block mb-1">Saturasi O2</span>
                            <span class="text-white font-bold text-sm">{{ $med['ttv']['saturasi'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Tindakan Operasi & Prosedur --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-procedures text-sky-400"></i> TINDAKAN / OPERASI
                        </h2>
                    </div>
                    <div class="p-5 space-y-4 text-sm">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-white/5 p-3 rounded-xl sm:col-span-1">
                                <span class="text-xs text-sky-300 block font-semibold">Nama Tindakan</span>
                                <span class="text-white font-bold text-base">{{ $operation->tindakan_operasi }}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-xs text-sky-300 block font-semibold">Waktu Mulai</span>
                                <span class="text-white font-semibold">{{ $med['tindakan']['waktu_mulai'] ?? '-' }} WIB</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-xs text-sky-300 block font-semibold">Waktu Selesai</span>
                                <span class="text-white font-semibold">{{ $med['tindakan']['waktu_selesai'] ?? '-' }} WIB</span>
                            </div>
                        </div>

                        @if(!empty($med['tindakan']['langkah_tindakan']))
                        <div>
                            <span class="text-xs text-sky-300 font-bold uppercase tracking-wider block mb-1">Langkah-Langkah Tindakan</span>
                            <div class="bg-white/5 p-3.5 rounded-xl text-white leading-relaxed font-mono text-xs border border-white/10 whitespace-pre-line">
                                {{ $med['tindakan']['langkah_tindakan'] }}
                            </div>
                        </div>
                        @endif

                        <div>
                            <span class="text-xs text-sky-300 font-bold uppercase tracking-wider block mb-1">Hasil Operasi</span>
                            <div class="bg-white/5 p-3.5 rounded-xl text-white leading-relaxed border border-white/10">
                                {!! nl2br(e($operation->hasil_operasi)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Table Score Anestesi & Obat-Obatan --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-pills text-sky-400"></i> MANAJEMEN ANESTESI & TABLE SCORE
                        </h2>
                    </div>
                    <div class="p-5 space-y-4 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 block mb-1 font-semibold">Jenis Anestesi</span>
                                <span class="text-white font-bold text-sm">{{ $med['anestesi']['jenis_anestesi'] ?? 'Anestesi Umum' }}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 block mb-1 font-semibold">Petugas Anestesi</span>
                                <span class="text-white font-bold text-sm">{{ $med['tim']['anestesi'] ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Obat Pra-Operasi (Induksi/Analgesik/Relaksan)</span>
                                <span class="text-white whitespace-pre-line">{!! nl2br(e($med['anestesi']['pra_operasi'] ?? '-')) !!}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Obat Pasca-Operasi (Antidote/Anti Mual/Analgesik)</span>
                                <span class="text-white whitespace-pre-line">{!! nl2br(e($med['anestesi']['pasca_operasi'] ?? '-')) !!}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-sky-300 font-bold uppercase tracking-wider block mb-2">Score Pemulihan Pasca Anestesi (Aldrete Score)</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-white/5 p-3.5 rounded-xl border border-white/10">
                                <div><span class="text-sky-400">Kesadaran:</span> <span class="text-white font-semibold">{{ $med['anestesi']['score_kesadaran'] ?? '-' }}</span></div>
                                <div><span class="text-sky-400">Mual/Muntah:</span> <span class="text-white font-semibold">{{ $med['anestesi']['score_mual'] ?? '-' }}</span></div>
                                <div><span class="text-sky-400">Pernapasan:</span> <span class="text-white font-semibold">{{ $med['anestesi']['score_pernapasan'] ?? '-' }}</span></div>
                                <div><span class="text-sky-400">Motorik:</span> <span class="text-white font-semibold">{{ $med['anestesi']['score_motorik'] ?? '-' }}</span></div>
                                <div><span class="text-sky-400">Tekanan Darah:</span> <span class="text-white font-semibold">{{ $med['anestesi']['score_td'] ?? '-' }}</span></div>
                                <div><span class="text-sky-400">Warna Kulit:</span> <span class="text-white font-semibold">{{ $med['anestesi']['score_warna_kulit'] ?? '-' }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 6: Penunjang, Obat-Obatan Pulang & Saran --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-microscope text-sky-400"></i> PENUNJANG, OBAT & SARAN ANJURAN
                        </h2>
                    </div>
                    <div class="p-5 space-y-3 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Hasil Laboratorium</span>
                                <span class="text-white">{!! nl2br(e($med['penunjang']['lab'] ?? '-')) !!}</span>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <span class="text-sky-300 font-semibold block mb-1">Hasil Radiologi / X-Ray</span>
                                <span class="text-white">{!! nl2br(e($med['penunjang']['radiologi'] ?? '-')) !!}</span>
                            </div>
                        </div>

                        <div class="bg-white/5 p-3.5 rounded-xl">
                            <span class="text-sky-300 font-bold uppercase block mb-1">Obat-Obatan Post Operasi</span>
                            <div class="text-white whitespace-pre-line text-sm">{!! nl2br(e($med['obat_obatan'] ?? '-')) !!}</div>
                        </div>

                        <div class="bg-white/5 p-3.5 rounded-xl">
                            <span class="text-sky-300 font-bold uppercase block mb-1">Saran dan Anjuran Dokter</span>
                            <div class="text-emerald-300 whitespace-pre-line text-sm">{!! nl2br(e($med['saran_anjuran'] ?? '-')) !!}</div>
                        </div>

                        @if($operation->catatan)
                        <div class="bg-amber-500/10 border border-amber-500/20 p-3.5 rounded-xl text-amber-200">
                            <span class="font-bold block mb-1">Catatan Tambahan:</span>
                            {!! nl2br(e($operation->catatan)) !!}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Foto Dokumentasi --}}
                @if($operation->photos->count() > 0)
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-3.5 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-images text-sky-400"></i> FOTO DOKUMENTASI OPERASI
                            <span class="ml-auto text-xs text-sky-300">{{ $operation->photos->count() }} Foto</span>
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($operation->photos as $photo)
                            @php
                                // file_path = "uploads/operations/filename.jpg"
                                // Langsung pakai asset() karena sudah dalam folder public/
                                $photoUrl = asset($photo->file_path);
                            @endphp
                            <a href="{{ $photoUrl }}" target="_blank"
                               class="block rounded-xl overflow-hidden border border-white/20 hover:border-sky-400 transition transform hover:scale-105 shadow-md bg-black/20">
                                <img src="{{ $photoUrl }}" class="w-full h-32 object-cover" alt="Foto Dokumentasi"
                                     onError="this.onerror=null; this.src=''; this.style.opacity='0.3';">
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif


            </div>

            {{-- Right Column: Tim Medis & Poin Duty Tracking --}}
            <div class="space-y-6">

                {{-- Tim Medis Card --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/10" style="background: rgba(14,165,233,0.15);">
                        <h2 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-user-md mr-2 text-sky-400"></i>Tim Operasi Medis
                            <span class="ml-auto px-2.5 py-0.5 bg-sky-500/20 text-sky-300 text-xs rounded-full">{{ $operation->members->count() }} Anggota</span>
                        </h2>
                    </div>

                    @php
                        $basePoinMapTim = [
                            'Operasi Mayor'           => 60,
                            'Operasi Minor'           => 30,
                            'Emergency'               => 25,
                            'Konsultasi Spesialisasi' => 40,
                        ];
                        $basePoinTim  = $basePoinMapTim[$operation->jenis_operasi] ?? 15;
                        $dpjpPoinTim  = $basePoinTim + 20;

                        // DPJP hanya berlaku untuk Operasi Mayor & Operasi Minor
                        $dpjpEnabled = in_array($operation->jenis_operasi, ['Operasi Mayor', 'Operasi Minor']);

                        if ($dpjpEnabled && $operation->dpjp_id && $operation->dpjp) {
                            $dpjpMember = $operation->dpjp;
                        } elseif ($dpjpEnabled) {
                            $dpjpMember = $operation->members->first();
                        } else {
                            $dpjpMember = null; // DPJP tidak berlaku
                        }
                    @endphp

                    {{-- DPJP Card — hanya muncul untuk Operasi Mayor & Minor --}}
                    @if($dpjpEnabled && $dpjpMember)
                    <div class="mx-4 mt-4 rounded-2xl overflow-hidden border border-emerald-500/40" style="background: linear-gradient(135deg, rgba(16,185,129,0.18) 0%, rgba(5,150,105,0.12) 100%);">
                        <div class="flex items-center gap-2 px-4 py-2 border-b border-emerald-500/20" style="background: rgba(16,185,129,0.15);">
                            <i class="fas fa-user-md text-emerald-400 text-sm"></i>
                            <span class="text-emerald-300 font-bold text-xs uppercase tracking-widest">DPJP (Penanggung Jawab)</span>
                        </div>
                        <div class="flex items-center gap-4 px-4 py-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black text-lg shadow-lg flex-shrink-0">
                                {{ substr($dpjpMember->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-white font-bold text-base truncate">{{ $dpjpMember->name }}</div>
                                <div class="text-emerald-300 text-xs">{{ $dpjpMember->role->name ?? 'Dokter' }}</div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-xl font-black text-emerald-300">+{{ $dpjpPoinTim }}</div>
                                <div class="text-[9px] text-emerald-400 uppercase">Poin DPJP</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Rincian Tim Medis Khusus --}}
                    @if(!empty($med['tim']))
                    <div class="p-4 space-y-2 text-xs border-b border-white/10">
                        <div class="text-sky-300 font-bold uppercase tracking-wider text-[11px] mb-1">Peran Tim Spesifik:</div>
                        @if(!empty($med['tim']['first_responder']))<div class="flex justify-between text-white bg-white/5 p-2 rounded-lg"><span>First Responder:</span><strong class="text-sky-300">{{ $med['tim']['first_responder'] }}</strong></div>@endif
                        @if(!empty($med['tim']['anestesi']))<div class="flex justify-between text-white bg-white/5 p-2 rounded-lg"><span>Petugas Anestesi:</span><strong class="text-sky-300">{{ $med['tim']['anestesi'] }}</strong></div>@endif
                        @if(!empty($med['tim']['radiologi']))<div class="flex justify-between text-white bg-white/5 p-2 rounded-lg"><span>Petugas Radiologi:</span><strong class="text-sky-300">{{ $med['tim']['radiologi'] }}</strong></div>@endif
                        @if(!empty($med['tim']['asisten_1']))<div class="flex justify-between text-white bg-white/5 p-2 rounded-lg"><span>Asisten 1:</span><strong class="text-sky-300">{{ $med['tim']['asisten_1'] }}</strong></div>@endif
                        @if(!empty($med['tim']['asisten_2']))<div class="flex justify-between text-white bg-white/5 p-2 rounded-lg"><span>Asisten 2:</span><strong class="text-sky-300">{{ $med['tim']['asisten_2'] }}</strong></div>@endif
                    </div>
                    @endif

                    {{-- Members List --}}
                    <div class="p-4 space-y-2">
                        <div class="text-sky-300 font-bold uppercase tracking-wider text-[11px] mb-2">Seluruh Anggota Bertugas:</div>
                        @foreach($operation->members as $index => $member)
                        @php
                            $isDpjp = $dpjpEnabled && ($dpjpMember && $dpjpMember->id == $member->id);
                            $memberPoin = $isDpjp ? $dpjpPoinTim : $basePoinTim;
                        @endphp
                        <div class="flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded-xl px-3 py-2.5 transition border border-white/5">
                            <div class="w-8 h-8 rounded-lg {{ $isDpjp ? 'bg-emerald-500' : 'bg-sky-500' }} flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0 text-xs">
                                <div class="text-white font-semibold truncate">{{ $member->name }}</div>
                                <div class="text-sky-300 text-[10px]">{{ $member->role->name ?? 'Staff' }}</div>
                            </div>
                            <span class="text-xs font-bold {{ $isDpjp ? 'text-emerald-400' : 'text-sky-400' }}">
                                +{{ $memberPoin }} Poin
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Admin / CEO / Executive --}}
                @php
                    $uRole = strtolower(trim(auth()->user()->role->name ?? ''));
                    $canDeleteRecord = auth()->user()->isAdmin() || in_array($uRole, ['ceo', 'executive', 'direktur', 'high_command']);
                @endphp
                @if($canDeleteRecord)
                <div class="bg-red-500/10 backdrop-blur-md rounded-2xl border border-red-500/20 p-5 text-center print:hidden">
                    <form action="{{ route('admin.operations.destroy', $operation->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekam operasi medis ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-trash-alt"></i> Hapus Rekam Operasi Ini
                        </button>
                    </form>
                </div>
                @endif

            </div>

        </div>

    </div>
</div>

{{-- MODAL FORMAT TEKS WORD --}}
<div id="wordModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-4 flex items-center justify-center print:hidden">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-3xl w-full p-6 shadow-2xl space-y-4 relative">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-word text-sky-400"></i> Format Teks Rekam Operasi (Siap Copy ke Word)
            </h3>
            <button type="button" onclick="closeWordModal()" class="text-slate-400 hover:text-white transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="space-y-2">
            <p class="text-xs text-sky-300">
                Klik <strong>"Salin Seluruh Teks"</strong> di bawah untuk langsung menempelkan (Paste/Ctrl+V) ke Microsoft Word atau Google Docs.
            </p>
            <textarea id="wordTextarea" rows="16" class="w-full bg-slate-950 border border-slate-800 text-slate-200 text-xs font-mono rounded-xl p-4 focus:outline-none focus:ring-2 focus:ring-sky-500 selection:bg-sky-500 selection:text-white" readonly>@php
$dpjpName = $dpjpMember ? $dpjpMember->name : '-';
$allMembersList = $operation->members->pluck('name')->implode(', ');
$timMedis = $med['tim'] ?? [];
$firstResp = $timMedis['first_responder'] ?? '-';
$anestesiStaf = $timMedis['anestesi'] ?? '-';
$radiologiStaf = $timMedis['radiologi'] ?? '-';
$asisten1 = $timMedis['asisten_1'] ?? '-';
$asisten2 = $timMedis['asisten_2'] ?? '-';

$pasien = $med['pasien'] ?? [];
$dob = $pasien['dob'] ?? '-';
$jk = $pasien['jenis_kelamin'] ?? '-';
$golDarah = $pasien['gol_darah'] ?? '-';
$noHp = $pasien['no_hp'] ?? '-';
$citizenId = $pasien['citizen_id'] ?? '-';

$anamnesis = $med['anamnesis'] ?? [];
$rpd = $anamnesis['riwayat_penyakit_dahulu'] ?? '-';
$rpk = $anamnesis['riwayat_penyakit_keluarga'] ?? '-';
$alergi = $anamnesis['riwayat_alergi'] ?? '-';
$pengobatan = $anamnesis['riwayat_pengobatan'] ?? '-';

$ttv = $med['ttv'] ?? [];
$ku = $ttv['keadaan_umum'] ?? '-';
$gcs = $ttv['gcs'] ?? '-';
$td = $ttv['tekanan_darah'] ?? '-';
$nadi = $ttv['nadi'] ?? '-';
$rr = $ttv['respirasi'] ?? '-';
$suhu = $ttv['suhu'] ?? '-';
$saturasi = $ttv['saturasi'] ?? '-';

$tindakan = $med['tindakan'] ?? [];
$waktuMulai = $tindakan['waktu_mulai'] ?? '-';
$waktuSelesai = $tindakan['waktu_selesai'] ?? '-';
$langkahTindakan = $tindakan['langkah_tindakan'] ?? '-';

$anestesi = $med['anestesi'] ?? [];
$jenisAnestesi = $anestesi['jenis_anestesi'] ?? $anestesi['jenis'] ?? '-';
$teknikAnestesi = $anestesi['teknik'] ?? '-';
$asaScore = $anestesi['asa_score'] ?? '-';
$praOp = $anestesi['pra_operasi'] ?? '-';
$pascaOp = $anestesi['pasca_operasi'] ?? '-';
$scoreKesadaran = $anestesi['score_kesadaran'] ?? '-';
$scoreMual = $anestesi['score_mual'] ?? '-';
$scorePernapasan = $anestesi['score_pernapasan'] ?? '-';
$scoreMotorik = $anestesi['score_motorik'] ?? '-';
$scoreTD = $anestesi['score_td'] ?? '-';
$scoreWarnaKulit = $anestesi['score_warna_kulit'] ?? '-';

$penunjang = $med['penunjang'] ?? [];
$lab = $penunjang['lab'] ?? '-';
$radiologi = $penunjang['radiologi'] ?? '-';
$pemeriksaanRadiologi = $penunjang['pemeriksaan_radiologi'] ?? '-';
$obatObatan = $med['obat_obatan'] ?? '-';
$saranAnjuran = $med['saran_anjuran'] ?? '-';
$catatan = $operation->catatan ?? '-';
@endphp================================================================================
LAPORAN REKAM OPERASI & PELAYANAN MEDIS TERPADU
{{ strtoupper($hospitalName) }}
================================================================================

I. KLASIFIKASI OPERASI & DATA UTAMA
- Jenis Operasi     : {{ $operation->jenis_operasi }}
- Tanggal & Waktu   : {{ $operation->tanggal_waktu->format('d F Y - H:i') }} WIB
- Ruangan / Lokasi  : {{ $operation->lokasi }}

II. IDENTITAS PASIEN
- Nama Pasien       : {{ $operation->nama_pasien }}
- Tanggal Lahir (DOB): {{ $dob }}
- Jenis Kelamin     : {{ $jk }}
- Golongan Darah    : {{ $golDarah }}
- No HP / Telepon   : {{ $noHp }}
- Citizen ID / KTP  : {{ $citizenId }}

III. TIM MEDIS BERTUGAS
- DPJP              : {{ $dpjpName }}
- First Responder   : {{ $firstResp }}
- Petugas Anestesi  : {{ $anestesiStaf }}
- Petugas Radiologi : {{ $radiologiStaf }}
- Asisten 1         : {{ $asisten1 }}
- Asisten 2         : {{ $asisten2 }}
- Seluruh Anggota   : {{ $allMembersList }}

IV. ANAMNESIS & RIWAYAT KESEHATAN
- Diagnosa Utama    : {{ $operation->diagnosa }}
- Riwayat Penyakit Dahulu : {{ $rpd }}
- Riwayat Penyakit Keluarga: {{ $rpk }}
- Riwayat Alergi    : {{ $alergi }}
- Riwayat Pengobatan: {{ $pengobatan }}

V. TANDA-TANDA VITAL (TTV) & PEMERIKSAAN FISIK
- Keadaan Umum      : {{ $ku }}
- Kesadaran (GCS)   : {{ $gcs }}
- Tekanan Darah     : {{ $td }}
- Nadi              : {{ $nadi }}
- Respirasi (RR)    : {{ $rr }}
- Suhu Body         : {{ $suhu }}
- Saturasi O2       : {{ $saturasi }}

VI. PROSEDUR & TINDAKAN OPERASI
- Nama Tindakan     : {{ $operation->tindakan_operasi }}
- Waktu Prosedur    : {{ $waktuMulai }} - {{ $waktuSelesai }} WIB
- Langkah Prosedur  : 
{{ $langkahTindakan }}
- Hasil Operasi     : {{ $operation->hasil_operasi }}

VII. MANAJEMEN ANESTESI & SCORE PEMULIHAN
- Jenis Anestesi    : {{ $jenisAnestesi }}
- Teknik Anestesi   : {{ $teknikAnestesi }}
- ASA Score         : {{ $asaScore }}
- Obat Pra-Operasi  : {{ $praOp }}
- Obat Pasca-Operasi: {{ $pascaOp }}
- Score Kesadaran   : {{ $scoreKesadaran }}
- Respon Mual/Muntah: {{ $scoreMual }}
- Score Pernapasan  : {{ $scorePernapasan }}
- Aktifitas Motorik : {{ $scoreMotorik }}
- Tekanan Darah     : {{ $scoreTD }}
- Warna Kulit       : {{ $scoreWarnaKulit }}

VIII. PEMERIKSAAN PENUNJANG & SARAN
- Hasil Lab         : {{ $lab }}
- Pemeriksaan Radiologi: {{ $pemeriksaanRadiologi }}
- Hasil Radiologi   : {{ $radiologi }}
- Obat Pulang       : {{ $obatObatan }}
- Saran & Anjuran   : {{ $saranAnjuran }}
- Catatan Tambahan  : {{ $catatan }}

================================================================================
Laporan Dibuat Oleh : {{ $operation->creator->name ?? 'Staf Medis' }}
Tanggal Cetak       : {{ now()->format('d F Y H:i') }} WIB
================================================================================</textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <span id="copySuccessMsg" class="text-xs text-emerald-400 font-semibold hidden">
                <i class="fas fa-check-circle mr-1"></i> Format Teks Berhasil Disalin ke Clipboard!
            </span>
            <div class="flex items-center gap-2 ml-auto">
                <button type="button" onclick="copyWordText()" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-copy"></i> Salin Seluruh Teks
                </button>
                <button type="button" onclick="exportToWord()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-file-word"></i> Download File Word (.doc)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openWordModal() {
    document.getElementById('wordModal').classList.remove('hidden');
}

function closeWordModal() {
    document.getElementById('wordModal').classList.add('hidden');
}

function copyWordText() {
    const textarea = document.getElementById('wordTextarea');
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textarea.value).then(() => showCopyAlert());
    } else {
        document.execCommand('copy');
        showCopyAlert();
    }
}

function showCopyAlert() {
    const msg = document.getElementById('copySuccessMsg');
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 3500);
}

function exportToWord() {
    const textareaContent = document.getElementById('wordTextarea').value;
    const htmlContent = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
        <head><meta charset='utf-8'><title>Laporan Rekam Operasi</title>
        <style>
            body { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #1e293b; padding: 20px; }
            h1 { font-size: 16pt; color: #0284c7; text-align: center; margin-bottom: 5px; }
            pre { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; white-space: pre-wrap; word-wrap: break-word; font-size: 11pt; }
        </style>
        </head>
        <body>
            <pre>${textareaContent}</pre>
        </body>
        </html>
    `;

    const blob = new Blob(['\ufeff', htmlContent], {
        type: 'application/msword'
    });
    
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Rekam_Operasi_{{ Str::slug($operation->nama_pasien) }}_{{ $operation->tanggal_waktu->format("Ymd") }}.doc';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection
