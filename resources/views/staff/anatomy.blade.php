@extends('layouts.app')

@section('title', 'Anatomy Tubuh 3D - Portal Medis iMe')

@section('content')
@php
    $anatomyUrl = !empty($anatomyUrl) ? $anatomyUrl : 'https://apotikmedicalcenter.my.id/anatomy/';
    $anatomyHost = parse_url((string) $anatomyUrl, PHP_URL_HOST) ?: $anatomyUrl;
@endphp
<style>
    footer,
    footer.footer-default-theme {
        display: none !important;
    }

    html, body {
        overflow: hidden !important;
        height: 100%;
    }

    main.pt-16 {
        height: 100vh;
        height: 100dvh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
</style>

<div id="anatomyWrapper" class="flex-1 flex flex-col min-h-0 bg-slate-950">
    {{-- Compact Viewer Toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-2 px-3 sm:px-5 py-2 bg-slate-900/95 border-b border-sky-500/25 text-white shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('staff.dashboard') }}"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-sky-100 text-xs font-semibold border border-white/15 transition">
                <i class="fas fa-arrow-left text-[11px]"></i>
                <span class="hidden sm:inline">Dashboard</span>
            </a>

            <div class="flex items-center gap-2 min-w-0">
                <span class="w-7 h-7 rounded-lg bg-cyan-500/20 border border-cyan-400/35 flex items-center justify-center text-cyan-300 shrink-0">
                    <i class="fas fa-brain text-xs"></i>
                </span>
                <div class="leading-tight min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xs sm:text-sm font-bold text-white truncate">Anatomy Tubuh</h1>
                        <span class="hidden md:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-200 border border-cyan-400/30">
                            External Live View
                        </span>
                    </div>
                    <p class="hidden sm:block text-[10px] text-slate-400 truncate">{{ $anatomyUrl }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button"
                    onclick="reloadAnatomyFrame()"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-100 text-xs font-medium border border-white/15 transition"
                    title="Muat ulang halaman Anatomy Tubuh">
                <i class="fas fa-sync-alt text-[11px]"></i>
                <span class="hidden md:inline">Muat Ulang</span>
            </button>

            <button type="button"
                    id="anatomyFullscreenBtn"
                    onclick="toggleAnatomyFullscreen()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white text-xs font-bold shadow-sm border border-sky-300/40 transition"
                    title="Tampilkan Layar Penuh (Tekan Esc untuk keluar)">
                <i id="anatomyFullscreenIcon" class="fas fa-expand text-[11px]"></i>
                <span id="anatomyFullscreenLabel">Fullscreen</span>
            </button>

            <a href="{{ $anatomyUrl }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-sky-200 hover:text-white text-xs font-medium border border-white/15 transition"
               title="Buka Anatomy Tubuh di Tab Baru">
                <i class="fas fa-external-link-alt text-[11px]"></i>
                <span class="hidden sm:inline">Tab Baru</span>
            </a>
        </div>
    </div>

    {{-- Full-height Iframe Container --}}
    <div id="anatomyContainer" class="relative flex-1 min-h-0 w-full bg-slate-950">
        <div id="anatomyLoader" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-slate-950 text-slate-200 z-10 transition-opacity duration-300">
            <div class="w-10 h-10 rounded-full border-2 border-cyan-400/30 border-t-cyan-400 animate-spin"></div>
            <div class="text-center px-4">
                <p class="text-sm font-semibold text-white">Memuat Anatomy Tubuh 3D...</p>
                <p class="text-xs text-slate-400 mt-0.5">Mengambil konten langsung dari {{ $anatomyHost }}</p>
            </div>
        </div>

        <iframe id="anatomyFrame"
                src="{{ $anatomyUrl }}"
                title="Anatomy Tubuh 3D"
                class="w-full h-full border-0 bg-slate-950 block"
                allowfullscreen
                webkitallowfullscreen
                mozallowfullscreen
                allow="fullscreen; accelerometer; gyroscope"
                loading="eager"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>

<script>
    (function () {
        const frame = document.getElementById('anatomyFrame');
        const container = document.getElementById('anatomyContainer');
        const loader = document.getElementById('anatomyLoader');
        const fsIcon = document.getElementById('anatomyFullscreenIcon');
        const fsLabel = document.getElementById('anatomyFullscreenLabel');

        function hideLoader() {
            if (!loader) return;
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }

        if (frame) {
            frame.addEventListener('load', hideLoader);
            setTimeout(hideLoader, 8000);
        }

        window.reloadAnatomyFrame = function () {
            if (!frame) return;
            if (loader) {
                loader.style.display = 'flex';
                loader.style.opacity = '1';
            }
            frame.src = frame.getAttribute('src');
        };

        window.toggleAnatomyFullscreen = function () {
            const fsEl = document.fullscreenElement || document.webkitFullscreenElement;
            if (fsEl) {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
                return;
            }

            const target = frame || container;
            if (!target) return;

            if (target.requestFullscreen) {
                target.requestFullscreen().catch(() => {
                    if (container && container !== target && container.requestFullscreen) {
                        container.requestFullscreen();
                    }
                });
            } else if (target.webkitRequestFullscreen) {
                target.webkitRequestFullscreen();
            }
        };

        function syncFullscreenButton() {
            const active = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
            if (fsIcon) {
                fsIcon.className = active ? 'fas fa-compress text-[11px]' : 'fas fa-expand text-[11px]';
            }
            if (fsLabel) {
                fsLabel.textContent = active ? 'Keluar Fullscreen' : 'Fullscreen';
            }
        }

        document.addEventListener('fullscreenchange', syncFullscreenButton);
        document.addEventListener('webkitfullscreenchange', syncFullscreenButton);
    })();
</script>
@endsection
