@extends('layouts.app')

@section('title', 'Portal Medis iMe Roleplay - Layanan Medis Terpadu')

@section('meta_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi.')

@section('content')
    <!-- Pop-up Gambar Regulasi Pengobatan -->
    <div id="regulationModal"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-[99999] overflow-y-auto p-3 sm:p-6 flex items-start justify-center"
        style="display: none;">
        
        <!-- Sticky Close Button -->
        <button onclick="closeRegulationModal()"
            class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[100000] w-11 h-11 bg-white hover:bg-slate-100 text-slate-800 rounded-full flex items-center justify-center border border-slate-300 transition-all duration-300 hover:scale-110 shadow-2xl">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Modal Body Container -->
        <div class="relative bg-white rounded-2xl sm:rounded-3xl shadow-2xl max-w-3xl w-full my-auto overflow-hidden border border-slate-200 p-2 sm:p-4 animate-fade-in-up">
            <img src="{{ asset('images/REGULASI_IME_MEDICAL_CENTER.jpg') }}"
                 alt="Regulasi iMe Medical Center"
                 class="w-full h-auto object-contain rounded-xl sm:rounded-2xl shadow-md block">
        </div>
    </div>

    <!-- INDEPENDENCE DAY SPECIAL TOP BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-600 via-red-500 to-rose-600 text-white py-3 px-4 shadow-md border-b border-red-700/30">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="max-w-[1240px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5 text-center sm:text-left relative z-10">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/bendera_indonesia.png') }}" alt="Bendera Indonesia" class="w-8 h-5 sm:w-9 sm:h-6 object-cover rounded shadow-md border border-white/60 animate-bounce shrink-0">
                <div>
                    <span class="inline-block bg-white text-red-600 text-[10px] font-black uppercase px-2 py-0.5 rounded tracking-widest mr-2 shadow-sm">HUT RI KE-81</span>
                    <span class="text-xs sm:text-sm font-extrabold tracking-wide text-white drop-shadow-sm">
                        Dirgahayu Republik Indonesia! 1945 — 2026
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-red-600 bg-white px-3.5 py-1.5 rounded-full shadow-md">
                <span>Nusantara Baru, Indonesia Sehat & Maju 🇲🇨</span>
            </div>
        </div>
    </div>

    <!-- MAIN PAGE CONTAINER (DOMINANT WHITE THEME) -->
    <div class="min-h-screen bg-slate-100/90 pt-6 pb-16 px-3 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-[1240px] mx-auto bg-white rounded-[32px] shadow-2xl border border-slate-200/80 overflow-hidden divide-y divide-slate-100 text-slate-800">

            <!-- =========================================================
                 1. HERO SECTION (DOMINANT WHITE + RED ACCENTS)
            ========================================================= -->
            <div class="p-6 sm:p-10 lg:p-12 bg-gradient-to-b from-red-50/70 via-white to-slate-50/80 relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

                    <!-- HERO LEFT COPY -->
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-100/80 border border-red-200 text-xs font-bold text-red-600 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-ping"></span> 🇮🇩 Edisi Khusus Hari Kemerdekaan RI Ke-81
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.05]">
                            iMe<br>
                            <span class="text-slate-900">Medical Center</span><span class="text-red-600">.</span>
                        </h1>

                        <p class="text-sm text-slate-600 leading-relaxed max-w-md">
                            Pusat pelayanan medis terpadu dan profesional untuk seluruh warga Los Santos dengan standar pelayanan terbaik 24/7 dan semangat pengabdian Kemerdekaan.
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="#layanan" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-sm font-bold shadow-lg shadow-red-500/25 transition-all hover:scale-105">
                                Lihat Layanan <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            <button onclick="showRegulationModal()" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-sm font-semibold transition-all shadow-sm">
                                Regulasi Pengobatan <i class="fas fa-file-alt text-xs text-red-600"></i>
                            </button>
                            <a href="#keluhan-warga" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-sm font-bold transition-all shadow-sm hover:scale-105">
                                <i class="fas fa-bullhorn text-xs text-red-600"></i> Form Keluhan Warga
                            </a>
                        </div>

                        <!-- ANIMATED HOSPITAL LOGOS (iMe, Alta Hospital, Roxwood Hospital) -->
                        <div class="pt-4 pb-2 border-t border-slate-200/80">
                            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest mb-3 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span> Jaringan Rumah Sakit Terpadu
                            </p>
                            <div class="flex items-center gap-3 sm:gap-4 overflow-x-auto custom-scrollbar pb-1">
                                <!-- Logo iMe Roleplay -->
                                <div class="flex items-center gap-2.5 bg-white px-3.5 py-2 rounded-2xl border border-slate-200 shadow-sm hover:border-red-500/60 transition-all hover:scale-105 group shrink-0 animate-float-1">
                                    <img src="{{ asset('images/logoime.webp') }}" alt="iMe Roleplay Logo" class="w-7 h-7 object-contain group-hover:rotate-12 transition-transform duration-300">
                                    <div class="leading-tight">
                                        <span class="text-xs font-bold text-slate-800 block">iMe Network</span>
                                        <span class="text-[9px] text-red-600 font-semibold">Roleplay Community</span>
                                    </div>
                                </div>

                                <!-- Logo Alta Medical Center / EMS -->
                                <div class="flex items-center gap-2.5 bg-white px-3.5 py-2 rounded-2xl border border-slate-200 shadow-sm hover:border-sky-500/60 transition-all hover:scale-105 group shrink-0 animate-float-2">
                                    <img src="{{ asset('images/logo_ems.webp') }}" alt="Alta Medical Center" class="w-7 h-7 object-contain group-hover:scale-110 transition-transform duration-300">
                                    <div class="leading-tight">
                                        <span class="text-xs font-bold text-slate-800 block">Alta Hospital</span>
                                        <span class="text-[9px] text-sky-600 font-semibold">Medical Center</span>
                                    </div>
                                </div>

                                <!-- Logo Roxwood Hospital -->
                                <div class="flex items-center gap-2.5 bg-white px-3.5 py-2 rounded-2xl border border-slate-200 shadow-sm hover:border-purple-500/60 transition-all hover:scale-105 group shrink-0 animate-float-3">
                                    <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood Hospital" class="w-7 h-7 object-contain group-hover:rotate-[-12deg] transition-transform duration-300">
                                    <div class="leading-tight">
                                        <span class="text-xs font-bold text-slate-800 block">Roxwood Hospital</span>
                                        <span class="text-[9px] text-purple-600 font-semibold">Medical Services</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STATS BOX -->
                        <div class="pt-4 border-t border-slate-200 flex items-center justify-between max-w-sm">
                            <div>
                                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_forms']) }}+</div>
                                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pasien</div>
                            </div>
                            <div class="h-8 w-px bg-slate-200"></div>
                            <div>
                                <div class="text-2xl font-black text-slate-900">{{ $stats['total_staff'] }}+</div>
                                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tenaga Medis</div>
                            </div>
                            <div class="h-8 w-px bg-slate-200"></div>
                            <div>
                                <div class="text-2xl font-black text-red-600">100%</div>
                                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Digital</div>
                            </div>
                        </div>
                    </div>

                    <!-- HERO RIGHT PHOTO & BADGES -->
                    <div class="relative flex justify-center items-center">
                        <div class="relative w-full max-w-md h-[400px] rounded-[28px] overflow-hidden shadow-2xl bg-gradient-to-b from-red-600 via-rose-600 to-red-800 border-4 border-white">
                            <img src="{{ asset('images/foto_dokter.jpg') }}"
                                 alt="Dokter iMe Medical Center - HUT RI Ke-81"
                                 class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700">

                            <!-- Floating Badges (Themes) -->
                            <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-slate-200 flex items-center gap-2 animate-float-1">
                                <span class="text-sm">🇮🇩</span>
                                <span class="text-xs font-bold text-slate-900">Merdeka 81th</span>
                            </div>

                            <div class="absolute top-12 right-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-amber-300 flex items-center gap-2 animate-float-2">
                                <i class="fas fa-fire-alt text-amber-500 text-xs animate-pulse"></i>
                                <span class="text-xs font-bold text-amber-700">Semangat 45</span>
                            </div>

                            <div class="absolute bottom-4 left-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-red-200 flex items-center gap-2 animate-float-3">
                                <i class="fas fa-heartbeat text-red-600 text-xs"></i>
                                <span class="text-xs font-bold text-red-600">Indonesia Sehat</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 2. ABOUT STATEMENT SECTION
            ========================================================= -->
            <div class="p-8 sm:p-12 text-center bg-slate-50/80 space-y-4">
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 max-w-3xl mx-auto leading-snug tracking-tight">
                    Kami menggabungkan teknologi
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 border border-red-200 text-red-600 text-sm mx-1 align-middle"><i class="fas fa-stethoscope"></i></span>
                    medis modern dengan pendekatan personal untuk membuat setiap pasien merasa
                    <span class="text-red-600 underline decoration-red-400 underline-offset-4">aman dan percaya diri.</span>
                </p>
                <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto">
                    Portal iMe adalah ruang kepercayaan dan perawatan medis profesional bagi seluruh warga Los Santos.
                </p>
            </div>

            <!-- =========================================================
                 3. OUR MEDICAL SERVICES (GRID OF 6 CARDS)
            ========================================================= -->
            <div id="layanan" class="p-6 sm:p-10 lg:p-12 space-y-8 bg-white">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-red-600">Layanan Spesialis</span>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Layanan Medis Kami</h2>
                    </div>
                    <p class="text-xs text-slate-500 max-w-xs">
                        Layanan medis komprehensif mulai dari konsultasi umum hingga penanganan khusus.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- CARD 01: Konsultasi Medis -->
                    <a href="{{ route('public.cek-kesehatan') }}" class="group reveal-on-scroll shimmer-card bg-slate-50/80 hover:bg-red-50/40 rounded-[24px] p-6 border border-slate-200 shadow-sm hover:shadow-xl hover:border-red-400 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-red-600 transition-colors">01</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-red-600 transition-colors">Konsultasi Medis</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Pemeriksaan umum, diagnosis penyakit, dan penanganan kesehatan rutin.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-600 group-hover:translate-x-1 transition-transform">
                            Surat Kesehatan <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- FEATURED CENTER CARD (RED INDONESIA THEME) -->
                    <a href="{{ route('public.doctor-schedule') }}" class="group reveal-on-scroll shimmer-card bg-gradient-to-br from-red-600 via-rose-600 to-red-700 rounded-[24px] p-6 shadow-xl border border-red-500 text-white flex flex-col justify-between min-h-[200px] relative overflow-hidden transition-all duration-300 hover:shadow-red-600/40 hover:-translate-y-1">
                        <div class="z-10">
                            <div class="text-lg font-black tracking-tight leading-tight">iMe<br><span class="font-normal opacity-90 text-xs">Medical Center</span></div>
                        </div>
                        <div class="z-10 space-y-1">
                            <h4 class="text-sm font-bold">Pelayanan RS 24 Jam</h4>
                            <p class="text-xs text-white/90">Tim medis profesional siap melayani kebutuhan Anda.</p>
                        </div>
                        <div class="z-10 flex items-center gap-2 text-xs font-bold text-white group-hover:translate-x-1 transition-transform">
                            Lihat Jadwal Dokter <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 02: Operasi Plastik -->
                    <a href="{{ route('public.operasi-plastik') }}" class="group reveal-on-scroll shimmer-card bg-slate-50/80 hover:bg-red-50/40 rounded-[24px] p-6 border border-slate-200 shadow-sm hover:shadow-xl hover:border-red-400 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-red-600 transition-colors">02</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-user-nurse"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-red-600 transition-colors">Operasi Plastik</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Prosedur bedah estetika oleh tim dokter spesialis berpengalaman.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-600 group-hover:translate-x-1 transition-transform">
                            Daftar Oplas <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 03: Konsultasi Psikologi -->
                    <a href="{{ route('public.surat-psikolog') }}" class="group reveal-on-scroll shimmer-card bg-slate-50/80 hover:bg-red-50/40 rounded-[24px] p-6 border border-slate-200 shadow-sm hover:shadow-xl hover:border-red-400 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-red-600 transition-colors">03</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-brain"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-red-600 transition-colors">Konsultasi Psikologi</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Dukungan & konseling kesehatan mental komprehensif.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-600 group-hover:translate-x-1 transition-transform">
                            Formulir Psikologi <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 04: Karakter Kill -->
                    <a href="{{ route('public.pendaftaran-karakter') }}" class="group reveal-on-scroll shimmer-card bg-slate-50/80 hover:bg-red-50/40 rounded-[24px] p-6 border border-slate-200 shadow-sm hover:shadow-xl hover:border-red-400 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-red-600 transition-colors">04</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-red-600 transition-colors">Karakter Kill</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Layanan penanganan medis khusus untuk storyline roleplay.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-600 group-hover:translate-x-1 transition-transform">
                            Daftar Sekarang <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 05: Tes Buta Warna WHO -->
                    <a href="{{ route('public.tes-buta-warna') }}" class="group reveal-on-scroll shimmer-card bg-slate-50/80 hover:bg-red-50/40 rounded-[24px] p-6 border border-slate-200 shadow-sm hover:shadow-xl hover:border-red-400 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400 group-hover:text-red-600 transition-colors">05</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-red-600 transition-colors">Tes Buta Warna (WHO)</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Uji penglihatan warna interaktif metode Ishihara standar medis WHO.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-600 group-hover:translate-x-1 transition-transform">
                            Mulai Tes Online <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 06: PHOTO CARD -->
                    <div class="bg-slate-900 reveal-on-scroll rounded-[24px] overflow-hidden shadow-lg border border-slate-200 relative min-h-[200px] group">
                        <img src="{{ asset('images/gambar 2.png') }}"
                             alt="Tim Medis Terpercaya Alta & Roxwood Hospital"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent p-6 flex flex-col justify-end">
                            <span class="text-xs font-bold text-red-400">Tim Medis Terpercaya</span>
                            <h4 class="text-sm font-bold text-white">Alta & Roxwood Hospital</h4>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 4. FORM KELUHAN & PENGADUAN (ALTA & ROXWOOD HOSPITAL)
            ========================================================= -->
            <div id="keluhan-warga" class="p-6 sm:p-10 lg:p-12 bg-gradient-to-b from-slate-50/90 via-white to-red-50/40 relative overflow-hidden">
                <div class="max-w-4xl mx-auto space-y-8">
                    
                    <!-- SECTION HEADER -->
                    <div class="text-center space-y-3">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-100/90 border border-red-200 text-xs font-bold text-red-700 shadow-sm">
                            <i class="fas fa-bullhorn text-red-600 animate-pulse"></i>
                            <span>LAYANAN PENGADUAN & KELUHAN WARGA</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            Kotak Keluhan & Aspirasi Warga
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
                            Layanan penyampaian pengaduan, kritik, dan saran untuk <strong>Alta Hospital</strong> & <strong>Roxwood Hospital</strong> bagi seluruh warga dan pasien (bisa dikirim secara <strong>Anonim</strong>).
                        </p>
                    </div>

                    <!-- FLASH MESSAGE SUCCESS -->
                    @if(session('feedback_success'))
                        <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 shadow-lg flex items-start gap-3.5 animate-fade-in-up">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 shadow-md">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="flex-1 text-xs sm:text-sm">
                                <strong class="font-black text-emerald-950 block text-sm mb-0.5">Pengaduan Berhasil Dikirim!</strong>
                                <span>{{ session('feedback_success') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- FLASH MESSAGE ERRORS -->
                    @if($errors->any())
                        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-lg flex items-start gap-3.5 animate-fade-in-up">
                            <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shrink-0 shadow-md">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="flex-1 text-xs sm:text-sm">
                                <strong class="font-black text-rose-950 block text-sm mb-1">Mohon Lengkapi Formulir:</strong>
                                <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- MAIN COMPLAINT FORM CARD -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200/90 shadow-xl space-y-6">
                        <form action="{{ route('feedback.submit') }}" method="POST" enctype="multipart/form-data" id="publicFeedbackForm" class="space-y-6">
                            @csrf
                            <input type="hidden" name="from_home" value="1">

                            <input type="hidden" name="reporter_type" value="warga">

                            <!-- 1. PILIH RUMAH SAKIT TUJUAN (ALTA VS ROXWOOD) -->
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                                        1. Pilih Rumah Sakit Terkait <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-slate-500 font-medium">Pilih rumah sakit terkait keluhan Anda</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <!-- Option: Alta Hospital -->
                                    <label class="relative flex items-start gap-3.5 p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50/70 has-[:checked]:ring-2 has-[:checked]:ring-sky-500/20 border-slate-200 hover:border-slate-300 group">
                                        <input type="radio" name="hospital" value="alta" class="w-4 h-4 text-sky-600 focus:ring-sky-500 border-slate-300 mt-1" {{ old('hospital', 'alta') === 'alta' ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold">
                                                    <i class="fas fa-hospital-alt"></i>
                                                </div>
                                                <span class="text-sm sm:text-base font-black text-slate-900">Alta Hospital</span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-snug">Rumah Sakit Utama Kota (Central Hospital, Los Santos EMS)</p>
                                        </div>
                                    </label>

                                    <!-- Option: Roxwood Hospital -->
                                    <label class="relative flex items-start gap-3.5 p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/70 has-[:checked]:ring-2 has-[:checked]:ring-emerald-600/20 border-slate-200 hover:border-slate-300 group">
                                        <input type="radio" name="hospital" value="roxwood" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 mt-1" {{ old('hospital') === 'roxwood' ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                                    <i class="fas fa-clinic-medical"></i>
                                                </div>
                                                <span class="text-sm sm:text-base font-black text-slate-900">Roxwood Hospital</span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-snug">Pusat Layanan Medis Roxwood County & Sekitarnya</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. PILIH JENIS PENGADUAN (LAPORAN VS SARAN) -->
                            <div class="space-y-2.5 pt-2 border-t border-slate-100">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700">
                                    2. Jenis Pengaduan <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <!-- Option: Laporan / Keluhan -->
                                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/60 has-[:checked]:ring-2 has-[:checked]:ring-red-500/20 border-slate-200 hover:border-slate-300">
                                        <input type="radio" name="type" value="laporan" class="w-4 h-4 text-red-600 focus:ring-red-500 border-slate-300" {{ old('type', 'laporan') === 'laporan' ? 'checked' : '' }}>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-sm font-bold shrink-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div>
                                                <span class="text-xs sm:text-sm font-bold text-slate-900 block">Laporan / Keluhan</span>
                                                <span class="text-[10px] text-slate-500 block">Komplain pelayanan, fasilitas, antrean, atau perilaku staf</span>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Option: Masukan / Saran -->
                                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/60 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 border-slate-200 hover:border-slate-300">
                                        <input type="radio" name="type" value="masukan" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300" {{ old('type') === 'masukan' ? 'checked' : '' }}>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                            <div>
                                                <span class="text-xs sm:text-sm font-bold text-slate-900 block">Masukan / Saran</span>
                                                <span class="text-[10px] text-slate-500 block">Ide perbaikan fasilitas, sarana, atau pelayanan RS</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- 3. NAMA PENGIRIM & SUBJEK KELUHAN -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                                <!-- Nama Pengirim (Opsional) -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label for="feedback_name" class="text-xs font-black uppercase tracking-wider text-slate-700">
                                            Nama Pasien / Warga
                                        </label>
                                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Opsional (Bisa Anonim)</span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-user text-xs"></i>
                                        </span>
                                        <input type="text" id="feedback_name" name="name" value="{{ old('name') }}"
                                            placeholder="Contoh: John Doe (Kosongkan jika Anonim)"
                                            class="w-full pl-9 pr-3.5 py-3 rounded-2xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition-all">
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Jika dikosongkan, laporan akan tercatat otomatis sebagai <strong>Warga Anonim</strong>.</p>
                                </div>

                                <!-- Subjek Keluhan -->
                                <div>
                                    <label for="feedback_subject" class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                                        Subjek / Topik Keluhan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-tag text-xs"></i>
                                        </span>
                                        <input type="text" id="feedback_subject" name="subject" value="{{ old('subject') }}"
                                            list="subjectSuggestions"
                                            required
                                            placeholder="Contoh: Pelayanan lambat di Alta Hospital"
                                            class="w-full pl-9 pr-3.5 py-3 rounded-2xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition-all">
                                        <datalist id="subjectSuggestions">
                                            <option value="Keluhan Pelayanan Antrean Alta Hospital">
                                            <option value="Keluhan Pelayanan Roxwood Hospital">
                                            <option value="Ketersediaan Dokter On-Duty / Piket">
                                            <option value="Respon Ambulans / Panggilan Darurat">
                                            <option value="Sikap & Perilaku Tenaga Medis">
                                            <option value="Laporan Fasilitas & Sarana Medis Internal">
                                            <option value="Kendala Radio / Komunikasi Dinas Medic">
                                            <option value="Masukan & Saran Peningkatan Fasilitas RS">
                                        </datalist>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. DETAIL KELUHAN (TEXTAREA) -->
                            <div class="space-y-1.5">
                                <label for="feedback_message" class="block text-xs font-black uppercase tracking-wider text-slate-700">
                                    4. Detail Kronologi / Isi Keluhan <span class="text-red-500">*</span>
                                </label>
                                <textarea id="feedback_message" name="message" rows="4" required
                                    placeholder="Jelaskan secara rinci kronologi keluhan, waktu kejadian, ruangan/lokasi di rumah sakit, serta nama pihak terkait (jika ada)..."
                                    class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-300 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 focus:outline-none transition-all leading-relaxed">{{ old('message') }}</textarea>
                                <p class="text-[10px] text-slate-500">Mohon berikan informasi yang jelas dan santun agar segera ditinjau oleh jajaran pimpinan rumah sakit.</p>
                            </div>

                            <!-- 5. LAMPIRAN BUKTI GAMBAR (OPSIONAL) -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-dashed border-slate-300 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <label for="feedback_image" class="text-xs font-bold text-slate-800 flex items-center gap-1.5 cursor-pointer">
                                            <i class="fas fa-camera text-red-600"></i>
                                            <span>Lampiran Bukti Foto / Screenshot</span>
                                            <span class="text-[10px] font-normal text-slate-400">(Opsional)</span>
                                        </label>
                                        <span class="text-[10px] text-slate-500 block">Format: JPG, PNG, GIF (Maks. 5MB)</span>
                                    </div>
                                    <input type="file" id="feedback_image" name="image" accept="image/*"
                                        onchange="previewFeedbackImage(this)"
                                        class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 file:cursor-pointer cursor-pointer">
                                </div>

                                <!-- Image Preview Container -->
                                <div id="feedbackImagePreview" class="hidden pt-2">
                                    <div class="relative inline-block border border-slate-200 rounded-2xl overflow-hidden shadow-sm bg-white p-1">
                                        <img id="feedbackPreviewImg" src="" alt="Preview Bukti" class="max-h-40 rounded-xl object-contain">
                                        <button type="button" onclick="removeFeedbackImage()" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center shadow-md transition-all">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. SUBMIT BUTTON -->
                            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <i class="fas fa-shield-alt text-emerald-600 text-sm"></i>
                                    <span>Laporan terenkripsi aman & langsung diteruskan ke Manajemen.</span>
                                </div>
                                <button type="submit" id="submitFeedbackBtn"
                                    class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-rose-800 text-white text-sm font-black shadow-lg shadow-red-600/30 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-paper-plane text-xs"></i>
                                    <span>Kirim Laporan Pengaduan</span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 5. WHY CHOOSE US (SIDE-BY-SIDE 2 COLUMNS)
            ========================================================= -->
            <div class="p-6 sm:p-10 lg:p-12 bg-slate-50/80">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

                    <!-- LEFT: RED FEATURED CARD -->
                    <div class="bg-gradient-to-br from-red-600 via-rose-600 to-red-700 reveal-on-scroll rounded-[28px] p-6 text-white min-h-[360px] relative overflow-hidden shadow-xl border border-red-500 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-amber-200 uppercase tracking-wider">iMe Portal</span>
                            <h3 class="text-3xl font-black mt-1">Mengapa Memilih Kami</h3>
                        </div>

                        <div class="relative mt-4 rounded-2xl overflow-hidden shadow-md border-2 border-white/40">
                            <img src="{{ asset('images/hero.png') }}"
                                 alt="Tim Dokter iMe Medical"
                                 class="w-full h-44 object-cover object-top">
                        </div>

                        <div class="flex items-center gap-2 mt-4">
                            <span class="bg-white/90 text-slate-900 text-[11px] font-bold px-3 py-1 rounded-full shadow-md flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-600"></span> Dokter Berpengalaman
                            </span>
                            <span class="bg-white/90 text-slate-900 text-[11px] font-bold px-3 py-1 rounded-full shadow-md flex items-center gap-1.5">
                                <i class="fas fa-microscope text-red-600 text-[10px]"></i> Peralatan Modern
                            </span>
                        </div>
                    </div>

                    <!-- RIGHT: ADVANTAGES & STATS GRID -->
                    <div class="space-y-6 reveal-on-scroll">
                        <div>
                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Keunggulan Kami</span>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 tracking-tight">Standar Medis Terbaik</h3>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Kami berkomitmen memberikan pelayanan kesehatan berkualitas dengan pencatatan digital otomatis bagi seluruh pasien.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                <div class="text-2xl font-black text-slate-900">10+</div>
                                <div class="text-xs font-bold text-slate-800 mt-1">Tahun Pengalaman</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Pelayanan roleplay medis</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                <div class="text-2xl font-black text-slate-900">15</div>
                                <div class="text-xs font-bold text-slate-800 mt-1">Bidang Layanan</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Spesialisasi kesehatan</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                <div class="text-2xl font-black text-red-600">95%</div>
                                <div class="text-xs font-bold text-slate-800 mt-1">Kepuasan Pasien</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Ulasan positif warganet</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                <div class="text-2xl font-black text-amber-500">98%</div>
                                <div class="text-xs font-bold text-slate-800 mt-1">Akurasi Diagnostik</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Penanganan tepat cepat</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 5. JAM OPERASIONAL & CTA CARD (SIDE-BY-SIDE 2 COLUMNS)
            ========================================================= -->
            <div id="jadwal" class="p-6 sm:p-10 lg:p-12 bg-white">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

                    <!-- LEFT: JAM OPERASIONAL TABLE -->
                    <div class="bg-slate-50/80 reveal-on-scroll rounded-[28px] p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col justify-between space-y-6">
                        <div>
                            <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4 mb-4">
                                <div>
                                    <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Jadwal Shift</span>
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">Jam Operasional</h3>
                                </div>
                            </div>

                            <div class="px-4 py-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-start gap-2.5 mb-6">
                                <i class="fas fa-exclamation-triangle text-amber-600 text-sm mt-0.5"></i>
                                <div>
                                    <strong class="font-bold">Info:</strong> Pelayanan sesuai ketersediaan tenaga medis (On Duty).
                                </div>
                            </div>

                            <div class="space-y-3">
                                <!-- Row 1: Operasi Plastik -->
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-sm flex-shrink-0">
                                            <i class="fas fa-user-nurse"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">Operasi Plastik</h4>
                                            <p class="text-xs text-slate-500">Shift 1: 13:00&ndash;16:00 &middot; Shift 2: 20:00&ndash;22:00 WIB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2: Surat-Suratan Medis -->
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-sm flex-shrink-0">
                                            <i class="fas fa-file-medical"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">Surat-Suratan Medis</h4>
                                            <p class="text-xs text-slate-500">Shift 1: 13:00&ndash;17:00 &middot; Shift 2: 19:00&ndash;22:00 WIB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Layanan Farmasi -->
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm flex-shrink-0">
                                            <i class="fas fa-pills"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">Layanan Farmasi</h4>
                                            <p class="text-xs text-emerald-700 font-medium">Pengambilan & pengobatan medis</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> 24 JAM
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: CTA CARD -->
                    <div class="bg-gradient-to-br from-red-600 via-rose-600 to-red-700 reveal-on-scroll rounded-[28px] p-8 text-white shadow-xl border border-red-500 flex flex-col justify-between space-y-6 relative overflow-hidden">
                        <div class="space-y-4 z-10">
                            <span class="text-xs font-bold uppercase tracking-widest text-amber-200">Ready when you are</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">Butuh layanan medis sekarang?</h3>
                            <p class="text-xs text-white/90 leading-relaxed">
                                Akses layanan iMe dengan mudah dan dapatkan bantuan dari tenaga medis kami kapan saja.
                            </p>
                        </div>

                        <div class="space-y-3 z-10 pt-4 border-t border-white/20">
                            <a href="#layanan" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-red-700 text-sm font-bold shadow-lg transition-all hover:scale-[1.02]">
                                Lihat Layanan <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            <button onclick="showRegulationModal()" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-white/20 hover:bg-white/30 text-white border border-white/30 text-sm font-bold backdrop-blur-md transition-all">
                                Regulasi Pengobatan <i class="fas fa-file-alt text-xs opacity-90"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width:6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background:#ef4444; border-radius:99px; }

    /* Floating Badges */
    @keyframes float1 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    @keyframes float2 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-7px); } }
    @keyframes float3 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }

    .animate-float-1 { animation: float1 3s ease-in-out infinite; }
    .animate-float-2 { animation: float2 3.5s ease-in-out infinite 0.3s; }
    .animate-float-3 { animation: float3 2.8s ease-in-out infinite 0.6s; }

    /* Fast & Snappy Scroll Reveal Motion */
    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.25s ease-out, transform 0.25s ease-out;
        will-change: opacity, transform;
    }
    .reveal-on-scroll.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Fast Card Shimmer Glow */
    .shimmer-card {
        position: relative;
        overflow: hidden;
    }
    .shimmer-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(239, 68, 68, 0.15),
            transparent
        );
        transform: skewX(-20deg);
        transition: left 0.35s ease-out;
        pointer-events: none;
        z-index: 20;
    }
    .shimmer-card:hover::before {
        left: 140%;
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

        // Fast pre-triggering IntersectionObserver
        const observerOptions = { threshold: 0.01, rootMargin: '0px 0px 150px 0px' };
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    scrollObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal-on-scroll').forEach(el => scrollObserver.observe(el));

        // Auto scroll to feedback section if there are errors or success
        @if(session('feedback_success') || $errors->any())
            const keluhanSec = document.getElementById('keluhan-warga');
            if (keluhanSec) {
                setTimeout(() => {
                    keluhanSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        @endif

        // Handle feedback form submit loading state
        const publicFeedbackForm = document.getElementById('publicFeedbackForm');
        if (publicFeedbackForm) {
            publicFeedbackForm.addEventListener('submit', function (e) {
                const btn = document.getElementById('submitFeedbackBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Mengirim Laporan...</span>';
                }
            });
        }
    });

    function previewFeedbackImage(input) {
        const previewImg = document.getElementById('feedbackPreviewImg');
        const previewContainer = document.getElementById('feedbackImagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (previewImg) previewImg.src = e.target.result;
                if (previewContainer) previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeFeedbackImage() {
        const input = document.getElementById('feedback_image');
        const previewImg = document.getElementById('feedbackPreviewImg');
        const previewContainer = document.getElementById('feedbackImagePreview');
        if (input) input.value = '';
        if (previewImg) previewImg.src = '';
        if (previewContainer) previewContainer.classList.add('hidden');
    }
</script>
@endpush
