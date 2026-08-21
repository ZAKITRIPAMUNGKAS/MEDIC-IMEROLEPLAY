@extends('layouts.app')

@section('title', 'Tes Buta Warna Expert Standar WHO (20 Lempeng Clinical Ishihara) - iMe Medical Center')
@section('meta_description', 'Uji Penglihatan Warna Ishihara Clinical Grade 20 Lempeng Standar WHO. Diagnosa presisi Protanopia, Deuteranopia, Tritanopia & sertifikat medis resmi.')

@section('content')
<div class="min-h-screen bg-slate-100/90 pt-6 pb-16 px-3 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-[1140px] mx-auto bg-white rounded-[32px] shadow-2xl border border-slate-200/80 overflow-hidden divide-y divide-slate-100 text-slate-800">
        
        <!-- HEADER BANNER -->
        <div class="p-6 sm:p-10 bg-gradient-to-br from-red-600 via-rose-600 to-red-700 text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
                <i class="fas fa-eye"></i>
            </div>
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold text-white border border-white/30 shadow-sm">
                    <i class="fas fa-user-md text-amber-300"></i> Clinical Grade &middot; WHO 20 Ishihara Pseudoisochromatic Plates
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
                    Tes Buta Warna Expert WHO (20 Lempeng)
                </h1>
                <p class="text-sm text-white/90 max-w-2xl leading-relaxed">
                    Pemeriksaan mata tingkat klinis dengan <strong class="text-white">20 Lempeng Ishihara Terkalibrasi Presisi</strong>. Mengidentifikasi secara spesifik tipe penglihatan warna: Normal, Protanopia (Merah), Deuteranopia (Hijau), & Tritanopia (Biru-Kuning).
                </p>
            </div>
        </div>

        <!-- MAIN TEST CONTAINER -->
        <div class="p-6 sm:p-10 bg-slate-50/50">

            <!-- PRE-TEST INSTRUCTION CARD -->
            <div id="preTestCard" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md space-y-6">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-xl font-bold shrink-0">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Protokol Pemeriksaan Klinis (WHO Standard)</h2>
                        <p class="text-xs text-slate-500">20 Lempeng Uji Presisi Tinggi untuk Sertifikasi Kesehatan Medis</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
                        <div>
                            <strong class="font-bold text-slate-900 block mb-1">Pencahayaan & Jarak (50 cm)</strong>
                            <p class="text-slate-600">Gunakan kecerahan layar 100%, jaga jarak 50-75 cm dari mata, tegak lurus terhadap pandangan.</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
                        <div>
                            <strong class="font-bold text-slate-900 block mb-1">Non-Filter Kacamata</strong>
                            <p class="text-slate-600">Matikan fitur Night Light / Eye Comfort Shield / Filter Warna Biru pada layar perangkat Anda.</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
                        <div>
                            <strong class="font-bold text-slate-900 block mb-1">Batas Waktu (10 Detik / Lempeng)</strong>
                            <p class="text-slate-600">Jawab setiap lempeng dalam kurun waktu 10 detik sesuai standar pengujian dokter mata WHO.</p>
                        </div>
                    </div>
                </div>

                <!-- PATIENT DATA INPUT & TIMER OPTION -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-id-card text-red-600"></i> Identitas Peserta Tes & Mode Pengujian
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Pasien / Peserta <span class="text-red-500">*</span></label>
                            <input type="text" id="patientName" placeholder="Contoh: Dr. Sarah Johnson" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition-all font-medium text-slate-900" value="{{ auth()->check() ? auth()->user()->name : '' }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Citizen ID / Staff ID (Opsional)</label>
                            <input type="text" id="patientId" placeholder="Contoh: ADM001 / JDN12345" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 transition-all font-medium text-slate-900" value="{{ auth()->check() ? auth()->user()->staff_id : '' }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" id="enableTimer" class="w-4 h-4 text-red-600 rounded focus:ring-red-500 cursor-pointer" checked>
                        <label for="enableTimer" class="text-xs font-bold text-slate-700 cursor-pointer">
                            Aktifkan Waktu Respon 10 Detik per Lempeng (Standar Pemeriksaan Medis WHO)
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button onclick="startColorBlindTest()" class="px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold rounded-2xl shadow-xl shadow-red-500/25 transition-all hover:scale-105 flex items-center gap-2 text-base">
                        Mulai Tes Expert (20 Lempeng Ishihara) <i class="fas fa-play text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- TEST INTERACTIVE QUIZ CARD (HIDDEN INITIAL) -->
            <div id="testQuizCard" class="hidden bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md space-y-6">
                <!-- PROGRESS BAR & TIMER -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span id="currentQuestionLabel">Lempeng 1 dari 20</span>
                        <div class="flex items-center gap-3">
                            <span id="timerBadge" class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-extrabold flex items-center gap-1">
                                <i class="fas fa-stopwatch text-xs"></i> <span id="timeLeft">10</span>s
                            </span>
                            <span id="progressPercentage" class="text-red-600">5%</span>
                        </div>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                        <div id="progressBarFill" class="h-full bg-gradient-to-r from-red-600 to-rose-600 transition-all duration-300 w-[5%]"></div>
                    </div>
                </div>

                <!-- ISHIHARA PLATE CANVAS & OPTIONS -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center pt-2">
                    <!-- PLATE CANVAS DISPLAY -->
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner relative">
                        <canvas id="ishiharaCanvas" width="340" height="340" class="rounded-full shadow-xl border-4 border-white bg-white"></canvas>
                        <span id="plateTag" class="mt-3 text-[11px] font-bold px-3 py-1 bg-slate-200 text-slate-700 rounded-full">Lempeng #1 (Demonstrasi)</span>
                    </div>

                    <!-- OPTIONS FORM -->
                    <div class="space-y-6">
                        <div>
                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider block mb-1">Soal Ke-<span id="questionNumTitle">1</span> / 20</span>
                            <h3 class="text-xl font-extrabold text-slate-900" id="questionPrompt">Angka berapa yang tampak pada lingkaran Ishihara ini?</h3>
                            <p class="text-xs text-slate-500 mt-1" id="questionHint">Pilih salah satu jawaban di bawah ini dengan cermat.</p>
                        </div>

                        <!-- MULTIPLE CHOICE BUTTONS -->
                        <div id="optionsContainer" class="grid grid-cols-2 gap-3">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                            <button onclick="prevQuestion()" id="btnPrev" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 disabled:opacity-40" disabled>
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <span class="text-xs font-bold text-slate-400">Clinical Ishihara 20-Plate WHO</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESULT CARD & MEDICAL CERTIFICATE (HIDDEN INITIAL) -->
            <div id="testResultCard" class="hidden space-y-8">
                <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xl space-y-8 print:p-0 print:border-none print:shadow-none">
                    
                    <!-- CERTIFICATE HEADER -->
                    <div class="text-center border-b-2 border-red-600 pb-6 space-y-2">
                        <div class="flex justify-center mb-2">
                            <img src="{{ asset('images/motionlife-logo.png') }}" alt="iMe Logo" class="h-16 w-16 object-contain">
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight">SERTIFIKAT HASIL UJI PENGLIHATAN WARNA</h2>
                        <p class="text-xs font-bold text-red-600 uppercase tracking-widest">iMe Medical Center &middot; WHO Standard 20 Clinical Ishihara Test</p>
                        <p class="text-[11px] text-slate-400">Nomor Registrasi Medis: MEM-ISH-{{ date('Ymd') }}-<span id="resDocId">9921</span></p>
                    </div>

                    <!-- PATIENT & TEST SUMMARY -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200">
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                                <span class="text-slate-500 font-medium">Nama Pasien:</span>
                                <strong class="text-slate-900 font-bold" id="resPatientName">-</strong>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                                <span class="text-slate-500 font-medium">ID Pasien / Staff:</span>
                                <strong class="text-slate-900 font-bold" id="resPatientId">-</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-medium">Tanggal Pemeriksaan:</span>
                                <strong class="text-slate-900 font-bold">{{ date('d F Y - H:i') }} WIB</strong>
                            </div>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                                <span class="text-slate-500 font-medium">Metode Pengujian:</span>
                                <strong class="text-slate-900 font-bold">20 Clinical Ishihara Plates (WHO)</strong>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                                <span class="text-slate-500 font-medium">Skor Akurasi Medis:</span>
                                <strong class="text-red-600 font-black text-sm" id="resScore">20 / 20 (100%)</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-medium">Status Diagnosa WHO:</span>
                                <strong class="text-emerald-600 font-extrabold uppercase" id="resStatusBadge">BEBAS BUTA WARNA</strong>
                            </div>
                        </div>
                    </div>

                    <!-- DIAGNOSIS SUBTYPE BREAKDOWN BOX -->
                    <div class="p-6 rounded-2xl border-2 space-y-4" id="resDiagnosisContainer">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl font-bold shrink-0" id="resDiagIcon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil Diagnosa Klinis WHO</span>
                                <h3 class="text-xl font-black text-slate-900" id="resDiagnosisTitle">Penglihatan Warna Normal (Normal Trichromacy)</h3>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60" id="resDiagnosisDesc">
                            Pasien membaca seluruh lempeng Ishihara klinik secara sempurna. Tidak ada defisiensi warna merah (Protan), hijau (Deuteran), atau biru-kuning (Tritan).
                        </p>

                        <!-- SUBTYPE SPECTRUM BREAKDOWN -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Trikromasi Normal</span>
                                <strong class="text-emerald-600 font-bold" id="subNormalScore">100%</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Defek Protan (Merah)</span>
                                <strong class="text-slate-800 font-bold" id="subProtanScore">0%</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Defek Deuteran (Hijau)</span>
                                <strong class="text-slate-800 font-bold" id="subDeuteranScore">0%</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Defek Tritan (Biru-Kuning)</span>
                                <strong class="text-slate-800 font-bold" id="subTritanScore">0%</strong>
                            </div>
                        </div>
                    </div>

                    <!-- BREAKDOWN TABLE OF 20 PLATES -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Tabel Analisis Medis 20 Lempeng Ishihara:</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border border-slate-200 rounded-xl overflow-hidden">
                                <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                                    <tr>
                                        <th class="p-3">Lempeng</th>
                                        <th class="p-3">Kategori Lempeng</th>
                                        <th class="p-3">Normal</th>
                                        <th class="p-3">Jawaban</th>
                                        <th class="p-3">Indikasi Klinis WHO</th>
                                        <th class="p-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="resTableBody" class="divide-y divide-slate-100 text-slate-700">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CERTIFICATE FOOTER SIGNATURE -->
                    <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-end gap-6 text-xs text-slate-600">
                        <div class="space-y-1">
                            <p class="font-bold text-slate-900">Validasi Medis WHO:</p>
                            <p class="text-[11px] text-slate-500 max-w-sm leading-relaxed">
                                Dokumen ini diverifikasi secara sistem menggunakan metode 20-Plate Pseudoisochromatic Ishihara Test WHO. Sertifikat ini berlaku untuk persyaratan rekam medis & surat kesehatan.
                            </p>
                        </div>
                        <div class="text-center sm:text-right space-y-2">
                            <p class="text-slate-500">Los Santos, {{ date('d F Y') }}</p>
                            <div class="inline-block p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="font-bold text-red-600 text-xs block">iMe Medical Center</span>
                                <span class="text-[10px] text-slate-400 block">Departemen Oftalmologi & Spesialis Mata</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex flex-wrap justify-between items-center gap-4 print:hidden">
                    <button onclick="restartTest()" class="px-6 py-3.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-2xl text-xs transition-all flex items-center gap-2">
                        <i class="fas fa-redo"></i> Ulangi Tes Expert
                    </button>
                    <div class="flex items-center gap-3">
                        <button onclick="window.print()" class="px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-xs transition-all shadow-lg flex items-center gap-2">
                            <i class="fas fa-print"></i> Cetak Sertifikat Medis
                        </button>
                        <a href="{{ route('public.cek-kesehatan') }}" class="px-6 py-3.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold rounded-2xl text-xs transition-all shadow-lg shadow-red-500/25 flex items-center gap-2">
                            Lampirkan ke Surat Kesehatan <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// =========================================================================
// CLINICAL 20-PLATE ISHIHARA ENGINE (ADVANCED DOT MATRIX & SUBTYPE SPECTRUM)
// =========================================================
const EXPERT_ISHIHARA_PLATES = [
    {
        id: 1,
        numberStr: '12',
        normalVal: '12',
        protanVal: '12',
        deuteranVal: '12',
        tag: 'Plat 1: Demonstrasi Kalibrasi',
        indication: 'Demonstrasi (Semua membaca 12)',
        bgPalette: ['#7ba05b', '#8eb36d', '#6e914f', '#82a862'],
        numPalette: ['#d9534f', '#e56b6f', '#c94245', '#ea8c55'],
        options: ['12', '15', '21', 'Tidak Ada Angka']
    },
    {
        id: 2,
        numberStr: '8',
        normalVal: '8',
        protanVal: '3',
        deuteranVal: '3',
        tag: 'Plat 2: Transformasi Angka (Protan/Deuteran)',
        indication: 'Defisiensi Merah-Hijau membaca 3',
        bgPalette: ['#d4a373', '#e9c46a', '#cca010', '#b5838d'],
        numPalette: ['#2a9d8f', '#264653', '#1b4332', '#40916c'],
        options: ['8', '3', '5', 'Tidak Ada Angka']
    },
    {
        id: 3,
        numberStr: '29',
        normalVal: '29',
        protanVal: '70',
        deuteranVal: '70',
        tag: 'Plat 3: Transformasi Angka (Protan/Deuteran)',
        indication: 'Defisiensi Merah-Hijau membaca 70',
        bgPalette: ['#a8dadc', '#90e0ef', '#8ecae6', '#72efdd'],
        numPalette: ['#e63946', '#d62828', '#f72585', '#b5179e'],
        options: ['29', '70', '20', 'Tidak Ada Angka']
    },
    {
        id: 4,
        numberStr: '5',
        normalVal: '5',
        protanVal: '2',
        deuteranVal: '2',
        tag: 'Plat 4: Transformasi Angka',
        indication: 'Defisiensi Merah-Hijau membaca 2',
        bgPalette: ['#ee9b00', '#ca6702', '#bb3e03', '#d97706'],
        numPalette: ['#005f73', '#0a9396', '#94d2bd', '#008000'],
        options: ['5', '2', '3', 'Tidak Ada Angka']
    },
    {
        id: 5,
        numberStr: '3',
        normalVal: '3',
        protanVal: '5',
        deuteranVal: '5',
        tag: 'Plat 5: Transformasi Angka',
        indication: 'Defisiensi Merah-Hijau membaca 5',
        bgPalette: ['#95d5b2', '#74c69d', '#52b788', '#40916c'],
        numPalette: ['#d90429', '#ef233c', '#d62828', '#b7094c'],
        options: ['3', '5', '8', 'Tidak Ada Angka']
    },
    {
        id: 6,
        numberStr: '15',
        normalVal: '15',
        protanVal: '17',
        deuteranVal: '17',
        tag: 'Plat 6: Transformasi Angka',
        indication: 'Defisiensi Merah-Hijau membaca 17',
        bgPalette: ['#f4a261', '#e76f51', '#e9c46a', '#ee9b00'],
        numPalette: ['#2a9d8f', '#264653', '#1d3557', '#457b9d'],
        options: ['15', '17', '13', 'Tidak Ada Angka']
    },
    {
        id: 7,
        numberStr: '74',
        normalVal: '74',
        protanVal: '21',
        deuteranVal: '21',
        tag: 'Plat 7: Transformasi Angka',
        indication: 'Defisiensi Merah-Hijau membaca 21',
        bgPalette: ['#00b4d8', '#0077b6', '#03045e', '#48cae4'],
        numPalette: ['#ff4d6d', '#c9184a', '#a4133c', '#ff758f'],
        options: ['74', '21', '71', 'Tidak Ada Angka']
    },
    {
        id: 8,
        numberStr: '6',
        normalVal: '6',
        protanVal: 'Tidak Ada Angka',
        deuteranVal: 'Tidak Ada Angka',
        tag: 'Plat 8: Vanishing Plate (Samar)',
        indication: 'Normal membaca 6, Buta Warna tidak tampak',
        bgPalette: ['#ddb892', '#b08968', '#7f5539', '#9c6644'],
        numPalette: ['#2d6a4f', '#40916c', '#52b788', '#1b4332'],
        options: ['6', '9', '8', 'Tidak Ada Angka']
    },
    {
        id: 9,
        numberStr: '45',
        normalVal: '45',
        protanVal: 'Tidak Ada Angka',
        deuteranVal: 'Tidak Ada Angka',
        tag: 'Plat 9: Vanishing Plate (Samar)',
        indication: 'Normal membaca 45, Buta Warna tidak tampak',
        bgPalette: ['#94d2bd', '#e9d8a6', '#ee9b00', '#d97706'],
        numPalette: ['#ae2012', '#9b2226', '#c9184a', '#800f2f'],
        options: ['45', '42', '15', 'Tidak Ada Angka']
    },
    {
        id: 10,
        numberStr: '7',
        normalVal: '7',
        protanVal: '1',
        deuteranVal: '1',
        tag: 'Plat 10: Transformasi Angka',
        indication: 'Normal membaca 7, Buta Warna membaca 1',
        bgPalette: ['#b8c0ff', '#c8b6ff', '#e7c6ff', '#ffd6ff'],
        numPalette: ['#d90429', '#ef233c', '#d62828', '#c9184a'],
        options: ['7', '1', '4', 'Tidak Ada Angka']
    },
    {
        id: 11,
        numberStr: '16',
        normalVal: '16',
        protanVal: '19',
        deuteranVal: '19',
        tag: 'Plat 11: Transformasi Angka Klinis',
        indication: 'Normal membaca 16, Buta Warna membaca 19',
        bgPalette: ['#e29578', '#ffddd2', '#83c5be', '#006d77'],
        numPalette: ['#d00000', '#dc2f02', '#e85d04', '#f48c06'],
        options: ['16', '19', '10', 'Tidak Ada Angka']
    },
    {
        id: 12,
        numberStr: '73',
        normalVal: '73',
        protanVal: '28',
        deuteranVal: '28',
        tag: 'Plat 12: Transformasi Angka Klinis',
        indication: 'Normal membaca 73, Buta Warna membaca 28',
        bgPalette: ['#7209b7', '#3f37c9', '#4361ee', '#4895ef'],
        numPalette: ['#f72585', '#7209b7', '#b5179e', '#f72585'],
        options: ['73', '28', '78', 'Tidak Ada Angka']
    },
    {
        id: 13,
        numberStr: '26',
        normalVal: '26',
        protanVal: '6',
        deuteranVal: '2',
        tag: 'Plat 13: Diagnostik Protan vs Deuteran (26)',
        indication: 'Protan (Merah) membaca 6, Deuteran (Hijau) membaca 2',
        bgPalette: ['#dda15e', '#bc6c25', '#fefae0', '#606c38'],
        numPalette: ['#bc6c25', '#dda15e', '#d62828', '#003049'],
        options: ['26', '6', '2', 'Tidak Ada Angka']
    },
    {
        id: 14,
        numberStr: '42',
        normalVal: '42',
        protanVal: '2',
        deuteranVal: '4',
        tag: 'Plat 14: Diagnostik Protan vs Deuteran (42)',
        indication: 'Protan (Merah) membaca 2, Deuteran (Hijau) membaca 4',
        bgPalette: ['#b7b7a4', '#a5a58d', '#6b705c', '#3f4238'],
        numPalette: ['#e76f51', '#f4a261', '#2a9d8f', '#e9c46a'],
        options: ['42', '2', '4', 'Tidak Ada Angka']
    },
    {
        id: 15,
        numberStr: '35',
        normalVal: 'Tidak Ada Angka',
        protanVal: '35',
        deuteranVal: '35',
        tag: 'Plat 15: Hidden Digit Plate (Reverse)',
        indication: 'Normal melihat acak, Buta Warna membaca 35',
        bgPalette: ['#a2d2ff', '#bde0fe', '#ffafcc', '#ffc8dd'],
        numPalette: ['#cdb4db', '#ffc8dd', '#ffafcc', '#bde0fe'],
        options: ['35', '38', '53', 'Tidak Ada Angka']
    },
    {
        id: 16,
        numberStr: '96',
        normalVal: '96',
        protanVal: '69',
        deuteranVal: '69',
        tag: 'Plat 16: Transformasi Angka Ganda',
        indication: 'Normal membaca 96, Buta Warna membaca 69',
        bgPalette: ['#2a9d8f', '#e9c46a', '#f4a261', '#e76f51'],
        numPalette: ['#264653', '#2a9d8f', '#e76f51', '#1d3557'],
        options: ['96', '69', '90', 'Tidak Ada Angka']
    },
    {
        id: 17,
        numberStr: '10',
        normalVal: '10',
        protanVal: 'Tidak Ada Angka',
        deuteranVal: 'Tidak Ada Angka',
        tag: 'Plat 17: Vanishing Plate Presisi Tinggi',
        indication: 'Normal membaca 10, Buta Warna tidak tampak',
        bgPalette: ['#e76f51', '#f4a261', '#e9c46a', '#2a9d8f'],
        numPalette: ['#1d3557', '#457b9d', '#0077b6', '#023e8a'],
        options: ['10', '18', '70', 'Tidak Ada Angka']
    },
    {
        id: 18,
        numberStr: '4',
        normalVal: '4',
        protanVal: '1',
        deuteranVal: '1',
        tag: 'Plat 18: Uji Kontras Biru-Kuning (Tritan)',
        indication: 'Uji Defisiensi Tritan / Monokromasi',
        bgPalette: ['#ffb703', '#fb8500', '#023e8a', '#0077b6'],
        numPalette: ['#8ecae6', '#219ebc', '#023e8a', '#0077b6'],
        options: ['4', '1', '7', 'Tidak Ada Angka']
    },
    {
        id: 19,
        numberStr: '9',
        normalVal: '9',
        protanVal: '5',
        deuteranVal: '5',
        tag: 'Plat 19: Transformasi Angka Klinis',
        indication: 'Normal membaca 9, Buta Warna membaca 5',
        bgPalette: ['#d94e34', '#f28e2b', '#e15759', '#76b7b2'],
        numPalette: ['#59a14f', '#edc948', '#b07aa1', '#ff9da7'],
        options: ['9', '5', '3', 'Tidak Ada Angka']
    },
    {
        id: 20,
        numberStr: '82',
        normalVal: '82',
        protanVal: '32',
        deuteranVal: '32',
        tag: 'Plat 20: Evaluasi Akhir Klinis',
        indication: 'Normal membaca 82, Buta Warna membaca 32',
        bgPalette: ['#4e79a7', '#f28e2b', '#e15759', '#76b7b2'],
        numPalette: ['#59a14f', '#edc948', '#b07aa1', '#9c755f'],
        options: ['82', '32', '83', 'Tidak Ada Angka']
    }
];

let currentStep = 0;
let userAnswers = {};
let patientData = { name: '', id: '', timerEnabled: true };
let timerInterval = null;
let timeLeft = 10;

function startColorBlindTest() {
    const nameInput = document.getElementById('patientName').value.trim();
    if (!nameInput) {
        alert('Harap isi Nama Lengkap Pasien terlebih dahulu.');
        document.getElementById('patientName').focus();
        return;
    }

    patientData.name = nameInput;
    patientData.id = document.getElementById('patientId').value.trim() || 'NON-ID';
    patientData.timerEnabled = document.getElementById('enableTimer').checked;

    document.getElementById('preTestCard').classList.add('hidden');
    document.getElementById('testQuizCard').classList.remove('hidden');

    currentStep = 0;
    userAnswers = {};
    renderQuestion(currentStep);
}

function renderQuestion(stepIndex) {
    clearInterval(timerInterval);

    const plate = EXPERT_ISHIHARA_PLATES[stepIndex];

    document.getElementById('currentQuestionLabel').textContent = `Lempeng ${stepIndex + 1} dari ${EXPERT_ISHIHARA_PLATES.length}`;
    const pct = Math.round(((stepIndex + 1) / EXPERT_ISHIHARA_PLATES.length) * 100);
    document.getElementById('progressPercentage').textContent = `${pct}%`;
    document.getElementById('progressBarFill').style.width = `${pct}%`;

    document.getElementById('questionNumTitle').textContent = stepIndex + 1;
    document.getElementById('plateTag').textContent = plate.tag;
    document.getElementById('btnPrev').disabled = (stepIndex === 0);

    // Draw Advanced Dot Matrix Canvas
    drawAdvancedIshiharaPlate('ishiharaCanvas', plate);

    // Render Option Buttons
    const optionsContainer = document.getElementById('optionsContainer');
    optionsContainer.innerHTML = '';

    plate.options.forEach(opt => {
        const isSelected = (userAnswers[plate.id] === opt);
        const btn = document.createElement('button');
        btn.className = `p-4 rounded-2xl font-extrabold text-sm border transition-all duration-200 flex items-center justify-between ${
            isSelected 
                ? 'bg-red-600 text-white border-red-600 shadow-lg shadow-red-600/30 scale-[1.02]' 
                : 'bg-slate-50 hover:bg-slate-100 text-slate-800 border-slate-200'
        }`;
        btn.innerHTML = `<span>${opt}</span> <i class="fas ${isSelected ? 'fa-check-circle' : 'fa-circle text-slate-300'}"></i>`;
        btn.onclick = () => selectOption(plate.id, opt);
        optionsContainer.appendChild(btn);
    });

    // Start 10-second timer if enabled
    if (patientData.timerEnabled) {
        document.getElementById('timerBadge').classList.remove('hidden');
        timeLeft = 10;
        document.getElementById('timeLeft').textContent = timeLeft;

        timerInterval = setInterval(() => {
            timeLeft--;
            document.getElementById('timeLeft').textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                if (!userAnswers[plate.id]) {
                    userAnswers[plate.id] = 'Waktu Habis';
                }
                autoAdvance();
            }
        }, 1000);
    } else {
        document.getElementById('timerBadge').classList.add('hidden');
    }
}

function selectOption(plateId, selectedVal) {
    clearInterval(timerInterval);
    userAnswers[plateId] = selectedVal;
    renderQuestion(currentStep);

    setTimeout(() => {
        autoAdvance();
    }, 200);
}

function autoAdvance() {
    clearInterval(timerInterval);
    if (currentStep < EXPERT_ISHIHARA_PLATES.length - 1) {
        currentStep++;
        renderQuestion(currentStep);
    } else {
        finishColorBlindTest();
    }
}

function prevQuestion() {
    clearInterval(timerInterval);
    if (currentStep > 0) {
        currentStep--;
        renderQuestion(currentStep);
    }
}

// ADVANCED HIGH-PRECISION ISHIHARA DOT MATRIX RENDERER
function drawAdvancedIshiharaPlate(canvasId, plateData) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const centerX = width / 2;
    const centerY = height / 2;
    const radius = width / 2 - 12;

    ctx.clearRect(0, 0, width, height);

    // Background circle container
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
    ctx.fillStyle = '#f1f5f9';
    ctx.fill();

    // Offscreen Canvas Mask for Number Text
    const maskCanvas = document.createElement('canvas');
    maskCanvas.width = width;
    maskCanvas.height = height;
    const maskCtx = maskCanvas.getContext('2d');

    maskCtx.fillStyle = '#000000';
    maskCtx.font = '900 145px sans-serif';
    maskCtx.textAlign = 'center';
    maskCtx.textBaseline = 'middle';
    maskCtx.fillText(plateData.numberStr, centerX, centerY + 6);

    const maskData = maskCtx.getImageData(0, 0, width, height).data;

    // Generate Advanced Variable-Radius Dot Matrix
    const dots = [];
    const step = 6.5;

    for (let x = 12; x < width - 12; x += step) {
        for (let y = 12; y < height - 12; y += step) {
            const dx = x - centerX;
            const dy = y - centerY;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < radius - 4) {
                const maskIndex = (Math.floor(y) * width + Math.floor(x)) * 4;
                const isNumberDot = maskData[maskIndex + 3] > 80;

                // Micro dot noise & clinical camouflage jitter
                const dotRadius = Math.random() * 3.5 + 2.5;
                const jitterX = x + (Math.random() - 0.5) * 3.2;
                const jitterY = y + (Math.random() - 0.5) * 3.2;

                const palette = isNumberDot ? plateData.numPalette : plateData.bgPalette;
                const color = palette[Math.floor(Math.random() * palette.length)];

                dots.push({ x: jitterX, y: jitterY, r: dotRadius, color });
            }
        }
    }

    // Render Dots on Main Canvas
    dots.forEach(dot => {
        ctx.beginPath();
        ctx.arc(dot.x, dot.y, dot.r, 0, Math.PI * 2);
        ctx.fillStyle = dot.color;
        ctx.fill();
    });
}

function finishColorBlindTest() {
    clearInterval(timerInterval);
    document.getElementById('testQuizCard').classList.add('hidden');
    document.getElementById('testResultCard').classList.remove('hidden');

    let correctCount = 0;
    let protanMatches = 0;
    let deuteranMatches = 0;

    const tableBody = document.getElementById('resTableBody');
    tableBody.innerHTML = '';

    EXPERT_ISHIHARA_PLATES.forEach(plate => {
        const ans = userAnswers[plate.id] || 'Tidak Dijawab';
        const isCorrect = (ans === plate.normalVal);
        if (isCorrect) correctCount++;

        if (ans === plate.protanVal && plate.protanVal !== plate.normalVal) {
            protanMatches++;
        }
        if (ans === plate.deuteranVal && plate.deuteranVal !== plate.normalVal) {
            deuteranMatches++;
        }

        const tr = document.createElement('tr');
        tr.className = isCorrect ? 'bg-emerald-50/50' : 'bg-red-50/50';
        tr.innerHTML = `
            <td class="p-3 font-bold">Lempeng ${plate.id}</td>
            <td class="p-3 font-medium text-slate-500 text-[11px]">${plate.tag}</td>
            <td class="p-3 font-semibold text-slate-800">${plate.normalVal}</td>
            <td class="p-3 font-bold ${isCorrect ? 'text-emerald-700' : 'text-red-600'}">${ans}</td>
            <td class="p-3 text-slate-500 text-[11px]">${plate.indication}</td>
            <td class="p-3 text-center">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold ${isCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}">
                    ${isCorrect ? 'BENAR' : 'SALAH'}
                </span>
            </td>
        `;
        tableBody.appendChild(tr);
    });

    const totalPlates = EXPERT_ISHIHARA_PLATES.length;
    const scorePct = Math.round((correctCount / totalPlates) * 100);
    document.getElementById('resPatientName').textContent = patientData.name;
    document.getElementById('resPatientId').textContent = patientData.id;
    document.getElementById('resDocId').textContent = Math.floor(10000 + Math.random() * 90000);
    document.getElementById('resScore').textContent = `${correctCount} / ${totalPlates} (${scorePct}%)`;

    document.getElementById('subNormalScore').textContent = `${scorePct}%`;
    document.getElementById('subProtanScore').textContent = `${Math.round((protanMatches / 5) * 100)}%`;
    document.getElementById('subDeuteranScore').textContent = `${Math.round((deuteranMatches / 5) * 100)}%`;
    document.getElementById('subTritanScore').textContent = (correctCount < 14) ? 'Indikasi' : '0%';

    const container = document.getElementById('resDiagnosisContainer');
    const badge = document.getElementById('resStatusBadge');
    const title = document.getElementById('resDiagnosisTitle');
    const desc = document.getElementById('resDiagnosisDesc');
    const icon = document.getElementById('resDiagIcon');

    if (correctCount >= 17) {
        // NORMAL TRICHROMACY
        badge.className = 'text-emerald-600 font-extrabold uppercase';
        badge.textContent = 'BEBAS BUTA WARNA (NORMAL TRICHROMACY)';

        container.className = 'p-6 rounded-2xl border-2 border-emerald-200 bg-emerald-50/60 space-y-4';
        icon.className = 'w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl font-bold shrink-0';
        icon.innerHTML = '<i class="fas fa-check-circle"></i>';

        title.textContent = 'Penglihatan Warna Normal (Normal Trichromacy)';
        desc.textContent = 'Pasien mampu mengidentifikasi seluruh 20 lempeng Ishihara klinik dengan tingkat akurasi sangat tinggi (Skor >= 85%). Tidak ada tanda defisiensi Protan, Deuteran, atau Tritan. Pasien dinyatakan BEBAS BUTA WARNA.';
    } else if (protanMatches > deuteranMatches) {
        // PROTANOPIA / PROTANOMALY
        badge.className = 'text-amber-600 font-extrabold uppercase';
        badge.textContent = 'BUTA WARNA PARSIAL (PROTANOPIA / MERAH)';

        container.className = 'p-6 rounded-2xl border-2 border-amber-200 bg-amber-50/60 space-y-4';
        icon.className = 'w-12 h-12 rounded-xl bg-amber-600 text-white flex items-center justify-center text-xl font-bold shrink-0';
        icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';

        title.textContent = 'Indikasi Buta Warna Parsial Tipe Protan (Red-Color Deficient)';
        desc.textContent = 'Hasil analisis lempeng diagnostik menunjukkan defisiensi pada fotoreseptor L-cone (persepsi warna merah / Protanomaly). Disarankan konsultasi lanjutan dengan Dokter Spesialis Mata.';
    } else if (deuteranMatches >= protanMatches && correctCount >= 10) {
        // DEUTERANOPIA / DEUTERANOMALY
        badge.className = 'text-amber-600 font-extrabold uppercase';
        badge.textContent = 'BUTA WARNA PARSIAL (DEUTERANOPIA / HIJAU)';

        container.className = 'p-6 rounded-2xl border-2 border-amber-200 bg-amber-50/60 space-y-4';
        icon.className = 'w-12 h-12 rounded-xl bg-amber-600 text-white flex items-center justify-center text-xl font-bold shrink-0';
        icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';

        title.textContent = 'Indikasi Buta Warna Parsial Tipe Deuteran (Green-Color Deficient)';
        desc.textContent = 'Hasil analisis lempeng diagnostik menunjukkan defisiensi pada fotoreseptor M-cone (persepsi warna hijau / Deuteranomaly). Disarankan konsultasi lanjutan dengan Dokter Spesialis Mata.';
    } else {
        // TOTAL / SEVERE COLOR BLINDNESS
        badge.className = 'text-red-600 font-extrabold uppercase';
        badge.textContent = 'BUTA WARNA TOTAL / BERAT';

        container.className = 'p-6 rounded-2xl border-2 border-red-200 bg-red-50/60 space-y-4';
        icon.className = 'w-12 h-12 rounded-xl bg-red-600 text-white flex items-center justify-center text-xl font-bold shrink-0';
        icon.innerHTML = '<i class="fas fa-eye-slash"></i>';

        title.textContent = 'Indikasi Buta Warna Total / Berat (Achromatopsia)';
        desc.textContent = 'Pasien mengalami keterbatasan signifikan dalam membedakan spektrum warna Ishihara (Skor < 50%). Disarankan menjalani pemeriksaan oftamologi klinis lengkap di iMe Medical Center.';
    }

    window.scrollTo({ top: 100, behavior: 'smooth' });
}

function restartTest() {
    clearInterval(timerInterval);
    document.getElementById('testResultCard').classList.add('hidden');
    document.getElementById('preTestCard').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endpush
