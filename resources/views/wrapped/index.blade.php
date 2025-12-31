<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#000000">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>MedicIME Wrapped {{ $year }} - {{ $user->name }}</title>

    <!-- Tailwind CSS (Local Build) -->
    <link rel="stylesheet" href="{{ asset('assets/wrapped/tailwind.min.css') }}">

    <!-- Swiper CSS (Local) -->
    <link rel="stylesheet" href="{{ asset('assets/wrapped/swiper-bundle.min.css') }}">

    <!-- Canvas Confetti (Local) -->
    <script src="{{ asset('assets/wrapped/confetti.browser.min.js') }}"></script>

    <!-- Fonts - Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --swiper-theme-color: #fff;
        }

        html,
        body {
            height: 100vh;
            height: 100dvh;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #000000;
            color: #ffffff;
            overflow: hidden;
            overscroll-behavior: none;
            -webkit-tap-highlight-color: transparent;
        }

        .noise-overlay {
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.04'/%3E%3C/svg%3E");
            z-index: 50;
            pointer-events: none;
        }

        .gradient-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            transition: transform .7s cubic-bezier(.16, 1, .3, 1);
        }

        .blob-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: blob 15s infinite alternate;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .glass-highlight {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* --- CINEMATIC SLIDE TRANSITIONS --- */
        .swiper-slide {
            transition:
                transform .7s cubic-bezier(.16, 1, .3, 1),
                filter .7s ease,
                opacity .7s ease;
        }

        .swiper-slide-prev,
        .swiper-slide-next {
            filter: blur(6px) brightness(.6);
            transform: scale(.92);
            opacity: .5;
        }

        .swiper-slide-active {
            filter: blur(0);
            transform: scale(1);
            opacity: 1;
        }

        /* --- TYPOGRAPHY GRADIENTS --- */
        .text-grad-blue {
            background: linear-gradient(to right, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-grad-gold {
            background: linear-gradient(to right, #fde68a, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-grad-green {
            background: linear-gradient(to right, #4ade80, #0d9488);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-grad-fire {
            background: linear-gradient(to right, #fbbf24, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- FESTIVE RAINBOW GRADIENT --- */
        .text-grad-rainbow {
            background: linear-gradient(90deg, #ff0080, #ff8c00, #40e0d0, #ff0080, #ff8c00);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: rainbow 4s linear infinite;
        }

        .bg-grad-party {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: rainbow 6s ease infinite;
        }

        /* --- PROGRESSIVE STORY BARS --- */
        .story-bars {
            position: fixed;
            top: env(safe-area-inset-top, 12px);
            left: 0;
            width: 100%;
            padding: 0 8px;
            z-index: 60;
            display: flex;
            gap: 4px;
        }

        .bar-track {
            flex: 1;
            height: 2px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #fff;
            width: 0%;
            transition: width .7s linear;
        }

        /* --- USER PHOTO STYLES --- */
        .user-photo-container {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 2rem;
        }

        .user-photo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 40px rgba(96, 165, 250, 0.4), 0 0 80px rgba(192, 132, 252, 0.2);
            animation: float 6s ease-in-out infinite, pulseGlow 3s ease-in-out infinite;
        }

        .photo-ring {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 2px solid transparent;
            background: linear-gradient(45deg, #60a5fa, #c084fc, #60a5fa) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: spin-slow infinite linear;
        }

        .sparkle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
            animation: sparkle 2s ease-in-out infinite;
        }

        .sparkle:nth-child(1) {
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .sparkle:nth-child(2) {
            top: 30%;
            left: 5%;
            animation-delay: 0.3s;
        }

        .sparkle:nth-child(3) {
            bottom: 20%;
            right: 15%;
            animation-delay: 0.6s;
        }

        .sparkle:nth-child(4) {
            bottom: 10%;
            left: 20%;
            animation-delay: 0.9s;
        }

        .sparkle:nth-child(5) {
            top: 50%;
            right: 0%;
            animation-delay: 1.2s;
        }

        .sparkle:nth-child(6) {
            top: 50%;
            left: 0%;
            animation-delay: 1.5s;
        }

        /* --- FLOATING PARTICLES --- */
        .particle {
            position: absolute;
            font-size: 20px;
            opacity: 0.4;
            pointer-events: none;
            animation: floatParticle 15s infinite ease-in-out;
        }

        .celebration-particle {
            position: absolute;
            font-size: 24px;
            opacity: 0.6;
            pointer-events: none;
            animation: floatParticle 12s infinite ease-in-out;
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.5));
        }

        @keyframes floatParticle {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.3;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translate(50px, -100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* --- MOTION TIMELINE SYSTEM --- */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scalePop {
            from {
                opacity: 0;
                transform: scale(.85);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Content visible by default - animation enhances, doesn't hide */
        .motion {
            opacity: 1;
        }

        .motion.show {
            animation: fadeUp .8s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        /* First load - elements start visible, will animate whe show class added */

        /* --- BADGE GLOW --- */
        .badge-glow {
            animation: badgePulse 3s infinite;
        }

        @keyframes badgePulse {

            0%,
            100% {
                box-shadow: 0 0 30px rgba(255, 215, 0, .3);
            }

            50% {
                box-shadow: 0 0 80px rgba(255, 215, 0, .9);
            }
        }

        /* --- SALARY ICON RING --- */
        .icon-ring {
            position: absolute;
            inset: -12px;
            border: 2px dashed rgba(16, 185, 129, .4);
            border-radius: 50%;
            animation: spin 18s linear infinite;
        }

        .icon-core {
            font-size: 4rem;
        }

        /* --- NUMBER PULSE & SHIMMER --- */
        .number-pulse {
            animation: numberPulse 2s ease-in-out infinite;
        }

        @keyframes numberPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .number-shimmer {
            position: relative;
            animation: shimmer 2s ease-in-out infinite;
        }

        /* --- CELEBRATION CARD EFFECTS --- */
        .card-bounce {
            animation: bounceGentle 2s ease-in-out infinite;
        }

        .card-glow-rainbow {
            box-shadow: 0 0 20px rgba(255, 0, 128, 0.3),
                0 0 40px rgba(64, 224, 208, 0.2);
            animation: rainbowGlow 3s ease-in-out infinite;
        }

        @keyframes rainbowGlow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 0, 128, 0.4), 0 0 40px rgba(64, 224, 208, 0.2);
            }

            33% {
                box-shadow: 0 0 20px rgba(255, 140, 0, 0.4), 0 0 40px rgba(75, 172, 226, 0.2);
            }

            66% {
                box-shadow: 0 0 20px rgba(64, 224, 208, 0.4), 0 0 40px rgba(255, 0, 128, 0.2);
            }
        }

        /* --- SWIPER FIXES --- */
        .swiper {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: env(safe-area-inset-top) 24px env(safe-area-inset-bottom) 24px;
        }
    </style>
</head>

<body>

    <!-- Noise Texture -->
    <div class="noise-overlay"></div>

    <!-- Logo Branding - Top Left 🏥 -->
    <div class="fixed top-[calc(env(safe-area-inset-top)+20px)] left-4 z-[70] flex items-center gap-3">
        <!-- IME Logo -->
        <div
            class="glass-panel rounded-2xl p-3 border border-white/10 backdrop-blur-xl hover:scale-110 transition-transform duration-300">
            <img src="{{ asset('images/logoime.webp') }}" alt="IME" class="h-10 w-auto object-contain">
        </div>
        <!-- Motion Life Logo -->
        <div
            class="glass-panel rounded-2xl p-2.5 border border-white/10 backdrop-blur-xl hover:scale-110 transition-transform duration-300">
            <img src="{{ asset('images/motionlife-logo.png') }}" alt="Motion Life" class="h-8 w-auto object-contain">
        </div>
    </div>

    <!-- Floating Particles Container -->
    <div id="particlesContainer" class="fixed inset-0 pointer-events-none z-1"></div>

    <!-- Progress Bars -->
    <div class="story-bars" id="storyBars"></div>

    <!-- Close Button -->
    <button onclick="closeWrapped()"
        class="fixed top-[calc(env(safe-area-inset-top)+20px)] right-4 z-[70] w-10 h-10 rounded-full glass-panel flex items-center justify-center text-white/70 hover:bg-white/10 hover:text-white transition active:scale-90">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>

    <!-- Music Mute/Unmute Button 🔊 -->
    <button onclick="toggleMusic()" id="musicToggle"
        class="fixed top-[calc(env(safe-area-inset-top)+80px)] right-4 z-[70] w-10 h-10 rounded-full glass-panel flex items-center justify-center text-white/70 hover:bg-white/10 hover:text-white transition active:scale-90">
        <!-- Sound On Icon (default) -->
        <svg id="soundOnIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
        </svg>
        <!-- Sound Off Icon (hidden by default) -->
        <svg id="soundOffIcon" class="hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
            <line x1="23" y1="9" x2="17" y2="15"></line>
            <line x1="17" y1="9" x2="23" y2="15"></line>
        </svg>
    </button>

    <!-- Background Music 🎵 -->
    <audio id="bgMusic" loop muted autoplay>
        <source src="/SnapTik.Net_7586158924605230343.mp3" type="audio/mpeg">
    </audio>

    <!-- Main Swiper -->
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">

            <!-- SLIDE 1: INTRO - HERO CINEMATIC -->
            <div class="swiper-slide" data-slide="intro">
                <div class="gradient-bg">
                    <div class="blob-shape bg-blue-600 w-80 h-80 top-[-10%] left-[-20%]"></div>
                    <div class="blob-shape bg-purple-600 w-96 h-96 bottom-[-10%] right-[-20%]"></div>
                    <div class="blob-shape bg-pink-500 w-72 h-72 top-[20%] right-[10%]"></div>
                </div>

                <div class="relative z-10 w-full max-w-lg text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-[11px] font-bold tracking-[0.2em] uppercase mb-8 motion"
                        data-delay="100">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                        MedicIME Wrapped {{ $year }}
                    </div>

                    <!-- USER PHOTO WITH SPARKLES -->
                    <div class="user-photo-container motion" data-delay="200"
                        style="animation: scalePop .8s cubic-bezier(.16,1,.3,1) forwards;">
                        <div class="photo-ring"></div>
                        @if($user->profile_image)
                            <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="user-photo"
                                onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'user-photo flex items-center justify-center text-5xl bg-gradient-to-br from-blue-500 to-purple-600\'>{{ strtoupper(substr($user->name, 0, 1)) }}</div>';">
                        @else
                            <div
                                class="user-photo flex items-center justify-center text-5xl bg-gradient-to-br from-blue-500 to-purple-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                        <div class="sparkle"></div>
                    </div>

                    <h1 class="text-5xl md:text-6xl font-bold tracking-tight leading-tight mb-2 motion"
                        data-delay="300">
                        Halo,
                    </h1>

                    <h2 class="text-6xl md:text-7xl font-extrabold text-grad-rainbow mb-6 motion" data-delay="500">
                        {{ $user->name }}
                    </h2>

                    <p class="text-lg text-white/60 leading-relaxed mb-8 motion" data-delay="800">
                        Inilah perjalanan medis kamu di {{ $year }}
                    </p>

                    <div class="absolute bottom-[calc(env(safe-area-inset-bottom)+20px)] left-0 w-full flex flex-col items-center justify-center gap-2 motion opacity-60"
                        data-delay="1100">
                        <div class="w-[1px] h-12 bg-gradient-to-b from-transparent via-white/50 to-transparent"></div>
                        <span class="text-[10px] uppercase tracking-widest text-white/50">Swipe Up</span>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: HOURS - DEDICATION -->
            <div class="swiper-slide" data-slide="hours">
                <div class="gradient-bg">
                    <div class="blob-shape bg-orange-600 w-full h-[60%] top-0 left-0 opacity-40"></div>
                    <div class="blob-shape bg-yellow-500 w-96 h-96 bottom-[-20%] right-[-10%] opacity-40"></div>
                </div>

                <div class="relative z-10 w-full max-w-md">
                    <h2 class="text-2xl font-bold mb-6 text-center motion" data-delay="200">Dedikasi Waktu</h2>

                    <!-- Hero Card -->
                    <div class="glass-panel glass-highlight rounded-[2rem] p-8 text-center mb-4 relative overflow-hidden motion"
                        data-delay="400" style="animation: scalePop .8s cubic-bezier(.16,1,.3,1) forwards;">
                        <div class="absolute top-0 right-0 p-5 opacity-10 text-6xl rotate-12 animate-spin-slow">⏱️</div>
                        <p class="text-xs font-mono text-orange-400 uppercase tracking-widest mb-2">Total Jam Praktik
                        </p>
                        <div class="font-mono text-7xl font-bold tracking-tighter text-white drop-shadow-lg number-pulse number-shimmer"
                            id="hoursNumber">
                            {{ number_format($stats['total_hours'], 0) }}
                            <span class="text-2xl text-white/30 font-normal">h</span>
                        </div>
                        @if($stats['total_hours'] == 0)
                            <p class="text-xs text-white/40 mt-4">Belum ada data kehadiran untuk {{ $year }}</p>
                        @endif
                    </div>

                    <!-- Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="glass-panel rounded-[1.5rem] p-5 flex flex-col justify-between h-36 motion card-bounce card-glow-rainbow"
                            data-delay="600">
                            <div class="text-2xl w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">📅
                            </div>
                            <div>
                                <div class="text-3xl font-bold font-mono number-shimmer">
                                    {{ $stats['total_days_worked'] }}
                                </div>
                                <div class="text-[10px] uppercase text-white/40 font-bold tracking-wider mt-1">Hari
                                    Kerja</div>
                            </div>
                        </div>
                        <div class="glass-panel rounded-[1.5rem] p-5 flex flex-col justify-between h-36 motion card-bounce card-glow-rainbow"
                            data-delay="800" style="animation-delay: 0.3s;">
                            <div class="text-2xl w-10 h-10 rounded-full bg-white/5 flex items-center justify-center">⚡
                            </div>
                            <div>
                                <div class="text-3xl font-bold font-mono number-shimmer">
                                    {{ $stats['average_hours_per_day'] }}
                                </div>
                                <div class="text-[10px] uppercase text-white/40 font-bold tracking-wider mt-1">Jam /
                                    Hari</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: PATIENTS - IMPACT -->
            @if($stats['total_patients'] > 0)
                <div class="swiper-slide" data-slide="patients">
                    <div class="gradient-bg">
                        <div class="blob-shape bg-pink-600 w-full h-full opacity-30"></div>
                        <div class="blob-shape bg-red-500 w-80 h-80 top-[20%] right-[-10%] opacity-30"></div>
                    </div>

                    <div class="relative z-10 w-full px-6 text-center">
                        <div class="inline-block relative mb-8 motion" data-delay="200"
                            style="animation: scalePop .8s cubic-bezier(.16,1,.3,1) forwards;">
                            <div class="absolute inset-0 bg-red-500 blur-[80px] opacity-30 animate-pulse-glow"></div>
                            <div class="text-8xl animate-float relative z-10">🫀</div>
                        </div>

                        <h2 class="text-sm font-bold text-white/60 uppercase tracking-[0.3em] mb-4 motion" data-delay="400">
                            Jiwa Disentuh</h2>

                        <div class="font-mono text-[6rem] leading-none font-bold text-grad-rainbow drop-shadow-2xl motion number-shimmer"
                            data-delay="700" id="patientsNumber">
                            {{ number_format($stats['total_patients']) }}
                        </div>

                        @if($stats['most_common_form_type'])
                            <div class="mt-12 glass-panel rounded-full py-3 pl-4 pr-6 inline-flex items-center gap-3 motion"
                                data-delay="1000">
                                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-sm">🩺
                                </div>
                                <div class="text-left">
                                    <div class="text-[9px] text-white/40 uppercase tracking-wider font-bold">Top Case</div>
                                    <div class="text-sm font-bold">{{ $stats['most_common_form_type']['formatted'] }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- SLIDE 4: PEAK MOMENT - TIMELINE -->
            <div class="swiper-slide" data-slide="timeline">
                <div class="gradient-bg">
                    <div class="blob-shape bg-orange-500 w-96 h-96 right-[-20%] top-[-10%] opacity-40"></div>
                    <div class="blob-shape bg-yellow-400 w-80 h-80 left-[-10%] bottom-0 opacity-40"></div>
                    <div class="blob-shape bg-red-500 w-72 h-72 right-[10%] bottom-[20%] opacity-30"></div>
                </div>

                <div class="relative z-10 w-full max-w-md">
                    <h2 class="text-3xl font-bold mb-8 text-center text-grad-fire motion" data-delay="200">Momen Puncak
                    </h2>

                    <div class="space-y-4">
                        @if($stats['busiest_month'])
                            <div class="glass-panel p-6 rounded-[1.5rem] flex items-center gap-5 border-l-4 border-l-orange-500 relative overflow-hidden group motion"
                                data-delay="400">
                                <div class="absolute inset-0 bg-orange-500/5 group-hover:bg-orange-500/10 transition"></div>
                                <div
                                    class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-2xl relative z-10">
                                    📆</div>
                                <div class="flex-1 relative z-10">
                                    <div class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Bulan
                                        Tersibuk</div>
                                    <div class="text-2xl font-bold font-mono mt-0.5">{{ $stats['busiest_month']['name'] }}
                                    </div>
                                </div>
                                <div class="text-right opacity-50 font-mono text-sm relative z-10">
                                    {{ number_format($stats['busiest_month']['hours'], 0) }}h
                                </div>
                            </div>
                        @endif

                        @if($stats['most_active_day'])
                            <div class="glass-panel p-6 rounded-[1.5rem] flex items-center gap-5 border-l-4 border-l-pink-500 relative overflow-hidden group motion"
                                data-delay="700">
                                <div class="absolute inset-0 bg-pink-500/5 group-hover:bg-pink-500/10 transition"></div>
                                <div
                                    class="w-12 h-12 rounded-full bg-pink-500/20 flex items-center justify-center text-2xl relative z-10">
                                    🚀</div>
                                <div class="flex-1 relative z-10">
                                    <div class="text-[10px] font-bold text-pink-400 uppercase tracking-widest">Hari Favorit
                                    </div>
                                    <div class="text-2xl font-bold font-mono mt-0.5">{{ $stats['most_active_day']['name'] }}
                                    </div>
                                </div>
                                <div class="text-right opacity-50 font-mono text-sm relative z-10">
                                    {{ $stats['most_active_day']['count'] }}x
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SLIDE 5: SALARY - PEAK MOMENT -->
            @if($stats['total_salary'] > 0)
                <div class="swiper-slide" data-slide="salary">
                    <div class="gradient-bg">
                        <div class="blob-shape bg-emerald-600 w-full h-[60%] bottom-0 opacity-35"></div>
                        <div class="blob-shape bg-teal-500 w-96 h-96 top-[-10%] right-[-10%] opacity-35"></div>
                        <div class="blob-shape bg-green-400 w-80 h-80 left-[10%] top-[30%] opacity-25"></div>
                    </div>

                    <div class="relative z-10 w-full px-4 text-center">
                        <!-- Salary Icon -->
                        <div class="relative w-20 h-20 mx-auto mb-8 motion" data-delay="200"
                            style="animation: scalePop .8s cubic-bezier(.16,1,.3,1) forwards;">
                            <div class="icon-ring"></div>
                            <div
                                class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center shadow-[0_0_40px_rgba(16,185,129,0.2)] border border-emerald-500/20">
                                <div class="icon-core">💸</div>
                            </div>
                        </div>

                        <p class="text-xs font-mono text-emerald-400 mb-4 uppercase tracking-[0.25em] motion"
                            data-delay="400">
                            Total Pendapatan {{ $year }}
                        </p>

                        <div class="relative group cursor-pointer select-none" onclick="triggerMoneyRain()">
                            <div
                                class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full opacity-0 group-hover:opacity-100 transition duration-500">
                            </div>
                            <div class="relative font-mono text-4xl md:text-5xl font-bold text-white py-4 px-2 motion transition transform active:scale-95"
                                data-delay="700">
                                Rp {{ number_format($stats['total_salary'], 0, ',', '.') }}
                            </div>
                            <div class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-full bg-white/5 border border-white/5 text-[10px] text-white/50 animate-pulse motion"
                                data-delay="1000">
                                <span>👆</span> Tap untuk rayakan hasil kerja kerasmu
                            </div>
                        </div>
                    </div>
                    <div id="moneyContainer" class="fixed inset-0 pointer-events-none z-[60]"></div>
                </div>
            @endif

            <!-- SLIDE 6: BADGE - LEGENDARY UNLOCK -->
            <div class="swiper-slide" data-slide="badge">
                <div class="gradient-bg">
                    <div class="blob-shape bg-yellow-500 w-full h-full opacity-20"></div>
                    <div class="blob-shape bg-orange-400 w-96 h-96 top-[-10%] left-[-10%] opacity-20"></div>
                    <div class="blob-shape bg-amber-500 w-80 h-80 bottom-[10%] right-[-10%] opacity-15"></div>
                </div>

                <div class="relative z-10 text-center px-6 max-w-sm">
                    <div class="text-yellow-200 text-[10px] font-bold tracking-[0.3em] uppercase mb-10 motion"
                        data-delay="200">
                        Persona Medis</div>

                    <div class="relative w-64 h-64 mx-auto mb-10 flex items-center justify-center motion"
                        data-delay="400" style="animation: scalePop .8s cubic-bezier(.16,1,.3,1) forwards;">
                        <div class="absolute inset-0 border border-yellow-500/30 rounded-full animate-[spin_20s_linear_infinite]"
                            style="border-style: dashed;"></div>
                        <div class="absolute inset-4 border border-white/10 rounded-full"></div>
                        <div class="absolute inset-0 bg-yellow-500/10 blur-3xl rounded-full badge-glow"></div>
                        <div
                            class="text-[8rem] relative z-10 drop-shadow-2xl transition transform hover:scale-110 duration-300 cursor-pointer">
                            {{ $stats['badge']['primary']['icon'] }}
                        </div>
                    </div>

                    <h1 class="text-4xl font-bold text-grad-gold mb-6 leading-tight motion" data-delay="700">
                        {{ $stats['badge']['primary']['title'] }}
                    </h1>

                    <div class="glass-panel p-5 rounded-2xl text-sm leading-relaxed text-white/80 border-t border-white/20 motion"
                        data-delay="900">
                        {{ $stats['badge']['primary']['description'] }}
                    </div>

                    <!-- Additional Badges -->
                    @if(count($stats['badge']['all']) > 1)
                        <div class="mt-6 motion" data-delay="1100">
                            <p class="text-xs text-white/40 uppercase tracking-wider mb-3">Achievement Unlocked</p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                @foreach($stats['badge']['all'] as $index => $badge)
                                    @if($index > 0)
                                        <div
                                            class="glass-panel px-3 py-2 rounded-full text-xs flex items-center gap-2 border border-white/5">
                                            <span class="text-lg">{{ $badge['icon'] }}</span>
                                            <span class="font-medium">{{ $badge['title'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SLIDE 7: OUTRO - CLOSING CINEMATIC -->
            <div class="swiper-slide" data-slide="outro">
                <div class="gradient-bg bg-grad-party">
                    <div class="blob-shape bg-blue-600 w-full h-full opacity-20"></div>
                    <div class="blob-shape bg-purple-500 w-96 h-96 top-[-10%] right-[-10%] opacity-20"></div>
                    <div class="blob-shape bg-pink-500 w-80 h-80 bottom-[-10%] left-[-10%] opacity-20"></div>
                </div>

                <div class="relative z-10 text-center px-6 w-full max-w-md">
                    <h2 class="text-5xl font-bold mb-10 motion" data-delay="300">Terima Kasih</h2>

                    <div class="glass-panel p-8 rounded-[2rem] mb-6 relative overflow-hidden text-left motion"
                        data-delay="700" style="animation: scalePop .8s cubic-bezier(.16,1,.3,1) forwards;">
                        <div
                            class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-400 via-purple-500 to-pink-500">
                        </div>
                        <p class="font-serif italic text-xl text-white/90 leading-relaxed">
                            "{{ $quote['text'] }}"
                        </p>
                        @if($quote['author'])
                            <p class="text-right text-sm text-white/50 mt-4">
                                — {{ $quote['author'] }}
                            </p>
                        @endif
                    </div>

                    <!-- Developer Message -->
                    <div class="glass-panel p-6 rounded-2xl mb-10 border border-white/10 motion" data-delay="900">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-lg">
                                💻
                            </div>
                            <div class="text-left">
                                <div class="text-xs text-cyan-400 font-bold uppercase tracking-wider">From the Developer
                                </div>
                                <div class="text-sm text-white/60">With ❤️</div>
                            </div>
                        </div>
                        <p class="text-sm text-white/80 leading-relaxed text-left">
                            Salam hangat dari developer yang jarang di rumah sakit ini 😄
                        </p>
                        <p class="text-right text-xs font-mono text-cyan-400/70 mt-3">
                            — Tepe Zhavarez
                        </p>
                    </div>

                    <!-- Admin Reminder Message -->
                    <div class="glass-panel p-6 rounded-2xl mb-10 border border-yellow-500/20 motion" data-delay="950">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-lg">
                                ⚠️
                            </div>
                            <div class="text-left">
                                <div class="text-xs text-yellow-400 font-bold uppercase tracking-wider">From Admin</div>
                                <div class="text-sm text-white/60">Reminder Penting!</div>
                            </div>
                        </div>
                        <p class="text-sm text-white/80 leading-relaxed text-left">
                            Jangan lupa check out setelah off duty, nanti dimarahin admin zoel 😤
                        </p>
                        <p class="text-right text-xs font-mono text-yellow-400/70 mt-3">
                            — Zoel Lysander
                        </p>
                    </div>

                    <button onclick="closeWrapped()"
                        class="w-full py-4 bg-white text-black font-bold rounded-full text-lg shadow-[0_0_30px_rgba(255,255,255,0.3)] hover:shadow-[0_0_50px_rgba(255,255,255,0.5)] transition transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 motion"
                        data-delay="1100">
                        <span>Mulai Lembaran Baru</span> 🚀
                    </button>

                    <div class="mt-8 opacity-30 text-[10px] font-mono motion" data-delay="1300">
                        MedicIME Wrapped {{ $year }} • Medical Center IME
                    </div>

                    <!-- Logo Watermark -->
                    <div class="mt-6 flex items-center justify-center gap-4 motion" data-delay="1400">
                        <div
                            class="glass-panel rounded-xl p-2.5 border border-white/10 opacity-50 hover:opacity-100 transition-opacity">
                            <img src="{{ asset('images/logoime.webp') }}" alt="IME" class="h-10 w-auto object-contain">
                        </div>
                        <div class="text-white/30 text-xs">×</div>
                        <div
                            class="glass-panel rounded-xl p-2.5 border border-white/10 opacity-50 hover:opacity-100 transition-opacity">
                            <img src="{{ asset('images/motionlife-logo.png') }}" alt="Motion Life"
                                class="h-10 w-auto object-contain">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Swiper JS (Local) -->
    <script src="{{ asset('assets/wrapped/swiper-bundle.min.js') }}"></script>
    <script>
        // --- FESTIVE CELEBRATION PARTICLES ---
        function createFloatingParticles() {
            const container = document.getElementById('particlesContainer');
            const medicalParticles = ['💊', '🩺', '❤️', '⚕️', '💉', '🏥'];
            const celebrationParticles = ['✨', '🎉', '🎊', '💫', '⭐', '🌟', '🎈', '🎁', '🎆'];
            const allParticles = [...medicalParticles, ...celebrationParticles];

            // More frequent spawning for festive feel
            setInterval(() => {
                const particle = document.createElement('div');
                const isCelebration = Math.random() > 0.4; // 60% celebration particles
                particle.className = isCelebration ? 'celebration-particle' : 'particle';
                particle.textContent = allParticles[Math.floor(Math.random() * allParticles.length)];
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = (Math.random() * 8 + 8) + 's'; // Faster
                particle.style.animationDelay = Math.random() * 1 + 's';
                container.appendChild(particle);

                setTimeout(() => particle.remove(), 14000);
            }, 1500); // Spawn more frequently
        }

        // --- PROGRESSIVE STORY BARS ---
        let slideCount = 0;

        function initStoryBars(count) {
            slideCount = count;
            const container = document.getElementById('storyBars');
            let html = '';
            for (let i = 0; i < count; i++) {
                html += `<div class="bar-track"><div class="bar-fill" id="bar-${i}"></div></div>`;
            }
            container.innerHTML = html;
            updateStoryBars(0);
        }

        function updateStoryBars(index) {
            for (let i = 0; i < slideCount; i++) {
                const bar = document.getElementById(`bar-${i}`);
                bar.style.transition = 'width .7s linear';

                if (i < index) {
                    bar.style.width = '100%';
                } else if (i === index) {
                    bar.style.width = '0%';
                    setTimeout(() => bar.style.width = '100%', 50);
                } else {
                    bar.style.width = '0%';
                }
            }
        }

        // --- MOTION TIMELINE ENGINE ---
        function playSlideTimeline(slide) {
            slide.querySelectorAll('[data-delay]').forEach(el => {
                el.classList.remove('show');
                setTimeout(() => {
                    el.classList.add('show');
                }, parseInt(el.dataset.delay));
            });
        }

        // --- NUMBER COUNT-UP ANIMATION ---
        function animateNumber(el, end) {
            if (!el) return;
            let start = 0;
            const step = end / 40;
            const timer = setInterval(() => {
                start += step;
                const current = Math.floor(start);
                el.innerText = current.toLocaleString('id-ID');
                if (start >= end) {
                    el.innerText = end.toLocaleString('id-ID');
                    clearInterval(timer);
                }
            }, 25);
        }

        // --- SOUND EFFECTS SYSTEM ---
        let audioContext = null;

        // Initialize Audio Context (lazy initialization)
        function getAudioContext() {
            if (!audioContext) {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
            return audioContext;
        }

        // Confetti Pop Sound - bright celebratory pop
        function playConfettiSound() {
            try {
                const ctx = getAudioContext();

                // Main pop tone
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.connect(gain);
                gain.connect(ctx.destination);

                // Bright pop sound with frequency sweep
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.05);
                osc.frequency.exponentialRampToValueAtTime(400, ctx.currentTime + 0.15);

                osc.type = 'sine';

                // Quick envelope
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);

                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.15);
            } catch (e) {
                // Silently fail if audio context not available
                console.log('Audio not available');
            }
        }

        // Money Ka-Ching Sound - cash register style
        function playMoneySound() {
            try {
                const ctx = getAudioContext();

                // First "ka" - metallic hit
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.frequency.setValueAtTime(1200, ctx.currentTime);
                osc1.frequency.exponentialRampToValueAtTime(800, ctx.currentTime + 0.05);
                osc1.type = 'square';
                gain1.gain.setValueAtTime(0.15, ctx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.05);
                osc1.start(ctx.currentTime);
                osc1.stop(ctx.currentTime + 0.05);

                // Second "ching" - bell-like tone
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.frequency.setValueAtTime(1600, ctx.currentTime + 0.06);
                osc2.type = 'sine';
                gain2.gain.setValueAtTime(0.2, ctx.currentTime + 0.06);
                gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc2.start(ctx.currentTime + 0.06);
                osc2.stop(ctx.currentTime + 0.3);

                // Third harmonic for richness
                const osc3 = ctx.createOscillator();
                const gain3 = ctx.createGain();
                osc3.connect(gain3);
                gain3.connect(ctx.destination);
                osc3.frequency.setValueAtTime(2400, ctx.currentTime + 0.06);
                osc3.type = 'sine';
                gain3.gain.setValueAtTime(0.1, ctx.currentTime + 0.06);
                gain3.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                osc3.start(ctx.currentTime + 0.06);
                osc3.stop(ctx.currentTime + 0.25);
            } catch (e) {
                console.log('Audio not available');
            }
        }

        // --- AUTO CELEBRATION CONFETTI ---
        function triggerMiniConfetti() {
            playConfettiSound(); // 🔊 Play sound!
            confetti({
                particleCount: 50,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#ff0080', '#ff8c00', '#40e0d0', '#FFD700', '#FDE68A', '#ffffff'],
                disableForReducedMotion: true
            });
        }

        // --- EFFECTS (Confetti & Money) ---
        function handleEffects(slide) {
            const type = slide.getAttribute('data-slide');

            // Clean up old money rain
            const moneyContainer = document.getElementById('moneyContainer');
            if (moneyContainer) moneyContainer.innerHTML = '';

            // Auto-confetti on every slide change (except first)
            if (type !== 'intro') {
                triggerMiniConfetti();
            }

            // Intro slide - welcome confetti
            if (type === 'intro') {
                setTimeout(() => {
                    playConfettiSound(); // 🔊 Welcome pop!
                    confetti({
                        particleCount: 80,
                        spread: 100,
                        origin: { y: 0.5 },
                        colors: ['#60a5fa', '#c084fc', '#ec4899', '#fbbf24'],
                        disableForReducedMotion: true
                    });
                }, 600);
            }

            // Count-up for patients
            if (type === 'patients') {
                setTimeout(() => {
                    const patientsEl = document.getElementById('patientsNumber');
                    if (patientsEl) {
                        const targetNumber = parseInt(patientsEl.innerText.replace(/\D/g, ''));
                        patientsEl.innerText = '0';
                        animateNumber(patientsEl, targetNumber);
                    }
                }, 700);
            }

            if (type === 'salary') {
                setTimeout(triggerMoneyRain, 400);
            }

            // Badge slide - BIG confetti explosion
            if (type === 'badge') {
                setTimeout(() => {
                    playConfettiSound(); // 🔊 Big celebration!
                    confetti({
                        particleCount: 200,
                        spread: 120,
                        origin: { y: 0.6 },
                        colors: ['#FFD700', '#FDE68A', '#ff8c00', '#ff0080', '#40e0d0', '#ffffff'],
                        disableForReducedMotion: true
                    });
                }, 400);
            }

            if (type === 'outro') {
                launchFireworks();
            }
        }

        function triggerMoneyRain() {
            playMoneySound(); // 🔊 Ka-ching!
            const container = document.getElementById('moneyContainer');
            if (!container) return;
            const emojis = ['💸', '💵', '💎', '💰'];

            if (!document.getElementById('animStyles')) {
                const style = document.createElement('style');
                style.id = 'animStyles';
                style.innerHTML = `@keyframes fall { to { transform: translateY(120vh) rotate(360deg); } }`;
                document.head.appendChild(style);
            }

            for (let i = 0; i < 20; i++) {
                const el = document.createElement('div');
                el.innerText = emojis[Math.floor(Math.random() * emojis.length)];
                el.style.position = 'absolute';
                el.style.left = Math.random() * 100 + '%';
                el.style.top = '-50px';
                el.style.fontSize = (Math.random() * 20 + 24) + 'px';
                el.style.animation = `fall ${Math.random() * 2 + 2}s linear forwards`;
                el.style.zIndex = '100';
                container.appendChild(el);
            }
        }

        function launchFireworks() {
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };
            var randomInRange = (min, max) => Math.random() * (max - min) + min;

            var interval = setInterval(function () {
                var timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) return clearInterval(interval);
                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }

        // --- MUSIC CONTROL ---
        function toggleMusic() {
            const bgMusic = document.getElementById('bgMusic');
            const soundOnIcon = document.getElementById('soundOnIcon');
            const soundOffIcon = document.getElementById('soundOffIcon');

            if (bgMusic.muted) {
                // Unmute
                bgMusic.muted = false;
                soundOnIcon.classList.remove('hidden');
                soundOffIcon.classList.add('hidden');
                console.log('🎵 Music unmuted');
            } else {
                // Mute
                bgMusic.muted = true;
                soundOnIcon.classList.add('hidden');
                soundOffIcon.classList.remove('hidden');
                console.log('🔇 Music muted');
            }
        }

        // --- BACKEND LOGIC ---
        function closeWrapped() {
            // Fade out music 🎵
            const bgMusic = document.getElementById('bgMusic');
            if (bgMusic) {
                const fadeOut = setInterval(() => {
                    if (bgMusic.volume > 0.05) {
                        bgMusic.volume -= 0.05;
                    } else {
                        bgMusic.pause();
                        clearInterval(fadeOut);
                    }
                }, 50);
            }

            const wrapper = document.querySelector('.swiper');
            wrapper.style.transition = 'all 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
            wrapper.style.opacity = '0';
            wrapper.style.transform = 'scale(0.9) translateY(20px)';

            document.getElementById('storyBars').style.opacity = '0';

            setTimeout(() => {
                fetch('{{ route("wrapped.dismiss") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ year: {{ $year }} })
                }).finally(() => {
                    sessionStorage.setItem('wrapped_{{ $year }}_seen', 'true');
                    window.location.href = '{{ route("staff.dashboard") }}';
                });
            }, 500);
        }

        function recordView() {
            if (sessionStorage.getItem('wrapped_recorded')) return;
            fetch('{{ route("wrapped.record") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ year: {{ $year }} })
            }).then(() => sessionStorage.setItem('wrapped_recorded', 'true'));
        }

        // --- SETUP SWIPER ---
        const swiper = new Swiper('.mySwiper', {
            direction: 'vertical',
            effect: 'slide',
            speed: 700,
            pagination: false,
            on: {
                init: function () {
                    initStoryBars(this.slides.length);
                    playSlideTimeline(this.slides[0]);
                    recordView();
                    createFloatingParticles();

                    // Initialize background music 🎵
                    const bgMusic = document.getElementById('bgMusic');
                    if (bgMusic) {
                        console.log('🎵 Music element found, initializing...');
                        bgMusic.volume = 0.3; // Set comfortable volume

                        // Start playing muted (always works)
                        bgMusic.play()
                            .then(() => {
                                console.log('🎵 Music started (muted) - will unmute on first swipe');
                            })
                            .catch((error) => {
                                console.log('🎵 Failed to start:', error);
                            });
                    } else {
                        console.error('🎵 Music element not found!');
                    }
                },
                slideChange: function () {
                    updateStoryBars(this.activeIndex);
                    playSlideTimeline(this.slides[this.activeIndex]);
                    handleEffects(this.slides[this.activeIndex]);
                    if (navigator.vibrate) navigator.vibrate(10);

                    // 🎵 Unmute music on first swipe
                    const bgMusic = document.getElementById('bgMusic');
                    console.log('🔍 Swipe detected! Index:', this.activeIndex);

                    if (bgMusic) {
                        console.log('🔍 Music state - Muted:', bgMusic.muted, 'Paused:', bgMusic.paused, 'Volume:', bgMusic.volume);

                        if (bgMusic.muted && this.activeIndex > 0) {
                            bgMusic.muted = false;
                            console.log('🎵 Music unmuted on swipe!');

                            // Ensure it's playing
                            if (bgMusic.paused) {
                                bgMusic.play().then(() => console.log('🎵 Music resumed'));
                            }

                            // 🎵 Smooth volume fade-in
                            bgMusic.volume = 0; // Start from silent
                            const targetVolume = 0.3;
                            const fadeInDuration = 1500; // 1.5 seconds
                            const steps = 60; // 60 steps for smooth transition
                            const stepDuration = fadeInDuration / steps;
                            const volumeIncrement = targetVolume / steps;

                            let currentStep = 0;
                            const fadeInterval = setInterval(() => {
                                currentStep++;
                                bgMusic.volume = Math.min(volumeIncrement * currentStep, targetVolume);

                                if (currentStep >= steps) {
                                    bgMusic.volume = targetVolume;
                                    clearInterval(fadeInterval);
                                    console.log('🎵 Fade-in complete! Volume:', bgMusic.volume);
                                }
                            }, stepDuration);
                        }
                    } else {
                        console.error('🔍 Music element not found on swipe!');
                    }
                },
                progress: function () {
                    // Parallax background effect
                    this.slides.forEach(slide => {
                        const bg = slide.querySelector('.gradient-bg');
                        if (!bg) return;
                        bg.style.transform = `translateY(${slide.progress * 60}px)`;
                    });
                }
            }
        });
    </script>
</body>

</html>