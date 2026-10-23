@extends('layouts.app')

@section('title', 'Dashboard Staf Medis - IME Portal Medis')

@section('content')
@php
    $userOpProfile = $userOpProfile ?? [
        'user_name' => auth()->user()?->name ?? 'Staf',
        'current_role' => auth()->user()?->role?->display_name ?? 'Staf Medis',
        'target_role' => 'Perawat',
        'total_ops' => 0,
        'op_target_done' => 0,
        'op_target_total' => 5,
        'op_percent' => 0,
        'op_met' => false,
        'op_summary_label' => 'Belum Memenuhi Operasi',
        'op_requirements' => [],
        'eval_avg' => 0,
        'eval_count' => 0,
        'latest_eval_comment' => null,
        'latest_pnd_notes' => null,
        'is_highest_level' => false,
    ];
@endphp
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Suppress global dark navbar and footer from layouts.app to let the integrated medical portal take over */
        body > nav,
        body > footer,
        footer.footer-default-theme,
        footer {
            display: none !important;
        }

        /* Reposition Wrapped floating button so it never overlaps the left sidebar menu or Live Chat */
        a[href*="wrapped"] {
            left: auto !important;
            right: 175px !important;
            bottom: 24px !important;
            z-index: 99990 !important;
        }

        @media (min-width: 1024px) {
            #sidebar {
                transform: none !important;
                position: static !important;
                display: flex !important;
            }
        }

        main.pt-16,
        main {
            padding-top: 0 !important;
            min-height: 100vh !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: linear-gradient(180deg, #0c4a6e 0%, #075985 50%, #0369a1 100%) !important;
            color: #ffffff !important;
            padding: 0 !important;
            min-height: 100vh !important;
        }

        @media (min-width: 768px) {
            body {
                padding: 1rem !important;
            }
        }

        @media (min-width: 1024px) {
            body {
                padding: 1.5rem !important;
            }
        }

        /* Custom Scrollbar - Themed */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(8, 47, 73, 0.4);
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(14, 165, 233, 0.35);
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(14, 165, 233, 0.6);
        }

        /* Circle Stat Ring - Default Theme */
        .circle-ring {
            width: 68px;
            height: 68px;
            border-radius: 9999px;
            border: 3.5px solid #0ea5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(8, 47, 73, 0.85);
            box-shadow: 0 0 16px rgba(14, 165, 233, 0.25);
            flex-shrink: 0;
            color: #38bdf8;
        }

        /* Sidebar Links - High Contrast & Themed */
        .sidebar-link {
            transition: all 0.2s ease;
            color: #bae6fd; /* sky-200 */
        }
        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }
        .sidebar-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.35) 0%, rgba(2, 132, 199, 0.35) 100%);
            border: 1px solid rgba(56, 189, 248, 0.45);
            font-weight: 700;
            box-shadow: 0 2px 10px rgba(14, 165, 233, 0.25);
        }

        /* Sidebar Sublinks */
        .sidebar-sublink {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.45rem 0.65rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #bae6fd;
            transition: all 0.15s ease;
        }
        .sidebar-sublink:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
        }
        .sidebar-sublink.active {
            color: #38bdf8;
            background-color: rgba(14, 165, 233, 0.15);
            font-weight: 700;
        }

        /* Soft Cards - Themed Glassmorphism */
        .soft-card {
            background-color: rgba(12, 74, 110, 0.45);
            border: 1px solid rgba(14, 165, 233, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .soft-card:hover {
            border-color: rgba(14, 165, 233, 0.45);
            box-shadow: 0 8px 24px -4px rgba(14, 165, 233, 0.2);
        }

        /* Duty Timer Numeric Display */
        .remaining-time-display {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.05em;
        }
    </style>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none" style="z-index: 99999999;"></div>

    <!-- Modal Detail / Preview (Themed) -->
    <div id="detailModal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 hidden items-center justify-center p-4" style="z-index: 9999999;">
        <div class="bg-sky-950 border border-sky-500/30 rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all text-white">
            <div class="flex items-center justify-between border-b border-sky-800/60 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center border border-sky-400/30">
                        <i class="fa-solid fa-file-medical"></i>
                    </div>
                    <h3 id="modalTitle" class="font-bold text-lg text-white">Detail Dokumen</h3>
                </div>
                <button onclick="closeModal()" class="text-sky-300 hover:text-white w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div id="modalBody" class="text-sm text-sky-100 space-y-3">
                <!-- Dynamic Content -->
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button onclick="closeModal()" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-white font-semibold text-sm transition shadow-md">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    @php
        $currentUser = auth()->user();
        $currentRole = $currentUser->role;
        $currentRoleName = strtolower(trim($currentRole?->name ?? ''));
        $currentRoleLevel = $currentRole?->level ?? 0;

        // Fast privilege check for Konsulen (exact same as in layouts.app)
        $isKonsulenUser = (!empty($currentUser->is_konsulen))
            || (method_exists($currentUser, 'isKonsulen') && $currentUser->isKonsulen())
            || ($currentRoleLevel >= 4)
            || $currentUser->isAdmin()
            || $currentUser->isExecutiveOrAbove()
            || $currentUser->isManagerOrAbove()
            || in_array($currentRoleName, ['dokter_spesialis', 'supervisor', 'staff_manager', 'manajer', 'executive', 'admin']);

        if (!$isKonsulenUser && $currentUser->isStaff()) {
            try {
                $isKonsulenUser = \App\Models\StaseApplication::where('konsulen_id', $currentUser->id)->exists();
            } catch (\Throwable $e) {
                $isKonsulenUser = false;
            }
        }

        $pendingKonsulenCount = 0;
        if ($isKonsulenUser) {
            try {
                $pendingKonsulenCount = \App\Models\StaseApplication::where('konsulen_id', $currentUser->id)
                    ->where('status', 'pending_konsulen')
                    ->count();
            } catch (\Throwable $e) {
                $pendingKonsulenCount = 0;
            }
        }

        // Unread messages count
        try {
            $unreadMessagesCount = \App\Models\MemberMessage::where('receiver_id', $currentUser->id)
                ->where('is_read', false)
                ->count();
        } catch (\Throwable $e) {
            $unreadMessagesCount = 0;
        }

        // Meeting requests count for admin
        $pendingMeetingCount = 0;
        if ($currentUser->isAdmin()) {
            try {
                $pendingMeetingCount = \App\Models\MeetingRequest::pending()->count();
            } catch (\Throwable $e) {
                $pendingMeetingCount = 0;
            }
        }

        // Feedback count
        $unreadFeedbackCount = 0;
        if ($currentUser->hasPermission('access_feedback')) {
            try {
                $unreadFeedbackCount = \App\Models\Feedback::where('is_read', false)->count();
            } catch (\Throwable $e) {
                $unreadFeedbackCount = 0;
            }
        }

        // Hospital & Portal Alta permission checks
        $userHospital = strtolower(trim($currentUser->hospital ?? 'alta'));
        $isAltaMember = ($userHospital === 'alta') || ($currentUser->isAdmin() && $userHospital !== 'roxwood');
        $canSeePortal = $currentUser->isStaff() && $isAltaMember;
        $isGa     = $isAltaMember && ($currentUser->isAdmin() || $currentUser->isExecutiveOrAbove() || $currentUser->isInDivision('ga'));
        $isMsl    = $isAltaMember && ($currentUser->isAdmin() || $currentUser->isExecutiveOrAbove() || $currentUser->isInDivision('msl'));
        $isPnd    = $isAltaMember && ($currentUser->isAdmin() || $currentUser->isExecutiveOrAbove() || $currentUser->isInDivision('pnd'));
        $isIe     = $isAltaMember && ($currentUser->isAdmin() || $currentUser->isExecutiveOrAbove() || $currentUser->isInDivision('ie'));
        $isComdis = $isAltaMember && ($currentUser->isAdmin() || $currentUser->isExecutiveOrAbove() || $currentUser->isInDivision('comdis'));

        $userHospitalNav = strtolower(trim($currentUser->hospital ?? 'alta'));
        $isAltaStaffNav  = ($userHospitalNav === 'alta') || ($currentUser->isAdmin() && $userHospitalNav !== 'roxwood');

        $canSeeAdmin = $currentUser->hasPermission('manage_users') 
            || $currentUser->hasPermission('manage_doctor_schedules') 
            || $currentUser->hasPermission('view_reports') 
            || $currentUser->hasPermission('view_attendance_reports') 
            || $currentUser->hasPermission('access_live_chat') 
            || $currentUser->hasPermission('access_feedback') 
            || $currentUser->isExecutiveOrAbove() 
            || $currentUser->isManagerOrAbove();
    @endphp

    <!-- Main Outer Frame (Gaya Layout Gambar 6 dengan Warna Default Portal Medis) -->
    <div class="max-w-[1600px] mx-auto bg-sky-950/45 backdrop-blur-xl md:rounded-[28px] shadow-2xl border border-sky-500/25 overflow-hidden flex min-h-[calc(100vh-3rem)] relative text-white">

        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebarBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-30 hidden lg:hidden"></div>

        <!-- ==================== LEFT SIDEBAR ==================== -->
        <aside id="sidebar" class="w-72 bg-sky-950/90 backdrop-blur-2xl border-r border-sky-500/20 flex flex-col justify-between shrink-0 fixed lg:static inset-y-0 left-0 z-40 -translate-x-full lg:translate-x-0 transition-transform duration-300">
            
            <!-- Top Profile & Brand Section -->
            <div class="flex flex-col flex-1 min-h-0">
                <!-- Mini Brand Header -->
                <div class="px-5 pt-4 pb-3 flex items-center justify-between border-b border-sky-800/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-400 font-bold text-xs shadow-xs">
                            <i class="fa-solid fa-staff-snake"></i>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-white tracking-tight text-sm">IME</span>
                            <span class="text-[11px] bg-sky-500/20 border border-sky-400/30 text-sky-200 px-2 py-0.5 rounded-full font-semibold">Portal Medis</span>
                        </div>
                    </div>
                    <button onclick="toggleMobileSidebar()" class="lg:hidden text-sky-300 hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Doctor Avatar & Identity -->
                <div class="px-5 py-4 text-center border-b border-sky-800/40 bg-sky-900/20">
                    <div class="relative w-20 h-20 mx-auto mb-2.5 rounded-full p-1 bg-gradient-to-b from-sky-400 to-cyan-500 shadow-lg shadow-sky-500/20">
                        <a href="{{ route('staff.members.show', auth()->id()) }}" class="block w-full h-full rounded-full bg-sky-950 overflow-hidden border-2 border-sky-300/40 group" title="Lihat Profil Anggota: {{ auth()->user()->name }}">
                            <img src="{{ auth()->user()->profile_image_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                 onerror="this.onerror=null;this.src='{{ asset('profile.jpg') }}';">
                        </a>
                        <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full {{ isset($activeSession) && $activeSession ? 'bg-amber-400' : 'bg-emerald-400' }} ring-2 ring-sky-950 shadow-xs" 
                              title="{{ isset($activeSession) && $activeSession ? 'Sesi Aktif' : 'Siap Bertugas' }}"></span>
                    </div>
                    <a href="{{ route('staff.members.show', auth()->id()) }}" class="font-bold text-base text-white hover:text-cyan-300 transition-colors tracking-tight block truncate" title="Lihat Profil Anggota">
                        Dr. {{ auth()->user()->name }}
                    </a>
                    <p class="text-[11px] font-medium text-sky-200/80 mt-0.5 truncate">
                        {{ auth()->user()->role->display_name ?? 'Staf Medis' }} • {{ auth()->user()->hospital ? ucfirst(str_replace('_', ' ', auth()->user()->hospital)) : 'Rumah Sakit iMe' }}
                    </p>
                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ isset($activeSession) && $activeSession ? 'bg-amber-500/20 border border-amber-400/40 text-amber-300' : 'bg-emerald-500/20 border border-emerald-400/40 text-emerald-300' }} text-[10px] font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full {{ isset($activeSession) && $activeSession ? 'bg-amber-400' : 'bg-emerald-400' }} animate-pulse"></span> 
                        {{ isset($activeSession) && $activeSession ? 'Sedang Bertugas (Sesi ' . $activeSession->session_number . ')' : 'Siap Bertugas' }}
                    </div>

                    <!-- Status & Progres Operasi Medis -->
                    <div class="mt-3 pt-3 border-t border-sky-800/40 text-left space-y-2">
                        <div class="flex items-center justify-between gap-1 text-[11px]">
                            <span class="font-bold text-sky-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-procedures text-cyan-400"></i> Progres Operasi
                            </span>
                            @if($userOpProfile['op_met'] ?? false)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1 shadow-xs">
                                    <i class="fa-solid fa-check text-[8px]"></i> Memenuhi
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center gap-1 shadow-xs">
                                    <i class="fa-solid fa-xmark text-[8px]"></i> Belum Memenuhi
                                </span>
                            @endif
                        </div>

                        <!-- Progress Bar & Count -->
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-sky-300/80">{{ $userOpProfile['op_target_done'] ?? 0 }} / {{ $userOpProfile['op_target_total'] ?? 0 }} Tindakan</span>
                                <span class="font-bold {{ ($userOpProfile['op_met'] ?? false) ? 'text-emerald-400' : 'text-sky-200' }}">{{ $userOpProfile['op_percent'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-sky-950/80 rounded-full h-1.5 overflow-hidden border border-sky-800/50 p-0.5">
                                <div class="h-full rounded-full transition-all duration-500 {{ ($userOpProfile['op_met'] ?? false) ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : 'bg-gradient-to-r from-sky-500 to-cyan-400' }}"
                                     style="width: {{ min(100, max(0, $userOpProfile['op_percent'] ?? 0)) }}%"></div>
                            </div>
                        </div>

                        <!-- Detail Syarat Operasi Pills -->
                        @if(!empty($userOpProfile['op_requirements']))
                            <div class="grid grid-cols-2 gap-1.5 pt-0.5">
                                @foreach($userOpProfile['op_requirements'] as $req)
                                    <div class="p-1.5 rounded-lg border text-[10px] flex items-center justify-between {{ $req['met'] ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-200' : 'bg-sky-950/60 border-sky-800/40 text-sky-300/80' }}" title="{{ $req['label'] }}: {{ $req['current'] }}/{{ $req['target'] }}">
                                        <span class="truncate pr-1 text-[9px] font-medium">{{ $req['label'] }}</span>
                                        <span class="font-bold shrink-0 text-[9px] {{ $req['met'] ? 'text-emerald-400' : 'text-slate-400' }}">
                                            {{ $req['current'] }}/{{ $req['target'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Catatan Staf & Medis (Persis Seperti Profil Anggota) -->
                        <div class="mt-2 p-2 rounded-xl bg-sky-950/70 border border-sky-800/50 space-y-1.5 shadow-inner">
                            <div class="flex items-center justify-between text-[10px] font-bold text-sky-300">
                                <span class="flex items-center gap-1 text-[10px]">
                                    <i class="fa-solid fa-clipboard-list text-amber-400"></i> Catatan Staf
                                </span>
                                @if(($userOpProfile['eval_count'] ?? 0) > 0)
                                    <span class="text-amber-300 font-bold text-[9px] flex items-center gap-0.5">
                                        <i class="fa-solid fa-star text-[8px]"></i> {{ number_format($userOpProfile['eval_avg'], 1) }} ({{ $userOpProfile['eval_count'] }})
                                    </span>
                                @endif
                            </div>

                            @if(!empty($userOpProfile['latest_eval_comment']))
                                <p class="text-[9.5px] text-slate-300 italic line-clamp-2 leading-snug">
                                    "{!! Str::limit(e($userOpProfile['latest_eval_comment']), 75) !!}"
                                </p>
                            @elseif(!empty($userOpProfile['latest_pnd_notes']))
                                <p class="text-[9.5px] text-sky-200/90 leading-snug">
                                    <strong class="text-cyan-300">PND:</strong> "{{ Str::limit(e($userOpProfile['latest_pnd_notes']), 75) }}"
                                </p>
                            @else
                                <p class="text-[9.5px] text-sky-200/70 leading-snug flex items-start gap-1">
                                    <i class="fa-solid fa-info-circle text-sky-400 mt-0.5 text-[8.5px] shrink-0"></i>
                                    <span>Data syarat &amp; kuota operasi otomatis tersinkronisasi dari rekam medis &amp; database.</span>
                                </p>
                            @endif

                            <div class="pt-1 border-t border-sky-800/40 flex items-center justify-between text-[9px]">
                                <span class="text-sky-400/80 truncate pr-1">Target: <strong class="text-white">{{ $userOpProfile['target_role'] ?? 'Perawat' }}</strong></span>
                                <a href="{{ route('staff.members.show', auth()->id()) }}" class="text-cyan-300 hover:text-white font-semibold flex items-center gap-1 transition-colors shrink-0" title="Buka Profil Lengkap">
                                    Detail <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Menu (All Features from Navbar Preserved) -->
                <nav class="p-3.5 space-y-1 overflow-y-auto flex-1 min-h-0 pb-8 text-xs">
                    
                    <!-- Kategori: Menu Utama -->
                    <p class="px-2.5 pt-1 pb-1 text-[10px] font-bold uppercase tracking-wider text-sky-400/70">Menu Utama</p>

                    <!-- Dashboard (Active) -->
                    <a href="{{ route('staff.dashboard') }}" class="sidebar-link active flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold">
                        <i class="fa-solid fa-table-cells-large w-4 text-center text-cyan-400"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Profil Pengaturan -->
                    <a href="{{ route('staff.profile') }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold">
                        <i class="fa-solid fa-user-gear w-4 text-center text-sky-300"></i>
                        <span>Profil Pengaturan</span>
                    </a>

                    <!-- Profil Anggota -->
                    <a href="{{ route('staff.members.show', auth()->id()) }}" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold" title="Lihat Profil Anggota: {{ auth()->user()->name }}">
                        <i class="fa-solid fa-id-badge w-4 text-center text-sky-400"></i>
                        <span>Profil Anggota</span>
                    </a>

                    <!-- Tanya AI (Gemini AI Assistant) -->
                    @if(\App\Models\AiSetting::getSettings()->enabled)
                    <button type="button" onclick="typeof openFullAiModal === 'function' ? openFullAiModal() : (typeof toggleFullAiModal === 'function' ? toggleFullAiModal() : showToast('Membuka Tanya AI...', 'info'))" class="sidebar-link w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold group text-left">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-wand-magic-sparkles w-4 text-center text-amber-300 group-hover:rotate-12 transition-transform"></i>
                            <span>Tanya AI</span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-sky-500/20 border border-sky-400/30 text-sky-200 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            AI
                        </span>
                    </button>
                    @endif

                    <!-- ==================== FITUR STAF (ACCORDION) ==================== -->
                    <div class="pt-1">
                        <button onclick="toggleSubmenu('menuStafSub', 'menuStafArrow')" class="sidebar-link w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-briefcase-medical w-4 text-center text-sky-300"></i>
                                <span>Menu Staf</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($unreadMessagesCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-red-500 text-white text-[9px] font-bold px-1 rounded-full animate-pulse">{{ $unreadMessagesCount }}</span>
                                @endif
                                <i id="menuStafArrow" class="fa-solid fa-chevron-down text-[10px] text-sky-300 transition-transform duration-200"></i>
                            </div>
                        </button>
                        <div id="menuStafSub" class="pl-3 pr-1 py-1 space-y-0.5 border-l-2 border-sky-500/20 ml-5 my-1">
                            <a href="{{ route('staff.members.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-users w-4 text-center text-sky-400"></i>
                                <span>Anggota Staf</span>
                            </a>
                            <a href="{{ route('staff.certificates.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-award w-4 text-center text-amber-400"></i>
                                <span>Sertifikat & Lisensi</span>
                            </a>
                            <a href="{{ route('staff.messages.index') }}" class="sidebar-sublink justify-between">
                                <span class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-envelope w-4 text-center text-cyan-400"></i>
                                    <span>Pesan Internal</span>
                                </span>
                                @if($unreadMessagesCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-red-500 text-white text-[9px] font-bold px-1 rounded-full animate-pulse">{{ $unreadMessagesCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('staff.voting.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-square-poll-vertical w-4 text-center text-indigo-400"></i>
                                <span>Voting & Poll</span>
                            </a>
                            <a href="{{ route('staff.manager-evaluations.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-star w-4 text-center text-amber-400"></i>
                                <span>Penilaian Manajer</span>
                            </a>
                            <a href="{{ route('staff.payroll.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-money-bill-wave w-4 text-center text-emerald-400"></i>
                                <span>Gaji Saya</span>
                            </a>
                            <a href="{{ route('staff.benefits.oplas') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-wand-magic w-4 text-center text-purple-400"></i>
                                <span>Oplas Gratis EMS</span>
                            </a>
                            @if(auth()->user()->isAdmin() || strtolower(trim(auth()->user()->role?->name ?? '')) !== 'trainee')
                                <a href="{{ route('staff.operations.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-file-medical w-4 text-center text-emerald-400"></i>
                                    <span>Rekam Medis</span>
                                </a>
                            @endif

                            <!-- Quick Section Shortcuts -->
                            <div class="pt-1.5 pb-0.5 px-2">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-sky-400/60">Pintasan Halaman</span>
                            </div>
                            <a href="#sesiSection" class="sidebar-sublink text-sky-300/80">
                                <i class="fa-solid fa-clock w-4 text-center text-sky-300"></i>
                                <span>Mulai Sesi Absensi</span>
                            </a>
                            <a href="#leaderboardSection" class="sidebar-sublink text-sky-300/80">
                                <i class="fa-solid fa-trophy w-4 text-center text-amber-300"></i>
                                <span>Leaderboard Absensi</span>
                            </a>
                            <a href="#leaderboardSuratSection" class="sidebar-sublink text-sky-300/80">
                                <i class="fa-solid fa-file-signature w-4 text-center text-cyan-300"></i>
                                <span>Leaderboard Surat</span>
                            </a>
                            <a href="#suratSection" class="sidebar-sublink text-sky-300/80">
                                <i class="fa-solid fa-envelope-open-text w-4 text-center text-sky-300"></i>
                                <span>Surat dari Form</span>
                            </a>
                            <a href="#janjiTemuSection" class="sidebar-sublink text-sky-300/80">
                                <i class="fa-solid fa-calendar-check w-4 text-center text-cyan-300"></i>
                                <span>Janji Temu Pasien</span>
                            </a>
                        </div>
                    </div>

                    <!-- ==================== PANEL ADMIN (ACCORDION) ==================== -->
                    @if($canSeeAdmin)
                    <div class="pt-1">
                        <button onclick="toggleSubmenu('adminSub', 'adminArrow')" class="sidebar-link w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-shield w-4 text-center text-amber-400"></i>
                                <span>Panel Admin</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($pendingMeetingCount > 0 || $unreadFeedbackCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-amber-500 text-slate-950 text-[9px] font-bold px-1 rounded-full">{{ $pendingMeetingCount + $unreadFeedbackCount }}</span>
                                @endif
                                <i id="adminArrow" class="fa-solid fa-chevron-down text-[10px] text-sky-300 transition-transform duration-200 -rotate-90"></i>
                            </div>
                        </button>
                        <div id="adminSub" class="hidden pl-3 pr-1 py-1 space-y-0.5 border-l-2 border-sky-500/20 ml-5 my-1">
                            @if(auth()->user()->hasPermission('manage_users') || auth()->user()->isManagerOrAbove())
                                <a href="{{ route('admin.staff.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-users-gear w-4 text-center text-slate-300"></i>
                                    <span>Manajemen Staf</span>
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_doctor_schedules'))
                                <a href="{{ route('admin.doctor-schedules.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-calendar-days w-4 text-center text-sky-400"></i>
                                    <span>Jadwal Dokter</span>
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('manage_users'))
                                <a href="{{ route('admin.voting.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-square-poll-horizontal w-4 text-center text-indigo-400"></i>
                                    <span>Kelola Voting</span>
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('access_live_chat'))
                                <a href="{{ route('admin.chat.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-comments w-4 text-center text-emerald-400"></i>
                                    <span>Live Chat Pasien</span>
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('access_feedback'))
                                <a href="{{ route('admin.feedback.index') }}" class="sidebar-sublink justify-between">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-comment-dots w-4 text-center text-orange-400"></i>
                                        <span>Laporan & Masukan</span>
                                    </span>
                                    @if($unreadFeedbackCount > 0)
                                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-orange-500 text-white text-[9px] font-bold px-1 rounded-full">{{ $unreadFeedbackCount }}</span>
                                    @endif
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.organizational-structure.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-sitemap w-4 text-center text-teal-400"></i>
                                    <span>Struktural EMS</span>
                                </a>
                                <a href="{{ route('admin.roles.permissions') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-shield-halved w-4 text-center text-purple-400"></i>
                                    <span>Role Permissions</span>
                                </a>
                                <a href="{{ route('admin.ai-settings.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-robot w-4 text-center text-sky-400"></i>
                                    <span>Pengaturan AI</span>
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_attendance_reports'))
                                <a href="{{ route('admin.attendance-reports.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-chart-line w-4 text-center text-sky-400"></i>
                                    <span>Laporan Absensi</span>
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.meeting-requests.index') }}" class="sidebar-sublink justify-between">
                                    <span class="flex items-center gap-2.5">
                                        <i class="fa-solid fa-clipboard-check w-4 text-center text-amber-400"></i>
                                        <span>Meeting Requests</span>
                                    </span>
                                    @if($pendingMeetingCount > 0)
                                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-amber-400 text-gray-900 text-[9px] font-bold px-1 rounded-full">{{ $pendingMeetingCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.duty-tracking.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-trophy w-4 text-center text-amber-400"></i>
                                    <span>Duty Tracking</span>
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin() || auth()->user()->isManagerOrAbove())
                                <a href="{{ route('admin.inactive-staff.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-user-slash w-4 text-center text-rose-400"></i>
                                    <span>Staf Tidak Aktif</span>
                                </a>
                            @endif
                            @if(auth()->user()->isExecutiveOrAbove() && $isAltaStaffNav)
                                <a href="{{ route('admin.sub-roles.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-layer-group w-4 text-center text-indigo-400"></i>
                                    <span>Sub-Jabatan / Divisi</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- ==================== PORTAL ALTA HOSPITAL (ACCORDION) ==================== -->
                    @if($canSeePortal)
                    <div class="pt-1">
                        <button onclick="toggleSubmenu('portalAltaSub', 'portalAltaArrow')" class="sidebar-link w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-hospital text-rose-400 w-4 text-center"></i>
                                <span>Portal Alta</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!empty($pendingKonsulenCount) && $pendingKonsulenCount > 0)
                                    <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-amber-500 text-white text-[9px] font-bold px-1 rounded-full">{{ $pendingKonsulenCount }}</span>
                                @endif
                                <i id="portalAltaArrow" class="fa-solid fa-chevron-down text-[10px] text-sky-300 transition-transform duration-200 -rotate-90"></i>
                            </div>
                        </button>
                        <div id="portalAltaSub" class="hidden pl-3 pr-1 py-1 space-y-0.5 border-l-2 border-sky-500/20 ml-5 my-1">
                            
                            <!-- Sub-Kategori: Pengajuan Saya -->
                            <div class="pt-1 pb-0.5 px-2">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-rose-400/80">Pengajuan Saya</span>
                            </div>
                            <a href="{{ route('portal.leave.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-calendar-check w-4 text-center text-rose-400"></i>
                                <span>Pengajuan Cuti</span>
                            </a>
                            <a href="{{ route('portal.leave.public-list') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-users-slash w-4 text-center text-rose-300"></i>
                                <span>Jadwal Cuti Medis</span>
                            </a>
                            <a href="{{ route('portal.resignation.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-file-signature w-4 text-center text-orange-400"></i>
                                <span>Pengajuan Resign</span>
                            </a>
                            <a href="{{ route('portal.stase.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-graduation-cap w-4 text-center text-sky-400"></i>
                                <span>Pengajuan Stase</span>
                            </a>
                            <a href="{{ route('portal.vehicle-cert.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-car w-4 text-center text-amber-400"></i>
                                <span>Sertifikat Kendaraan</span>
                            </a>
                            <a href="{{ route('portal.operation-cert.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-award w-4 text-center text-emerald-400"></i>
                                <span>Sertifikat Operasi</span>
                            </a>
                            <a href="{{ route('portal.promotion.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-turn-up w-4 text-center text-violet-400"></i>
                                <span>Kenaikan Jabatan</span>
                            </a>
                            <a href="{{ route('credit-score.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-star-half-stroke w-4 text-center text-teal-400"></i>
                                <span>Credit Score Saya</span>
                            </a>
                            <a href="{{ route('portal.interview.apply-form') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-user-plus w-4 text-center text-indigo-400"></i>
                                <span>Pengajuan Role Interviewer</span>
                            </a>
                            <a href="{{ route('portal.training.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-chalkboard-user w-4 text-center text-emerald-400"></i>
                                <span>Pendaftaran Pelatihan</span>
                            </a>

                            <!-- Sub-Kategori: Kelola Divisi -->
                            @if($isGa || $isMsl || $isPnd || $isIe || $isComdis)
                                <div class="pt-2 pb-0.5 px-2 border-t border-sky-800/40 mt-1.5">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-teal-400/80">Kelola Divisi</span>
                                </div>
                                @if($isGa)
                                    <a href="{{ route('portal.ga.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-car w-4 text-center text-amber-400"></i>
                                        <span>GA: Sertifikat Kendaraan</span>
                                    </a>
                                @endif
                                @if($isMsl)
                                    <a href="{{ route('portal.msl.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-stethoscope w-4 text-center text-teal-400"></i>
                                        <span>MSL: Sertifikat Visum</span>
                                    </a>
                                    <a href="{{ route('portal.msl.stase.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-book-medical w-4 text-center text-teal-300"></i>
                                        <span>MSL: Kelola Stase</span>
                                    </a>
                                @endif
                                @if($isPnd)
                                    <a href="{{ route('portal.recruitment.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-bullhorn w-4 text-center text-emerald-400"></i>
                                        <span>PND: Kelola Recruitment</span>
                                    </a>
                                    <a href="{{ route('portal.pnd.cert-index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-award w-4 text-center text-green-400"></i>
                                        <span>PND: Sertifikat Operasi</span>
                                    </a>
                                    <a href="{{ route('portal.promotion.period.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-toggle-on w-4 text-center text-violet-400"></i>
                                        <span>PND: Periode Kenaikan</span>
                                    </a>
                                    <a href="{{ route('portal.promotion.applications') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-clipboard-list w-4 text-center text-violet-300"></i>
                                        <span>PND: Review Kenaikan</span>
                                    </a>
                                    <a href="{{ route('portal.pnd.training.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-list-check w-4 text-center text-emerald-400"></i>
                                        <span>PND: Kelola Pelatihan</span>
                                    </a>
                                @endif
                                @if($isIe)
                                    <a href="{{ route('portal.recruitment.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-bullhorn w-4 text-center text-emerald-400"></i>
                                        <span>IE: Kelola Recruitment</span>
                                    </a>
                                    <a href="{{ route('portal.ie.roles.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-user-tag w-4 text-center text-sky-400"></i>
                                        <span>IE: Manajemen Jabatan</span>
                                    </a>
                                    <a href="{{ route('portal.ie.pemutihan.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-calendar-xmark w-4 text-center text-sky-400"></i>
                                        <span>IE: Pemutihan Duty</span>
                                    </a>
                                    <a href="{{ route('portal.ie.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-file-contract w-4 text-center text-sky-400"></i>
                                        <span>IE: Kontrak Medis</span>
                                    </a>
                                    <a href="{{ route('portal.leave.manage') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-calendar-days w-4 text-center text-rose-400"></i>
                                        <span>IE: Kelola Cuti</span>
                                    </a>
                                    <a href="{{ route('portal.resignation.manage.ie') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-hand-holding-dollar w-4 text-center text-orange-400"></i>
                                        <span>IE: Denda Resign</span>
                                    </a>
                                    <a href="{{ route('portal.resignation.logs') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-box-archive w-4 text-center text-emerald-400"></i>
                                        <span>IE: Log Resign</span>
                                    </a>
                                @endif
                                @if($isComdis)
                                    <a href="{{ route('credit-score.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-scale-balanced w-4 text-center text-emerald-400"></i>
                                        <span>Comdis: Credit Score</span>
                                    </a>
                                @endif
                            @endif

                            <!-- Sub-Kategori Khusus / Peran -->
                            @if($isPnd)
                                <div class="pt-1.5 border-t border-sky-800/40 mt-1">
                                    <a href="{{ route('portal.resignation.manage.pnd') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-circle-check w-4 text-center text-orange-400"></i>
                                        <span>PND: Verifikasi Resign</span>
                                    </a>
                                </div>
                            @endif

                            @if(!empty($isKonsulenUser))
                                <div class="pt-1 border-t border-sky-800/40 mt-1">
                                    <a href="{{ route('portal.stase.konsulen.index') }}" class="sidebar-sublink justify-between">
                                        <span class="flex items-center gap-2.5">
                                            <i class="fa-solid fa-user-check w-4 text-center text-sky-400"></i>
                                            <span>Konsulen: Setujui Stase</span>
                                        </span>
                                        @if(!empty($pendingKonsulenCount) && $pendingKonsulenCount > 0)
                                            <span class="inline-flex items-center justify-center min-w-[18px] h-4 bg-amber-500 text-white text-[9px] font-bold px-1 rounded-full">{{ $pendingKonsulenCount }}</span>
                                        @endif
                                    </a>
                                </div>
                            @endif

                            @if(auth()->user()->isInterviewer())
                                <div class="pt-1 border-t border-sky-800/40 mt-1">
                                    <a href="{{ route('portal.interview.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-user-tie w-4 text-center text-indigo-400"></i>
                                        <span>Interviewer: Calon Medis</span>
                                    </a>
                                </div>
                            @else
                                <div class="pt-1 border-t border-sky-800/40 mt-1">
                                    <a href="{{ route('portal.interview.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-user-plus w-4 text-center text-indigo-300"></i>
                                        <span>Ajukan Jadi Interviewer</span>
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>
                    @endif

                    <!-- ==================== PENGGAJIAN (ACCORDION) ==================== -->
                    <div class="pt-1">
                        <button onclick="toggleSubmenu('gajiSub', 'gajiArrow')" class="sidebar-link w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-wallet w-4 text-center text-emerald-400"></i>
                                <span>Gaji & Keuangan</span>
                            </div>
                            <i id="gajiArrow" class="fa-solid fa-chevron-down text-[10px] text-sky-300 transition-transform duration-200 -rotate-90"></i>
                        </button>
                        <div id="gajiSub" class="hidden pl-3 pr-1 py-1 space-y-0.5 border-l-2 border-sky-500/20 ml-5 my-1">
                            <a href="{{ route('staff.payroll.index') }}" class="sidebar-sublink">
                                <i class="fa-solid fa-money-bill-wave w-4 text-center text-emerald-400"></i>
                                <span>Gaji Saya</span>
                            </a>
                            @if(auth()->user()->hasPermission('manage_payroll'))
                                <div class="pt-1.5 pb-0.5 px-2 border-t border-sky-800/40 mt-1">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-400/80">Kelola Payroll Admin</span>
                                </div>
                                <a href="{{ route('admin.payroll.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-list-check w-4 text-center text-slate-300"></i>
                                    <span>Daftar Gaji Staf</span>
                                </a>
                                <a href="{{ route('admin.salary-settings.index') }}" class="sidebar-sublink">
                                    <i class="fa-solid fa-sliders w-4 text-center text-sky-400"></i>
                                    <span>Atur Gaji</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.reimbursements.index') }}" class="sidebar-sublink">
                                        <i class="fa-solid fa-file-invoice-dollar w-4 text-center text-emerald-400"></i>
                                        <span>Reimbursement</span>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- ==================== INSTAL APLIKASI (PWA) ==================== -->
                    <div class="pt-3">
                        <button type="button" onclick="typeof triggerPwaInstall === 'function' ? triggerPwaInstall() : showToast('Aplikasi siap diinstal di browser Anda.', 'success')" class="pwa-install-trigger w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white shadow-md shadow-sky-500/20 transition active:scale-95">
                            <i class="fa-solid fa-download text-amber-300"></i>
                            <span>Instal Aplikasi</span>
                        </button>
                    </div>

                </nav>
            </div>

            <!-- Bottom Logout Button (High Contrast) -->
            <div class="p-3.5 border-t border-sky-800/50 bg-sky-950/90 shrink-0">
                <form method="POST" action="{{ route('staff.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30 hover:bg-rose-600 hover:text-white transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ==================== MAIN CONTENT AREA ==================== -->
        <div class="flex-1 flex flex-col min-w-0 bg-transparent">

            <!-- TOP HEADER BAR (Themed & Fully Responsive) -->
            <header id="top" class="sticky top-0 z-20 bg-sky-950/70 backdrop-blur-md px-5 lg:px-8 py-4 border-b border-sky-500/20">
                <div class="bg-sky-900/40 rounded-2xl px-5 py-3.5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border border-sky-400/25">
                    <!-- Left Title & Welcome -->
                    <div class="flex items-center gap-3">
                        <button onclick="toggleMobileSidebar()" class="lg:hidden w-10 h-10 rounded-xl bg-sky-900/60 border border-sky-400/30 text-white flex items-center justify-center shadow-xs">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl lg:text-2xl font-black text-white tracking-tight">Dashboard Staf Medis</h1>
                                <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-500/20 border border-sky-400/30 text-sky-200">
                                    Live Portal
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-sky-200">
                                Selamat datang kembali, <strong class="text-cyan-300 font-bold">Dr. {{ auth()->user()->name }}</strong>. Kelola operasional medis Anda di sini.
                            </p>
                        </div>
                    </div>

                    <!-- Right Icons & Search Bar -->
                    <div class="flex flex-wrap items-center gap-3 self-end md:self-auto w-full md:w-auto justify-end">
                        <!-- Mail Icon (Pesan Internal) -->
                        <a href="{{ route('staff.messages.index') }}" 
                           class="relative w-10 h-10 rounded-xl bg-sky-900/60 hover:bg-sky-800 text-sky-200 hover:text-white flex items-center justify-center border border-sky-400/30 transition shadow-xs" title="Pesan Internal">
                            <i class="fa-regular fa-envelope text-base"></i>
                            @if(!empty($unreadMessagesCount) && $unreadMessagesCount > 0)
                                <span class="absolute -top-1 -right-1 bg-cyan-400 text-slate-950 text-[10px] font-black px-1.5 rounded-full shadow-xs">{{ $unreadMessagesCount }}</span>
                            @endif
                        </a>

                        <!-- Notification Bell (Janji Temu) -->
                        <button onclick="document.getElementById('janjiTemuSection') ? document.getElementById('janjiTemuSection').scrollIntoView({behavior:'smooth'}) : null" 
                                class="relative w-10 h-10 rounded-xl bg-sky-900/60 hover:bg-sky-800 text-sky-200 hover:text-white flex items-center justify-center border border-sky-400/30 transition shadow-xs" title="Janji Temu Pasien">
                            <i class="fa-solid fa-bell text-base text-cyan-300"></i>
                            @if(isset($recentAppointments) && $recentAppointments->count() > 0)
                                <span class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-rose-400 ring-2 ring-sky-900"></span>
                            @endif
                        </button>

                        <!-- Search Input -->
                        <div class="relative flex-1 sm:w-64 min-w-[180px]">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sky-300/70 text-xs"></i>
                            <input id="globalSearch" oninput="filterTables(this.value)" type="text" placeholder="Cari karakter, ID, atau staf..." 
                                   class="w-full pl-9 pr-4 py-2 rounded-xl bg-sky-950/70 focus:bg-sky-950 text-xs sm:text-sm text-white placeholder-sky-300/60 border border-sky-500/30 focus:border-cyan-400 focus:outline-none transition">
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN SCROLLABLE BODY -->
            <main class="p-5 lg:p-8 space-y-7">

                <!-- Alert for Missing Citizen ID -->
                @if(!auth()->user()->citizen_id)
                <div class="p-4 rounded-2xl bg-purple-500/20 border border-purple-400/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/30 text-purple-200 flex items-center justify-center shrink-0 border border-purple-400/30">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">Citizen ID (FiveM) Belum Diatur</h4>
                            <p class="text-xs text-purple-200">Data Citizen ID diperlukan untuk integrasi absensi otomatis.</p>
                        </div>
                    </div>
                    <a href="{{ route('staff.profile') }}" class="px-4 py-2 bg-purple-500 hover:bg-purple-400 text-white font-bold text-xs rounded-xl whitespace-nowrap shadow-xs transition">
                        Lengkapi Profil
                    </a>
                </div>
                @endif

                <!-- Warning for Previous Day Active Session -->
                @if(isset($anyActiveSession) && $anyActiveSession && !isset($activeSession))
                <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-400/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/30 text-rose-200 flex items-center justify-center shrink-0 border border-rose-400/30">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">Sesi Aktif dari Hari Sebelumnya</h4>
                            <p class="text-xs text-rose-200">
                                Sesi belum di-clock out dari tanggal {{ $anyActiveSession->work_date->format('d/m/Y') }}. Durasi: <span id="previous-session-duration" data-clock-in="{{ $anyActiveSession->clock_in->toISOString() }}" class="font-bold font-mono text-white">00:00:00</span>
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('staff.attendance.clock-out') }}" id="dashboardClockOutForm" data-action="{{ route('staff.attendance.clock-out') }}">
                        @csrf
                        <button type="submit" id="dashboardClockOutBtn" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-xs whitespace-nowrap transition">
                            <span class="btn-text"><i class="fa-solid fa-right-from-bracket mr-1.5"></i>Clock Out Sesi Kemarin</span>
                            <span class="btn-loading hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Memproses...</span>
                        </button>
                    </form>
                </div>
                @endif

                @php
                    $weeklyHoursFormatted = optional($weeklyStats)->total_hours_formatted ?? '00:00:00';
                    $weeklyActiveDays = optional($weeklyStats)->total_days ?? 0;
                    $accumulatedEmsHours = $totalEmsHours['formatted'] ?? '00:00:00';
                @endphp

                <!-- ==================== 1. TOP 3 CIRCULAR STAT CARDS ==================== -->
                <section id="sesiSection" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Card 1: Total Jam Kerja Minggu Ini -->
                    <div class="soft-card rounded-2xl p-5 flex items-center gap-5">
                        <div class="circle-ring">
                            <i class="fa-solid fa-fire-flame-curved text-2xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-wider text-sky-300">Total Jam Kerja Minggu Ini</p>
                            <h3 id="weeklyTimerDisplay" class="text-2xl lg:text-3xl font-black text-white mt-1 font-mono tracking-tight">
                                {{ $weeklyHoursFormatted }}
                            </h3>
                            <p class="text-xs text-sky-200/70 mt-1">
                                {{ $weeklyActiveDays > 0 ? $weeklyActiveDays . ' hari kerja aktif' : 'Reset otomatis setiap Senin' }}
                            </p>
                        </div>
                    </div>

                    <!-- Card 2: Akumulasi Jam Sebagai EMS -->
                    <div class="soft-card rounded-2xl p-5 flex items-center gap-5">
                        <div class="circle-ring">
                            <i class="fa-regular fa-clock text-2xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-wider text-sky-300">Akumulasi Jam Sebagai EMS</p>
                            <h3 id="totalEmsDisplay" class="text-2xl lg:text-3xl font-black text-white mt-1 font-mono tracking-tight">
                                {{ $accumulatedEmsHours }}
                            </h3>
                            <p class="text-xs text-sky-200/70 mt-1">Total jam operasional tercatat</p>
                        </div>
                    </div>

                    <!-- Card 3: Sesi Hari Ini -->
                    <div class="soft-card rounded-2xl p-5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div class="circle-ring">
                                @if(isset($activeSession) && $activeSession)
                                    <i id="sessionIcon" class="fa-solid fa-heart-pulse text-2xl text-emerald-400 animate-pulse"></i>
                                @else
                                    <i id="sessionIcon" class="fa-solid fa-moon text-2xl text-sky-400"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-sky-300">Sesi Hari Ini</p>
                                    <span id="sessionCountBadge" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ isset($activeSession) && $activeSession ? 'bg-amber-500/20 text-amber-300 border border-amber-400/30' : 'bg-sky-500/20 text-sky-200 border border-sky-400/30' }}">
                                        {{ isset($todaySessions) ? $todaySessions->count() : 0 }} Sesi
                                    </span>
                                </div>
                                @if(isset($activeSession) && $activeSession)
                                    <h3 id="sessionStatusTitle" class="text-lg font-black text-emerald-300 mt-1">
                                        Sesi {{ $activeSession->session_number }} Aktif
                                    </h3>
                                    <p id="sessionStatusSub" class="text-xs text-sky-200 mt-0.5">
                                        @if($activeSession->scheduled_duty_minutes)
                                            Waktu tersisa: <span id="remaining-time" data-end-time="{{ $activeSession->scheduled_end_time ? $activeSession->scheduled_end_time->toISOString() : '' }}" class="remaining-time-display font-mono font-bold text-amber-300">00:00:00</span>
                                        @else
                                            Durasi: <span id="active-session-duration" data-clock-in="{{ $activeSession->clock_in->toISOString() }}" class="font-mono font-bold text-white">00:00:00</span>
                                        @endif
                                    </p>
                                @else
                                    <h3 id="sessionStatusTitle" class="text-lg font-black text-white mt-1">
                                        {{ isset($todaySessions) && $todaySessions->count() > 0 ? 'Siap Sesi Berikutnya' : 'Belum Ada Sesi' }}
                                    </h3>
                                    <p id="sessionStatusSub" class="text-xs text-sky-200/70 mt-0.5">
                                        {{ $todayTotalHours ? \App\Helpers\TimeHelper::formatDuration($todayTotalHours) . ' telah selesai hari ini' : 'Belum ada sesi yang dimulai hari ini.' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================== 2. ACTION BANNERS ==================== -->
                <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    
                    <!-- Mulai Sesi Kerja Baru / Sesi Sedang Berjalan -->
                    <div class="lg:col-span-12 soft-card rounded-2xl p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        @if(isset($activeSession) && $activeSession)
                            <!-- Sesi Sedang Aktif -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 flex items-center justify-center text-xl shadow-lg shrink-0 animate-pulse">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-white text-base sm:text-lg">
                                        Sesi {{ $activeSession->session_number }} Sedang Berjalan
                                    </h3>
                                    <p class="text-xs sm:text-sm text-sky-200">
                                        Dimulai {{ $activeSession->clock_in->diffForHumans() }} • Jenis: <strong class="text-white">{{ ucfirst($activeSession->session_type) }}</strong>
                                        @if($activeSession->scheduled_duty_minutes)
                                            • Waktu Tersisa: 
                                            <span id="remaining-time-badge" data-end-time="{{ $activeSession->scheduled_end_time ? $activeSession->scheduled_end_time->toISOString() : '' }}" class="remaining-time-display font-mono font-bold text-amber-300 bg-sky-950/80 border border-amber-400/40 px-2 py-0.5 rounded-lg ml-1">
                                                00:00:00
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('staff.attendance.clock-out') }}" id="activeSessionClockOutForm" data-action="{{ route('staff.attendance.clock-out') }}">
                                @csrf
                                <button type="submit" id="activeSessionClockOutBtn" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg transition shrink-0">
                                    <span class="btn-text"><i class="fa-solid fa-right-from-bracket"></i> Clock Out</span>
                                    <span class="btn-loading hidden"><i class="fa-solid fa-spinner fa-spin"></i> Memproses...</span>
                                </button>
                            </form>
                        @else
                            <!-- Mulai Sesi Kerja Baru -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-sky-500/20 border border-sky-400/40 text-cyan-300 flex items-center justify-center text-xl shadow-lg shrink-0">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-white text-base sm:text-lg">Mulai Sesi Kerja Baru</h3>
                                    <p class="text-xs sm:text-sm text-sky-200">Masukkan durasi kerja untuk memulai shift operasional medis Anda</p>
                                </div>
                            </div>

                            <form id="dashboardClockInForm" method="POST" action="{{ route('staff.attendance.clock-in') }}" data-action="{{ route('staff.attendance.clock-in') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full md:w-auto">
                                @csrf
                                <select name="session_type" id="jenisSesi" class="px-3.5 py-2.5 rounded-xl bg-sky-950/80 border border-sky-500/30 text-white text-xs sm:text-sm font-semibold focus:outline-none focus:border-cyan-400">
                                    <option value="work" class="bg-sky-950 text-white">Kerja</option>
                                    <option value="meeting" class="bg-sky-950 text-white">Meeting</option>
                                </select>
                                <input name="scheduled_duty_minutes" id="scheduled_duty_minutes" type="number" min="1" max="300" required placeholder="Durasi (menit)" 
                                       class="w-full sm:w-40 px-3.5 py-2.5 rounded-xl bg-sky-950/80 border border-sky-500/30 text-white placeholder-sky-300/50 text-xs sm:text-sm focus:outline-none focus:border-cyan-400">
                                <button type="submit" id="dashboardClockInBtn" disabled 
                                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-500/50 text-sky-200 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 border border-white/10 transition shrink-0 cursor-not-allowed opacity-60"
                                        title="Masukkan durasi terlebih dahulu (1-300 menit)">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    <span id="clockInBtnText">Clock In</span>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Laporan & Keluhan Warga (Khusus Manager Ke Atas) -->
                    @if(auth()->user()->isManagerOrAbove())
                    @php
                        try {
                            $newFeedbackCount = \App\Models\Feedback::where('status', 'new')->count();
                            $totalFeedbackCount = \App\Models\Feedback::count();
                        } catch (\Throwable $e) {
                            $newFeedbackCount = 0;
                            $totalFeedbackCount = 0;
                        }
                    @endphp
                    <div id="pengaduanSection" class="lg:col-span-6 rounded-2xl p-5 bg-amber-950/30 border border-amber-500/30 flex flex-col justify-between gap-4 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 flex items-center justify-center text-xl shadow-md shrink-0">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2.5 py-0.5 rounded-md bg-amber-500/20 border border-amber-400/40 text-amber-200 font-extrabold text-[10px] uppercase tracking-wider">
                                        🔥 Khusus Jabatan Manager Ke Atas
                                    </span>
                                    @if($newFeedbackCount > 0)
                                        <span class="px-2.5 py-0.5 rounded-md bg-rose-500 text-white font-bold text-[10px] animate-bounce">
                                            {{ $newFeedbackCount }} Pengaduan Baru
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-black text-white text-base">Laporan & Keluhan Warga</h3>
                                <p class="text-xs text-amber-200/80">Tinjau masukan dan komplain yang dikirimkan warga melalui portal utama secara real-time.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.feedback.index') }}" 
                           class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition">
                            <i class="fa-solid fa-inbox"></i>
                            <span>Buka Kotak Pengaduan ({{ $totalFeedbackCount }})</span>
                        </a>
                    </div>
                    @endif

                    <!-- Slip & Status Gaji Saya -->
                    @php
                        try {
                            $latestUserPayroll = \App\Models\Payroll::where('user_id', auth()->id())->latest('period_start')->first();
                            $pendingUserPayrollsCount = \App\Models\Payroll::where('user_id', auth()->id())->where('status', 'pending')->count();
                        } catch (\Throwable $e) {
                            $latestUserPayroll = null;
                            $pendingUserPayrollsCount = 0;
                        }
                    @endphp
                    <div id="gajiSection" class="{{ auth()->user()->isManagerOrAbove() ? 'lg:col-span-6' : 'lg:col-span-12' }} rounded-2xl p-5 bg-emerald-950/30 border border-emerald-500/30 flex flex-col justify-between gap-4 shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 flex items-center justify-center text-xl shadow-md shrink-0">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-black text-white text-base">Slip & Status Gaji Saya</h3>
                                    @if($latestUserPayroll && $latestUserPayroll->status === 'paid')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 font-bold text-[10px] flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check"></i> Sudah Dibayar
                                        </span>
                                    @elseif($pendingUserPayrollsCount > 0)
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 font-bold text-[10px] flex items-center gap-1 animate-pulse">
                                            <i class="fa-solid fa-clock"></i> {{ $pendingUserPayrollsCount }} Menunggu Pembayaran
                                        </span>
                                    @endif
                                </div>
                                @if($latestUserPayroll)
                                    <p class="text-xs sm:text-sm text-sky-200">
                                        Periode: <strong class="text-white">{{ $latestUserPayroll->period_description }}</strong> • Gaji: <strong class="text-emerald-300 font-black">{{ $latestUserPayroll->formatted_salary }}</strong>
                                    </p>
                                    <p class="text-[11px] text-sky-300/70">Insentif jam kerja & penanganan surat medis telah diakumulasi.</p>
                                @else
                                    <p class="text-xs text-emerald-200/80">Belum ada slip gaji yang dibuat. Gaji akan tercatat saat admin memproses rekap absensi.</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('staff.payroll.index') }}" 
                           class="w-full py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Buka Gaji Saya <i class="fa-solid fa-arrow-right ml-1"></i></span>
                        </a>
                    </div>
                </section>

                <!-- ==================== 3. STATISTIK FORMULIR & OPERASI ==================== -->
                @if(auth()->user()->isAdmin() || (optional(auth()->user()->role)->level ?? 0) >= 2)
                <section id="statistikSection" class="soft-card rounded-3xl p-5 lg:p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base lg:text-lg font-black text-white">Ringkasan Formulir & Tindakan Operasi</h2>
                            <p class="text-xs text-sky-200/80">Rekapitulasi status surat medis dan jadwal operasi rumah sakit</p>
                        </div>
                        <span class="text-xs font-semibold text-cyan-200 bg-sky-500/20 px-3 py-1 rounded-full border border-sky-400/30 self-start sm:self-auto">
                            Update Real-Time • {{ now()->format('M Y') }}
                        </span>
                    </div>

                    <!-- Baris 1: 4 Kartu Status Formulir -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Menunggu Review -->
                        <div class="bg-sky-950/50 border border-amber-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-400/30 text-amber-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-amber-300">{{ $stats['pending_forms'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Menunggu Review</div>
                            </div>
                        </div>

                        <!-- Disetujui -->
                        <div class="bg-sky-950/50 border border-emerald-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-emerald-300">{{ $stats['approved_forms'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Disetujui</div>
                            </div>
                        </div>

                        <!-- Ditolak -->
                        <div class="bg-sky-950/50 border border-rose-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-400/30 text-rose-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-rose-300">{{ $stats['rejected_forms'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Ditolak</div>
                            </div>
                        </div>

                        <!-- Formulir Hari Ini -->
                        <div class="bg-sky-950/50 border border-sky-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-sky-500/20 border border-sky-400/30 text-cyan-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-regular fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $stats['total_forms_today'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Formulir Hari Ini</div>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 2: 3 Kartu Operasi -->
                    @if(auth()->user()->isAdmin() || in_array(auth()->user()->role->name ?? '', ['perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis', 'management', 'high_command']))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Total Operasi -->
                        <div class="bg-sky-950/50 border border-cyan-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-cyan-500/20 border border-cyan-400/30 text-cyan-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-bed-pulse"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $opStats['total'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Total Operasi</div>
                            </div>
                        </div>

                        <!-- Operasi Bulan Ini -->
                        <div class="bg-sky-950/50 border border-blue-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-blue-500/20 border border-blue-400/30 text-blue-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-regular fa-calendar-days"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $opStats['monthly'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Operasi Bulan Ini</div>
                            </div>
                        </div>

                        <!-- Operasi Hari Ini -->
                        <div class="bg-sky-950/50 border border-indigo-500/30 rounded-2xl p-4 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-user-nurse"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $opStats['daily'] }}</div>
                                <div class="text-xs font-bold text-sky-200">Operasi Hari Ini</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </section>
                @endif

                <!-- ==================== 4. LEADERBOARD DUAL SECTION (KANAN - KIRI) ==================== -->
                @php
                    $canSeeSuratLb = auth()->user()->isAdmin() || (optional(auth()->user()->role)->level ?? 0) >= 2;
                @endphp
                <div class="grid grid-cols-1 {{ $canSeeSuratLb ? 'lg:grid-cols-2' : '' }} gap-6 items-start">

                    <!-- KIRI: LEADERBOARD ABSENSI (DUTY) -->
                    <section id="leaderboardSection" class="soft-card rounded-3xl p-5 lg:p-6 space-y-4">
                        <!-- Header -->
                        <div class="flex items-start sm:items-center justify-between gap-2 pb-3 border-b border-sky-800/60">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 border border-amber-400/30 text-amber-400 flex items-center justify-center text-lg shadow-xs shrink-0">
                                    <i class="fa-solid fa-trophy"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h3 class="text-base font-black text-white">Leaderboard Absensi</h3>
                                        <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 border border-amber-400/30 text-amber-200 px-2 py-0.5 rounded-full">Top Performa</span>
                                    </div>
                                    <p class="text-xs text-sky-200 mt-0.5 truncate">Peringkat jam kerja • {{ $lbFilter['label'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs font-bold bg-sky-500/25 border border-sky-400/30 text-cyan-200 px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-users mr-1"></i>{{ $leaderboard->count() }} Staf
                                </span>
                            </div>
                        </div>

                        <!-- Filter Controls (Compact) -->
                        <form method="GET" action="{{ url()->current() }}" id="lb-filter-form" class="bg-sky-950/60 p-3 rounded-2xl border border-sky-500/30 flex flex-col gap-2.5">
                            @if(request('year'))
                                <input type="hidden" name="year" value="{{ request('year') }}">
                            @endif

                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="inline-flex rounded-xl bg-sky-900/60 p-1 border border-sky-500/20 shrink-0">
                                    <button type="button" id="btnPerBulan" onclick="switchPeriodMode('bulan')" class="px-3 py-1 rounded-lg text-xs font-bold bg-sky-500 text-white shadow-xs transition">
                                        <i class="fa-regular fa-calendar mr-1"></i>Per Bulan
                                    </button>
                                    <button type="button" id="btnRentang" onclick="switchPeriodMode('rentang')" class="px-3 py-1 rounded-lg text-xs font-semibold text-sky-200 hover:text-white transition">
                                        <i class="fa-solid fa-calendar-week mr-1"></i>Rentang Tanggal
                                    </button>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-bold flex items-center gap-1.5 transition shadow-xs">
                                        <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
                                    </button>
                                    <a href="{{ url()->current() }}" class="px-2.5 py-1.5 rounded-xl bg-sky-900/60 hover:bg-sky-800 text-sky-200 text-xs font-semibold transition flex items-center gap-1 border border-sky-500/20" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="w-full">
                                <div id="lb-month-wrapper" class="w-full">
                                    <input type="month" name="lb_month" id="filterMonthInput" value="{{ $lbFilter['month'] }}" max="{{ now()->format('Y-m') }}" 
                                           class="w-full px-3.5 py-2 rounded-xl bg-sky-950/80 border border-sky-500/30 text-xs font-semibold text-white focus:outline-none focus:border-cyan-400">
                                </div>
                                <div id="lb-range-wrapper" class="hidden w-full flex items-center gap-2">
                                    <input type="date" name="lb_start" id="filterStartInput" value="{{ $lbFilter['start'] }}" max="{{ now()->format('Y-m-d') }}" 
                                           class="w-full px-3 py-2 rounded-xl bg-sky-950/80 border border-sky-500/30 text-xs font-semibold text-white focus:outline-none focus:border-cyan-400">
                                    <span class="text-xs text-sky-300">s/d</span>
                                    <input type="date" name="lb_end" id="filterEndInput" value="{{ $lbFilter['end'] }}" max="{{ now()->format('Y-m-d') }}" 
                                           class="w-full px-3 py-2 rounded-xl bg-sky-950/80 border border-sky-500/30 text-xs font-semibold text-white focus:outline-none focus:border-cyan-400">
                                </div>
                            </div>
                        </form>

                        <!-- Daftar Peringkat Absensi -->
                        <div class="space-y-2.5">
                            @forelse($leaderboard as $index => $staff)
                                <div class="bg-sky-950/50 rounded-2xl p-3 sm:p-3.5 border {{ $index === 0 ? 'border-amber-400/60 bg-amber-500/10 shadow-xs' : ($index === 1 ? 'border-slate-300/40 bg-slate-400/10' : ($index === 2 ? 'border-amber-600/40 bg-amber-600/10' : 'border-sky-800/50')) }} flex items-center justify-between gap-3 hover:bg-white/5 transition">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <div class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl {{ $index === 0 ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : ($index === 1 ? 'bg-slate-300 text-slate-900 font-bold' : ($index === 2 ? 'bg-amber-600 text-white font-bold' : 'bg-sky-600 text-white font-bold')) }} flex items-center justify-center text-xs sm:text-sm shrink-0">
                                            {{ $index + 1 }}
                                            @if($index === 0)
                                                <i class="fa-solid fa-crown text-[9px] text-amber-300 absolute -top-1.5 -right-1 bg-slate-950 rounded-full p-0.5 border border-amber-400/50"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-xs sm:text-sm text-white">{{ $staff->name }}</span>
                                                @if(isset($staff->total_juara_1_count) && $staff->total_juara_1_count > 0)
                                                    <span class="px-1.5 py-0.5 rounded-full bg-amber-400/20 border border-amber-400/40 text-amber-300 font-bold text-[9px]">
                                                        <i class="fa-solid fa-star text-[8px] mr-0.5"></i>x{{ $staff->total_juara_1_count }}
                                                    </span>
                                                @endif
                                                @if($index < 3)
                                                    <span class="px-1.5 py-0.5 rounded-full {{ $index === 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : ($index === 1 ? 'bg-slate-300/20 text-slate-200 border border-slate-300/40' : 'bg-amber-600/20 text-amber-300 border border-amber-600/40') }} font-extrabold text-[9px]">
                                                        Juara {{ $index + 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-sky-300 mt-0.5">{{ $staff->role->display_name ?? 'Staf' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="font-black text-sm sm:text-base text-cyan-300 font-mono">
                                            {{ \App\Helpers\TimeHelper::formatDuration($staff->attendances_sum_session_duration ?: 0) }}
                                        </div>
                                        <div class="text-[10px] text-sky-200/70">{{ $staff->unique_work_days ?? $staff->attendances_count }} hari kerja</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-sky-300/60 text-xs font-semibold">
                                    Belum ada data absensi untuk periode ini.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <!-- KANAN: LEADERBOARD PENANGANAN SURAT BULANAN -->
                    @if($canSeeSuratLb)
                    <section id="leaderboardSuratSection" class="soft-card rounded-3xl p-5 lg:p-6 space-y-4">
                        <!-- Header -->
                        <div class="flex items-start sm:items-center justify-between gap-2 pb-3 border-b border-sky-800/60">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-cyan-500/20 border border-cyan-400/30 text-cyan-300 flex items-center justify-center text-lg shadow-xs shrink-0">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h3 class="text-base font-black text-white truncate">Leaderboard Surat</h3>
                                        <span class="text-[10px] font-bold uppercase tracking-wider bg-cyan-500/20 border border-cyan-400/30 text-cyan-200 px-2 py-0.5 rounded-full">Bulan Ini</span>
                                    </div>
                                    <p class="text-xs text-sky-200 mt-0.5 truncate">Peringkat penanganan surat medis</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs font-bold bg-sky-500/25 border border-sky-400/30 text-cyan-200 px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-users mr-1"></i>{{ $formLeaderboard->count() }} Staf
                                </span>
                            </div>
                        </div>

                        <!-- Table Container -->
                        <div class="overflow-x-auto bg-sky-950/60 rounded-2xl border border-sky-500/25">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 bg-sky-900/95 backdrop-blur-md z-10 shadow-xs">
                                    <tr class="text-sky-200 text-[10px] sm:text-[11px] font-black uppercase tracking-wider border-b border-sky-800/80">
                                        <th class="py-2.5 px-2 text-center w-8 whitespace-nowrap">#</th>
                                        <th class="py-2.5 px-2 whitespace-nowrap">Staf Medis</th>
                                        <th class="py-2.5 px-2 text-center whitespace-nowrap">Total</th>
                                        <th class="py-2.5 px-2 text-center whitespace-nowrap">Disetujui</th>
                                        <th class="py-2.5 px-2 text-center whitespace-nowrap w-16">Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-800/40 text-xs">
                                    @forelse($formLeaderboard as $index => $staff)
                                        @php
                                            $rejectedCount = max(0, $staff->forms_count - $staff->approved_count);
                                            $approvalRate = $staff->forms_count > 0 ? round(($staff->approved_count / $staff->forms_count) * 100, 1) : 0;
                                        @endphp
                                        <tr class="hover:bg-white/5 transition {{ $index === 0 ? 'bg-amber-500/10' : ($index === 1 ? 'bg-slate-400/5' : ($index === 2 ? 'bg-amber-600/5' : '')) }}">
                                            <!-- Peringkat -->
                                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                                <div class="inline-flex items-center justify-center relative">
                                                    <div class="w-7 h-7 rounded-lg {{ $index === 0 ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : ($index === 1 ? 'bg-slate-300 text-slate-900 font-bold' : ($index === 2 ? 'bg-amber-600 text-white font-bold' : 'bg-sky-700/60 text-sky-200 font-semibold border border-sky-500/30')) }} flex items-center justify-center text-xs">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    @if($index === 0)
                                                        <i class="fa-solid fa-crown text-[8px] text-amber-300 absolute -top-1.5 -right-1 bg-slate-950 rounded-full p-0.5 border border-amber-400/50"></i>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Staf Medis & Role -->
                                            <td class="py-2.5 px-2 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-sky-900 border border-sky-400/30 flex items-center justify-center text-sky-200 text-[10px] font-bold shrink-0">
                                                        {{ strtoupper(substr($staff->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="font-bold text-white text-xs whitespace-nowrap">{{ $staff->name }}</span>
                                                            @if($index < 3)
                                                                <span class="px-1.5 py-0.5 rounded-full {{ $index === 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : ($index === 1 ? 'bg-slate-300/20 text-slate-200 border border-slate-300/40' : 'bg-amber-600/20 text-amber-300 border border-amber-600/40') }} font-extrabold text-[9px]">
                                                                    #{{ $index + 1 }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <span class="text-[10px] text-sky-300/80 block whitespace-nowrap">{{ $staff->role->display_name ?? 'Staf' }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Total Ditangani -->
                                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                                <span class="font-black text-sm text-cyan-300 font-mono">{{ $staff->forms_count }}</span>
                                                <span class="text-[10px] text-sky-300/60 block">surat</span>
                                            </td>

                                            <!-- Disetujui / Ditolak -->
                                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                                <div class="inline-flex flex-col items-center gap-0.5">
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-300">
                                                        <i class="fa-solid fa-circle-check text-[10px]"></i> {{ $staff->approved_count }}
                                                    </span>
                                                    @if($rejectedCount > 0)
                                                        <span class="text-[10px] text-rose-300 font-medium">
                                                            <i class="fa-solid fa-circle-xmark text-[9px]"></i> {{ $rejectedCount }} ditolak
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Tingkat Persetujuan -->
                                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                                <div class="inline-flex flex-col items-center gap-1 w-16">
                                                    <span class="text-xs font-bold text-white font-mono">{{ $approvalRate }}%</span>
                                                    <div class="w-full h-1 bg-sky-900 rounded-full overflow-hidden border border-sky-700/40">
                                                        <div class="h-full bg-gradient-to-r from-emerald-500 to-cyan-400 rounded-full" style="width: {{ $approvalRate }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-sky-300/60 text-xs font-semibold">
                                                Belum ada data penanganan surat bulan ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                    @endif

                </div>

                <!-- ==================== 5. TABEL 1: SURAT DARI FORM ==================== -->
                @if(auth()->user()->isAdmin() || (optional(auth()->user()->role)->level ?? 0) >= 2)
                <section id="suratSection" class="soft-card rounded-3xl p-5 lg:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-sky-500/20 border border-sky-400/30 text-cyan-300 flex items-center justify-center">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div>
                                <h3 class="text-base lg:text-lg font-black text-white">Surat dari Form</h3>
                                <p class="text-xs text-sky-200">Daftar pengajuan surat tindakan medis & psikolog warga</p>
                            </div>
                        </div>
                        <a href="{{ route('staff.forms') }}" class="text-xs sm:text-sm font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 transition">
                            Lihat Semua <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto bg-sky-950/60 rounded-2xl border border-sky-500/25">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-900/60 text-sky-200 text-[11px] font-black uppercase tracking-wider border-b border-sky-800/60">
                                    <th class="py-3.5 px-4 whitespace-nowrap">ID</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Karakter</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Jenis</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Status</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Diproses Oleh</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Tanggal</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="suratTableBody" class="divide-y divide-sky-800/40 text-xs sm:text-sm text-white">
                                @forelse($recentForms as $form)
                                    @php
                                        $canApprove = $form->status === 'pending' && auth()->user()->canApproveForm($form->form_type);
                                    @endphp
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-sky-300 whitespace-nowrap">
                                            #{{ str_pad($form->id, 6, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-white whitespace-nowrap">
                                            {{ $form->character_name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-sky-200 whitespace-nowrap">
                                            {{ ucfirst(str_replace('_', ' ', $form->form_type)) }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($form->status === 'pending')
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/40">Menunggu</span>
                                            @elseif($form->status === 'approved')
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">Disetujui</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-400/40">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($form->processedBy && $form->status !== 'pending')
                                                <div class="font-bold text-white">{{ $form->processedBy->name }}</div>
                                                @if($form->processed_at)
                                                    <div class="text-[11px] text-sky-300/70">{{ $form->processed_at->format('d/m/Y H:i') }}</div>
                                                @endif
                                            @else
                                                <span class="text-sky-300/50">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-sky-200/80 whitespace-nowrap">
                                            {{ $form->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <a href="{{ route('staff.forms.show', $form->id) }}" class="px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 text-white font-bold text-xs flex items-center gap-1.5 transition shadow-xs">
                                                    <i class="fa-solid fa-eye"></i> Lihat
                                                </a>
                                                @if($form->status === 'pending')
                                                    @if($canApprove)
                                                        <button type="button" onclick="approveForm({{ $form->id }}, this)" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1 transition shadow-xs">
                                                            <i class="fa-solid fa-check"></i> Setujui
                                                        </button>
                                                    @endif
                                                    <button type="button" onclick="rejectForm({{ $form->id }}, this)" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs flex items-center gap-1 transition shadow-xs">
                                                        <i class="fa-solid fa-xmark"></i> Tolak
                                                    </button>
                                                @else
                                                    <span class="text-xs font-semibold text-sky-300/70">Selesai</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-sky-300/60 text-xs font-semibold">
                                            Belum ada formulir yang masuk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                <!-- ==================== 6. TABEL 2: JANJI TEMU TERBARU ==================== -->
                @if(auth()->user()->isAdmin() || (optional(auth()->user()->role)->level ?? 0) >= 3)
                <section id="janjiTemuSection" class="soft-card rounded-3xl p-5 lg:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 flex items-center justify-center">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="text-base lg:text-lg font-black text-white">Janji Temu Terbaru</h3>
                                <p class="text-xs text-sky-200">Daftar antrean konsultasi poliklinik & spesialis warga</p>
                            </div>
                        </div>
                        <a href="{{ route('staff.forms') }}?type=appointment" class="text-xs sm:text-sm font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 transition">
                            Lihat Semua <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                    <div class="overflow-x-auto bg-sky-950/60 rounded-2xl border border-sky-500/25">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-900/60 text-sky-200 text-[11px] font-black uppercase tracking-wider border-b border-sky-800/60">
                                    <th class="py-3.5 px-4 whitespace-nowrap">ID</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Karakter</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Poli</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Tanggal & Waktu</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Status</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Diproses Oleh</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="janjiTableBody" class="divide-y divide-sky-800/40 text-xs sm:text-sm text-white">
                                @forelse($recentAppointments as $appointment)
                                    @php
                                        $formData = $appointment->form_data ?? [];
                                        $appointmentDate = $formData['appointment_date'] ?? null;
                                        $appointmentTime = $formData['appointment_time'] ?? null;
                                        $canApproveAppt = $appointment->status === 'pending' && auth()->user()->canApproveForm($appointment->form_type);
                                    @endphp
                                    <tr id="row-{{ $appointment->id }}" class="hover:bg-white/5 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-sky-300 whitespace-nowrap">
                                            #{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-white whitespace-nowrap">
                                            {{ $appointment->character_name }}
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-cyan-300 whitespace-nowrap">
                                            {{ ucfirst(str_replace(['_', '-'], ' ', $appointment->form_type)) }}
                                        </td>
                                        <td class="py-3.5 px-4 text-sky-200/80 whitespace-nowrap">
                                            @if($appointmentDate)
                                                {{ \Carbon\Carbon::parse($appointmentDate)->format('d/m/Y') }} {{ $appointmentTime }}
                                            @else
                                                {{ $appointment->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap" id="status-{{ $appointment->id }}">
                                            @if($appointment->status === 'pending')
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/40">Menunggu</span>
                                            @elseif($appointment->status === 'approved')
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">Sudah Ditemui</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-400/40">Tolak Formulir</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-sky-200/80" id="proc-{{ $appointment->id }}">
                                            @if($appointment->processedBy && $appointment->status !== 'pending')
                                                <div class="font-bold text-white">{{ $appointment->processedBy->name }}</div>
                                                @if($appointment->processed_at)
                                                    <div class="text-[11px] text-sky-300/70">{{ $appointment->processed_at->format('d/m/Y H:i') }}</div>
                                                @endif
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap" id="aksi-{{ $appointment->id }}">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <a href="{{ route('staff.forms.show', $appointment->id) }}" class="px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 text-white font-bold text-xs flex items-center gap-1 transition shadow-xs">
                                                    <i class="fa-solid fa-eye"></i> Lihat
                                                </a>
                                                @if($appointment->status === 'pending')
                                                    @if($canApproveAppt)
                                                        <button type="button" onclick="approveForm({{ $appointment->id }}, this, true)" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1 transition shadow-xs">
                                                            <i class="fa-solid fa-check"></i> Sudah Ditemui
                                                        </button>
                                                    @endif
                                                    <button type="button" onclick="rejectForm({{ $appointment->id }}, this, true)" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs flex items-center gap-1 transition shadow-xs">
                                                        <i class="fa-solid fa-xmark"></i> Tolak Formulir
                                                    </button>
                                                @else
                                                    <span class="text-xs font-semibold text-sky-300/70">Selesai</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-sky-300/60 text-xs font-semibold">
                                            Belum ada janji temu yang masuk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                <!-- ==================== 7. FOOTER ==================== -->
                <footer class="pt-6 pb-4 border-t border-sky-500/20 text-center space-y-3">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-sky-900/40 border border-sky-400/30">
                        <span class="w-5 h-5 rounded-full bg-emerald-400 text-slate-950 text-[10px] flex items-center justify-center font-black">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        <span class="font-black text-xs tracking-wide text-white">IMe ROLEPLAY</span>
                    </div>
                    <p class="text-xs text-sky-200">Layanan medis profesional untuk komunitas role-playing</p>
                    <div class="flex items-center justify-center gap-5 text-xs font-semibold text-sky-300">
                        <span><i class="fa-solid fa-shield-heart text-cyan-400 mr-1"></i> Terpercaya</span>
                        <span><i class="fa-regular fa-clock text-cyan-400 mr-1"></i> 24/7</span>
                        <span><i class="fa-solid fa-user-doctor text-cyan-400 mr-1"></i> Profesional</span>
                    </div>
                    <p class="text-[11px] text-sky-300/60 pt-2">
                        © 2026 Portal Medis IMe. Semua hak dilindungi.
                    </p>
                </footer>

            </main>
        </div>
    </div>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // Toggle Sidebar on Mobile
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
            }
        }

        // Toggle Sidebar Submenus
        function toggleSubmenu(subId, arrowId) {
            const sub = document.getElementById(subId);
            const arrow = document.getElementById(arrowId);
            if (sub) sub.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('-rotate-90');
        }

        // Toast Notification System
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const colors = {
                info: 'bg-sky-600 text-white border border-sky-400/40',
                success: 'bg-emerald-600 text-white border border-emerald-400/40',
                warning: 'bg-amber-600 text-white border border-amber-400/40',
                error: 'bg-rose-600 text-white border border-rose-400/40'
            };
            const icons = {
                info: 'fa-circle-info',
                success: 'fa-circle-check',
                warning: 'fa-triangle-exclamation',
                error: 'fa-circle-xmark'
            };

            const toast = document.createElement('div');
            toast.className = `${colors[type] || colors.info} px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-xs sm:text-sm font-semibold pointer-events-auto transition-all duration-300 transform translate-x-0`;
            toast.innerHTML = `
                <i class="fa-solid ${icons[type] || icons.info} text-base shrink-0"></i>
                <span>${message}</span>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-4');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Modal Helpers
        function openModal(title, htmlContent) {
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modal = document.getElementById('detailModal');
            if (modalTitle) modalTitle.textContent = title;
            if (modalBody) modalBody.innerHTML = htmlContent;
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Period Mode Switcher (Bulan / Rentang)
        function switchPeriodMode(mode) {
            const btnBulan = document.getElementById('btnPerBulan');
            const btnRentang = document.getElementById('btnRentang');
            const monthWrap = document.getElementById('lb-month-wrapper');
            const rangeWrap = document.getElementById('lb-range-wrapper');

            const startInput = document.getElementById('filterStartInput');
            const endInput = document.getElementById('filterEndInput');
            const mnInput = document.getElementById('filterMonthInput');

            if (mode === 'bulan') {
                if (btnBulan) btnBulan.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-sky-500 text-white shadow-xs transition';
                if (btnRentang) btnRentang.className = 'px-3 py-1 rounded-lg text-xs font-semibold text-sky-200 hover:text-white transition';
                if (monthWrap) { monthWrap.classList.remove('hidden'); monthWrap.classList.add('flex'); }
                if (rangeWrap) { rangeWrap.classList.remove('flex'); rangeWrap.classList.add('hidden'); }

                if (startInput) { startInput.removeAttribute('name'); startInput.disabled = true; }
                if (endInput) { endInput.removeAttribute('name'); endInput.disabled = true; }
                if (mnInput) { mnInput.name = 'lb_month'; mnInput.disabled = false; }
            } else {
                if (btnRentang) btnRentang.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-sky-500 text-white shadow-xs transition';
                if (btnBulan) btnBulan.className = 'px-3 py-1 rounded-lg text-xs font-semibold text-sky-200 hover:text-white transition';
                if (rangeWrap) { rangeWrap.classList.remove('hidden'); rangeWrap.classList.add('flex'); }
                if (monthWrap) { monthWrap.classList.remove('flex'); monthWrap.classList.add('hidden'); }

                if (mnInput) { mnInput.removeAttribute('name'); mnInput.disabled = true; }
                if (startInput) { startInput.name = 'lb_start'; startInput.disabled = false; }
                if (endInput) { endInput.name = 'lb_end'; endInput.disabled = false; }
            }
        }

        // Global Table Filter
        function filterTables(query) {
            const q = query.toLowerCase().trim();
            const rows1 = document.querySelectorAll('#suratTableBody tr');
            const rows2 = document.querySelectorAll('#janjiTableBody tr');

            [...rows1, ...rows2].forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // Live Duty Timers & Validation
        document.addEventListener('DOMContentLoaded', function () {
            // Flash sessions from Laravel
            @if(session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
            @if(session('error'))
                showToast('{{ session('error') }}', 'error');
            @endif
            @if(session('warning'))
                showToast('{{ session('warning') }}', 'warning');
            @endif
            @if(session('info'))
                showToast('{{ session('info') }}', 'info');
            @endif

            // Initialize leaderboard mode
            const activeMode = @json($lbFilter['mode'] ?? 'month');
            switchPeriodMode(activeMode === 'range' ? 'rentang' : 'bulan');

            // Clock In input validation
            const scheduledDutyMinutesInput = document.getElementById('scheduled_duty_minutes');
            const dashboardClockInBtn = document.getElementById('dashboardClockInBtn');

            if (scheduledDutyMinutesInput && dashboardClockInBtn) {
                function updateClockInButtonState() {
                    const value = scheduledDutyMinutesInput.value.trim();
                    const numValue = parseInt(value);

                    if (value === '' || isNaN(numValue) || numValue < 1 || numValue > 300) {
                        dashboardClockInBtn.disabled = true;
                        dashboardClockInBtn.classList.remove('bg-emerald-500', 'hover:bg-emerald-400', 'text-slate-950', 'cursor-pointer');
                        dashboardClockInBtn.classList.add('bg-gray-500/50', 'text-sky-200', 'cursor-not-allowed', 'opacity-60');
                    } else {
                        dashboardClockInBtn.disabled = false;
                        dashboardClockInBtn.classList.remove('bg-gray-500/50', 'text-sky-200', 'cursor-not-allowed', 'opacity-60');
                        dashboardClockInBtn.classList.add('bg-emerald-500', 'hover:bg-emerald-400', 'text-slate-950', 'cursor-pointer');
                    }
                }

                updateClockInButtonState();
                scheduledDutyMinutesInput.addEventListener('input', updateClockInButtonState);
                scheduledDutyMinutesInput.addEventListener('change', updateClockInButtonState);
                scheduledDutyMinutesInput.addEventListener('keyup', updateClockInButtonState);
            }

            // Duty countdown timer
            function updateRemainingTime() {
                const remainingElements = document.querySelectorAll('#remaining-time, #remaining-time-badge, .remaining-time-display');

                remainingElements.forEach(element => {
                    const endTimeStr = element.getAttribute('data-end-time');
                    if (!endTimeStr) return;

                    const endTime = new Date(endTimeStr).getTime();
                    const now = new Date().getTime();
                    const diffSeconds = Math.floor((endTime - now) / 1000);

                    if (diffSeconds <= 0) {
                        element.textContent = '00:00:00';
                        element.classList.add('text-red-400', 'font-black');

                        if (!element.getAttribute('data-expired-alerted')) {
                            element.setAttribute('data-expired-alerted', 'true');
                            autoCheckoutExpiredSession();
                        }
                        return;
                    }

                    const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(diffSeconds % 60).padStart(2, '0');

                    element.textContent = `${hours}:${minutes}:${seconds}`;
                });
            }

            // Active session countup timer
            function updateActiveSessionDuration() {
                const activeDurationEl = document.getElementById('active-session-duration');
                if (activeDurationEl) {
                    const clockInStr = activeDurationEl.getAttribute('data-clock-in');
                    if (clockInStr) {
                        const clockInTime = new Date(clockInStr).getTime();
                        const now = new Date().getTime();
                        const diffSeconds = Math.max(0, Math.floor((now - clockInTime) / 1000));

                        const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                        const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                        const seconds = String(diffSeconds % 60).padStart(2, '0');

                        activeDurationEl.textContent = `${hours}:${minutes}:${seconds}`;
                    }
                }

                const prevDurationEl = document.getElementById('previous-session-duration');
                if (prevDurationEl) {
                    const clockInStr = prevDurationEl.getAttribute('data-clock-in');
                    if (clockInStr) {
                        const clockInTime = new Date(clockInStr).getTime();
                        const now = new Date().getTime();
                        const diffSeconds = Math.max(0, Math.floor((now - clockInTime) / 1000));

                        const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                        const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                        const seconds = String(diffSeconds % 60).padStart(2, '0');

                        prevDurationEl.textContent = `${hours}:${minutes}:${seconds}`;
                    }
                }
            }

            if (document.getElementById('remaining-time') || document.getElementById('remaining-time-badge')) {
                setInterval(updateRemainingTime, 1000);
                updateRemainingTime();
            }

            if (document.getElementById('active-session-duration') || document.getElementById('previous-session-duration')) {
                setInterval(updateActiveSessionDuration, 1000);
                updateActiveSessionDuration();
            }
        });

        // Form Approval & Rejection Ajax Handlers
        async function approveForm(formId, button, isAppointment = false) {
            const endpoint = isAppointment ? `/staff/appointments/${formId}/approve` : `/staff/forms/${formId}/approve`;
            const originalContent = button.innerHTML;
            const row = button.closest('tr');

            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || (isAppointment ? 'Janji temu ditandai Sudah Ditemui.' : 'Formulir berhasil disetujui.'), 'success');

                    const statusCell = row.querySelector('td:nth-child(5)') || row.querySelector('td:nth-child(4)');
                    if (statusCell) {
                        statusCell.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">${isAppointment ? 'Sudah Ditemui' : 'Disetujui'}</span>`;
                    }

                    const actionCell = button.closest('td');
                    if (actionCell) {
                        actionCell.innerHTML = `<span class="text-sky-300/70 text-xs font-medium">Selesai</span>`;
                    }
                } else {
                    throw new Error(data.message || 'Gagal memproses formulir');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast(error.message || 'Terjadi kesalahan sistem', 'error');
                button.disabled = false;
                button.innerHTML = originalContent;
            }
        }

        async function rejectForm(formId, button, isAppointment = false) {
            const reason = prompt('Masukkan alasan penolakan:');
            if (reason === null) return;

            if (reason.trim() === '') {
                showToast('Alasan penolakan harus diisi!', 'warning');
                return;
            }

            const endpoint = isAppointment ? `/staff/appointments/${formId}/reject` : `/staff/forms/${formId}/reject`;
            const originalContent = button.innerHTML;
            const row = button.closest('tr');

            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rejection_reason: reason })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || 'Formulir berhasil ditolak.', 'success');

                    const statusCell = row.querySelector('td:nth-child(5)') || row.querySelector('td:nth-child(4)');
                    if (statusCell) {
                        statusCell.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-400/40">${isAppointment ? 'Tolak Formulir' : 'Ditolak'}</span>`;
                    }

                    const actionCell = button.closest('td');
                    if (actionCell) {
                        actionCell.innerHTML = `<span class="text-sky-300/70 text-xs font-medium">Selesai</span>`;
                    }
                } else {
                    throw new Error(data.message || 'Gagal menolak formulir');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast(error.message || 'Terjadi kesalahan sistem', 'error');
                button.disabled = false;
                button.innerHTML = originalContent;
            }
        }

        // Auto Checkout When Duty Expires
        function autoCheckoutExpiredSession() {
            if (window.isAutoCheckingOut) return;
            window.isAutoCheckingOut = true;

            showToast('Waktu durasi tugas telah selesai. Menyelesaikan sesi...', 'warning');

            const activeClockOutForm = document.getElementById('activeSessionClockOutForm');
            const dashboardClockOutForm = document.getElementById('dashboardClockOutForm');

            setTimeout(() => {
                if (activeClockOutForm) {
                    activeClockOutForm.submit();
                } else if (dashboardClockOutForm) {
                    dashboardClockOutForm.submit();
                } else {
                    fetch('{{ route("staff.attendance.clock-out") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(() => window.location.reload())
                    .catch(() => window.location.reload());
                }
            }, 2500);
        }
    </script>
@endsection
