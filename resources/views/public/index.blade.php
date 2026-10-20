@extends('layouts.app')

@section('title', 'Medical Center iMe — Layanan Kesehatan & Medis Terpadu Modern')

@section('meta_description', 'Portal Layanan Medis Modern iMe Roleplay — Pelayanan kesehatan komprehensif, surat kesehatan, jadwal dokter 24/7, dan pengaduan warga Los Santos.')

@push('styles')
<!-- Google Fonts: Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    /* Reset & Typography */
    body {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        background: #f8fafc !important;
        color: #1c274c !important;
        margin: 0;
        padding: 0;
    }

    .font-jakarta {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* Full Width Hero Gradient */
    .hero-gradient {
        background: linear-gradient(115deg, #1d4ed8 0%, #2563eb 45%, #3b82f6 80%, #60a5fa 100%) !important;
        color: #ffffff !important;
    }

    /* Reference Active Card Blue Gradient */
    .card-blue-gradient {
        background: linear-gradient(140deg, #3862d8 0%, #4f7cf6 55%, #628efc 100%) !important;
        color: #ffffff !important;
    }
    .card-blue-gradient * {
        color: #ffffff !important;
    }

    /* Glassmorphism Rating Card */
    .glass-rating {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(14px) !important;
        -webkit-backdrop-filter: blur(14px) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        color: #ffffff !important;
    }
    .glass-rating * {
        color: #ffffff !important;
    }

    /* Strict Contrast Enforcement for Buttons & Cards */
    .ime-btn-white {
        background-color: #ffffff !important;
        color: #1c274c !important;
    }
    .ime-btn-white span {
        color: #1c274c !important;
    }
    .ime-btn-white:hover {
        background-color: #f1f5f9 !important;
    }

    /* Reference Light Gray Bento Cards */
    .ime-bento-card {
        background-color: #f0f4fb !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        color: #1c274c !important;
        border-radius: 28px !important;
    }
    .ime-bento-card h3 {
        color: #1c274c !important;
        font-weight: 800 !important;
    }
    .ime-bento-card .bento-sub {
        color: #1c274c !important;
        font-weight: 700 !important;
    }
    .ime-bento-card .bento-desc {
        color: #627193 !important;
        line-height: 1.6 !important;
    }
    .ime-bento-card .bento-btn {
        background-color: #ffffff !important;
        color: #1c274c !important;
        font-weight: 800 !important;
        box-shadow: 0 2px 6px rgba(28, 39, 76, 0.06) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        transition: all 0.2s ease !important;
    }
    .ime-bento-card .bento-btn:hover,
    .ime-bento-card .bento-btn:hover * {
        background-color: #3862d8 !important;
        color: #ffffff !important;
        border-color: #3862d8 !important;
    }
    .ime-bento-card .bento-btn-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.3) !important;
        border: 1px solid rgba(220, 38, 38, 0.8) !important;
        transition: all 0.2s ease !important;
    }
    .ime-bento-card .bento-btn-red:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(185, 28, 28, 0.45) !important;
        transform: translateY(-1px);
    }
    .ime-bento-card .bento-btn-red span,
    .ime-bento-card .bento-btn-red i {
        color: #ffffff !important;
    }

    /* Bento Text & Illustration Gap Controls - Strictly prevent text/image overlap */
    .bento-text-col {
        position: relative;
        z-index: 10;
        width: 58% !important;
        max-width: 58% !important;
        padding-right: 14px !important;
    }
    .bento-text-col-wide {
        position: relative;
        z-index: 10;
        width: 62% !important;
        max-width: 62% !important;
        padding-right: 24px !important;
    }
    .bento-btn-col {
        position: relative;
        z-index: 10;
        max-width: 62% !important;
    }
    .bento-illu-box {
        position: absolute;
        bottom: 12px;
        right: 12px;
        width: 110px;
        height: 110px;
        pointer-events: none;
        z-index: 2;
    }
    .bento-illu-box-wide {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 28px;
        width: 125px;
        height: 125px;
        pointer-events: none;
        z-index: 2;
    }

    @media (min-width: 1280px) {
        .bento-text-col {
            width: 58% !important;
            max-width: 58% !important;
            padding-right: 18px !important;
        }
        .bento-btn-col {
            max-width: 60% !important;
        }
        .bento-illu-box {
            width: 120px;
            height: 120px;
            right: 16px;
            bottom: 16px;
        }
        .bento-illu-box-wide {
            width: 140px;
            height: 140px;
            right: 36px;
        }
    }

    @media (max-width: 768px) {
        .bento-text-col {
            width: 64% !important;
            max-width: 64% !important;
            padding-right: 10px !important;
        }
        .bento-btn-col {
            max-width: 68% !important;
        }
        .bento-illu-box {
            width: 95px;
            height: 95px;
            right: 10px;
            bottom: 10px;
        }
        .bento-illu-box-wide {
            width: 110px;
            height: 110px;
            right: 16px;
        }
    }

    /* =========================================================
       Section 3: Standar Medis & Feature Switcher
    ========================================================= */
    .standar-medis-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        align-items: stretch;
    }
    .standar-medis-left {
        width: 100%;
        display: flex;
        gap: 1rem;
    }
    .standar-medis-right {
        width: 100%;
        position: relative;
    }
    @media (min-width: 1024px) {
        .standar-medis-layout {
            flex-direction: row !important;
            align-items: stretch !important;
            gap: 1.75rem !important;
        }
        .standar-medis-left {
            width: 58% !important;
            flex: 0 0 58% !important;
        }
        .standar-medis-right {
            width: 42% !important;
            flex: 0 0 42% !important;
        }
    }

    /* Vertical Indicator Bar */
    .feat-indicator-wrap {
        display: none;
    }
    @media (min-width: 640px) {
        .feat-indicator-wrap {
            display: flex;
            flex-direction: column;
            width: 6px;
            background-color: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            padding: 2px 0;
            position: relative;
        }
        .feat-indicator-bar {
            width: 100%;
            height: 25%;
            background-color: #2563eb;
            border-radius: 9999px;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
    }

    /* 4 Feature Switcher Cards */
    .feature-card-item {
        border-radius: 20px !important;
        padding: 1.25rem 1.35rem !important;
        min-height: 155px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-start !important;
        cursor: pointer !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    /* Active Card: Vibrant Blue Gradient */
    .feature-card-item.is-active {
        background: linear-gradient(135deg, #3862d8 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.28) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        transform: translateY(-2px);
    }
    .feature-card-item.is-active h3 {
        color: #ffffff !important;
        font-weight: 800 !important;
    }
    .feature-card-item.is-active p {
        color: rgba(255, 255, 255, 0.92) !important;
        font-size: 0.75rem !important;
        line-height: 1.55 !important;
    }
    .feature-card-item.is-active .feat-icon-box {
        background-color: #ffffff !important;
        color: #2563eb !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
    }
    .feature-card-item.is-active .feat-icon-box i {
        color: #2563eb !important;
    }

    /* Inactive Card: Soft Light Slate Blue */
    .feature-card-item.is-inactive {
        background-color: #f0f4fb !important;
        color: #1c274c !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 2px 8px rgba(28, 39, 76, 0.04) !important;
    }
    .feature-card-item.is-inactive:hover {
        background-color: #e5effd !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-2px);
    }
    .feature-card-item.is-inactive h3 {
        color: #1c274c !important;
        font-weight: 800 !important;
    }
    .feature-card-item.is-inactive p {
        color: #627193 !important;
        font-size: 0.75rem !important;
        line-height: 1.55 !important;
    }
    .feature-card-item.is-inactive .feat-icon-box {
        background-color: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25) !important;
    }
    .feature-card-item.is-inactive .feat-icon-box i {
        color: #ffffff !important;
    }

    .feat-icon-box {
        width: 42px !important;
        height: 42px !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.1rem !important;
        margin-bottom: 0.85rem !important;
        transition: all 0.25s ease !important;
        flex-shrink: 0 !important;
    }

    /* Right Side Photo Container with Offset Decor */
    .standar-medis-photo-box {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 340px;
        display: flex;
        align-items: stretch;
    }
    .standar-medis-photo-offset {
        position: absolute;
        right: 0;
        top: 1rem;
        bottom: 0.5rem;
        width: 4.5rem;
        background-color: #2563eb;
        border-radius: 24px;
        z-index: 1;
    }
    .standar-medis-photo-card {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 100%;
        min-height: 340px;
        border-radius: 24px !important;
        overflow: hidden !important;
        border: 4px solid #ffffff !important;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12) !important;
        background-color: #0f172a !important;
    }
    .standar-medis-photo-card img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        display: block;
    }
    .standar-medis-floating-badge {
        position: absolute;
        bottom: 16px;
        left: 16px;
        right: 16px;
        background-color: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 18px !important;
        padding: 12px 18px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        z-index: 5;
    }

    /* 3D Floating Animations */
    @keyframes floatSlow {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-6px) rotate(1.5deg); }
    }
    .float-3d {
        animation: floatSlow 5s ease-in-out infinite;
    }

    @keyframes float1 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    @keyframes float2 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-7px); } }
    @keyframes float3 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }

    .animate-float-1 { animation: float1 3s ease-in-out infinite; }
    .animate-float-2 { animation: float2 3.5s ease-in-out infinite 0.3s; }
    .animate-float-3 { animation: float3 2.8s ease-in-out infinite 0.6s; }

    /* Custom Responsive Hero Grid */
    .hero-grid {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    @media (min-width: 1024px) {
        .hero-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            align-items: center;
            gap: 2.5rem;
        }
        .hero-left-col {
            grid-column: span 7 / span 7;
        }
        .hero-right-col {
            grid-column: span 5 / span 5;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
    }
    @media (min-width: 1280px) {
        .hero-grid {
            gap: 3.5rem;
        }
    }

    /* Hero Full-Width Background with Doctor Team Illustration */
    .hero-team-bg {
        position: relative;
        background-color: #123494;
        background-image: 
            linear-gradient(to right, 
                rgba(12, 33, 102, 0.97) 0%, 
                rgba(18, 52, 148, 0.92) 32%, 
                rgba(28, 77, 216, 0.72) 52%, 
                rgba(37, 99, 235, 0.40) 75%, 
                rgba(37, 99, 235, 0.20) 100%
            ),
            url('{{ asset("images/tim_dokter.png") }}');
        background-size: cover;
        background-position: center center !important;
        background-repeat: no-repeat;
    }

    @media (max-width: 1023px) {
        .hero-team-bg {
            background-image: 
                linear-gradient(to bottom, 
                    rgba(12, 33, 102, 0.96) 0%, 
                    rgba(18, 52, 148, 0.90) 50%, 
                    rgba(28, 77, 216, 0.80) 100%
                ),
                url('{{ asset("images/tim_dokter.png") }}');
            background-position: center center !important;
        }
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #ffffff;
        border-radius: 9999px;
    }
</style>
@endpush

@section('content')
<div class="w-full font-jakarta text-[#1c274c] antialiased selection:bg-[#2563eb] selection:text-white -mt-2">

    <!-- Pop-up Gambar Regulasi Pengobatan -->
    <div id="regulationModal"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-[99999] overflow-y-auto p-3 sm:p-6 flex items-start justify-center"
        style="display: none;">
        <button onclick="closeRegulationModal()"
            class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[100000] w-11 h-11 bg-white hover:bg-slate-100 text-slate-800 rounded-full flex items-center justify-center border border-slate-300 transition-all duration-300 hover:scale-110 shadow-2xl">
            <i class="fas fa-times text-xl"></i>
        </button>
        <div class="relative bg-white rounded-2xl sm:rounded-3xl shadow-2xl max-w-3xl w-full my-auto overflow-hidden border border-slate-200 p-2 sm:p-4 animate-fade-in-up">
            <img src="{{ asset('images/REGULASI_IME_MEDICAL_CENTER.jpg') }}"
                 alt="Regulasi iMe Medical Center"
                 class="w-full h-auto object-contain rounded-xl sm:rounded-2xl shadow-md block">
        </div>
    </div>

    @php
        $activeRecruitment = \App\Models\RecruitmentPeriod::currentOpen('alta');
    @endphp

    <!-- =========================================================
         1. FULL-WIDTH HERO SECTION (MODERN MEDICAL BLUE)
    ========================================================= -->
    <section class="w-full hero-team-bg text-white pt-6 sm:pt-10 pb-12 sm:pb-20 px-4 sm:px-6 lg:px-8 shadow-xl relative overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10">

            @if($activeRecruitment)
            <!-- RECRUITMENT BANNER INSIDE HERO -->
            <div class="mb-6 sm:mb-8 p-3 sm:p-4 rounded-2xl bg-amber-500/20 backdrop-blur-xl border border-amber-400/40 shadow-[0_8px_24px_rgba(245,158,11,0.2)] flex flex-col sm:flex-row items-center justify-between gap-3 text-white">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="w-9 h-9 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-sm shrink-0 shadow-md">
                        <i class="fa-solid fa-bullhorn animate-bounce"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-block bg-amber-400 text-slate-950 font-black text-[10px] uppercase px-2 py-0.5 rounded tracking-wider shadow-sm">
                                OPEN RECRUITMENT
                            </span>
                            <span class="text-xs sm:text-sm font-bold text-amber-300">
                                Medic iMe Sedang Buka Pendaftaran!
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-white/90 font-normal mt-0.5">
                            Bergabunglah bersama kami sebagai Paramedic iMe Medical Center ({{ $activeRecruitment->batch_name }}). Pendaftaran online telah dibuka!
                        </p>
                    </div>
                </div>
                <div class="shrink-0 w-full sm:w-auto text-right">
                    <a href="{{ route('public.recruitment') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black shadow-lg transition-all hover:scale-105 w-full sm:w-auto" style="color: #0f172a !important;">
                        <span style="color: #0f172a !important;">Daftar Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-[10px]" style="color: #0f172a !important;"></i>
                    </a>
                </div>
            </div>
            @endif
            
            <div class="hero-grid">
                
                <!-- Hero Left Column -->
                <div class="hero-left-col z-20">
                    
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-xs font-bold text-white mb-4 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>medicalcenterime.my.id &middot; Pelayanan Terpadu 24/7</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-[1.1] tracking-tight mb-4 text-white">
                        Layanan Medis Modern & Terpercaya di Kota Los Santos<span class="text-amber-300">.</span>
                    </h1>

                    <p class="text-white/90 text-sm sm:text-base leading-relaxed max-w-2xl mb-8 font-normal">
                        Pusat pelayanan medis terpadu dan profesional untuk seluruh warga Los Santos dengan standar pelayanan terbaik, cepat, ramah, didukung sistem rekam medis digital otomatis 24 jam.
                    </p>

                    <!-- Two Primary Pill CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-8 sm:mb-10">
                        <a href="{{ route('public.form', ['type' => 'janji_temu', 'poli' => 'Poli Umum']) }}" 
                           class="ime-btn-white pl-5 pr-2 py-2 rounded-full font-extrabold text-xs sm:text-sm flex items-center gap-3 shadow-lg hover:scale-105 transition duration-200">
                            <span style="color: #1c274c !important;">Buat Janji Temu</span>
                            <span class="w-7 h-7 rounded-full bg-[#2563eb] text-white flex items-center justify-center transition">
                                <i class="fa-solid fa-arrow-right text-xs text-white"></i>
                            </span>
                        </a>
                        <a href="{{ route('public.cek-kesehatan') }}" 
                           class="border-2 border-white/80 hover:border-white hover:bg-white/15 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-full shadow-md transition hover:scale-105" style="color: #ffffff !important;">
                            Surat Kesehatan
                        </a>
                        <button onclick="showRegulationModal()" 
                           class="border-2 border-white/80 hover:border-white hover:bg-white/15 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-full shadow-md transition hover:scale-105" style="color: #ffffff !important;">
                            Regulasi Medis
                        </button>
                    </div>

                    <!-- ANIMATED INTEGRATED HOSPITAL NETWORK LOGOS -->
                    <div class="mb-8 pt-4 pb-4 border-t border-b border-white/20">
                        <p class="text-[10px] uppercase font-bold text-white/85 tracking-widest mb-3 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Jaringan Rumah Sakit Terpadu
                        </p>
                        <div class="flex items-center gap-3 sm:gap-4 overflow-x-auto custom-scrollbar pb-1">
                            <!-- Logo iMe Roleplay -->
                            <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-md shrink-0 animate-float-1">
                                <img src="{{ asset('images/logoime.webp') }}" alt="iMe Roleplay Logo" class="w-7 h-7 object-contain">
                                <div class="leading-tight">
                                    <span class="text-xs font-bold block" style="color: #1c274c !important;">iMe Network</span>
                                    <span class="text-[9px] font-semibold" style="color: #2563eb !important;">Roleplay Community</span>
                                </div>
                            </div>

                            <!-- Logo Alta Medical Center / EMS -->
                            <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-md shrink-0 animate-float-2">
                                <img src="{{ asset('images/logo_ems.webp') }}" alt="Alta Medical Center" class="w-7 h-7 object-contain">
                                <div class="leading-tight">
                                    <span class="text-xs font-bold block" style="color: #1c274c !important;">Alta Hospital</span>
                                    <span class="text-[9px] font-semibold" style="color: #2563eb !important;">Medical Center</span>
                                </div>
                            </div>

                            <!-- Logo Roxwood Hospital -->
                            <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-md shrink-0 animate-float-3">
                                <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood Hospital" class="w-7 h-7 object-contain">
                                <div class="leading-tight">
                                    <span class="text-xs font-bold block" style="color: #1c274c !important;">Roxwood Hospital</span>
                                    <span class="text-[9px] font-semibold" style="color: #7c3aed !important;">Medical Services</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Glassmorphism Rating & Stats Box -->
                    <div class="glass-rating rounded-[24px] p-4 sm:px-6 sm:py-4 inline-flex flex-wrap sm:flex-nowrap items-center gap-6 sm:gap-8 shadow-xl">
                        <div>
                            <div class="text-3xl sm:text-[40px] font-black leading-none tracking-tight flex items-start text-white">
                                4.9<span class="text-xl sm:text-2xl ml-0.5 text-amber-300">*</span>
                            </div>
                            <div class="text-[11px] text-white/90 leading-tight mt-1.5 font-medium">
                                Kepuasan Pasien<br>Kota Los Santos
                            </div>
                        </div>

                        <div class="h-10 w-px bg-white/30 hidden sm:block"></div>

                        <div>
                            <div class="text-2xl sm:text-3xl font-black leading-none tracking-tight text-white">
                                {{ ($stats['total_forms'] ?? 0) > 0 ? number_format($stats['total_forms']) . '+' : '1.500+' }}
                            </div>
                            <div class="text-[11px] text-white/90 leading-tight mt-1 font-medium">
                                Pasien Terlayani
                            </div>
                        </div>

                        <div class="h-10 w-px bg-white/30 hidden sm:block"></div>

                        <div>
                            <div class="text-2xl sm:text-3xl font-black leading-none tracking-tight text-white">
                                {{ ($stats['total_staff'] ?? 0) > 0 ? $stats['total_staff'] . '+' : '50+' }}
                            </div>
                            <div class="text-[11px] text-white/90 leading-tight mt-1 font-medium">
                                Tenaga Medis
                            </div>
                        </div>

                        <button onclick="openModal('reviewModal')" class="bg-white/20 hover:bg-white/30 text-white text-xs font-bold px-4 py-2 rounded-full border border-white/30 transition shadow-sm ml-auto sm:ml-2">
                            Lihat Ulasan
                        </button>
                    </div>

                </div>

                <!-- Hero Right Column: Translucent Live Status & Quick Action Card -->
                <div class="hero-right-col relative flex flex-col items-center lg:items-end justify-center w-full mt-8 lg:mt-0">
                    
                    <div class="w-full max-w-md bg-slate-950/45 backdrop-blur-xl rounded-[30px] p-6 border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.35)] text-white">
                        
                        <!-- Header Status -->
                        <div class="flex items-center justify-between pb-3.5 border-b border-white/15 mb-4">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-400 block mb-0.5">Status Pelayanan Medis</span>
                                <h3 class="text-base sm:text-lg font-black text-white">Instalasi & Dokter Jaga</h3>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-500/25 text-emerald-300 border border-emerald-400/40 text-[10px] font-black flex items-center gap-1.5 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> SIAGA 24/7
                            </span>
                        </div>

                        <!-- Live Service Status Rows -->
                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-truck-medical"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-[11px] sm:text-xs">IGD & Gawat Darurat</p>
                                        <p class="text-[9px] sm:text-[10px] text-white/70">Alta & Roxwood Hospital</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-[10px] font-extrabold">24 Jam</span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-300 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-file-waveform"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-[11px] sm:text-xs">Surat Sehat Online</p>
                                        <p class="text-[9px] sm:text-[10px] text-white/70">Lisensi, Kerja & Warga</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 text-[10px] font-extrabold">Aktif</span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-stethoscope"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-[11px] sm:text-xs">Poliklinik & Dokter Jaga</p>
                                        <p class="text-[9px] sm:text-[10px] text-white/70">Pemeriksaan & Konsultasi</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-extrabold">On-Duty</span>
                            </div>
                        </div>

                        <!-- Card Action CTA -->
                        <div class="mt-4 pt-3.5 border-t border-white/15 flex items-center justify-between gap-3">
                            <span class="text-[11px] text-white/80">Dokter siaga melayani Anda</span>
                            <a href="{{ route('public.doctor-schedule') }}" class="bg-white hover:bg-blue-50 text-[#1c274c] font-black text-xs px-4 py-2 rounded-full shadow-md inline-flex items-center gap-1.5 transition shrink-0" style="color: #1c274c !important;">
                                <span style="color: #1c274c !important;">Cek Jadwal Jaga</span>
                                <i class="fa-solid fa-arrow-right text-[10px]" style="color: #1c274c !important;"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Verified Badge underneath card -->
                    <div class="mt-3 flex items-center gap-2 text-xs font-semibold text-white/90 bg-slate-950/30 backdrop-blur-md px-4 py-1.5 rounded-full border border-white/15 shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-400"></i>
                        <span class="text-[11px]">50+ Dokter & Paramedic Terakreditasi Los Santos</span>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================
         2. LAYANAN MEDIS KAMI (BENTO GRID 3 ATAS + 2 BAWAH)
    ========================================================= -->
    <section id="layanan" class="w-full bg-white py-12 sm:py-16 px-4 sm:px-6 lg:px-8 border-b border-slate-100">
        <div class="max-w-7xl mx-auto">
            
            <!-- Section Heading -->
            <div class="mb-8 sm:mb-10">
                <h2 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold text-[#1c274c] leading-[1.18] tracking-tight">
                    Layanan Medis Kami &mdash;<br>
                    <span class="text-[#1c274c]">Peduli & Siaga untuk Kesehatan Anda</span>
                </h2>
            </div>

            <!-- TOP ROW: 3 Bento Columns (Surat Sehat, Jadwal Dokter, Tes Buta Warna) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                
                <!-- Card 1: Surat Kesehatan (Light Gray Card with 3D SVG) -->
                <div class="ime-bento-card hover:bg-[#eaf1fb] transition-all duration-300 p-6 sm:p-7 relative overflow-hidden flex flex-col justify-between min-h-[270px] group">
                    <div class="bento-text-col">
                        <h3 class="text-xl sm:text-2xl font-black text-[#1c274c] leading-snug">
                            Surat Kesehatan
                        </h3>
                        <p class="bento-sub text-xs sm:text-sm text-[#1c274c] mt-0.5 mb-2.5">
                            administrasi & lisensi cepat
                        </p>
                        <p class="bento-desc text-[11px] sm:text-xs leading-relaxed">
                            Pengurusan surat keterangan sehat resmi untuk lisensi berkendara, instansi, & izin kerja.
                        </p>
                    </div>

                    <div class="bento-btn-col mt-6">
                        <a href="{{ route('public.cek-kesehatan') }}" class="bento-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs">
                            <span>Buat Surat Sehat</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <!-- 3D Medical SVG -->
                    <div class="bento-illu-box float-3d">
                        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-xl">
                            <defs>
                                <linearGradient id="blue3d1" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#6894ff" />
                                    <stop offset="100%" stop-color="#2b54c4" />
                                </linearGradient>
                                <linearGradient id="white3d" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#ffffff" />
                                    <stop offset="100%" stop-color="#dbe6ff" />
                                </linearGradient>
                            </defs>
                            <rect x="45" y="95" width="120" height="65" rx="32" transform="rotate(-35 105 127)" fill="url(#blue3d1)" />
                            <rect x="57" y="105" width="96" height="44" rx="22" transform="rotate(-35 105 127)" fill="#1d3f99" />
                            <rect x="75" y="45" width="34" height="120" rx="17" transform="rotate(28 92 105)" fill="url(#white3d)" />
                            <ellipse cx="132" cy="52" rx="18" ry="13" transform="rotate(28 132 52)" fill="#4d79eb" />
                            <ellipse cx="128" cy="50" rx="13" ry="9" transform="rotate(28 128 50)" fill="#ffffff" />
                            <circle cx="52" cy="88" r="11" fill="url(#white3d)" />
                            <circle cx="156" cy="96" r="7" fill="url(#blue3d1)" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Jadwal Dokter (Highlighted Blue Gradient Card with 3D Chair SVG) -->
                <div class="card-blue-gradient text-white rounded-[28px] p-6 sm:p-7 relative overflow-hidden flex flex-col justify-between min-h-[270px] shadow-[0_12px_32px_rgba(56,98,216,0.28)] group">
                    <div class="bento-text-col">
                        <h3 class="text-xl sm:text-2xl font-black text-white leading-snug">
                            Jadwal Dokter
                        </h3>
                        <p class="text-xs sm:text-sm font-semibold text-white/95 mt-0.5 mb-2.5">
                            pelayanan medis siaga 24 jam
                        </p>
                        <p class="text-[11px] sm:text-xs text-white/85 leading-relaxed font-normal">
                            Cek ketersediaan dokter on-duty, poliklinik umum, dan jadwal praktek harian.
                        </p>
                    </div>

                    <div class="bento-btn-col mt-6 flex flex-wrap items-center gap-2">
                        <a href="{{ route('public.doctor-schedule') }}" class="bg-white hover:bg-blue-50 text-[#1c274c] font-black text-xs px-4 py-2.5 rounded-full shadow-md inline-flex items-center gap-1.5 transition" style="color: #1c274c !important;">
                            <span style="color: #1c274c !important;">Lihat Jadwal</span>
                            <i class="fa-solid fa-arrow-right text-[10px]" style="color: #1c274c !important;"></i>
                        </a>
                        <a href="{{ route('public.form', ['type' => 'janji_temu', 'poli' => 'Poli Umum']) }}" class="bg-white/20 hover:bg-white/30 text-white font-bold text-xs px-3.5 py-2.5 rounded-full border border-white/30 inline-flex items-center gap-1 transition backdrop-blur-sm">
                            <span class="text-white">Janji Temu</span>
                            <i class="fa-solid fa-calendar-check text-[10px] text-white"></i>
                        </a>
                    </div>

                    <!-- 3D Dental / Medical Chair SVG -->
                    <div class="bento-illu-box float-3d" style="animation-delay: 0.7s;">
                        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-2xl">
                            <ellipse cx="115" cy="170" rx="62" ry="18" fill="#5e90ff" opacity="0.6"/>
                            <ellipse cx="115" cy="165" rx="52" ry="14" fill="#8bb1ff" />
                            <rect x="103" y="130" width="24" height="34" rx="6" fill="#e2ebff" />
                            <rect x="75" y="112" width="68" height="22" rx="11" transform="rotate(-8 109 123)" fill="#244bc0" />
                            <rect x="115" y="58" width="26" height="66" rx="13" transform="rotate(15 128 91)" fill="#ffffff" />
                            <rect x="119" y="64" width="18" height="52" rx="9" transform="rotate(15 128 91)" fill="#3b66d6" />
                            <rect x="85" y="96" width="38" height="10" rx="5" fill="#ffffff" />
                            <path d="M72 155 L72 70 Q72 48 95 48 L112 48" stroke="#dbe7ff" stroke-width="6" fill="none" stroke-linecap="round"/>
                            <ellipse cx="115" cy="48" rx="12" ry="7" fill="#ffffff" />
                        </svg>
                    </div>
                </div>

                <!-- Card 3: Tes Buta Warna (Light Gray Card with 3D Diagnostic Mirror SVG) -->
                <div class="ime-bento-card hover:bg-[#eaf1fb] transition-all duration-300 p-6 sm:p-7 relative overflow-hidden flex flex-col justify-between min-h-[270px] group">
                    <div class="bento-text-col">
                        <h3 class="text-xl sm:text-2xl font-black text-[#1c274c] leading-snug">
                            Tes Buta Warna
                        </h3>
                        <p class="bento-sub text-xs sm:text-sm text-[#1c274c] mt-0.5 mb-2.5">
                            standar medis WHO Ishihara
                        </p>
                        <p class="bento-desc text-[11px] sm:text-xs leading-relaxed">
                            Uji penglihatan warna interaktif 14 plates Ishihara dengan sertifikasi hasil instan.
                        </p>
                    </div>

                    <div class="bento-btn-col mt-6">
                        <a href="{{ route('public.tes-buta-warna') }}" class="bento-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs">
                            <span>Mulai Tes Online</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <!-- 3D Magnifying Glass / Mirror SVG -->
                    <div class="bento-illu-box float-3d" style="animation-delay: 1.3s;">
                        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-xl">
                            <defs>
                                <linearGradient id="glassLens" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#9ec0ff" />
                                    <stop offset="50%" stop-color="#4e7df0" />
                                    <stop offset="100%" stop-color="#2952bf" />
                                </linearGradient>
                            </defs>
                            <rect x="52" y="138" width="65" height="16" rx="8" transform="rotate(-45 84 146)" fill="#94a3b8" />
                            <rect x="42" y="148" width="45" height="18" rx="9" transform="rotate(-45 64 157)" fill="#64748b" />
                            <circle cx="122" cy="92" r="46" fill="#cbd5e1" stroke="#94a3b8" stroke-width="4" />
                            <circle cx="120" cy="90" r="39" fill="url(#glassLens)" />
                            <ellipse cx="106" cy="74" rx="16" ry="8" transform="rotate(-30 106 74)" fill="#ffffff" opacity="0.55" />
                        </svg>
                    </div>
                </div>

            </div>

            <!-- BOTTOM ROW: 2 Wider Bento Cards (Operasi Plastik & Psikologi / Karakter Kill) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                
                <!-- Card 4: Operasi Plastik (Wide Card with 3D Tooth & Heart Badge SVG) -->
                <div class="ime-bento-card hover:bg-[#eaf1fb] transition-all duration-300 p-6 sm:p-8 relative overflow-hidden flex flex-col justify-between min-h-[250px]">
                    <div class="bento-text-col-wide">
                        <h3 class="text-xl sm:text-2xl font-black text-[#1c274c] leading-snug">
                            Operasi Plastik
                        </h3>
                        <p class="bento-sub text-xs sm:text-sm text-[#1c274c] mt-0.5 mb-2.5">
                            bedah estetika & rekonstruksi
                        </p>
                        <p class="bento-desc text-[11px] sm:text-xs leading-relaxed">
                            Prosedur bedah estetika wajah/tubuh, rekonstruksi penampilan, dan penanganan luka oleh tim dokter spesialis.
                        </p>
                    </div>

                    <div class="relative z-10 mt-6">
                        <a href="{{ route('public.operasi-plastik') }}" class="bento-btn inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs">
                            <span>Pendaftaran Oplas</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <!-- 3D Tooth on Blue Circle with Heart Badge SVG -->
                    <div class="bento-illu-box-wide float-3d" style="animation-delay: 0.4s;">
                        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-xl">
                            <circle cx="104" cy="104" r="76" fill="#2550c2" />
                            <circle cx="100" cy="100" r="74" fill="url(#blue3d1)" />
                            <path d="M72 62 C55 62 48 80 54 102 C58 118 64 142 74 142 C82 142 86 120 100 120 C114 120 118 142 126 142 C136 142 142 118 146 102 C152 80 145 62 128 62 C116 62 108 70 100 70 C92 70 84 62 72 62 Z" fill="url(#white3d)" />
                            <path d="M68 106 C61 98 48 102 48 113 C48 124 68 134 68 138 C68 134 88 124 88 113 C88 102 75 98 68 106 Z" fill="#2e5ce0" stroke="#9ec0ff" stroke-width="2" />
                        </svg>
                    </div>
                </div>

                <!-- Card 5: Psikologi & Karakter Kill (Wide Card with 3D Blue Medical Case SVG) -->
                <div class="ime-bento-card hover:bg-[#eaf1fb] transition-all duration-300 p-6 sm:p-8 relative overflow-hidden flex flex-col justify-between min-h-[250px]">
                    <div class="bento-text-col-wide">
                        <h3 class="text-xl sm:text-2xl font-black text-[#1c274c] leading-snug">
                            Psikologi & Kasus Medis
                        </h3>
                        <p class="bento-sub text-xs sm:text-sm text-[#1c274c] mt-0.5 mb-2.5">
                            konseling mental & karakter kill
                        </p>
                        <p class="bento-desc text-[11px] sm:text-xs leading-relaxed">
                            Dukungan konseling kesehatan mental komprehensif serta penanganan medis khusus storyline roleplay Los Santos.
                        </p>
                    </div>

                    <div class="relative z-10 mt-6 flex flex-wrap gap-2.5">
                        <a href="{{ route('public.surat-psikolog') }}" class="bento-btn inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-xs">
                            <span>Form Psikologi</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                        <a href="{{ route('public.pendaftaran-karakter') }}" class="bento-btn-red inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-xs" style="color: #ffffff !important;">
                            <span style="color: #ffffff !important;">Karakter Kill</span>
                            <i class="fa-solid fa-arrow-right text-[10px]" style="color: #ffffff !important;"></i>
                        </a>
                    </div>

                    <!-- 3D Blue Medical Kit Box SVG -->
                    <div class="bento-illu-box-wide float-3d" style="animation-delay: 1s;">
                        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-xl">
                            <rect x="36" y="42" width="130" height="115" rx="26" transform="rotate(8 101 99)" fill="#1d44b8" />
                            <rect x="30" y="38" width="130" height="115" rx="26" transform="rotate(8 95 95)" fill="url(#blue3d1)" />
                            <circle cx="68" cy="82" r="12" fill="#ffffff" />
                            <rect x="48" y="114" width="22" height="12" rx="6" transform="rotate(8 59 120)" fill="#ffffff" />
                            <rect x="78" y="118" width="22" height="12" rx="6" transform="rotate(8 89 124)" fill="#ffffff" />
                            <rect x="108" y="122" width="22" height="12" rx="6" transform="rotate(8 119 128)" fill="#ffffff" />
                        </svg>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================
         3. FULL-WIDTH STANDAR MEDIS & FEATURE SWITCHER
    ========================================================= -->
    <section id="tentang" class="w-full bg-[#f8fafc] py-12 sm:py-16 px-4 sm:px-6 lg:px-8 border-b border-slate-100">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-6 sm:mb-8">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#2563eb] block mb-1">Standar Medis Terbaik</span>
                <h2 class="text-2xl sm:text-4xl font-black text-[#1c274c] leading-tight tracking-tight">
                    Klinik Medis Terpercaya<br>
                    untuk Seluruh Warga & Komunitas Los Santos
                </h2>
            </div>

            <div class="standar-medis-layout">
                
                <!-- Left Side: Vertical Indicator Bar + 2x2 Feature Cards -->
                <div class="standar-medis-left">
                    
                    <!-- Vertical Indicator Bar -->
                    <div class="feat-indicator-wrap">
                        <div id="featureIndicator" class="feat-indicator-bar"></div>
                    </div>

                    <!-- 2x2 Feature Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 flex-1">
                        
                        <!-- Feature 0 (Active Blue Gradient by default) -->
                        <div onclick="highlightFeature(0)" id="featCard0" class="feature-card-item is-active">
                            <div class="feat-icon-box">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg mb-1.5 font-bold">10+ Tahun Pengalaman</h3>
                                <p>
                                    Dedikasi tinggi melayani pasien dan menghadirkan standar medis profesional, aman, & terpercaya.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 1 -->
                        <div onclick="highlightFeature(1)" id="featCard1" class="feature-card-item is-inactive">
                            <div class="feat-icon-box">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg mb-1.5 font-bold">95% Kepuasan Pasien</h3>
                                <p>
                                    Ribuan warga telah menjalani pemeriksaan, perawatan gawat darurat, & konsultasi dengan puas.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div onclick="highlightFeature(2)" id="featCard2" class="feature-card-item is-inactive">
                            <div class="feat-icon-box">
                                <i class="fa-solid fa-stethoscope"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg mb-1.5 font-bold">15 Bidang Layanan Medis</h3>
                                <p>
                                    Tim dokter umum, spesialis jiwa, bedah estetika, dan farmasi yang siaga melayani kebutuhan Anda.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div onclick="highlightFeature(3)" id="featCard3" class="feature-card-item is-inactive">
                            <div class="feat-icon-box">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg mb-1.5 font-bold">98% Akurasi Diagnostik</h3>
                                <p>
                                    Peralatan medis modern, sterilisasi autoklaf rumah sakit, & rekam medis digital terpusat.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Side: Featured Photo with Blue Offset Card (Preserving hero.png) -->
                <div class="standar-medis-right">
                    <div class="standar-medis-photo-box">
                        <div class="standar-medis-photo-offset"></div>
                        
                        <div class="standar-medis-photo-card">
                            <img 
                                src="{{ asset('images/klinik_medis.png') }}" 
                                alt="Fasilitas Rumah Sakit & Tim Medis iMe Medical Center" 
                            >
                            <div class="absolute inset-0 z-10 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-6">
                                <div class="standar-medis-floating-badge">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold" style="background-color: #dbeafe !important; color: #2563eb !important;">
                                            <i class="fa-solid fa-heart-pulse text-lg" style="color: #2563eb !important;"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black" style="color: #1c274c !important;">Fasilitas Ramah & Modern</p>
                                            <p class="text-[11px]" style="color: #627193 !important;">Pelayanan terpadu seluruh warga</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('public.doctor-schedule') }}" class="text-xs font-extrabold hover:underline px-2" style="color: #2563eb !important;">
                                        Jadwal &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================
         4. JAM OPERASIONAL & LAYANAN SIAGA 24 JAM
    ========================================================= -->
    <section id="jadwal" class="w-full bg-white py-12 sm:py-16 px-4 sm:px-6 lg:px-8 border-b border-slate-100">
        <div class="max-w-7xl mx-auto">
            
            <div class="bg-[#f4f7fb] rounded-[32px] p-6 sm:p-10 lg:p-12 border border-slate-200">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-[#2563eb] block mb-1">Jadwal Shift Pelayanan</span>
                        <h3 class="text-2xl sm:text-4xl font-black text-[#1c274c] mb-3">Jam Operasional Pelayanan</h3>
                        <p class="text-xs sm:text-sm text-[#627193] mb-6 leading-relaxed">
                            Layanan dokter umum dan poliklinik beroperasi terjadwal, didukung instalasi gawat darurat dan farmasi yang siaga 24 jam di Alta & Roxwood Hospital.
                        </p>

                        <div class="space-y-3.5">
                            <!-- Operasi Plastik -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#2563eb] flex items-center justify-center text-sm font-bold">
                                        <i class="fa-solid fa-user-nurse"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-[#1c274c] text-sm">Operasi Plastik</h4>
                                        <p class="text-xs text-slate-500">Shift 1: 13:00&ndash;16:00 &middot; Shift 2: 20:00&ndash;22:00 WIB</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-[#2563eb] text-[10px] font-black">Terjadwal</span>
                            </div>

                            <!-- Surat-Suratan Medis -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#2563eb] flex items-center justify-center text-sm font-bold">
                                        <i class="fa-solid fa-file-medical"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-[#1c274c] text-sm">Surat-Suratan Medis</h4>
                                        <p class="text-xs text-slate-500">Shift 1: 13:00&ndash;17:00 &middot; Shift 2: 19:00&ndash;22:00 WIB</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-[#2563eb] text-[10px] font-black">Terjadwal</span>
                            </div>

                            <!-- Layanan Farmasi & UGD -->
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">
                                        <i class="fa-solid fa-pills"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-sm">Layanan Farmasi & UGD</h4>
                                        <p class="text-xs text-emerald-700 font-medium">Pengambilan obat & penanganan darurat</p>
                                    </div>
                                </div>
                                <span class="px-3.5 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-black flex items-center gap-1.5 shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> 24 JAM
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right CTA Box -->
                    <div class="hero-gradient text-white rounded-[28px] p-6 sm:p-10 flex flex-col justify-between shadow-xl relative overflow-hidden min-h-[320px]">
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-white/80 block mb-2">Pelayanan Cepat & Tanggap</span>
                            <h3 class="text-2xl sm:text-3xl font-black mb-3 text-white">Butuh Layanan Medis Sekarang?</h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed mb-8">
                                Tenaga medis kami selalu siap membantu Anda. Ajukan surat sehat secara online atau jadwalkan janji temu dengan dokter kami tanpa antre panjang.
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3.5">
                            <a href="{{ route('public.form', ['type' => 'janji_temu', 'poli' => 'Poli Umum']) }}" 
                               class="ime-btn-white flex-1 font-black text-xs sm:text-sm py-3.5 rounded-full text-center shadow-md transition hover:scale-105">
                                <span style="color: #1c274c !important;">Buat Janji Sekarang</span>
                            </a>
                            <button onclick="showRegulationModal()" 
                               class="px-6 py-3.5 rounded-full bg-white/20 hover:bg-white/30 text-white font-bold text-xs sm:text-sm border-2 border-white/40 transition hover:scale-105" style="color: #ffffff !important;">
                                Regulasi Pengobatan
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- =========================================================
         5. FULL-WIDTH FORM KELUHAN & PENGADUAN WARGA
    ========================================================= -->
    <section id="keluhan-warga" class="w-full bg-[#f8fafc] py-12 sm:py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">
            
            <!-- Section Header -->
            <div class="text-center space-y-2.5">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 border border-blue-200 text-xs font-extrabold text-[#2563eb] shadow-sm">
                    <i class="fa-solid fa-bullhorn animate-pulse"></i>
                    <span>LAYANAN PENGADUAN & KELUHAN WARGA</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-[#1c274c] tracking-tight">
                    Kotak Keluhan & Aspirasi Warga
                </h2>
                <p class="text-xs sm:text-sm text-[#627193] max-w-2xl mx-auto leading-relaxed">
                    Layanan pengaduan, masukan, dan kritik bagi <strong>Alta Hospital</strong> & <strong>Roxwood Hospital</strong>. Laporan dapat dikirimkan secara <strong>Anonim</strong> demi kenyamanan Anda.
                </p>
            </div>

            <!-- Flash Message Success -->
            @if(session('feedback_success'))
                <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 shadow-md flex items-start gap-3.5 animate-fade-in-up">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="flex-1 text-xs sm:text-sm">
                        <strong class="font-black text-emerald-950 block text-sm mb-0.5">Pengaduan Berhasil Dikirim!</strong>
                        <span>{{ session('feedback_success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Flash Message Errors -->
            @if($errors->any())
                <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-md flex items-start gap-3.5 animate-fade-in-up">
                    <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
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

            <!-- Form Container Card -->
            <div class="bg-white rounded-[32px] p-6 sm:p-10 border border-slate-200 shadow-xl">
                <form action="{{ route('feedback.submit') }}" method="POST" enctype="multipart/form-data" id="publicFeedbackForm" class="space-y-6">
                    @csrf
                    <input type="hidden" name="from_home" value="1">
                    <input type="hidden" name="reporter_type" value="warga">

                    <!-- 1. Pilih RS Tujuan -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black uppercase tracking-wider text-[#1c274c]">
                                1. Pilih Rumah Sakit Terkait <span class="text-[#2563eb]">*</span>
                            </label>
                            <span class="text-[11px] text-slate-500 font-medium">Pilih rumah sakit terkait</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <label class="relative flex items-start gap-3.5 p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-[#2563eb] has-[:checked]:bg-blue-50/50 has-[:checked]:ring-2 has-[:checked]:ring-[#2563eb]/20 border-slate-200 hover:border-slate-300 group bg-slate-50/50">
                                <input type="radio" name="hospital" value="alta" class="w-4 h-4 text-[#2563eb] focus:ring-[#2563eb] border-slate-300 mt-1" {{ old('hospital', 'alta') === 'alta' ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-[#2563eb] flex items-center justify-center text-xs font-bold">
                                            <i class="fa-solid fa-hospital"></i>
                                        </div>
                                        <span class="text-sm sm:text-base font-black text-[#1c274c]">Alta Hospital</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-snug">Rumah Sakit Utama Kota (Central Hospital, Los Santos EMS)</p>
                                </div>
                            </label>

                            <label class="relative flex items-start gap-3.5 p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-600/20 border-slate-200 hover:border-slate-300 group bg-slate-50/50">
                                <input type="radio" name="hospital" value="roxwood" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 mt-1" {{ old('hospital') === 'roxwood' ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                            <i class="fa-solid fa-clinic-medical"></i>
                                        </div>
                                        <span class="text-sm sm:text-base font-black text-[#1c274c]">Roxwood Hospital</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-snug">Pusat Layanan Medis Roxwood County & Sekitarnya</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Jenis Pengaduan -->
                    <div class="space-y-2.5 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-black uppercase tracking-wider text-[#1c274c]">
                            2. Jenis Pengaduan <span class="text-[#2563eb]">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/50 has-[:checked]:ring-2 has-[:checked]:ring-amber-500/20 border-slate-200 hover:border-slate-300 bg-slate-50/50">
                                <input type="radio" name="type" value="laporan" class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-slate-300" {{ old('type', 'laporan') === 'laporan' ? 'checked' : '' }}>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-[#1c274c] block">Laporan / Keluhan</span>
                                        <span class="text-[10px] text-slate-500 block">Komplain pelayanan, fasilitas, antrean, atau perilaku staf</span>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-[#2563eb] has-[:checked]:bg-blue-50/50 has-[:checked]:ring-2 has-[:checked]:ring-[#2563eb]/20 border-slate-200 hover:border-slate-300 bg-slate-50/50">
                                <input type="radio" name="type" value="masukan" class="w-4 h-4 text-[#2563eb] focus:ring-[#2563eb] border-slate-300" {{ old('type') === 'masukan' ? 'checked' : '' }}>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-[#2563eb] flex items-center justify-center text-sm font-bold shrink-0">
                                        <i class="fa-solid fa-lightbulb"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-[#1c274c] block">Masukan / Saran</span>
                                        <span class="text-[10px] text-slate-500 block">Ide perbaikan fasilitas, sarana, atau mutu pelayanan</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 3. Nama Pengirim & Subjek -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="feedback_name" class="text-xs font-black uppercase tracking-wider text-[#1c274c]">
                                    Nama Pasien / Warga
                                </label>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Bisa Anonim</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </span>
                                <input type="text" id="feedback_name" name="name" value="{{ old('name') }}"
                                    placeholder="Contoh: John Doe (Kosongkan jika Anonim)"
                                    class="w-full pl-9 pr-3.5 py-3 rounded-2xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 focus:outline-none transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="feedback_subject" class="block text-xs font-black uppercase tracking-wider text-[#1c274c] mb-1.5">
                                Subjek / Topik Keluhan <span class="text-[#2563eb]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-tag text-xs"></i>
                                </span>
                                <input type="text" id="feedback_subject" name="subject" value="{{ old('subject') }}"
                                    list="subjectSuggestions"
                                    required
                                    placeholder="Contoh: Pelayanan antrean di Alta Hospital"
                                    class="w-full pl-9 pr-3.5 py-3 rounded-2xl bg-slate-50 border border-slate-300 text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 focus:outline-none transition-all">
                                <datalist id="subjectSuggestions">
                                    <option value="Keluhan Pelayanan Antrean Alta Hospital">
                                    <option value="Keluhan Pelayanan Roxwood Hospital">
                                    <option value="Ketersediaan Dokter On-Duty / Piket">
                                    <option value="Respon Ambulans / Panggilan Darurat">
                                    <option value="Sikap & Perilaku Tenaga Medis">
                                    <option value="Masukan & Saran Peningkatan Fasilitas RS">
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Detail Kronologi -->
                    <div class="space-y-1.5">
                        <label for="feedback_message" class="block text-xs font-black uppercase tracking-wider text-[#1c274c]">
                            4. Detail Kronologi / Isi Keluhan <span class="text-[#2563eb]">*</span>
                        </label>
                        <textarea id="feedback_message" name="message" rows="4" required
                            placeholder="Jelaskan secara rinci kronologi kejadian, waktu, ruangan di rumah sakit, serta nama pihak terkait..."
                            class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-300 text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 focus:outline-none transition-all leading-relaxed">{{ old('message') }}</textarea>
                    </div>

                    <!-- 5. Lampiran Bukti Gambar -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-dashed border-slate-300 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <label for="feedback_image" class="text-xs font-bold text-slate-800 flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-camera text-[#2563eb]"></i>
                                    <span>Lampiran Bukti Foto / Tangkapan Layar</span>
                                    <span class="text-[10px] font-normal text-slate-400">(Opsional)</span>
                                </label>
                                <span class="text-[10px] text-slate-500 block">Format: JPG, PNG, GIF (Maks. 5MB)</span>
                            </div>
                            <input type="file" id="feedback_image" name="image" accept="image/*"
                                onchange="previewFeedbackImage(this)"
                                class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-[#2563eb] hover:file:bg-blue-200 file:cursor-pointer cursor-pointer">
                        </div>

                        <div id="feedbackImagePreview" class="hidden pt-2">
                            <div class="relative inline-block border border-slate-200 rounded-2xl overflow-hidden shadow-sm bg-white p-1">
                                <img id="feedbackPreviewImg" src="" alt="Preview Bukti" class="max-h-40 rounded-xl object-contain">
                                <button type="button" onclick="removeFeedbackImage()" class="absolute top-2 right-2 w-7 h-7 rounded-full bg-slate-700 hover:bg-slate-900 text-white flex items-center justify-center shadow-md transition-all">
                                    <i class="fa-solid fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Submit Button -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <i class="fa-solid fa-shield-halved text-[#2563eb] text-sm"></i>
                            <span>Laporan terenkripsi aman & langsung ditinjau oleh Manajemen RS.</span>
                        </div>
                        <button type="submit" id="submitFeedbackBtn"
                            class="w-full sm:w-auto px-8 py-3.5 rounded-full card-blue-gradient text-white text-sm font-extrabold shadow-lg shadow-blue-500/25 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2" style="color: #ffffff !important;">
                            <i class="fa-solid fa-paper-plane text-xs text-white"></i>
                            <span style="color: #ffffff !important;">Kirim Laporan Pengaduan</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <!-- =========================================================
         INTERACTIVE PROMO & DETAIL MODALS
    ========================================================= -->

    <!-- PROMO DETAIL MODAL (Opens when clicking "Selengkapnya") -->
    <div id="promoModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99999] hidden items-center justify-center p-4">
        <div class="bg-white rounded-[28px] max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in-up border border-slate-200">
            <button onclick="closeModal('promoModal')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-[#1c274c] flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <span class="inline-block bg-blue-100 text-[#2563eb] text-[11px] font-black px-3.5 py-1 rounded-full mb-3">Promo Aktif Bulan Ini</span>
            <h3 id="promoModalTitle" class="text-xl font-black text-[#1c274c] mb-2 leading-snug">Judul Promo</h3>
            <p id="promoModalDesc" class="text-xs text-[#627193] leading-relaxed mb-6">Deskripsi lengkap promo.</p>
            <div class="flex gap-3">
                <a href="{{ route('public.form', ['type' => 'janji_temu', 'poli' => 'Poli Umum']) }}" class="flex-1 hero-gradient text-white font-extrabold text-xs py-3.5 rounded-full shadow-md text-center hover:opacity-95 transition" style="color: #ffffff !important;">
                    Klaim Promo / Buat Janji
                </a>
                <button onclick="closeModal('promoModal')" class="px-5 py-3.5 rounded-full bg-slate-100 text-[#1c274c] font-bold text-xs hover:bg-slate-200 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- REVIEWS MODAL -->
    <div id="reviewModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99999] hidden items-center justify-center p-4">
        <div class="bg-white rounded-[28px] max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in-up border border-slate-200">
            <button onclick="closeModal('reviewModal')" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-[#1c274c] flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="flex items-center gap-2 text-amber-400 text-sm mb-2">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                <span class="text-[#1c274c] font-black ml-1">4.9 / 5.0 (Kepuasan Warga)</span>
            </div>
            <h3 class="text-lg font-black text-[#1c274c] mb-4">Apa Kata Pasien & Warga?</h3>
            <div class="space-y-3 text-xs mb-6">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="font-bold text-[#1c274c] mb-1">Rina Wulandari &mdash; <span class="text-emerald-600 font-semibold">Surat Sehat & Check-up</span></p>
                    <p class="text-[#627193]">"Pelayanannya cepat sekali, pengurusan surat kesehatan tidak berbelit dan dokternya sangat ramah serta detail."</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="font-bold text-[#1c274c] mb-1">Hendra Wijaya &mdash; <span class="text-emerald-600 font-semibold">UGD & Poli Umum</span></p>
                    <p class="text-[#627193]">"Penanganan darurat responsif, ruang tunggu nyaman dan bersih. Fasilitas Alta Hospital sangat memadai."</p>
                </div>
            </div>
            <button onclick="closeModal('reviewModal')" class="w-full bg-slate-100 text-[#1c274c] font-extrabold text-xs py-3 rounded-full hover:bg-slate-200 transition">
                Tutup Ulasan
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Modal Utilities
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    // Promo Modal Specific Function
    function openPromoModal(title, desc) {
        const titleEl = document.getElementById('promoModalTitle');
        const descEl = document.getElementById('promoModalDesc');
        if (titleEl) titleEl.textContent = title;
        if (descEl) descEl.textContent = desc;
        openModal('promoModal');
    }

    // Regulation Modal Handlers
    function showRegulationModal() {
        const modal = document.getElementById('regulationModal');
        if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    }
    function closeRegulationModal() {
        const modal = document.getElementById('regulationModal');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; }
    }

    // 2x2 Feature Switcher
    function highlightFeature(index) {
        for (let i = 0; i < 4; i++) {
            const card = document.getElementById('featCard' + i);
            if (!card) continue;

            if (i === index) {
                card.classList.remove('is-inactive');
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
                card.classList.add('is-inactive');
            }
        }

        const indicator = document.getElementById('featureIndicator');
        if (indicator) {
            indicator.style.transform = `translateY(${index * 100}%)`;
        }
    }

    // Feedback image preview & submission handlers
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

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('regulationModal');
        if (modal) {
            modal.addEventListener('click', e => { if (e.target === modal) closeRegulationModal(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRegulationModal(); });
        }

        // Close promo and review modals with ESC or clicking outside
        ['promoModal', 'reviewModal'].forEach(id => {
            const m = document.getElementById(id);
            if (m) {
                m.addEventListener('click', e => { if (e.target === m) closeModal(id); });
                document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(id); });
            }
        });

        // Auto scroll to feedback section if there are errors or success
        @if(session('feedback_success') || $errors->any())
            const keluhanSec = document.getElementById('keluhan-warga');
            if (keluhanSec) {
                setTimeout(() => {
                    keluhanSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        @endif

        // Loading state on feedback submission
        const publicFeedbackForm = document.getElementById('publicFeedbackForm');
        if (publicFeedbackForm) {
            publicFeedbackForm.addEventListener('submit', function (e) {
                const btn = document.getElementById('submitFeedbackBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-white"></i> <span style="color:#ffffff !important;">Mengirim Laporan...</span>';
                }
            });
        }
    });
</script>
@endpush
