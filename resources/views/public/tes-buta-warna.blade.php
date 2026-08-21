@extends('layouts.app')

@section('title', 'Tes Mata Buta Warna Standar WHO - iMe Medical Center')
@section('meta_description', 'Uji Penglihatan Warna Ishihara Standar WHO secara online dan interaktif. Dapatkan hasil diagnosa penglihatan warna & sertifikat resmi iMe Medical Center.')

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
                    <i class="fas fa-microscope text-amber-300"></i> Standar WHO & Ishihara Pseudoisochromatic Plates
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
                    Tes Mata Buta Warna Online
                </h1>
                <p class="text-sm text-white/90 max-w-2xl leading-relaxed">
                    Pemeriksaan kesehatan penglihatan warna interaktif menggunakan metode <strong class="text-white">Ishihara Plates Standar Medis WHO</strong>. Diperuntukkan untuk persyaratan pembuatan Surat Kesehatan, seleksi pekerjaan, maupun pemeriksaan mandiri.
                </p>
            </div>
        </div>

        <!-- MAIN TEST CONTAINER -->
        <div class="p-6 sm:p-10 bg-slate-50/50">

            <!-- PRE-TEST INSTRUCTION CARD -->
            <div id="preTestCard" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md space-y-6">
                <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-xl font-bold shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Petunjuk Pelaksanaan Tes (WHO Standards)</h2>
                        <p class="text-xs text-slate-500">Harap baca petunjuk berikut sebelum memulai pengujian</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
                        <div>
                            <strong class="font-bold text-slate-900 block mb-1">Pencahayaan Ruangan</strong>
                            <p class="text-slate-600">Pastikan layar monitor Anda berada pada kecerahan normal (tidak terlalu gelap/redup) dan ruangan terang.</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
                        <div>
                            <strong class="font-bold text-slate-900 block mb-1">Jarak Pandang Mata</strong>
                            <p class="text-slate-600">Posisikan mata Anda kira-kira <strong>50 cm – 75 cm</strong> dari layar monitor saat mengamati setiap lempeng Ishihara.</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
                        <div>
                            <strong class="font-bold text-slate-900 block mb-1">Tanpa Filter Warna</strong>
                            <p class="text-slate-600">Lepaskan kacamata/lensa pemfilter warna (seperti Night Mode / Eye Care Filter / Blue Light Filter Layar).</p>
                        </div>
                    </div>
                </div>

                <!-- PATIENT DATA INPUT -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-id-card text-red-600"></i> Masukkan Identitas Peserta Tes
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
                </div>

                <div class="flex justify-end pt-4">
                    <button onclick="startColorBlindTest()" class="px-8 py-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold rounded-2xl shadow-xl shadow-red-500/25 transition-all hover:scale-105 flex items-center gap-2 text-base">
                        Mulai Tes Ishihara (10 Lempeng) <i class="fas fa-play text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- TEST INTERACTIVE QUIZ CARD (HIDDEN INITIAL) -->
            <div id="testQuizCard" class="hidden bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md space-y-6">
                <!-- PROGRESS BAR -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span id="currentQuestionLabel">Lempeng 1 dari 10</span>
                        <span id="progressPercentage" class="text-red-600">10%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                        <div id="progressBarFill" class="h-full bg-gradient-to-r from-red-600 to-rose-600 transition-all duration-300 w-[10%]"></div>
                    </div>
                </div>

                <!-- ISHIHARA PLATE CANVAS & OPTIONS -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center pt-2">
                    <!-- PLATE CANVAS DISPLAY -->
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner">
                        <canvas id="ishiharaCanvas" width="300" height="300" class="rounded-full shadow-lg border-4 border-white bg-white"></canvas>
                        <p class="text-[11px] font-semibold text-slate-400 mt-3 flex items-center gap-1.5">
                            <i class="fas fa-eye text-red-500"></i> Amati lempeng di atas, angka atau pola apa yang Anda lihat?
                        </p>
                    </div>

                    <!-- OPTIONS FORM -->
                    <div class="space-y-6">
                        <div>
                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider block mb-1">Pertanyaan <span id="questionNumTitle">1</span></span>
                            <h3 class="text-xl font-extrabold text-slate-900" id="questionPrompt">Angka berapa yang tampak pada lingkaran Ishihara ini?</h3>
                            <p class="text-xs text-slate-500 mt-1" id="questionHint">Pilih salah satu jawaban di bawah ini berdasarkan penglihatan Anda.</p>
                        </div>

                        <!-- MULTIPLE CHOICE BUTTONS -->
                        <div id="optionsContainer" class="grid grid-cols-2 gap-3">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                            <button onclick="prevQuestion()" id="btnPrev" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 disabled:opacity-40" disabled>
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <span class="text-xs font-medium text-slate-400">Ishihara Plate WHO-10</span>
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
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight">HASIL PEMERIKSAAN PENGLIHATAN WARNA</h2>
                        <p class="text-xs font-bold text-red-600 uppercase tracking-widest">iMe Medical Center &middot; WHO Standard Ishihara Test</p>
                        <p class="text-[11px] text-slate-400">Nomor Dokumen: MEM-ISH-{{ date('Ymd') }}-<span id="resDocId">8849</span></p>
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
                                <strong class="text-slate-900 font-bold">10 Ishihara Plates (WHO)</strong>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                                <span class="text-slate-500 font-medium">Skor Benar:</span>
                                <strong class="text-red-600 font-black text-sm" id="resScore">10 / 10 (100%)</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 font-medium">Status Diagnosa:</span>
                                <strong class="text-emerald-600 font-extrabold uppercase" id="resStatusBadge">NORMAL</strong>
                            </div>
                        </div>
                    </div>

                    <!-- DIAGNOSIS DETAILS BOX -->
                    <div class="p-6 rounded-2xl border-2 space-y-3" id="resDiagnosisContainer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg font-bold" id="resDiagIcon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil Diagnosis Medis WHO</span>
                                <h3 class="text-xl font-extrabold text-slate-900" id="resDiagnosisTitle">Penglihatan Warna Normal (Normal Vision)</h3>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60" id="resDiagnosisDesc">
                            Pasien mampu membaca seluruh lempeng Ishihara dengan akurat. Tidak ditemukan tanda-tanda buta warna merah-hijau (Protanopia/Deuteranopia) maupun buta warna total.
                        </p>
                    </div>

                    <!-- BREAKDOWN TABLE OF 10 PLATES -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Detail Hasil Jawaban per Lempeng:</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border border-slate-200 rounded-xl overflow-hidden">
                                <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                                    <tr>
                                        <th class="p-3">Lempeng</th>
                                        <th class="p-3">Target Normal</th>
                                        <th class="p-3">Jawaban Pasien</th>
                                        <th class="p-3">Indikasi Buta Warna</th>
                                        <th class="p-3 text-center">Hasil</th>
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
                            <p class="font-bold text-slate-900">Catatan Medis WHO:</p>
                            <p class="text-[11px] text-slate-500 max-w-sm leading-relaxed">
                                Tes ini merupakan skrining awal penglihatan warna. Untuk keperluan rekam medis resmi, konsultasi lanjutan dapat dilakukan bersama Dokter Spesialis Mata iMe Medical Center.
                            </p>
                        </div>
                        <div class="text-center sm:text-right space-y-2">
                            <p class="text-slate-500">Los Santos, {{ date('d F Y') }}</p>
                            <div class="inline-block p-2 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="font-bold text-red-600 text-xs block">iMe Medical Center</span>
                                <span class="text-[10px] text-slate-400 block">Tim Penguji Kesehatan Mata</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex flex-wrap justify-between items-center gap-4 print:hidden">
                    <button onclick="restartTest()" class="px-6 py-3.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-2xl text-xs transition-all flex items-center gap-2">
                        <i class="fas fa-redo"></i> Ulangi Tes Mata
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
// =========================================================
// ISHIHARA DATA & CANVAS GENERATOR ENGINE (WHO STANDARDS)
// =========================================================
const ISHIHARA_PLATES = [
    {
        id: 1,
        numberStr: '12',
        normalVal: '12',
        colorBlindVal: '12',
        indication: 'Plat Demostrasi (Normal & Buta Warna membaca 12)',
        bgPalette: ['#8db580', '#a2c797', '#7ca16f', '#96b88b', '#6b8e5e'],
        numPalette: ['#e26d5c', '#ea8c55', '#c8553d', '#f26419', '#d64045'],
        options: ['12', '15', '21', 'Tidak Ada Angka']
    },
    {
        id: 2,
        numberStr: '8',
        normalVal: '8',
        colorBlindVal: '3',
        indication: 'Red-Green Deficient biasa membaca angka 3',
        bgPalette: ['#e6b89c', '#ead2ac', '#d4a373', '#ddb892', '#b08968'],
        numPalette: ['#588157', '#3a5a40', '#344e41', '#4f772d', '#386641'],
        options: ['8', '3', '5', 'Tidak Ada Angka']
    },
    {
        id: 3,
        numberStr: '29',
        normalVal: '29',
        colorBlindVal: '70',
        indication: 'Red-Green Deficient biasa membaca angka 70',
        bgPalette: ['#a8dadc', '#b1e5f2', '#8ecae6', '#90e0ef', '#72efdd'],
        numPalette: ['#e63946', '#d62828', '#f72585', '#b5179e', '#7209b7'],
        options: ['29', '70', '20', 'Tidak Ada Angka']
    },
    {
        id: 4,
        numberStr: '5',
        normalVal: '5',
        colorBlindVal: '2',
        indication: 'Red-Green Deficient biasa membaca angka 2',
        bgPalette: ['#e9d8a6', '#ee9b00', '#ca6702', '#bb3e03', '#d97706'],
        numPalette: ['#005f73', '#0a9396', '#94d2bd', '#008000', '#2d6a4f'],
        options: ['5', '2', '3', 'Tidak Ada Angka']
    },
    {
        id: 5,
        numberStr: '3',
        normalVal: '3',
        colorBlindVal: '5',
        indication: 'Red-Green Deficient biasa membaca angka 5',
        bgPalette: ['#b7e4c7', '#95d5b2', '#74c69d', '#52b788', '#40916c'],
        numPalette: ['#d90429', '#ef233c', '#d62828', '#b7094c', '#a01a58'],
        options: ['3', '5', '8', 'Tidak Ada Angka']
    },
    {
        id: 6,
        numberStr: '15',
        normalVal: '15',
        colorBlindVal: '17',
        indication: 'Red-Green Deficient biasa membaca angka 17',
        bgPalette: ['#f4a261', '#e76f51', '#e9c46a', '#ee9b00', '#f48c06'],
        numPalette: ['#2a9d8f', '#264653', '#1d3557', '#457b9d', '#0077b6'],
        options: ['15', '17', '13', 'Tidak Ada Angka']
    },
    {
        id: 7,
        numberStr: '74',
        normalVal: '74',
        colorBlindVal: '21',
        indication: 'Red-Green Deficient biasa membaca angka 21',
        bgPalette: ['#90e0ef', '#00b4d8', '#0077b6', '#03045e', '#48cae4'],
        numPalette: ['#ff4d6d', '#c9184a', '#a4133c', '#800f2f', '#ff758f'],
        options: ['74', '21', '71', 'Tidak Ada Angka']
    },
    {
        id: 8,
        numberStr: '6',
        normalVal: '6',
        colorBlindVal: 'Tidak Ada Angka',
        indication: 'Protanopia / Deuteranopia tidak dapat melihat angka 6',
        bgPalette: ['#ddb892', '#b08968', '#7f5539', '#9c6644', '#ca6702'],
        numPalette: ['#2d6a4f', '#40916c', '#52b788', '#1b4332', '#081c15'],
        options: ['6', '9', '8', 'Tidak Ada Angka']
    },
    {
        id: 9,
        numberStr: '45',
        normalVal: '45',
        colorBlindVal: 'Tidak Ada Angka',
        indication: 'Protanopia / Deuteranopia tidak dapat melihat angka 45',
        bgPalette: ['#94d2bd', '#e9d8a6', '#ee9b00', '#d97706', '#80b918'],
        numPalette: ['#ae2012', '#9b2226', '#c9184a', '#800f2f', '#d90429'],
        options: ['45', '42', '15', 'Tidak Ada Angka']
    },
    {
        id: 10,
        numberStr: '7',
        normalVal: '7',
        colorBlindVal: '1',
        indication: 'Buta warna parsial membaca 1 atau tidak tampak',
        bgPalette: ['#b8c0ff', '#c8b6ff', '#e7c6ff', '#ffd6ff', '#bbd0ff'],
        numPalette: ['#d90429', '#ef233c', '#d62828', '#c9184a', '#9d0208'],
        options: ['7', '1', '4', 'Tidak Ada Angka']
    }
];

let currentStep = 0;
let userAnswers = {};
let patientData = { name: '', id: '' };

function startColorBlindTest() {
    const nameInput = document.getElementById('patientName').value.trim();
    if (!nameInput) {
        alert('Harap isi Nama Lengkap Pasien terlebih dahulu.');
        document.getElementById('patientName').focus();
        return;
    }

    patientData.name = nameInput;
    patientData.id = document.getElementById('patientId').value.trim() || 'NON-ID';

    document.getElementById('preTestCard').classList.add('hidden');
    document.getElementById('testQuizCard').classList.remove('hidden');

    currentStep = 0;
    userAnswers = {};
    renderQuestion(currentStep);
}

function renderQuestion(stepIndex) {
    const plate = ISHIHARA_PLATES[stepIndex];

    document.getElementById('currentQuestionLabel').textContent = `Lempeng ${stepIndex + 1} dari ${ISHIHARA_PLATES.length}`;
    const pct = Math.round(((stepIndex + 1) / ISHIHARA_PLATES.length) * 100);
    document.getElementById('progressPercentage').textContent = `${pct}%`;
    document.getElementById('progressBarFill').style.width = `${pct}%`;

    document.getElementById('questionNumTitle').textContent = stepIndex + 1;
    document.getElementById('btnPrev').disabled = (stepIndex === 0);

    // Generate Ishihara Canvas Plate
    drawIshiharaPlate('ishiharaCanvas', plate);

    // Populate Option Buttons
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
}

function selectOption(plateId, selectedVal) {
    userAnswers[plateId] = selectedVal;
    renderQuestion(currentStep);

    setTimeout(() => {
        if (currentStep < ISHIHARA_PLATES.length - 1) {
            currentStep++;
            renderQuestion(currentStep);
        } else {
            finishColorBlindTest();
        }
    }, 250);
}

function prevQuestion() {
    if (currentStep > 0) {
        currentStep--;
        renderQuestion(currentStep);
    }
}

// Draw realistic Ishihara Dot Matrix Plate on Canvas
function drawIshiharaPlate(canvasId, plateData) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const centerX = width / 2;
    const centerY = height / 2;
    const radius = width / 2 - 10;

    ctx.clearRect(0, 0, width, height);

    // Draw background circle container
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
    ctx.fillStyle = '#f8fafc';
    ctx.fill();

    // Create Offscreen Mask Canvas for Number Text
    const maskCanvas = document.createElement('canvas');
    maskCanvas.width = width;
    maskCanvas.height = height;
    const maskCtx = maskCanvas.getContext('2d');

    maskCtx.fillStyle = '#000000';
    maskCtx.font = '900 130px sans-serif';
    maskCtx.textAlign = 'center';
    maskCtx.textBaseline = 'middle';
    maskCtx.fillText(plateData.numberStr, centerX, centerY + 6);

    const maskData = maskCtx.getImageData(0, 0, width, height).data;

    // Generate Random Dots in Grid with Jitter
    const dots = [];
    const step = 8;

    for (let x = 12; x < width - 12; x += step) {
        for (let y = 12; y < height - 12; y += step) {
            const dx = x - centerX;
            const dy = y - centerY;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < radius - 6) {
                // Check if dot falls inside mask number
                const maskIndex = (Math.floor(y) * width + Math.floor(x)) * 4;
                const isNumberDot = maskData[maskIndex + 3] > 100;

                const dotRadius = Math.random() * 3 + 3.5;
                const jitterX = x + (Math.random() - 0.5) * 3;
                const jitterY = y + (Math.random() - 0.5) * 3;

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
    document.getElementById('testQuizCard').classList.add('hidden');
    document.getElementById('testResultCard').classList.remove('hidden');

    let correctCount = 0;
    const tableBody = document.getElementById('resTableBody');
    tableBody.innerHTML = '';

    ISHIHARA_PLATES.forEach(plate => {
        const ans = userAnswers[plate.id] || 'Tidak Dijawab';
        const isCorrect = (ans === plate.normalVal);
        if (isCorrect) correctCount++;

        const tr = document.createElement('tr');
        tr.className = isCorrect ? 'bg-emerald-50/50' : 'bg-red-50/50';
        tr.innerHTML = `
            <td class="p-3 font-bold">Lempeng ${plate.id}</td>
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

    const scorePct = Math.round((correctCount / ISHIHARA_PLATES.length) * 100);
    document.getElementById('resPatientName').textContent = patientData.name;
    document.getElementById('resPatientId').textContent = patientData.id;
    document.getElementById('resDocId').textContent = Math.floor(1000 + Math.random() * 9000);
    document.getElementById('resScore').textContent = `${correctCount} / ${ISHIHARA_PLATES.length} (${scorePct}%)`;

    const container = document.getElementById('resDiagnosisContainer');
    const badge = document.getElementById('resStatusBadge');
    const title = document.getElementById('resDiagnosisTitle');
    const desc = document.getElementById('resDiagnosisDesc');
    const icon = document.getElementById('resDiagIcon');

    if (correctCount >= 9) {
        // NORMAL VISION
        badge.className = 'text-emerald-600 font-extrabold uppercase';
        badge.textContent = 'BEBAS BUTA WARNA (NORMAL)';

        container.className = 'p-6 rounded-2xl border-2 border-emerald-200 bg-emerald-50/60 space-y-3';
        icon.className = 'w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold';
        icon.innerHTML = '<i class="fas fa-check-circle"></i>';

        title.textContent = 'Penglihatan Warna Normal (Normal Color Vision)';
        desc.textContent = 'Pasien mampu mengidentifikasi seluruh lempeng Ishihara dengan sempurna (Skor >= 90%). Tidak ditemukan indikasi buta warna parsial (Merah-Hijau) maupun buta warna total. Pasien dinyatakan BEBAS BUTA WARNA.';
    } else if (correctCount >= 5) {
        // PARTIAL COLOR BLINDNESS (RED-GREEN)
        badge.className = 'text-amber-600 font-extrabold uppercase';
        badge.textContent = 'BUTA WARNA PARSIAL';

        container.className = 'p-6 rounded-2xl border-2 border-amber-200 bg-amber-50/60 space-y-3';
        icon.className = 'w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-bold';
        icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';

        title.textContent = 'Indikasi Buta Warna Parsial / Merah-Hijau (Protanomaly / Deuteranomaly)';
        desc.textContent = 'Pasien mengalami kesulitan mengidentifikasi persepsi kontras warna merah dan hijau pada beberapa lempeng Ishihara. Disarankan melakukan konsultasi & pemeriksaan mata lengkap di iMe Medical Center.';
    } else {
        // TOTAL COLOR BLINDNESS / SEVERE
        badge.className = 'text-red-600 font-extrabold uppercase';
        badge.textContent = 'BUTA WARNA TOTAL';

        container.className = 'p-6 rounded-2xl border-2 border-red-200 bg-red-50/60 space-y-3';
        icon.className = 'w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center text-lg font-bold';
        icon.innerHTML = '<i class="fas fa-eye-slash"></i>';

        title.textContent = 'Indikasi Buta Warna Total (Achromatopsia / Monochromacy)';
        desc.textContent = 'Pasien mengalami keterbatasan signifikan dalam membedakan spektrum warna Ishihara (Skor <= 40%). Sangat disarankan untuk menjalani evaluasi klinis bersama Dokter Spesialis Mata.';
    }

    window.scrollTo({ top: 100, behavior: 'smooth' });
}

function restartTest() {
    document.getElementById('testResultCard').classList.add('hidden');
    document.getElementById('preTestCard').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endpush
