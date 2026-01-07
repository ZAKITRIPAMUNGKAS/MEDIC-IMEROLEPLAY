@extends('layouts.app')

@section('title', 'Struktural Organisasi EMS')

@section('content')
    <main
        class="min-h-screen bg-gradient-to-br from-sky-900 via-blue-900 to-indigo-900 py-12 sm:py-16 relative overflow-hidden">
        <!-- Animated Background Particles -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
            <div
                class="absolute top-20 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute top-40 right-10 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-32 left-1/2 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Page Header Section -->
            <header class="text-center mb-16 sm:mb-20 relative">
                <div class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                    <div
                        class="w-96 h-96 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-full blur-3xl opacity-20 animate-pulse-slow">
                    </div>
                </div>

                <div class="relative z-10">
                    <div class="inline-block mb-8 transform hover:scale-110 transition-transform duration-500">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-3xl blur-2xl opacity-50 animate-pulse"
                                aria-hidden="true"></div>
                            <div
                                class="relative w-32 h-32 sm:w-36 sm:h-36 bg-gradient-to-br from-cyan-400 via-blue-400 to-indigo-500 rounded-3xl flex items-center justify-center mx-auto shadow-2xl ring-4 ring-white/30">
                                <i class="fas fa-sitemap text-white text-5xl sm:text-6xl drop-shadow-2xl"
                                    aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black mb-6 sm:mb-8 relative">
                        <span
                            class="bg-gradient-to-r from-cyan-300 via-blue-300 to-indigo-300 bg-clip-text text-transparent animate-gradient drop-shadow-2xl">
                            Struktur Organisasi EMS
                        </span>
                    </h1>

                    <p
                        class="text-xl sm:text-2xl md:text-3xl text-sky-100 max-w-4xl mx-auto leading-relaxed px-4 sm:px-0 font-light backdrop-blur-sm bg-white/5 rounded-2xl py-4 px-8 border border-white/10">
                        Hierarki lengkap organisasi Emergency Medical Services
                    </p>
                </div>
            </header>

            <!-- Organizational Chart Structure -->
            <section class="relative space-y-24" aria-label="Struktur Organisasi">
                
                {{-- 1. HIGH COMMAND SECTION (Pyramid) --}}
                <div class="relative">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl sm:text-4xl font-black text-white uppercase tracking-widest drop-shadow-lg">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-orange-300 to-red-300">
                                High Command
                            </span>
                        </h2>
                        <div class="h-1 w-32 mx-auto bg-gradient-to-r from-transparent via-yellow-400 to-transparent mt-4"></div>
                    </div>

                    <div class="flex flex-col items-center gap-12 relative z-10">
                        {{-- LEVEL 0: TOP (CEO, Directors) --}}
                        <div class="flex flex-wrap justify-center gap-8 md:gap-16">
                            @foreach($hierarchy['high_command']['top'] as $pos)
                                <div class="group relative">
                                    {{-- Card --}}
                                    <div class="w-64 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 text-center transform transition-all hover:scale-105 hover:bg-white/20 shadow-2xl">
                                        {{-- Avatar --}}
                                        <div class="w-32 h-32 mx-auto mb-4 relative">
                                            <div class="absolute inset-0 bg-blue-500 rounded-full blur-lg opacity-50 group-hover:opacity-80 transition-opacity"></div>
                                            @php
                                                $avatarUrl = $pos->user && $pos->user->profile_image 
                                                    ? asset('uploads/profile-images/' . basename($pos->user->profile_image)) 
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($pos->user ? $pos->user->name : '?') . '&background=0ea5e9&color=fff';
                                            @endphp
                                            <img src="{{ $avatarUrl }}" alt="{{ $pos->title }}" 
                                                 class="w-32 h-32 rounded-full object-cover border-4 border-white/50 relative z-10 shadow-lg"
                                                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ str_replace(' ', '+', $pos->user ? $pos->user->name : '?') }}&background=0ea5e9&color=fff';">
                                        </div>
                                        
                                        {{-- Info --}}
                                        <h3 class="text-white font-bold text-lg mb-1 leading-tight">{{ $pos->user ? $pos->user->name : 'VACANT' }}</h3>
                                        <div class="text-sky-300 text-sm font-semibold uppercase tracking-wider mb-2">{{ $pos->title }}</div>
                                        @if($pos->user && $pos->user->role)
                                            <span class="inline-block px-3 py-1 bg-blue-600/50 rounded-full text-xs text-blue-100 border border-blue-400/30">
                                                {{ $pos->user->role->display_name }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Connecting Line (Down) --}}
                                    <div class="absolute left-1/2 -bottom-12 w-0.5 h-12 bg-white/20"></div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Connecting Line (Horizontal for Level 1) --}}
                        <div class="h-0.5 w-3/4 max-w-4xl bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                        {{-- LEVEL 1: DEPT HEADS (4 Pillars) --}}
                        <div class="flex flex-wrap justify-center gap-6 md:gap-10">
                            @foreach($hierarchy['high_command']['heads'] as $pos)
                                <a href="#dept-{{ $pos->id }}" class="group relative w-56 cursor-pointer">
                                    {{-- Vertical Line (Up) --}}
                                    <div class="absolute left-1/2 -top-12 h-12 w-0.5 bg-white/20"></div>

                                    <div class="bg-gradient-to-b from-slate-800/80 to-slate-900/80 backdrop-blur-sm rounded-xl p-5 border border-white/10 text-center hover:border-sky-400/50 transition-all hover:-translate-y-2 shadow-xl">
                                        <div class="w-24 h-24 mx-auto mb-3">
                                            @php
                                                $avatarUrl = $pos->user && $pos->user->profile_image 
                                                    ? asset('uploads/profile-images/' . basename($pos->user->profile_image)) 
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($pos->user ? $pos->user->name : '?') . '&background=6366f1&color=fff';
                                            @endphp
                                            <img src="{{ $avatarUrl }}" alt="{{ $pos->title }}" 
                                                 class="w-24 h-24 rounded-full object-cover border-2 border-sky-400/30 group-hover:border-sky-400 transition-colors"
                                                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ str_replace(' ', '+', $pos->user ? $pos->user->name : '?') }}&background=6366f1&color=fff';">
                                        </div>
                                        <h4 class="text-gray-100 font-bold text-sm mb-1 truncate">{{ $pos->user ? $pos->user->name : 'VACANT' }}</h4>
                                        <div class="text-indigo-300 text-xs font-medium leading-tight">{{ $pos->title }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 2. DEPARTMENT SECTIONS --}}
                <div class="space-y-32 pt-16">
                    @foreach($hierarchy['departments'] as $dept)
                        <div id="dept-{{ $dept['id'] }}" class="relative scroll-mt-32">
                            {{-- Header --}}
                            <div class="text-center mb-16 relative">
                                <h3 class="text-3xl md:text-5xl font-black text-white uppercase tracking-tighter drop-shadow-2xl">
                                    {{ $dept['title'] }}
                                </h3>
                                <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-sky-500 rounded-full"></div>
                                <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 w-48 h-1 bg-sky-500/30 rounded-full blur-sm"></div>
                            </div>

                            {{-- Department Head (Featured) --}}
                            <div class="flex justify-center mb-16 relative z-10">
                                <div class="w-80 bg-gradient-to-br from-white/90 to-blue-50/90 backdrop-blur-sm rounded-3xl p-8 text-center shadow-[0_0_50px_rgba(14,165,233,0.3)] border-4 border-white transform hover:scale-105 transition-transform duration-500">
                                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-sky-600 text-white px-6 py-1 rounded-full text-sm font-bold uppercase tracking-widest shadow-lg">
                                        Department Head
                                    </div>
                                    
                                    <div class="w-40 h-40 mx-auto mb-6 relative">
                                        <div class="absolute inset-0 bg-sky-400 rounded-full blur-xl opacity-40 animate-pulse"></div>
                                        @php
                                            $head = $dept['head'];
                                            $avatarUrl = $head->user && $head->user->profile_image 
                                                ? asset('uploads/profile-images/' . basename($head->user->profile_image)) 
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($head->user ? $head->user->name : '?') . '&background=0ea5e9&color=fff';
                                        @endphp
                                        <img src="{{ $avatarUrl }}" 
                                             class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-xl relative z-10"
                                             onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ str_replace(' ', '+', $head->user ? $head->user->name : '?') }}&background=0ea5e9&color=fff';">
                                    </div>
                                    
                                    <h4 class="text-2xl font-black text-gray-800 mb-2">{{ $head->user ? $head->user->name : 'VACANT' }}</h4>
                                    <p class="text-sky-600 font-bold uppercase text-sm mb-4">{{ $head->title }}</p>
                                    @if($head->user && $head->user->role)
                                        <span class="inline-block px-4 py-1.5 bg-gray-200 rounded-lg text-gray-700 font-semibold text-xs uppercase tracking-wide">
                                            {{ $head->user->role->display_name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Connection Line --}}
                            <div class="absolute left-1/2 top-[350px] bottom-0 w-0.5 bg-gradient-to-b from-sky-500/50 to-transparent -translate-x-1/2 z-0 hidden md:block"></div>

                            {{-- Units & Staff Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-4 relative z-10">
                                @foreach($dept['units'] as $unit)
                                    <div class="bg-white/5 backdrop-blur-md rounded-3xl p-8 border border-white/10 hover:bg-white/10 transition-colors">
                                        <h5 class="text-xl font-bold text-sky-300 mb-6 flex items-center">
                                            <i class="fas fa-layer-group mr-3 opacity-70"></i>
                                            {{ $unit['position']->title }}
                                        </h5>

                                        {{-- Unit Head (if assigned and different from Dept Head) --}}
                                        @if($unit['position']->user)
                                            <div class="flex items-center gap-4 mb-6 p-4 bg-black/20 rounded-xl border border-white/5">
                                                 @php
                                                    $uHead = $unit['position']->user;
                                                    $avatarUrl = $uHead->profile_image 
                                                        ? asset('uploads/profile-images/' . basename($uHead->profile_image)) 
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($uHead->name) . '&background=random&color=fff';
                                                @endphp
                                                <img src="{{ $avatarUrl }}" class="w-16 h-16 rounded-full object-cover border-2 border-white/20"
                                                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ str_replace(' ', '+', $uHead->name) }}&background=random&color=fff';">
                                                <div>
                                                    <div class="text-white font-bold">{{ $uHead->name }}</div>
                                                    <div class="text-xs text-gray-400 uppercase">Unit Lead</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Staff List --}}
                                        @if($unit['staff']->isNotEmpty())
                                            <div class="space-y-3">
                                                @foreach($unit['staff'] as $staffPos)
                                                    @if($staffPos->user)
                                                        <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 transition-colors">
                                                            @php
                                                                $sAvatar = $staffPos->user->profile_image 
                                                                    ? asset('uploads/profile-images/' . basename($staffPos->user->profile_image)) 
                                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($staffPos->user->name) . '&background=random&color=fff';
                                                            @endphp
                                                            <img src="{{ $sAvatar }}" class="w-10 h-10 rounded-full object-cover border border-white/10"
                                                                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ str_replace(' ', '+', $staffPos->user->name) }}&background=random&color=fff';">
                                                            <div>
                                                                <div class="text-gray-200 text-sm font-medium">{{ $staffPos->user->name }}</div>
                                                                <div class="text-gray-500 text-xs">{{ $staffPos->title }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4 text-gray-500 text-sm italic">
                                                Belum ada staff assigned
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Description / Footer of Section --}}
                            <div class="mt-12 bg-blue-900/40 rounded-2xl p-6 text-center border border-blue-500/20 max-w-4xl mx-auto backdrop-blur-sm">
                                <p class="text-blue-200/80 italic">
                                    "{{ $dept['title'] }} bertanggung jawab atas operasional dan kinerja unit-unit dibawahnya untuk memastikan pelayanan terbaik di EMS Motion Life."
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </section>
                        <!-- Level Title -->
                        <div class="text-center mb-8 sm:mb-10 relative z-10">
                            <div
                                class="inline-flex items-center space-x-5 bg-gradient-to-r {{ $levelColors[$levelKey]['gradient'] ?? 'from-gray-500 to-gray-600' }} text-white px-8 py-5 rounded-2xl shadow-2xl transform hover:scale-105 transition-all duration-500 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full hover:translate-x-full transition-transform duration-1000"
                                    aria-hidden="true"></div>
                                <i class="fas {{ $levelColors[$levelKey]['icon'] ?? 'fa-user' }} text-3xl sm:text-4xl relative z-10"
                                    aria-hidden="true"></i>
                                <h2 id="level-{{ $loop->index }}-title"
                                    class="text-2xl sm:text-3xl md:text-4xl font-black relative z-10">{{ $levelData['title'] }}
                                </h2>
                            </div>
                        </div>

                        @if(isset($levelData['positions']))
                            <!-- Position Cards Section (Level 0, 1, 2) -->
                            <div class="relative">
                                @php
                                    $positionCount = count($levelData['positions']);
                                    $isMultiple = $positionCount > 1;
                                @endphp

                                <!-- Main vertical line from title -->
                                <div class="absolute left-1/2 top-0 w-0.5 h-16 sm:h-20 bg-gradient-to-b from-white/40 to-white/20 transform -translate-x-1/2 z-0"
                                    aria-hidden="true"></div>

                                @if($isMultiple)
                                    <!-- Horizontal connecting line -->
                                    <div class="absolute left-1/2 top-16 sm:top-20 w-3/4 h-0.5 bg-gradient-to-r from-white/20 via-white/30 to-white/20 transform -translate-x-1/2 z-0"
                                        aria-hidden="true"></div>
                                @endif

                                <div class="flex flex-wrap justify-center gap-6 sm:gap-8 relative z-10 pt-20 sm:pt-24">
                                    @foreach($levelData['positions'] as $position => $name)
                                        @php
                                            // Normalize names for better matching
                                            $normalizeName = function ($str) {
                                                return trim(strtolower(preg_replace('/\s+/', ' ', $str)));
                                            };
                                            $normalizedName = $normalizeName($name);

                                            // Helper to check if user should be excluded (for Tan Ackeric vs Tan Noah Rafael)
                                            $shouldExcludeUser = function ($userName) use ($normalizeName, $normalizedName) {
                                                $normalizedUserName = $normalizeName($userName);

                                                // Jika mencari "tan ackeric", exclude semua yang mengandung "noah" atau "rafael"
                                                if (str_contains($normalizedName, 'tan') && str_contains($normalizedName, 'ackeric')) {
                                                    if (str_contains($normalizedUserName, 'noah') || str_contains($normalizedUserName, 'rafael')) {
                                                        return true;
                                                    }
                                                }

                                                return false;
                                            };

                                            // Try to find user using multiple strategies
                                            $user = null;

                                            // Strategy 1: Use userByNameMap if available (fast lookup)
                                            if (isset($userByNameMap) && isset($userByNameMap[$normalizedName])) {
                                                $potentialUser = $userByNameMap[$normalizedName];
                                                if (!$shouldExcludeUser($potentialUser->name)) {
                                                    $user = $potentialUser;
                                                }
                                            }

                                            // Strategy 2: Exact match from users collection
                                            if (!$user) {
                                                $user = $users->first(function ($u) use ($normalizedName, $normalizeName, $shouldExcludeUser) {
                
            </section>
                                <!-- Staff Grid -->
                                <div
                                    class="relative grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
                                    @foreach($staffByRoleRoxwood[$roleKey] as $staff)
                                        <article class="group relative">
                                            <div
                                                class="relative bg-white rounded-xl p-5 sm:p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                                                <!-- Profile Image -->
                                                <div class="relative mx-auto mb-4">
                                                    <div class="relative inline-block">
                                                        @if($staff->isClockedIn())
                                                            <!-- Green Ring for Clocked In -->
                                                            <div class="absolute -inset-1.5 rounded-full bg-green-500 animate-pulse opacity-60"
                                                                aria-hidden="true"></div>
                                                            <div class="absolute -inset-0.5 rounded-full bg-green-400" aria-hidden="true">
                                                            </div>
                                                        @endif
                                                        @if($staff->profile_image)
                                                            <div class="absolute -inset-1 bg-gradient-to-r {{ $roxwoodGradient }} rounded-full blur opacity-0 group-hover:opacity-30 transition-opacity"
                                                                aria-hidden="true"></div>
                                                            <img src="{{ $staff->profile_image_url }}" alt="{{ $staff->name }}"
                                                                onerror="this.onerror=null;this.src='{{ asset('profile.jpg') }}';"
                                                                class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-3 {{ $staff->isClockedIn() ? 'border-green-500' : 'border-gray-200' }} group-hover:border-amber-400 transition-all duration-300">
                                                        @else
                                                            <div
                                                                class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-gradient-to-br {{ $roxwoodGradient }} flex items-center justify-center border-3 {{ $staff->isClockedIn() ? 'border-green-500' : 'border-gray-200' }}">
                                                                <i class="fas {{ $config['icon'] }} text-white text-2xl sm:text-3xl"
                                                                    aria-hidden="true"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Staff Name -->
                                                <h3
                                                    class="text-sm sm:text-base font-semibold text-gray-800 mb-3 line-clamp-2 text-center leading-snug min-h-[2.5rem] flex items-center justify-center">
                                                    {{ $staff->name }}
                                                </h3>

                                                <!-- Role Badge -->
                                                @if($staff->role)
                                                    <div class="flex justify-center">
                                                        <span
                                                            class="inline-block px-3 py-1.5 bg-gradient-to-r {{ $roxwoodGradient }} text-white text-xs sm:text-sm rounded-lg font-semibold shadow-sm">
                                                            {{ $staff->role->display_name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </section>

            <!-- Back Button -->
            <nav class="mt-16 sm:mt-20 text-center" aria-label="Navigasi kembali">
                <a href="{{ route('public.index') }}"
                    class="group relative inline-flex items-center space-x-4 bg-white/10 backdrop-blur-xl hover:bg-white/20 text-white px-10 py-5 rounded-2xl font-black text-lg transition-all duration-300 transform hover:scale-105 shadow-2xl border-2 border-white/30 hover:border-white/50 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300"
                        aria-hidden="true"></div>
                    <i class="fas fa-arrow-left text-xl relative z-10" aria-hidden="true"></i>
                    <span class="relative z-10">Kembali ke Beranda</span>
                </a>
            </nav>
        </div>
    </main>

    @push('styles')
        <style>
            @keyframes pulse-slow {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.6;
                }
            }

            .animate-pulse-slow {
                animation: pulse-slow 3s ease-in-out infinite;
            }

            @keyframes blob {

                0%,
                100% {
                    transform: translate(0, 0) scale(1);
                }

                33% {
                    transform: translate(30px, -50px) scale(1.1);
                }

                66% {
                    transform: translate(-20px, 20px) scale(0.9);
                }
            }

            .animate-blob {
                animation: blob 7s infinite;
            }

            .animation-delay-2000 {
                animation-delay: 2s;
            }

            .animation-delay-4000 {
                animation-delay: 4s;
            }

            @keyframes gradient {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 3s ease infinite;
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Organizational Chart Lines */
            .org-chart-line {
                position: absolute;
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
            }

            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 10px;
            }

            ::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
            }

            ::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, #06b6d4, #3b82f6);
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, #0891b2, #2563eb);
            }
        </style>
    @endpush
@endsection