@extends('layouts.app')

@section('title', 'Portal Medis iMe Roleplay - Layanan Medis Terpadu')

@section('meta_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi.')

@section('content')
    <!-- Pop-up Gambar Regulasi Pengobatan -->
    <div id="regulationModal"
        class="fixed inset-0 bg-black/85 backdrop-blur-md z-[99999] overflow-y-auto p-3 sm:p-6 flex items-start justify-center"
        style="display: none;">
        
        <!-- Sticky Close Button -->
        <button onclick="closeRegulationModal()"
            class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[100000] w-11 h-11 bg-black/80 hover:bg-black text-white rounded-full flex items-center justify-center border border-white/30 transition-all duration-300 hover:scale-110 shadow-2xl">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Modal Body Container -->
        <div class="relative bg-slate-950 rounded-2xl sm:rounded-3xl shadow-2xl max-w-3xl w-full my-auto overflow-hidden border border-white/20 p-2 sm:p-4 animate-fade-in-up">
            <img src="{{ asset('images/REGULASI_IME_MEDICAL_CENTER.jpg') }}"
                 alt="Regulasi iMe Medical Center"
                 class="w-full h-auto object-contain rounded-xl sm:rounded-2xl shadow-xl block">
        </div>
    </div>

    <!-- INDEPENDENCE DAY SPECIAL TOP BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-r from-red-700 via-rose-600 to-red-800 text-white py-3 px-4 shadow-xl border-b border-red-500/40">
        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="max-w-[1240px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5 text-center sm:text-left relative z-10">
            <div class="flex items-center gap-3">
                <span class="text-2xl animate-bounce">🇲🇨</span>
                <div>
                    <span class="inline-block bg-white/20 text-amber-200 text-[10px] font-black uppercase px-2 py-0.5 rounded tracking-widest mr-2 border border-white/30 shadow-sm">HUT RI KE-81</span>
                    <span class="text-xs sm:text-sm font-extrabold tracking-wide text-white drop-shadow-sm">
                        Dirgahayu Republik Indonesia! 1945 — 2026
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-red-100 bg-black/25 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 shadow-inner">
                <span>Nusantara Baru, Indonesia Sehat & Maju 🎆</span>
            </div>
        </div>
    </div>

    <!-- MAIN PAGE CONTAINER -->
    <div class="min-h-screen bg-slate-950 pt-8 pb-16 px-3 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-[1240px] mx-auto bg-slate-900/90 backdrop-blur-2xl rounded-[32px] shadow-2xl border border-white/10 overflow-hidden divide-y divide-slate-800 text-white">

            <!-- =========================================================
                 1. HERO SECTION
            ========================================================= -->
            <div class="p-6 sm:p-10 lg:p-12 bg-gradient-to-b from-red-950/40 via-slate-900 to-slate-900/95 relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

                    <!-- HERO LEFT COPY -->
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-500/10 border border-red-500/30 text-xs font-bold text-red-400 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></span> 🇮🇩 Edisi Khusus Hari Kemerdekaan RI Ke-81
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.05]">
                            iMe<br>
                            <span class="text-white">Medical Center</span><span class="text-red-500">.</span>
                        </h1>

                        <p class="text-sm text-slate-300/90 leading-relaxed max-w-md">
                            Pusat pelayanan medis terpadu dan profesional untuk seluruh warga Los Santos dengan standar pelayanan terbaik 24/7 dan semangat pengabdian Kemerdekaan.
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="#layanan" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-sm font-bold shadow-lg shadow-red-500/30 transition-all hover:scale-105">
                                Lihat Layanan <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            <button onclick="showRegulationModal()" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/20 text-sm font-semibold transition-all backdrop-blur-md">
                                Regulasi Pengobatan <i class="fas fa-file-alt text-xs text-slate-300"></i>
                            </button>
                        </div>

                        <!-- ANIMATED HOSPITAL LOGOS (iMe, Alta Hospital, Roxwood Hospital) -->
                        <div class="pt-4 pb-2 border-t border-slate-800/80">
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-3 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span> Jaringan Rumah Sakit Terpadu
                            </p>
                            <div class="flex items-center gap-3 sm:gap-4 overflow-x-auto custom-scrollbar pb-1">
                                <!-- Logo iMe Roleplay -->
                                <div class="flex items-center gap-2.5 bg-slate-800/90 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-white/10 shadow-md hover:border-red-500/50 transition-all hover:scale-105 group shrink-0 animate-float-1">
                                    <img src="{{ asset('images/logoime.webp') }}" alt="iMe Roleplay Logo" class="w-7 h-7 object-contain group-hover:rotate-12 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(239,68,68,0.5)]">
                                    <div class="leading-tight">
                                        <span class="text-xs font-bold text-white block">iMe Network</span>
                                        <span class="text-[9px] text-red-400 font-medium">Roleplay Community</span>
                                    </div>
                                </div>

                                <!-- Logo Alta Medical Center / EMS -->
                                <div class="flex items-center gap-2.5 bg-slate-800/90 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-white/10 shadow-md hover:border-sky-500/50 transition-all hover:scale-105 group shrink-0 animate-float-2">
                                    <img src="{{ asset('images/logo_ems.webp') }}" alt="Alta Medical Center" class="w-7 h-7 object-contain group-hover:scale-110 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]">
                                    <div class="leading-tight">
                                        <span class="text-xs font-bold text-white block">Alta Hospital</span>
                                        <span class="text-[9px] text-sky-400 font-medium">Medical Center</span>
                                    </div>
                                </div>

                                <!-- Logo Roxwood Hospital -->
                                <div class="flex items-center gap-2.5 bg-slate-800/90 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-white/10 shadow-md hover:border-purple-500/50 transition-all hover:scale-105 group shrink-0 animate-float-3">
                                    <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood Hospital" class="w-7 h-7 object-contain group-hover:rotate-[-12deg] transition-transform duration-300 drop-shadow-[0_0_8px_rgba(168,85,247,0.5)]">
                                    <div class="leading-tight">
                                        <span class="text-xs font-bold text-white block">Roxwood Hospital</span>
                                        <span class="text-[9px] text-purple-400 font-medium">Medical Services</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STATS BOX -->
                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between max-w-sm">
                            <div>
                                <div class="text-2xl font-black text-white">{{ number_format($stats['total_forms']) }}+</div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pasien</div>
                            </div>
                            <div class="h-8 w-px bg-slate-800"></div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $stats['total_staff'] }}+</div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tenaga Medis</div>
                            </div>
                            <div class="h-8 w-px bg-slate-800"></div>
                            <div>
                                <div class="text-2xl font-black text-red-500">100%</div>
                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Digital</div>
                            </div>
                        </div>
                    </div>

                    <!-- HERO RIGHT PHOTO & BADGES -->
                    <div class="relative flex justify-center items-center">
                        <div class="relative w-full max-w-md h-[400px] rounded-[28px] overflow-hidden shadow-2xl bg-gradient-to-b from-red-600 via-rose-700 to-slate-950 border-4 border-white/20">
                            <img src="{{ asset('images/foto_dokter.jpg') }}"
                                 alt="Dokter iMe Medical Center - HUT RI Ke-81"
                                 class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700">

                            <!-- Floating Badges (Themes) -->
                            <div class="absolute top-4 left-4 bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-white/20 flex items-center gap-2 animate-float-1">
                                <span class="text-sm">🇮🇩</span>
                                <span class="text-xs font-bold text-white">Merdeka 81th</span>
                            </div>

                            <div class="absolute top-12 right-4 bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-amber-500/40 flex items-center gap-2 animate-float-2">
                                <i class="fas fa-fire-alt text-amber-500 text-xs animate-pulse"></i>
                                <span class="text-xs font-bold text-amber-300">Semangat 45</span>
                            </div>

                            <div class="absolute bottom-4 left-4 bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-lg border border-red-500/40 flex items-center gap-2 animate-float-3">
                                <i class="fas fa-heartbeat text-red-500 text-xs"></i>
                                <span class="text-xs font-bold text-red-300">Indonesia Sehat</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 2. ABOUT STATEMENT SECTION
            ========================================================= -->
            <div class="p-8 sm:p-12 text-center bg-slate-950/60 space-y-4">
                <p class="text-2xl sm:text-3xl font-extrabold text-white max-w-3xl mx-auto leading-snug tracking-tight">
                    Kami menggabungkan teknologi
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-500/20 border border-red-500/30 text-red-400 text-sm mx-1 align-middle"><i class="fas fa-stethoscope"></i></span>
                    medis modern dengan pendekatan personal untuk membuat setiap pasien merasa
                    <span class="text-red-400 underline decoration-red-500/50 underline-offset-4">aman dan percaya diri.</span>
                </p>
                <p class="text-xs sm:text-sm text-slate-400 max-w-lg mx-auto">
                    Portal iMe adalah ruang kepercayaan dan perawatan medis profesional bagi seluruh warga Los Santos.
                </p>
            </div>

            <!-- =========================================================
                 3. OUR MEDICAL SERVICES (GRID OF 6 CARDS)
            ========================================================= -->
            <div id="layanan" class="p-6 sm:p-10 lg:p-12 space-y-8 bg-slate-900/80">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-red-500">Layanan Spesialis</span>
                        <h2 class="text-3xl font-black text-white tracking-tight">Our medical services</h2>
                    </div>
                    <p class="text-xs text-slate-400 max-w-xs">
                        Layanan medis komprehensif mulai dari konsultasi umum hingga penanganan khusus.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- CARD 01: Konsultasi Medis -->
                    <a href="{{ route('public.cek-kesehatan') }}" class="group reveal-on-scroll shimmer-card bg-slate-800/80 hover:bg-slate-800 rounded-[24px] p-6 border border-white/10 shadow-md hover:shadow-2xl hover:border-red-500/50 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 group-hover:text-red-400 transition-colors">01</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-white group-hover:text-red-400 transition-colors">Konsultasi Medis</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Pemeriksaan umum, diagnosis penyakit, dan penanganan kesehatan rutin.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-400 group-hover:translate-x-1 transition-transform">
                            Surat Kesehatan <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- FEATURED CENTER CARD -->
                    <a href="{{ route('public.doctor-schedule') }}" class="group reveal-on-scroll shimmer-card bg-gradient-to-br from-red-600 via-rose-700 to-red-900 rounded-[24px] p-6 shadow-xl border border-red-500/40 text-white flex flex-col justify-between min-h-[200px] relative overflow-hidden transition-all duration-300 hover:shadow-red-900/50 hover:-translate-y-1">
                        <div class="z-10">
                            <div class="text-lg font-black tracking-tight leading-tight">iMe<br><span class="font-normal opacity-80 text-xs">Medical Center</span></div>
                        </div>
                        <div class="z-10 space-y-1">
                            <h4 class="text-sm font-bold">Pelayanan RS 24 Jam</h4>
                            <p class="text-xs text-white/80">Tim medis profesional siap melayani kebutuhan Anda.</p>
                        </div>
                        <div class="z-10 flex items-center gap-2 text-xs font-bold text-white/90 group-hover:translate-x-1 transition-transform">
                            Lihat Jadwal Dokter <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 02: Operasi Plastik -->
                    <a href="{{ route('public.operasi-plastik') }}" class="group reveal-on-scroll shimmer-card bg-slate-800/80 hover:bg-slate-800 rounded-[24px] p-6 border border-white/10 shadow-md hover:shadow-2xl hover:border-red-500/50 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 group-hover:text-red-400 transition-colors">02</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-user-nurse"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-white group-hover:text-red-400 transition-colors">Operasi Plastik</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Prosedur bedah estetika oleh tim dokter spesialis berpengalaman.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-400 group-hover:translate-x-1 transition-transform">
                            Daftar Oplas <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 03: Konsultasi Psikologi -->
                    <a href="{{ route('public.surat-psikolog') }}" class="group reveal-on-scroll shimmer-card bg-slate-800/80 hover:bg-slate-800 rounded-[24px] p-6 border border-white/10 shadow-md hover:shadow-2xl hover:border-red-500/50 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 group-hover:text-red-400 transition-colors">03</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-brain"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-white group-hover:text-red-400 transition-colors">Konsultasi Psikologi</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Dukungan & konseling kesehatan mental komprehensif.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-400 group-hover:translate-x-1 transition-transform">
                            Formulir Psikologi <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 04: Karakter Kill -->
                    <a href="{{ route('public.pendaftaran-karakter') }}" class="group reveal-on-scroll shimmer-card bg-slate-800/80 hover:bg-slate-800 rounded-[24px] p-6 border border-white/10 shadow-md hover:shadow-2xl hover:border-red-500/50 transition-all duration-300 flex flex-col justify-between min-h-[200px] hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 group-hover:text-red-400 transition-colors">04</span>
                            <div class="w-10 h-10 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-base group-hover:bg-red-600 group-hover:text-white transition-all">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        </div>
                        <div class="my-3 space-y-1">
                            <h3 class="text-base font-bold text-white group-hover:text-red-400 transition-colors">Karakter Kill</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">Layanan penanganan medis khusus untuk storyline roleplay.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold text-red-400 group-hover:translate-x-1 transition-transform">
                            Daftar Sekarang <i class="fas fa-arrow-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- CARD 05: PHOTO CARD -->
                    <div class="bg-slate-950 reveal-on-scroll rounded-[24px] overflow-hidden shadow-lg border border-white/10 relative min-h-[200px] group">
                        <img src="{{ asset('images/gambar 2.png') }}"
                             alt="Tim Medis Terpercaya Alta & Roxwood Hospital"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent p-6 flex flex-col justify-end">
                            <span class="text-xs font-bold text-red-400">Tim Medis Terpercaya</span>
                            <h4 class="text-sm font-bold text-white">Alta & Roxwood Hospital</h4>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 4. WHY CHOOSE US (SIDE-BY-SIDE 2 COLUMNS)
            ========================================================= -->
            <div class="p-6 sm:p-10 lg:p-12 bg-slate-950/60">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

                    <!-- LEFT: RED FEATURED CARD -->
                    <div class="bg-gradient-to-br from-red-700 via-rose-800 to-slate-900 reveal-on-scroll rounded-[28px] p-6 text-white min-h-[360px] relative overflow-hidden shadow-xl border border-red-500/30 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-amber-200 uppercase tracking-wider">iMe Portal</span>
                            <h3 class="text-3xl font-black mt-1">Why<br>choose us</h3>
                        </div>

                        <div class="relative mt-4 rounded-2xl overflow-hidden shadow-md border-2 border-white/20">
                            <img src="{{ asset('images/hero.png') }}"
                                 alt="Tim Dokter iMe Medical"
                                 class="w-full h-44 object-cover object-top">
                        </div>

                        <div class="flex items-center gap-2 mt-4">
                            <span class="bg-slate-900/90 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md border border-white/10 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Experienced Doctors
                            </span>
                            <span class="bg-slate-900/90 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md border border-white/10 flex items-center gap-1.5">
                                <i class="fas fa-microscope text-red-400 text-[10px]"></i> Modern Equipment
                            </span>
                        </div>
                    </div>

                    <!-- RIGHT: ADVANTAGES & STATS GRID -->
                    <div class="space-y-6 reveal-on-scroll">
                        <div>
                            <span class="text-xs font-bold text-red-500 uppercase tracking-wider">Keunggulan Kami</span>
                            <h3 class="text-3xl font-black text-white mt-1 tracking-tight">Standar Medis Terbaik</h3>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                Kami berkomitmen memberikan pelayanan kesehatan berkualitas dengan pencatatan digital otomatis bagi seluruh pasien.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 shadow-sm">
                                <div class="text-2xl font-black text-white">10+</div>
                                <div class="text-xs font-bold text-slate-200 mt-1">Tahun Pengalaman</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Pelayanan roleplay medis</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 shadow-sm">
                                <div class="text-2xl font-black text-white">15</div>
                                <div class="text-xs font-bold text-slate-200 mt-1">Bidang Layanan</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Spesialisasi kesehatan</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 shadow-sm">
                                <div class="text-2xl font-black text-red-400">95%</div>
                                <div class="text-xs font-bold text-slate-200 mt-1">Kepuasan Pasien</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Ulasan positif warganet</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-800/80 border border-white/10 shadow-sm">
                                <div class="text-2xl font-black text-amber-400">98%</div>
                                <div class="text-xs font-bold text-slate-200 mt-1">Akurasi Diagnostik</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Penanganan tepat cepat</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =========================================================
                 5. JAM OPERASIONAL & CTA CARD (SIDE-BY-SIDE 2 COLUMNS)
            ========================================================= -->
            <div id="jadwal" class="p-6 sm:p-10 lg:p-12 bg-slate-900">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

                    <!-- LEFT: JAM OPERASIONAL TABLE -->
                    <div class="bg-slate-800/80 reveal-on-scroll rounded-[28px] p-6 sm:p-8 border border-white/10 shadow-sm flex flex-col justify-between space-y-6">
                        <div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4 mb-4">
                                <div>
                                    <span class="text-xs font-bold text-red-400 uppercase tracking-wider">Jadwal Shift</span>
                                    <h3 class="text-2xl font-black text-white tracking-tight">Jam Operasional</h3>
                                </div>
                            </div>

                            <div class="px-4 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-medium flex items-start gap-2.5 mb-6">
                                <i class="fas fa-exclamation-triangle text-amber-400 text-sm mt-0.5"></i>
                                <div>
                                    <strong class="font-bold">Info:</strong> Pelayanan sesuai ketersediaan tenaga medis (On Duty).
                                </div>
                            </div>

                            <div class="space-y-3">
                                <!-- Row 1: Operasi Plastik -->
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900/90 border border-white/10 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-sm flex-shrink-0">
                                            <i class="fas fa-user-nurse"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white text-sm">Operasi Plastik</h4>
                                            <p class="text-xs text-slate-400">Shift 1: 13:00&ndash;16:00 &middot; Shift 2: 20:00&ndash;22:00 WIB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2: Surat-Suratan Medis -->
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900/90 border border-white/10 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-red-500/20 text-red-400 border border-red-500/30 flex items-center justify-center text-sm flex-shrink-0">
                                            <i class="fas fa-file-medical"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white text-sm">Surat-Suratan Medis</h4>
                                            <p class="text-xs text-slate-400">Shift 1: 13:00&ndash;17:00 &middot; Shift 2: 19:00&ndash;22:00 WIB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Layanan Farmasi -->
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm flex-shrink-0">
                                            <i class="fas fa-pills"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white text-sm">Layanan Farmasi</h4>
                                            <p class="text-xs text-emerald-400 font-medium">Pengambilan & pengobatan medis</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> 24 JAM
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: CTA CARD -->
                    <div class="bg-gradient-to-br from-red-700 via-rose-800 to-slate-950 reveal-on-scroll rounded-[28px] p-8 text-white shadow-xl border border-red-500/30 flex flex-col justify-between space-y-6 relative overflow-hidden">
                        <div class="space-y-4 z-10">
                            <span class="text-xs font-bold uppercase tracking-widest text-amber-200">Ready when you are</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">Butuh layanan medis sekarang?</h3>
                            <p class="text-xs text-white/80 leading-relaxed">
                                Akses layanan iMe dengan mudah dan dapatkan bantuan dari tenaga medis kami kapan saja.
                            </p>
                        </div>

                        <div class="space-y-3 z-10 pt-4 border-t border-white/15">
                            <a href="#layanan" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-red-700 text-sm font-bold shadow-lg transition-all hover:scale-[1.02]">
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
    </div>
@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width:6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background:#0ea5e9; border-radius:99px; }

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
            rgba(255, 255, 255, 0.25),
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
    });
</script>
@endpush
