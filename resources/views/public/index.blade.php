@extends('layouts.app')

@section('title', 'Portal Medis iMe Roleplay - Layanan Medis untuk Komunitas RP')

@section('meta_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi.')

@section('content')
    <!-- Pop-up Informasi Kenaikan Regulasi -->
    <div id="regulationModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4"
        style="display: none;">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[95vh] overflow-hidden animate-fade-in-up flex flex-col">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-red-500 via-red-600 to-pink-600 text-white p-6 sm:p-8 relative overflow-hidden">
                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 sm:space-x-6">
                        <div
                            class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-lg animate-pulse flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-2xl sm:text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-3xl font-black mb-1 sm:mb-2">UPDATE REGULASI</h2>
                            <div class="flex flex-wrap items-center mt-2 sm:mt-3 gap-2 sm:space-x-4">
                                <i class="fas fa-calendar-alt text-xs sm:text-sm"></i>
                                <span class="text-xs sm:text-sm font-semibold">07 Januari 2026</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="closeRegulationModal()"
                    class="absolute top-4 right-4 w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 p-6 sm:p-8 space-y-6 bg-slate-50 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-cash-register text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Cash Payment</h4>
                        <p class="text-sm opacity-90">Pembayaran Tunai</p>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-file-invoice text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Invoice Payment</h4>
                        <p class="text-sm opacity-90">Pembayaran Tagihan</p>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-chart-line text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Price Update</h4>
                        <p class="text-sm opacity-90">Update Harga</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                    <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-pills text-sky-500"></i> Regulasi Pengobatan Cash & Invoice
                    </h3>
                    <p class="text-sm text-slate-600 mb-4">Treatment RS: <strong>$200</strong> | Treatment Luar RS: <strong>$220</strong> | Surat Sehat: <strong>$2,000</strong> | Surat Psikologi: <strong>$3,000</strong></p>
                </div>
            </div>

            <div class="p-4 bg-white border-t border-slate-200 flex justify-end gap-3">
                <button onclick="closeRegulationModal()" class="px-6 py-2.5 bg-slate-600 text-white font-bold rounded-xl text-sm hover:bg-slate-700 transition-all">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MAIN WRAPPER WITH VIBRANT BLUE OUTER CONTAINER -->
    <div class="min-h-screen bg-[#2563eb] pt-20 pb-16 px-4 sm:px-6 lg:px-12 font-sans">
        <div class="max-w-[1320px] mx-auto space-y-8">

            <!-- =========================================================
                 1. HERO CARD (WHITE CONTAINER)
            ========================================================= -->
            <div class="bg-white rounded-[32px] p-6 sm:p-10 lg:p-12 shadow-2xl relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                    <!-- HERO LEFT COPY -->
                    <div class="lg:col-span-5 space-y-6 z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span> iMe Medical Portal
                        </div>

                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-slate-900 tracking-tight leading-[1.05]">
                            Innovation<br>
                            <span class="text-slate-900">Clinic</span><span class="text-sky-500">.</span>
                        </h1>

                        <p class="text-sm text-slate-500 leading-relaxed max-w-sm">
                            We treat not only symptoms &mdash; <strong class="text-slate-800">we care about each person</strong> with professional medical standards.
                        </p>

                        <div class="flex items-center gap-4 pt-2">
                            <a href="#layanan" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-sky-500 hover:bg-sky-600 text-white text-sm font-bold shadow-lg shadow-sky-500/30 transition-all hover:scale-105">
                                Find Doctor <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            <button onclick="showRegulationModal()" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-all">
                                Regulasi <i class="fas fa-file-alt text-xs text-slate-400"></i>
                            </button>
                        </div>

                        <!-- STATS BOX -->
                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between max-w-sm">
                            <div>
                                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_forms']) }}+</div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pasien</div>
                            </div>
                            <div class="h-8 w-px bg-slate-200"></div>
                            <div>
                                <div class="text-2xl font-black text-slate-900">{{ $stats['total_staff'] }}+</div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Dokter</div>
                            </div>
                            <div class="h-8 w-px bg-slate-200"></div>
                            <div>
                                <div class="text-2xl font-black text-sky-500">100%</div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Digital</div>
                            </div>
                        </div>
                    </div>

                    <!-- HERO CENTER/RIGHT: DOCTOR PHOTO & FLOATING BADGES -->
                    <div class="lg:col-span-7 relative flex justify-center lg:justify-end items-center min-h-[420px]">
                        <!-- Background Accent Card -->
                        <div class="absolute inset-0 bg-gradient-to-br from-sky-400 to-blue-600 rounded-[28px] transform rotate-1 scale-95 opacity-90"></div>

                        <!-- Main Doctor Image Container -->
                        <div class="relative w-full max-w-md rounded-[28px] overflow-hidden shadow-2xl bg-gradient-to-b from-sky-300 to-blue-500 border-4 border-white/20">
                            <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=900&q=85"
                                 alt="Female Doctor"
                                 class="w-full h-[440px] object-cover object-top mix-blend-normal">

                            <!-- Floating Badge 1 -->
                            <div class="absolute top-6 left-6 bg-white/95 backdrop-blur-md px-4 py-2 rounded-full shadow-lg border border-slate-100 flex items-center gap-2 animate-bounce-slow">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-bold text-slate-800">Reliability</span>
                            </div>

                            <!-- Floating Badge 2 -->
                            <div class="absolute top-16 right-6 bg-white/95 backdrop-blur-md px-4 py-2 rounded-full shadow-lg border border-slate-100 flex items-center gap-2">
                                <i class="fas fa-award text-amber-500 text-xs"></i>
                                <span class="text-xs font-bold text-slate-800">Experience</span>
                            </div>

                            <!-- Floating Badge 3 -->
                            <div class="absolute bottom-6 left-6 bg-white/95 backdrop-blur-md px-4 py-2 rounded-full shadow-lg border border-slate-100 flex items-center gap-2">
                                <i class="fas fa-user-md text-sky-500 text-xs"></i>
                                <span class="text-xs font-bold text-slate-800">Professional</span>
                            </div>
                        </div>

                        <!-- Top Right Label -->
                        <div class="hidden xl:block absolute top-4 right-4 bg-white/90 backdrop-blur-md p-4 rounded-2xl max-w-[200px] border border-slate-100 shadow-lg">
                            <h4 class="text-xs font-bold text-slate-900 mb-1">With Advanced Technologies</h4>
                            <p class="text-[10px] text-slate-500 leading-snug">Digital diagnostic & modern health services.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 2. ABOUT STATEMENT CARD (WHITE CONTAINER)
            ========================================================= -->
            <div class="bg-white rounded-[32px] p-8 sm:p-12 text-center shadow-xl space-y-4">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 max-w-4xl mx-auto leading-snug tracking-tight">
                    We combine innovative
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-sky-100 text-sky-600 text-lg mx-1 align-middle"><i class="fas fa-stethoscope"></i></span>
                    technologies with a human approach to make every patient
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 text-blue-600 text-lg mx-1 align-middle"><i class="fas fa-user-md"></i></span>
                    feel <span class="text-sky-500 underline decoration-sky-300 underline-offset-4">confident and calm.</span>
                </p>
                <p class="text-xs sm:text-sm text-slate-400 max-w-xl mx-auto">
                    Portal medis iMe menghadirkan pelayanan kesehatan cepat, transparan, dan terpercaya bagi seluruh warga Los Santos.
                </p>
                <div class="pt-2">
                    <a href="#tentang" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all hover:scale-105">
                        More about us <i class="fas fa-arrow-down text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- =========================================================
                 3. OUR MEDICAL SERVICES GRID (6 CARDS LIKE SCREENSHOT)
            ========================================================= -->
            <div id="layanan" class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 px-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-white/80">Layanan Medis</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Our medical services</h2>
                    </div>
                    <p class="text-xs text-white/70 max-w-xs">
                        We provide a full range of medical services &mdash; from consultation to diagnosis and treatment.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- CARD 01: Konsultasi Medis -->
                    <a href="{{ route('public.cek-kesehatan') }}" class="group bg-white rounded-[28px] p-7 shadow-lg border border-slate-100 flex flex-col justify-between min-h-[220px] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-sky-300 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-sky-500 transition-colors">01</span>
                            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-lg group-hover:bg-sky-500 group-hover:text-white transition-all">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                        </div>
                        <div class="space-y-1 my-3">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Konsultasi Medis</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Pemeriksaan umum, diagnosis penyakit, dan penanganan kesehatan rutin.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-sky-500 group-hover:translate-x-1 transition-transform">
                            Surat Kesehatan <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- FEATURED CENTER BLUE CARD -->
                    <a href="{{ route('public.doctor-schedule') }}" class="group bg-gradient-to-br from-sky-500 via-blue-600 to-blue-700 rounded-[28px] p-7 shadow-xl flex flex-col justify-between min-h-[220px] relative overflow-hidden text-white transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                        <div class="z-10">
                            <div class="text-xl font-black tracking-tight leading-tight">iMe<br><span class="font-normal opacity-80 text-sm">Medical</span></div>
                        </div>
                        <div class="z-10 space-y-1">
                            <h4 class="text-sm font-bold text-white/90">Pelayanan RS 24 Jam</h4>
                            <p class="text-xs text-white/70">Tim medis profesional selalu siap melayani Anda.</p>
                        </div>
                        <div class="z-10 flex items-center gap-2 text-xs font-bold text-white/90 group-hover:translate-x-1 transition-transform">
                            Lihat Jadwal Dokter <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 02: Operasi Plastik -->
                    <a href="{{ route('public.operasi-plastik') }}" class="group bg-white rounded-[28px] p-7 shadow-lg border border-slate-100 flex flex-col justify-between min-h-[220px] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-sky-300 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-sky-500 transition-colors">02</span>
                            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-lg group-hover:bg-sky-500 group-hover:text-white transition-all">
                                <i class="fas fa-user-nurse"></i>
                            </div>
                        </div>
                        <div class="space-y-1 my-3">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Operasi Plastik</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Prosedur bedah estetika oleh tim dokter spesialis berpengalaman.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-sky-500 group-hover:translate-x-1 transition-transform">
                            Daftar Oplas <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 03: Konsultasi Psikologi -->
                    <a href="{{ route('public.surat-psikolog') }}" class="group bg-white rounded-[28px] p-7 shadow-lg border border-slate-100 flex flex-col justify-between min-h-[220px] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-sky-300 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-sky-500 transition-colors">03</span>
                            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-lg group-hover:bg-sky-500 group-hover:text-white transition-all">
                                <i class="fas fa-brain"></i>
                            </div>
                        </div>
                        <div class="space-y-1 my-3">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Konsultasi Psikologi</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Dukungan & konseling kesehatan mental komprehensif.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-sky-500 group-hover:translate-x-1 transition-transform">
                            Formulir Psikologi <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 04: Karakter Kill -->
                    <a href="{{ route('public.pendaftaran-karakter') }}" class="group bg-white rounded-[28px] p-7 shadow-lg border border-slate-100 flex flex-col justify-between min-h-[220px] transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:border-sky-300 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-sky-500 transition-colors">04</span>
                            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-lg group-hover:bg-sky-500 group-hover:text-white transition-all">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        </div>
                        <div class="space-y-1 my-3">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Karakter Kill</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Layanan penanganan medis khusus untuk storyline roleplay.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-sky-500 group-hover:translate-x-1 transition-transform">
                            Daftar Sekarang <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 05: PHOTO CARD (DOCTORS IN HOSPITAL) -->
                    <div class="bg-white rounded-[28px] overflow-hidden shadow-lg border border-slate-100 relative min-h-[220px] group">
                        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=85"
                             alt="Medical Team"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent p-6 flex flex-col justify-end">
                            <span class="text-xs font-bold text-sky-400">Tim Medis Terpercaya</span>
                            <h4 class="text-sm font-bold text-white">Alta & Roxwood Hospital</h4>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 4. WHY CHOOSE US CARD (MATCHING SCREENSHOT LAYOUT)
            ========================================================= -->
            <div class="bg-white rounded-[32px] p-6 sm:p-10 shadow-2xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                    <!-- LEFT: BLUE CARD WITH DOCTORS PHOTO & FLOATING CHIPS -->
                    <div class="lg:col-span-6 relative">
                        <div class="bg-gradient-to-br from-blue-600 to-sky-500 rounded-[28px] p-6 text-white min-h-[380px] relative overflow-hidden shadow-xl flex flex-col justify-between">

                            <div>
                                <span class="text-xs font-bold text-sky-200 uppercase tracking-wider">iMe Portal</span>
                                <h3 class="text-3xl font-extrabold mt-1">Why<br>choose us</h3>
                            </div>

                            <!-- Doctor Image inside Card -->
                            <div class="relative mt-4 rounded-2xl overflow-hidden shadow-lg border-2 border-white/20">
                                <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&w=800&q=85"
                                     alt="Doctors Team"
                                     class="w-full h-48 object-cover object-top">
                            </div>

                            <!-- Floating Pills -->
                            <div class="absolute bottom-8 left-8 bg-white/95 text-slate-800 text-[11px] font-bold px-3.5 py-1.5 rounded-full shadow-lg backdrop-blur-md flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span> Experienced Doctors
                            </div>
                            <div class="absolute top-24 right-8 bg-white/95 text-slate-800 text-[11px] font-bold px-3.5 py-1.5 rounded-full shadow-lg backdrop-blur-md flex items-center gap-1.5">
                                <i class="fas fa-microscope text-sky-500 text-[10px]"></i> Modern Equipment
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: STATS & ADVANTAGES LIST -->
                    <div class="lg:col-span-6 space-y-6 lg:pl-4">
                        <div>
                            <span class="text-xs font-bold text-sky-500 uppercase tracking-wider">Keunggulan Kami</span>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 tracking-tight">Standar Medis Terbaik</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Kami berkomitmen memberikan pelayanan kesehatan berkualitas dengan pencatatan digital otomatis bagi seluruh pasien.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-6 pt-2">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-3xl font-black text-slate-900">10+</div>
                                <div class="text-xs font-bold text-slate-700 mt-1">Tahun Pengalaman</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Pelayanan roleplay medis</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-3xl font-black text-slate-900">15</div>
                                <div class="text-xs font-bold text-slate-700 mt-1">Bidang Layanan</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Spesialisasi kesehatan</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-3xl font-black text-sky-500">95%</div>
                                <div class="text-xs font-bold text-slate-700 mt-1">Kepuasan Pasien</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Ulasan positif warganet</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="text-3xl font-black text-emerald-500">98%</div>
                                <div class="text-xs font-bold text-slate-700 mt-1">Akurasi Diagnostik</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Penanganan tepat cepat</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 5. JAM OPERASIONAL & CTA CARD (SIDE-BY-SIDE)
            ========================================================= -->
            <div id="jadwal" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

                <!-- LEFT: JAM OPERASIONAL TABLE -->
                <div class="lg:col-span-7 bg-white rounded-[32px] p-6 sm:p-8 shadow-2xl flex flex-col justify-between space-y-6">
                    <div>
                        <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-4">
                            <div>
                                <span class="text-xs font-bold text-sky-500 uppercase tracking-wider">Jadwal Shift</span>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Jam Operasional</h3>
                            </div>
                        </div>

                        <!-- Notice banner -->
                        <div class="px-4 py-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-start gap-2.5 mb-6">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-sm mt-0.5"></i>
                            <div>
                                <strong class="font-bold">Info:</strong> Pelayanan sesuai ketersediaan tenaga medis (On Duty).
                            </div>
                        </div>

                        <!-- Rows -->
                        <div class="space-y-4">
                            <!-- Row 1: Operasi Plastik -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-sky-200 transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fas fa-user-nurse"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm">Operasi Plastik</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">Shift 1: 13:00&ndash;16:00 &middot; Shift 2: 20:00&ndash;22:00 WIB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Surat-Suratan Medis -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-sky-200 transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fas fa-file-medical"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm">Surat-Suratan Medis</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">Shift 1: 13:00&ndash;17:00 &middot; Shift 2: 19:00&ndash;22:00 WIB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Layanan Farmasi (24 Jam) -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200 hover:border-emerald-300 transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fas fa-pills"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm">Layanan Farmasi</h4>
                                        <p class="text-xs text-emerald-700 font-medium mt-0.5">Pengambilan & pengobatan medis</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 rounded-full bg-emerald-500 text-white text-[11px] font-bold flex items-center gap-1.5 shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> BUKA 24 JAM
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: CTA CARD -->
                <div class="lg:col-span-5 bg-gradient-to-br from-sky-500 via-blue-600 to-blue-700 rounded-[32px] p-8 text-white shadow-2xl flex flex-col justify-between space-y-6 relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="space-y-4 z-10">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-200">Ready when you are</span>
                        <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">Butuh layanan medis sekarang?</h3>
                        <p class="text-xs text-white/80 leading-relaxed">
                            Akses layanan iMe dengan mudah dan dapatkan bantuan dari tenaga medis kami kapan saja.
                        </p>
                    </div>

                    <div class="space-y-3 z-10 pt-4 border-t border-white/15">
                        <a href="#layanan" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-white hover:bg-sky-50 text-sky-600 text-sm font-bold shadow-lg transition-all hover:scale-[1.02]">
                            Lihat Layanan <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                        <button onclick="showRegulationModal()" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white border border-white/30 text-sm font-bold backdrop-blur-md transition-all">
                            Regulasi Pengobatan <i class="fas fa-file-alt text-xs opacity-80"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('styles')
<style>
    @keyframes bounceSlow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .animate-bounce-slow {
        animation: bounceSlow 3.5s ease-in-out infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    function showRegulationModal() {
        const modal = document.getElementById('regulationModal');
        if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    }
    function closeRegulationModal() {
        const modal = document.getElementById('regulationModal');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; }
    }
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('regulationModal');
        if (modal) {
            modal.addEventListener('click', e => { if (e.target === modal) closeRegulationModal(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRegulationModal(); });
        }
    });
</script>
@endpush
