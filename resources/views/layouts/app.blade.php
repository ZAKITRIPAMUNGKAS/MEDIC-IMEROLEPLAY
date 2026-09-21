<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="cache-buster" content="{{ time() }}">
    <title>@yield('title', 'Portal Medis iMe Roleplay')</title>

    <!-- Meta Tags for SEO -->
    <meta name="description"
        content="@yield('meta_description', 'Portal Medis iMe Roleplay - Layanan medis terpadu untuk komunitas roleplay')">
    <meta name="keywords"
        content="@yield('meta_keywords', 'ime roleplay, portal medis ime roleplay, motion ime roleplay, gta roleplay, motionlife roleplay')">
    <meta name="author" content="Motion Medical Center">
    <meta name="robots" content="index, follow">


    <!-- Tailwind CSS (Local - Compiled) -->
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">

    <!-- Google Fonts (Local) -->
    <link href="{{ asset('css/inter-font.css') }}?v={{ time() }}" rel="stylesheet">

    <!-- Font Awesome (Local) -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}?v={{ time() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/motionlife-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/motionlife-logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/motionlife-logo.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/motionlife-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- PWA Manifest & Web App Meta -->
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <meta name="theme-color" content="#0284c7">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="iMe Medis">

    <!-- Inline Image Error Handler - Must load early to prevent 404 errors -->
    @stack('styles')
    @livewireStyles
    <script>
        (function () {
            'use strict';
            var defaultProfileImage = '{{ asset("profile.jpg") }}';
            var handledErrors = new Set();

            // Global error handler for all images (catches errors before they reach console)
            function handleImageError(img) {
                if (!img || img.tagName !== 'IMG') return false;

                var src = img.src || img.getAttribute('src') || '';
                var imgKey = src + '_' + (img.alt || '');

                // Prevent handling the same error multiple times
                if (handledErrors.has(imgKey)) return false;

                // Only handle profile images
                if (src && (
                    src.indexOf('profile-images') !== -1 ||
                    src.indexOf('/storage/') !== -1 ||
                    src.indexOf('/uploads/') !== -1 ||
                    src.indexOf('storage/profile-images') !== -1 ||
                    src.indexOf('uploads/profile-images') !== -1
                )) {
                    // Don't handle if it's already the default image
                    if (src.indexOf('profile.jpg') === -1 && src.indexOf('profile-image') === -1) {
                        handledErrors.add(imgKey);
                        img.onerror = null; // Prevent infinite loop
                        img.src = defaultProfileImage;
                        return true;
                    }
                }
                return false;
            }

            // Intercept image errors using error event (capture phase)
            document.addEventListener('error', function (e) {
                if (e.target && e.target.tagName === 'IMG') {
                    if (handleImageError(e.target)) {
                        e.preventDefault(); // Prevent default error handling
                        e.stopPropagation(); // Stop error propagation
                    }
                }
            }, true);

            // Set up onerror handlers for all existing and future images
            function setupImageErrorHandlers() {
                var imgs = document.querySelectorAll('img');
                for (var i = 0; i < imgs.length; i++) {
                    var img = imgs[i];
                    var src = img.getAttribute('src') || img.src || '';

                    // Only set handler for profile images
                    if (src && (
                        src.indexOf('profile-images') !== -1 ||
                        src.indexOf('/storage/') !== -1 ||
                        src.indexOf('/uploads/') !== -1 ||
                        src.indexOf('storage/profile-images') !== -1 ||
                        src.indexOf('uploads/profile-images') !== -1
                    )) {
                        if (src.indexOf('profile.jpg') === -1) {
                            // Add onerror handler if not already present
                            if (!img.hasAttribute('data-error-handled')) {
                                img.setAttribute('data-error-handled', 'true');
                                img.onerror = function () {
                                    handleImageError(this);
                                };
                            }
                        }
                    }
                }
            }

            // Set up handlers when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupImageErrorHandlers);
            } else {
                setupImageErrorHandlers();
            }

            // Also handle dynamically added images using MutationObserver
            if (typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        mutation.addedNodes.forEach(function (node) {
                            if (node.nodeType === 1) {
                                if (node.tagName === 'IMG') {
                                    var src = node.getAttribute('src') || node.src || '';
                                    if (src && (
                                        src.indexOf('profile-images') !== -1 ||
                                        src.indexOf('/storage/') !== -1 ||
                                        src.indexOf('/uploads/') !== -1
                                    )) {
                                        if (src.indexOf('profile.jpg') === -1 && !node.hasAttribute('data-error-handled')) {
                                            node.setAttribute('data-error-handled', 'true');
                                            node.onerror = function () {
                                                handleImageError(this);
                                            };
                                        }
                                    }
                                } else if (node.querySelectorAll) {
                                    // Handle images inside added nodes
                                    var imgs = node.querySelectorAll('img');
                                    for (var i = 0; i < imgs.length; i++) {
                                        var img = imgs[i];
                                        var src = img.getAttribute('src') || img.src || '';
                                        if (src && (
                                            src.indexOf('profile-images') !== -1 ||
                                            src.indexOf('/storage/') !== -1 ||
                                            src.indexOf('/uploads/') !== -1
                                        )) {
                                            if (src.indexOf('profile.jpg') === -1 && !img.hasAttribute('data-error-handled')) {
                                                img.setAttribute('data-error-handled', 'true');
                                                img.onerror = function () {
                                                    handleImageError(this);
                                                };
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                });

                observer.observe(document.body || document.documentElement, {
                    childList: true,
                    subtree: true
                });
            }
        })();
    </script>

    <style>
        /* Cache busting: {{ time() }} */
        :root {
            /* Theme Default Dark Sky Blue Medis */
            --ml-bg-start: #0c4a6e;   /* sky-900 */
            --ml-bg-mid: #075985;     /* sky-800 */
            --ml-bg-end: #0369a1;     /* sky-700 */

            --ml-primary: #0ea5e9;    /* sky-500 */
            --ml-primary-700: #0284c7;/* sky-600 */
            --ml-secondary: #059669;  /* emerald-600 */
            --ml-accent: #06b6d4;     /* cyan-500 */
            --ml-success: #10b981;    /* emerald-500 */
            --ml-warning: #f59e0b;    /* amber-500 */
            --ml-danger: #ef4444;     /* red-500 */
            --ml-muted: #94a3b8;      /* slate-400 */
            --ml-surface: rgba(12, 74, 110, 0.92);
            --ml-border: rgba(14, 165, 233, 0.35);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, var(--ml-bg-start) 0%, var(--ml-bg-mid) 50%, var(--ml-bg-end) 100%);
            min-height: 100vh;
            color: #ffffff;
        }

        .glass-effect {
            background: var(--ml-surface);
            backdrop-filter: blur(16px);
            border: 1px solid var(--ml-border);
        }

        nav.glass-effect,
        header {
            background: rgba(12, 74, 110, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(14, 165, 233, 0.35);
        }

        footer.footer-default-theme,
        footer {
            background: #0c4a6e;
            border-top: 1px solid rgba(14, 165, 233, 0.4);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.3);
            color: #f1f5f9;
        }

        footer.footer-default-theme p,
        footer.footer-default-theme span,
        footer.footer-default-theme div {
            color: #e2e8f0;
        }

        footer a {
            color: #bae6fd;
        }

        footer a:hover {
            color: #38bdf8;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--ml-primary), var(--ml-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            padding: .75rem 1rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--ml-primary), var(--ml-accent));
            color: #fff;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, var(--ml-primary-700), #0284c7);
        }

        .btn-outline {
            background: transparent;
            color: var(--ml-primary);
            border: 1px solid var(--ml-primary);
        }

        .btn-outline:hover {
            background: rgba(37, 99, 235, .06);
        }

        /* Cards */
        .card {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.06);
        }

        .card-muted {
            background: rgba(255, 255, 255, .85);
        }

        /* Pills & badges */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem .9rem;
            border-radius: 9999px;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px solid var(--ml-border);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .3rem .6rem;
            border-radius: .375rem;
            font-size: .75rem;
            border: 1px solid var(--ml-border);
            background: #fff;
            color: #0f172a;
        }

        /* Stats */
        .stat-card {
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            border: 1px solid var(--ml-border);
            border-radius: 1rem;
            padding: 1.25rem;
            text-align: center;
        }

        .stat-card h4 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--ml-primary);
        }

        .stat-card p {
            color: var(--ml-muted);
            font-weight: 600;
            font-size: .9rem;
        }


        .subtle-float {
            animation: subtle-float 8s ease-in-out infinite;
        }

        @keyframes subtle-float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .medical-icon {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .elegant-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .elegant-shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.5rem;
            }

            .service-card {
                margin-bottom: 1.5rem;
            }

            .subtle-float {
                display: none;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        @media (max-width: 640px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1.25rem;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }

            .nav-buttons a {
                text-align: center;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }

        /* Extra Small Devices (phones, 480px and down) */
        @media (max-width: 480px) {
            .xs\:inline {
                display: inline;
            }

            .hero-title {
                font-size: 1.75rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }
        }

        /* Small Devices (phones, 640px and down) */
        @media (max-width: 640px) {
            .nav-buttons {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(12, 74, 110, 0.98);
                backdrop-filter: blur(15px);
                padding: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 40;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            .nav-buttons.show {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .nav-buttons .flex {
                flex-direction: column;
                gap: 0.5rem;
            }

            .nav-buttons a,
            .nav-buttons button {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }

            .mobile-menu-btn {
                display: block;
            }

            .xs\:inline {
                display: none;
            }
        }

        /* Medium Devices (tablets, 768px and down) */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.5rem;
            }

            .service-card {
                margin-bottom: 1.5rem;
            }

            .subtle-float {
                display: none;
            }
        }

        .mobile-menu-btn {
            display: none;
        }

        /* Dropdown Menu Styles */
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }

        .group:hover .group-hover\:visible {
            visibility: visible;
        }

        /* Ensure dropdown is above other elements */
        .relative.group {
            z-index: 50;
        }


        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200px 0;
            }

            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        @keyframes shimmer-icon {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        @keyframes horror-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(220, 38, 38, 0.3), 0 0 40px rgba(220, 38, 38, 0.1);
                border-color: rgba(220, 38, 38, 0.5);
            }

            50% {
                box-shadow: 0 0 30px rgba(220, 38, 38, 0.6), 0 0 60px rgba(220, 38, 38, 0.3);
                border-color: rgba(220, 38, 38, 0.8);
            }
        }

        @keyframes horror-pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }
        }

        @keyframes horror-float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            25% {
                transform: translateY(-10px) rotate(5deg);
            }

            50% {
                transform: translateY(-5px) rotate(-3deg);
            }

            75% {
                transform: translateY(-15px) rotate(2deg);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-fade-in-left {
            animation: fadeInLeft 0.6s ease-out;
        }

        .animate-fade-in-right {
            animation: fadeInRight 0.6s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse 2s ease-in-out infinite;
        }

        .animate-horror-glow {
            animation: horror-glow 3s ease-in-out infinite;
        }

        .animate-horror-pulse {
            animation: horror-pulse 2s ease-in-out infinite;
        }

        .animate-horror-float {
            animation: horror-float 4s ease-in-out infinite;
        }

        .card-hover:hover {
            transform: translateY(-5px);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .pill:hover {
            transform: translateY(-2px) scale(1.05);
        }

        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            background-size: 200px 100%;
            animation: shimmer 2s infinite;
        }

        .hero-bg {
            background: linear-gradient(135deg, var(--ml-bg-start) 0%, var(--ml-bg-mid) 50%, var(--ml-bg-end) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset("images/hero.webp") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        /* Global Dropdown/Select Styles for Better Visibility */
        select {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        select option {
            background-color: #1e293b !important;
            /* slate-800 */
            color: #f1f5f9 !important;
            /* slate-100 */
            padding: 8px 12px !important;
        }

        select option:hover {
            background-color: #334155 !important;
            /* slate-700 */
            color: #ffffff !important;
        }

        select option:checked {
            background-color: #0ea5e9 !important;
            /* sky-500 */
            color: #ffffff !important;
        }

        select option:disabled {
            background-color: #475569 !important;
            /* slate-600 */
            color: #94a3b8 !important;
            /* slate-400 */
        }

        /* Focus states for better accessibility */
        select:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.4) !important;
            border-color: #0ea5e9 !important;
        }

        /* Custom dropdown arrow */
        select {
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%23f1f5f9%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M10%2012l-6-6h12l-6%206z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E') !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 1.25em 1.25em !important;
            padding-right: 2.5rem !important;
        }

        /* Appointment Form Styles */
        .appointment-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 2rem;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #06b6d4, #3b82f6);
            border-radius: 2rem 2rem 0 0;
        }

        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 35px 70px -12px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .appointment-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .appointment-icon {
            width: 5rem;
            height: 5rem;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3);
            position: relative;
            overflow: hidden;
        }

        .appointment-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer-icon 3s infinite;
        }

        .appointment-icon i {
            color: white;
            font-size: 2rem;
            z-index: 1;
            position: relative;
        }

        .appointment-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .appointment-subtitle {
            color: #475569;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .appointment-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: #0ea5e9;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 3px solid #374151 !important;
            border-radius: 1rem;
            font-size: 16px !important;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            position: relative;
            z-index: 1;
            font-weight: 700 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .form-input::placeholder {
            color: #6b7280 !important;
            font-weight: 600 !important;
            font-size: 16px;
        }

        .form-input:focus {
            outline: none !important;
            border-color: #1d4ed8 !important;
            box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.4) !important;
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
        }

        .form-input:focus+.input-focus-border {
            opacity: 1;
            transform: scaleX(1);
        }

        .input-focus-border {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #0ea5e9, #06b6d4);
            border-radius: 0 0 1rem 1rem;
            opacity: 0;
            transform: scaleX(0);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .select-wrapper {
            position: relative;
        }

        .form-select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 3px solid #374151 !important;
            border-radius: 1rem;
            font-size: 16px !important;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            appearance: none;
            cursor: pointer;
            position: relative;
            z-index: 1;
            font-weight: 700 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .form-select:not([value=""]) {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        .form-select[value=""] {
            color: #e2e8f0 !important;
            font-weight: 500;
        }

        .form-select option {
            background-color: #0f172a !important;
            color: #ffffff !important;
            padding: 12px 16px !important;
            font-weight: 600 !important;
        }

        /* Default select text color inherits; jangan paksa putih agar bisa di-override per halaman */
        .form-select,
        .form-select * {
            color: inherit !important;
        }

        .form-select option[selected] {
            background-color: #0ea5e9 !important;
            color: #ffffff !important;
        }

        .form-select option:hover {
            background-color: #f1f5f9 !important;
            color: #0ea5e9 !important;
        }

        .form-select option:checked {
            background-color: #0ea5e9 !important;
            color: #ffffff !important;
        }

        .form-select option:disabled {
            background-color: #f8fafc !important;
            color: #94a3b8 !important;
        }

        .form-select:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff !important;
        }

        .form-select option:checked {
            background-color: #0ea5e9 !important;
            color: #ffffff !important;
        }

        .form-select option:not(:checked) {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }

        .select-arrow {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            pointer-events: none;
            z-index: 2;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .form-select:focus+.select-arrow {
            color: #0ea5e9;
            transform: translateY(-50%) rotate(180deg);
        }

        .submit-btn {
            position: relative;
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            border: none;
            border-radius: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn.loading .btn-text,
        .submit-btn.success .btn-text {
            opacity: 0;
            transform: translateY(-20px);
        }

        .submit-btn .btn-loading,
        .submit-btn .btn-success {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .submit-btn.loading .btn-loading {
            opacity: 1;
        }

        .submit-btn.success .btn-success {
            opacity: 1;
        }

        .submit-btn.success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Input validation states */
        .form-input.valid {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-input.invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-select.valid {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-select.invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Focused state for form groups */
        .form-group.focused .form-label {
            color: #0ea5e9;
            transform: translateY(-2px);
        }

        .form-group.focused .form-label i {
            color: #0ea5e9;
            transform: scale(1.1);
        }

        /* Enhanced hover effects */
        .form-input:hover,
        .form-select:hover {
            border-color: #94a3b8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }

        /* Loading state for submit button */
        .submit-btn:disabled {
            cursor: not-allowed;
            opacity: 0.8;
        }

        /* Success animation */
        @keyframes success-pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .submit-btn.success {
            animation: success-pulse 0.6s ease-in-out;
        }

        /* Enhanced card animations */
        .appointment-card {
            animation: card-enter 0.8s ease-out;
        }

        @keyframes card-enter {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Form group animations */
        .form-group {
            animation: form-group-enter 0.6s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-group:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.4s;
        }

        @keyframes form-group-enter {
            0% {
                opacity: 0;
                transform: translateX(-20px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .appointment-card {
                padding: 1.5rem;
                margin: 0 1rem;
            }

            .appointment-title {
                font-size: 1.75rem;
            }

            .appointment-icon {
                width: 4rem;
                height: 4rem;
            }

            .appointment-icon i {
                font-size: 1.5rem;
            }

            .form-input,
            .form-select {
                padding: 0.875rem 1rem;
                font-size: 0.95rem;
            }

            .submit-btn {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Notification Container Position - Adjusted to be below navbar */
        .notification-container {
            position: fixed;
            top: 80px;
            /* Adjusted to be below the navbar */
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            pointer-events: none;
            /* Allow clicking through container */
        }

        .notification-container>* {
            pointer-events: auto;
            /* Re-enable pointer events for notifications */
        }

        /* Mobile adjustment */
        @media (max-width: 640px) {
            .notification-container {
                left: 20px;
                right: 20px;
                width: auto;
                max-width: none;
        /* Solid Navigation Background - 100% Non-Transparent (Default Theme Sky-900) */
        nav,
        #mobile-menu {
            background-color: #0c4a6e !important;
            background: linear-gradient(135deg, #0c4a6e 0%, #075985 100%) !important;
            opacity: 1 !important;
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-[9999] shadow-lg" style="background-color: #0c4a6e !important; background: linear-gradient(135deg, #0c4a6e 0%, #075985 100%) !important; border-bottom: 1px solid rgba(14, 165, 233, 0.35);">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Brand Logo --}}
                <a href="{{ route('public.index') }}" class="flex-shrink-0 flex items-center gap-2.5 group">
                    <div class="flex items-center gap-1.5 bg-white/95 px-2 py-1 rounded-xl shadow-md group-hover:shadow-lg group-hover:bg-white transition-all duration-300">
                        <img src="{{ asset('images/motionlife-logo.png') }}" alt="EMS Alta" class="h-6 w-6 object-contain" title="Alta Hospital (EMS)">
                        <div class="h-4 w-px bg-slate-300"></div>
                        <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood Hospital" class="h-6 w-6 object-contain" title="Roxwood Hospital">
                    </div>
                    <div class="leading-none">
                        <div class="text-sm font-bold text-white tracking-widest uppercase group-hover:text-sky-200 transition-colors duration-300">iMe</div>
                        <div class="text-[10px] text-sky-200/80 tracking-wide">Portal Medis</div>
                    </div>
                </a>

                {{-- Mobile Toggle --}}
                <div class="sm:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-white p-2 rounded-lg hover:bg-white/10 focus:outline-none transition-all duration-300">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden sm:flex items-center gap-1.5" id="desktop-menu">
                    <!-- PWA Install Button -->
                    <button type="button" onclick="triggerPwaInstall()" class="pwa-install-trigger inline-flex items-center gap-1.5 h-9 px-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white text-xs font-bold rounded-lg border border-sky-400/40 transition-all duration-200 whitespace-nowrap shadow-md">
                        <i class="fas fa-download text-xs text-amber-300"></i>
                        <span>Instal Aplikasi</span>
                    </button>

                    @guest
                        <button onclick="openRecruitmentModal()" class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                            <i class="fas fa-user-plus text-sm"></i><span>Recruitment</span>
                        </button>
                        <a href="{{ route('staff.login') }}" class="inline-flex items-center gap-1.5 h-9 px-3 bg-sky-500/30 hover:bg-sky-500/50 text-white text-xs font-semibold rounded-lg border border-sky-400/40 transition-all duration-200 whitespace-nowrap">
                            <i class="fas fa-user-md text-sm"></i><span>Login Staf</span>
                        </a>
                    @endguest
                    
                    @auth
                        {{-- User Badge / Direct Link to Member Profile --}}
                        <a href="{{ route('staff.members.show', auth()->id()) }}" 
                           class="hidden xl:inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/15 hover:border-white/30 px-3 h-9 rounded-lg mr-1 transition-all duration-200 group" 
                           title="Lihat Profil Anggota: {{ auth()->user()->name }}">
                            <i class="fas fa-user-circle text-sky-300 group-hover:text-sky-200 text-sm"></i>
                            <span class="text-white/90 group-hover:text-white text-xs font-medium max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        </a>

                        {{-- Dashboard --}}
                        <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                            <i class="fas fa-tachometer-alt text-sm"></i><span>Dashboard</span>
                        </a>

                        {{-- Profil --}}
                        <a href="{{ route('staff.profile') }}" class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                            <i class="fas fa-user-cog text-sm"></i><span>Profil</span>
                        </a>

                        {{-- Tanya AI Button in Navbar --}}
                        @if(\App\Models\AiSetting::getSettings()->enabled)
                        <button type="button" onclick="openFullAiModal()" class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-white text-xs font-bold rounded-lg shadow-md shadow-sky-950/30 border border-sky-300/40 transition-all duration-200 whitespace-nowrap active:scale-95 group" title="Buka Gemini AI Assistant">
                            <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                            <svg class="w-3.5 h-3.5 text-amber-300 group-hover:rotate-12 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
                            </svg>
                            <span>Tanya AI</span>
                        </button>
                        @endif

                        {{-- Menu Staf Dropdown --}}
                        @php
                            try { $unreadMessagesCount = \App\Models\MemberMessage::where('receiver_id', auth()->id())->where('is_read', false)->count(); }
                            catch (\Throwable $e) { $unreadMessagesCount = 0; }
                        @endphp
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                                <i class="fas fa-th-large text-sm text-sky-300"></i>
                                <span>Menu Staf</span>
                                @if($unreadMessagesCount > 0)
                                    <span class="inline-flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full animate-pulse">{{ $unreadMessagesCount }}</span>
                                @else
                                    <i class="fas fa-chevron-down text-[9px] opacity-60"></i>
                                @endif
                            </button>
                            <div class="absolute left-0 top-full mt-2 w-52 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 transition-all duration-200 z-[9999] overflow-hidden">
                                <div class="px-3 pt-2.5 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">Fitur Staf</div>
                                <div class="py-1">
                                    <a href="{{ route('staff.members.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                        <i class="fas fa-users w-4 text-sky-500 text-sm"></i> Anggota Staf
                                    </a>
                                    <a href="{{ route('staff.messages.index') }}" class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                        <span class="flex items-center gap-2.5"><i class="fas fa-envelope w-4 text-cyan-500 text-sm"></i> Pesan Internal</span>
                                        @if($unreadMessagesCount > 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white text-[9px] font-bold rounded-full animate-pulse">{{ $unreadMessagesCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('staff.voting.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                        <i class="fas fa-vote-yea w-4 text-indigo-500 text-sm"></i> Voting & Poll
                                    </a>
                                    <a href="{{ route('staff.manager-evaluations.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                        <i class="fas fa-star w-4 text-amber-500 text-sm"></i> Penilaian Manajer
                                    </a>
                                    <button type="button" onclick="triggerPwaInstall()" class="pwa-install-trigger w-full text-left flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                        <i class="fas fa-download w-4 text-sky-500 text-sm"></i> Instal Aplikasi
                                    </button>
                                    @if(auth()->user()->isAdmin() || strtolower(trim(auth()->user()->role?->name ?? '')) !== 'trainee')
                                        <a href="{{ route('staff.operations.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                            <i class="fas fa-file-medical w-4 text-emerald-500 text-sm"></i> Rekam Medis
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Admin Dropdown --}}
                        @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_doctor_schedules') || auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_attendance_reports') || auth()->user()->hasPermission('access_live_chat') || auth()->user()->hasPermission('access_feedback') || auth()->user()->isExecutiveOrAbove() || auth()->user()->isManagerOrAbove())
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                                <i class="fas fa-user-shield text-sm text-amber-300"></i>
                                <span>Admin</span>
                                <i class="fas fa-chevron-down text-[9px] opacity-60"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 transition-all duration-200 z-[9999] overflow-hidden">
                                <div class="px-3 pt-2.5 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">Panel Admin</div>
                                <div class="py-1">
                                    @if(auth()->user()->hasPermission('manage_users'))
                                        <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-users-cog w-4 text-slate-500 text-sm"></i> Manajemen Staf</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_doctor_schedules'))
                                        <a href="{{ route('admin.doctor-schedules.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-calendar-alt w-4 text-blue-500 text-sm"></i> Jadwal Dokter</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('manage_users'))
                                        <a href="{{ route('admin.voting.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-poll-h w-4 text-indigo-500 text-sm"></i> Kelola Voting</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('access_live_chat'))
                                        <a href="{{ route('admin.chat.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-comments w-4 text-green-500 text-sm"></i> Live Chat</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('access_feedback'))
                                        <a href="{{ route('admin.feedback.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-comment-dots w-4 text-orange-500 text-sm"></i> Laporan & Masukan</a>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.organizational-structure.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-sitemap w-4 text-teal-500 text-sm"></i> Struktural EMS</a>
                                        <a href="{{ route('admin.roles.permissions') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-shield-alt w-4 text-violet-500 text-sm"></i> Role Permissions</a>
                                        <a href="{{ route('admin.ai-settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-robot w-4 text-sky-500 text-sm"></i> Pengaturan AI</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_attendance_reports'))
                                        <div class="my-1 border-t border-gray-100"></div>
                                        <a href="{{ route('admin.attendance-reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-chart-bar w-4 text-sky-500 text-sm"></i> Laporan Absensi</a>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.meeting-requests.index') }}" class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <span class="flex items-center gap-2.5"><i class="fas fa-clipboard-check w-4 text-yellow-500 text-sm"></i> Meeting Requests</span>
                                            @php $pendingMeetingCount = \App\Models\MeetingRequest::pending()->count(); @endphp
                                            @if($pendingMeetingCount > 0)<span class="inline-flex items-center justify-center min-w-[20px] h-5 bg-yellow-400 text-gray-800 text-[9px] font-bold px-1.5 rounded-full">{{ $pendingMeetingCount }}</span>@endif
                                        </a>
                                        <a href="{{ route('admin.duty-tracking.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-trophy w-4 text-amber-500 text-sm"></i> Duty Tracking</a>
                                    @endif
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManagerOrAbove())
                                        <a href="{{ route('admin.inactive-staff.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-user-slash w-4 text-red-500 text-sm"></i> Staf Tidak Aktif</a>
                                    @endif
                                    @php
                                        $userHospitalNav = strtolower(trim(auth()->user()->hospital ?? 'alta'));
                                        $isAltaStaffNav  = ($userHospitalNav === 'alta') || (auth()->user()->isAdmin() && $userHospitalNav !== 'roxwood');
                                    @endphp
                                    @if(auth()->user()->isExecutiveOrAbove() && $isAltaStaffNav)
                                        <div class="my-1 border-t border-gray-100"></div>
                                        <a href="{{ route('admin.sub-roles.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-layer-group w-4 text-indigo-500 text-sm"></i> Sub-Jabatan / Divisi (Alta)</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ═══ PORTAL MANAJEMEN ALTA HOSPITAL (RBAC-Aware, Khusus Alta) ═══ --}}
                        @php
                            $userHospital = strtolower(trim(auth()->user()->hospital ?? 'alta'));
                            $isAltaMember = ($userHospital === 'alta') || (auth()->user()->isAdmin() && $userHospital !== 'roxwood');
                            $canSeePortal = auth()->user()->isStaff() && $isAltaMember;
                            $isGa     = $isAltaMember && (auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('ga'));
                            $isMsl    = $isAltaMember && (auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('msl'));
                            $isPnd    = $isAltaMember && (auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('pnd'));
                            $isIe     = $isAltaMember && (auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('ie'));
                            $isComdis = $isAltaMember && (auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('comdis'));
                        @endphp
                        @if($canSeePortal)
                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                                <i class="fas fa-hospital-alt text-sm text-rose-300"></i>
                                <span>Portal Alta</span>
                                <i class="fas fa-chevron-down text-[9px] opacity-60"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-60 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 transition-all duration-200 z-[9999] overflow-hidden">
                                {{-- Semua Anggota --}}
                                <div class="px-3 pt-2.5 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">Pengajuan Saya</div>
                                <div class="py-1">
                                    <a href="{{ route('portal.leave.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                                        <i class="fas fa-calendar-check w-4 text-rose-400 text-sm"></i> Pengajuan Cuti
                                    </a>
                                    <a href="{{ route('portal.leave.public-list') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                                        <i class="fas fa-users-slash w-4 text-rose-400 text-sm"></i> Jadwal Cuti Medis
                                    </a>
                                    <a href="{{ route('portal.resignation.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition-colors">
                                        <i class="fas fa-file-signature w-4 text-orange-400 text-sm"></i> Pengajuan Resign
                                    </a>
                                    <a href="{{ route('portal.stase.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                        <i class="fas fa-graduation-cap w-4 text-blue-400 text-sm"></i> Pengajuan Stase
                                    </a>
                                    <a href="{{ route('portal.vehicle-cert.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                        <i class="fas fa-car w-4 text-amber-500 text-sm"></i> Pengajuan Sertifikat Kendaraan
                                    </a>
                                    <a href="{{ route('portal.operation-cert.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <i class="fas fa-award w-4 text-emerald-500 text-sm"></i> Pengajuan Sertifikat Operasi
                                    </a>
                                    <a href="{{ route('portal.promotion.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                                        <i class="fas fa-level-up-alt w-4 text-violet-400 text-sm"></i> Kenaikan Jabatan
                                    </a>
                                    <a href="{{ route('credit-score.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition-colors">
                                        <i class="fas fa-star-half-alt w-4 text-teal-400 text-sm"></i> Credit Score Saya
                                    </a>
                                    <a href="{{ route('portal.interview.apply-form') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        <i class="fas fa-user-plus w-4 text-indigo-500 text-sm"></i> Pengajuan Role Interviewer
                                    </a>
                                </div>

                                {{-- Divisi-specific --}}
                                @if($isGa || $isMsl || $isPnd || $isIe || $isComdis)
                                <div class="px-3 pt-2.5 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b border-t border-gray-100 mt-1">Kelola Divisi</div>
                                <div class="py-1">
                                    @if($isGa)
                                    <a href="{{ route('portal.ga.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                        <i class="fas fa-car w-4 text-amber-500 text-sm"></i> GA: Sertifikat Kendaraan
                                    </a>
                                    @endif
                                    @if($isMsl)
                                    <a href="{{ route('portal.msl.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition-colors">
                                        <i class="fas fa-stethoscope w-4 text-teal-500 text-sm"></i> MSL: Sertifikat Visum
                                    </a>
                                    <a href="{{ route('portal.msl.stase.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 transition-colors">
                                        <i class="fas fa-book-medical w-4 text-teal-400 text-sm"></i> MSL: Kelola Stase
                                    </a>
                                    @endif
                                    @if($isPnd)
                                    <a href="{{ route('portal.recruitment.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <i class="fas fa-bullhorn w-4 text-emerald-500 text-sm"></i> PND: Kelola Recruitment
                                    </a>
                                    <a href="{{ route('portal.pnd.cert-index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                        <i class="fas fa-award w-4 text-green-400 text-sm"></i> PND: Sertifikat Operasi
                                    </a>
                                    <a href="{{ route('portal.promotion.period.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                                        <i class="fas fa-toggle-on w-4 text-violet-500 text-sm"></i> PND: Periode Kenaikan
                                    </a>
                                    <a href="{{ route('portal.promotion.applications') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-violet-50 hover:text-violet-700 transition-colors">
                                        <i class="fas fa-clipboard-list w-4 text-violet-400 text-sm"></i> PND: Review Kenaikan
                                    </a>
                                    @endif
                                    @if($isIe)
                                    <a href="{{ route('portal.recruitment.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <i class="fas fa-bullhorn w-4 text-emerald-500 text-sm"></i> IE: Kelola Recruitment
                                    </a>
                                    <a href="{{ route('portal.ie.roles.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-sky-50 hover:text-sky-700 transition-colors">
                                        <i class="fas fa-user-tag w-4 text-sky-500 text-sm"></i> IE: Manajemen Jabatan
                                    </a>
                                    <a href="{{ route('portal.ie.pemutihan.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-sky-50 hover:text-sky-700 transition-colors">
                                        <i class="fas fa-calendar-times w-4 text-sky-500 text-sm"></i> IE: Pemutihan Duty
                                    </a>
                                    <a href="{{ route('portal.ie.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-sky-50 hover:text-sky-700 transition-colors">
                                        <i class="fas fa-file-contract w-4 text-sky-500 text-sm"></i> IE: Kontrak Medis
                                    </a>
                                    <a href="{{ route('portal.resignation.manage.ie') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition-colors">
                                        <i class="fas fa-hand-holding-usd w-4 text-orange-400 text-sm"></i> IE: Denda Resign
                                    </a>
                                    @endif
                                    @if($isComdis)
                                    <a href="{{ route('credit-score.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <i class="fas fa-balance-scale w-4 text-emerald-500 text-sm"></i> Comdis: Credit Score
                                    </a>
                                    @endif
                                </div>
                                @endif

                                {{-- PND: Verifikasi Resign --}}
                                @if($isPnd)
                                <div class="py-1 border-t border-gray-100">
                                    <a href="{{ route('portal.resignation.manage.pnd') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition-colors">
                                        <i class="fas fa-check-circle w-4 text-orange-400 text-sm"></i> PND: Verifikasi Resign
                                    </a>
                                    <a href="{{ route('portal.leave.manage') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                                        <i class="fas fa-calendar-alt w-4 text-rose-400 text-sm"></i> PND: Kelola Cuti
                                    </a>
                                </div>
                                @endif

                                {{-- Konsulen --}}
                                @if(auth()->user()->role?->level >= 4)
                                <div class="py-1 border-t border-gray-100">
                                    <a href="{{ route('portal.stase.konsulen.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                        <i class="fas fa-user-check w-4 text-blue-400 text-sm"></i> Konsulen: Setujui Stase
                                    </a>
                                </div>
                                @endif

                                {{-- Interviewer --}}
                                @if(auth()->user()->isInterviewer())
                                <div class="py-1 border-t border-gray-100">
                                    <a href="{{ route('portal.interview.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        <i class="fas fa-user-tie w-4 text-indigo-500 text-sm"></i> Interviewer: Calon Medis
                                    </a>
                                </div>
                                @else
                                <div class="py-1 border-t border-gray-100">
                                    <a href="{{ route('portal.interview.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        <i class="fas fa-user-plus w-4 text-indigo-400 text-xs"></i> Ajukan Jadi Interviewer
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Gaji Dropdown --}}

                        <div class="relative group">
                            <button class="inline-flex items-center gap-1.5 h-9 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg border border-white/20 transition-all duration-200 whitespace-nowrap">
                                <i class="fas fa-wallet text-sm text-emerald-300"></i>
                                <span>Gaji</span>
                                <i class="fas fa-chevron-down text-[9px] opacity-60"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-1 group-hover:translate-y-0 transition-all duration-200 z-[9999] overflow-hidden">
                                <div class="px-3 pt-2.5 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">Penggajian</div>
                                <div class="py-1">
                                    @if(auth()->user()->hasPermission('manage_payroll'))
                                        <a href="{{ route('admin.payroll.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-list w-4 text-slate-500 text-sm"></i> Daftar Gaji</a>
                                        <a href="{{ route('admin.salary-settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-sliders-h w-4 text-blue-500 text-sm"></i> Atur Gaji</a>
                                        @if(auth()->user()->isAdmin())
                                            <div class="my-1 border-t border-gray-100"></div>
                                            <a href="{{ route('admin.reimbursements.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-file-invoice-dollar w-4 text-green-500 text-sm"></i> Reimbursement</a>
                                        @endif
                                    @else
                                        <a href="{{ route('staff.payroll.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"><i class="fas fa-money-bill-wave w-4 text-green-500 text-sm"></i> Gaji Saya</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Logout --}}
                        <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-3 bg-red-500/15 hover:bg-red-500/30 text-red-300 hover:text-white text-xs font-medium rounded-lg border border-red-500/25 transition-all duration-200 whitespace-nowrap">
                                <i class="fas fa-sign-out-alt text-sm"></i><span>Keluar</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer (Fixed Full-Height Under Navbar, 100% Solid & Non-Transparent) -->
        <div id="mobile-menu" class="sm:hidden hidden fixed top-16 left-0 right-0 bottom-0 w-full px-4 pt-4 pb-28 overflow-y-auto overscroll-contain shadow-2xl z-[99999] border-t border-sky-500/40" style="background-color: #0b1329 !important; background: #0b1329 !important; -webkit-overflow-scrolling: touch; touch-action: pan-y; height: calc(100dvh - 4rem); max-height: calc(100dvh - 4rem); opacity: 1 !important;">
            <div class="space-y-3.5 pb-20">
                
                <!-- Quick Action PWA & Recruitment / Login -->
                <div class="grid grid-cols-2 gap-2 pb-2 border-b border-white/10">
                    <button type="button" onclick="triggerPwaInstall()"
                        class="pwa-install-trigger flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500 text-white text-xs font-bold shadow-md shadow-sky-500/20">
                        <i class="fas fa-download text-amber-300 text-xs"></i>
                        <span>Instal App</span>
                    </button>
                    @guest
                        <a href="{{ route('staff.login') }}"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-sky-500/30 text-white text-xs font-bold border border-sky-400/40 hover:bg-sky-500/50 transition-all">
                            <i class="fas fa-user-md text-xs"></i>
                            <span>Login Staf</span>
                        </a>
                    @else
                        <a href="{{ route('staff.dashboard') }}"
                            class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-emerald-500/30 text-white text-xs font-bold border border-emerald-400/40 hover:bg-emerald-500/50 transition-all">
                            <i class="fas fa-tachometer-alt text-xs text-emerald-300"></i>
                            <span>Dashboard</span>
                        </a>
                    @endguest
                </div>

                @auth
                    <!-- User Profile Card in Mobile Menu -->
                    <div class="bg-white/5 rounded-2xl p-3 border border-white/10">
                        <a href="{{ route('staff.members.show', auth()->id()) }}" 
                           class="flex items-center justify-between text-white hover:text-sky-200 transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-sm">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                                    <div class="text-[10px] text-sky-300 font-medium truncate">{{ auth()->user()->role?->display_name ?? 'Staf Medis' }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] text-sky-300 bg-sky-500/20 px-2 py-0.5 rounded-full border border-sky-400/30 font-semibold shrink-0">Profil &rarr;</span>
                        </a>
                    </div>

                    <!-- Section: Navigasi Staf -->
                    <div class="space-y-1">
                        <div class="px-2 pt-1 text-[10px] font-black tracking-widest uppercase text-sky-300/70">Akses Staf Medis</div>
                        
                        <a href="{{ route('staff.dashboard') }}"
                            class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-tachometer-alt w-5 text-sky-400 text-sm"></i>
                            <span>Dashboard Utama</span>
                        </a>
                        
                        <a href="{{ route('staff.profile') }}"
                            class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-user-cog w-5 text-slate-400 text-sm"></i>
                            <span>Pengaturan Akun & Profil</span>
                        </a>

                        @if(auth()->user()->isAdmin() || strtolower(trim(auth()->user()->role?->name ?? '')) !== 'trainee')
                            <a href="{{ route('staff.operations.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-file-medical w-5 text-emerald-400 text-sm"></i>
                                <span>Rekam Medis Pasien</span>
                            </a>
                        @endif

                        <a href="{{ route('staff.members.index') }}"
                            class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-users w-5 text-cyan-400 text-sm"></i>
                            <span>Daftar Anggota Staf</span>
                        </a>

                        @php
                            try { $unreadMessagesCount = \App\Models\MemberMessage::where('receiver_id', auth()->id())->where('is_read', false)->count(); }
                            catch (\Throwable $e) { $unreadMessagesCount = 0; }
                        @endphp
                        <a href="{{ route('staff.messages.index') }}"
                            class="flex items-center justify-between text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-envelope w-5 text-indigo-400 text-sm"></i>
                                <span>Pesan Internal</span>
                            </div>
                            @if($unreadMessagesCount > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $unreadMessagesCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('staff.voting.index') }}"
                            class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-vote-yea w-5 text-purple-400 text-sm"></i>
                            <span>Voting & Poll</span>
                        </a>

                        <a href="{{ route('staff.manager-evaluations.index') }}"
                            class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                            <i class="fas fa-star w-5 text-amber-400 text-sm"></i>
                            <span>Penilaian Manajer</span>
                        </a>

                        @if(auth()->user()->hasPermission('manage_payroll'))
                            <a href="{{ route('admin.payroll.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-list w-5 text-emerald-400 text-sm"></i>
                                <span>Kelola Penggajian</span>
                            </a>
                        @else
                            <a href="{{ route('staff.payroll.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-money-bill-wave w-5 text-emerald-400 text-sm"></i>
                                <span>Gaji Saya</span>
                            </a>
                        @endif
                    </div>

                    <!-- Section: Admin Panel (if permitted) -->
                    @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_doctor_schedules') || auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_attendance_reports') || auth()->user()->hasPermission('access_live_chat') || auth()->user()->hasPermission('access_feedback') || auth()->user()->isExecutiveOrAbove() || auth()->user()->isManagerOrAbove())
                        <div class="space-y-1 pt-2 border-t border-white/10">
                            <div class="px-2 pt-1 text-[10px] font-black tracking-widest uppercase text-amber-300/80">Menu Administrator</div>
                            
                            @if(auth()->user()->hasPermission('manage_users'))
                                <a href="{{ route('admin.staff.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-users-cog w-5 text-amber-400 text-sm"></i>
                                    <span>Manajemen Staf</span>
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_doctor_schedules'))
                                <a href="{{ route('admin.doctor-schedules.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-calendar-alt w-5 text-blue-400 text-sm"></i>
                                    <span>Kelola Jadwal Dokter</span>
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('access_live_chat'))
                                <a href="{{ route('admin.chat.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-comments w-5 text-green-400 text-sm"></i>
                                    <span>Live Chat Pasien</span>
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('access_feedback'))
                                <a href="{{ route('admin.feedback.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-comment-dots w-5 text-orange-400 text-sm"></i>
                                    <span>Laporan & Feedback</span>
                                </a>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.organizational-structure.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-sitemap w-5 text-teal-400 text-sm"></i>
                                    <span>Struktural EMS</span>
                                </a>
                                <a href="{{ route('admin.roles.permissions') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-shield-alt w-5 text-violet-400 text-sm"></i>
                                    <span>Role & Permissions</span>
                                </a>
                            @endif

                            @if(auth()->user()->hasPermission('view_reports') || auth()->user()->hasPermission('view_attendance_reports'))
                                <a href="{{ route('admin.attendance-reports.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-chart-bar w-5 text-sky-400 text-sm"></i>
                                    <span>Laporan Absensi</span>
                                </a>
                            @endif

                            @if(auth()->user()->isAdmin() || auth()->user()->isManagerOrAbove())
                                <a href="{{ route('admin.inactive-staff.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-user-slash w-5 text-red-400 text-sm"></i>
                                    <span>Staf Tidak Aktif</span>
                                </a>
                            @endif
                            @php
                                $userHospitalMobile = strtolower(trim(auth()->user()->hospital ?? 'alta'));
                                $isAltaMobile = ($userHospitalMobile === 'alta') || (auth()->user()->isAdmin() && $userHospitalMobile !== 'roxwood');
                            @endphp
                            @if(auth()->user()->isExecutiveOrAbove() && $isAltaMobile)
                                <a href="{{ route('admin.sub-roles.index') }}"
                                    class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                    <i class="fas fa-layer-group w-5 text-indigo-400 text-sm"></i>
                                    <span>Sub-Jabatan / Divisi (Alta)</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Portal Alta Hospital (Khusus Alta) --}}
                    @php
                        $userHospitalMob = strtolower(trim(auth()->user()->hospital ?? 'alta'));
                        $isAltaUserMob   = ($userHospitalMob === 'alta') || (auth()->user()->isAdmin() && $userHospitalMob !== 'roxwood');
                    @endphp
                    @if($isAltaUserMob)
                        <div class="space-y-1 pt-2 border-t border-white/10">
                            <div class="px-2 pt-1 text-[10px] font-black tracking-widest uppercase text-rose-300/80">Portal Alta Hospital</div>
                            <a href="{{ route('portal.leave.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-calendar-check w-5 text-rose-400 text-sm"></i>
                                <span>Pengajuan Cuti</span>
                            </a>
                            <a href="{{ route('portal.leave.public-list') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-users-slash w-5 text-rose-400 text-sm"></i>
                                <span>Jadwal Cuti Medis</span>
                            </a>
                            <a href="{{ route('portal.resignation.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-file-signature w-5 text-orange-400 text-sm"></i>
                                <span>Pengajuan Resign</span>
                            </a>
                            <a href="{{ route('portal.stase.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-graduation-cap w-5 text-blue-400 text-sm"></i>
                                <span>Pengajuan Stase</span>
                            </a>
                            <a href="{{ route('portal.vehicle-cert.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-car w-5 text-amber-400 text-sm"></i>
                                <span>Pengajuan Sertifikat Kendaraan</span>
                            </a>
                            <a href="{{ route('portal.operation-cert.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-award w-5 text-emerald-400 text-sm"></i>
                                <span>Pengajuan Sertifikat Operasi</span>
                            </a>
                            <a href="{{ route('credit-score.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-star-half-alt w-5 text-teal-400 text-sm"></i>
                                <span>Credit Score Alta</span>
                            </a>
                            <a href="{{ route('portal.interview.apply-form') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-user-plus w-5 text-indigo-400 text-sm"></i>
                                <span>Pengajuan Role Interviewer</span>
                            </a>

                            @if(auth()->user()->isAdmin() || auth()->user()->isExecutiveOrAbove() || auth()->user()->isInDivision('ie') || auth()->user()->isInDivision('pnd'))
                            <div class="px-2 pt-2 text-[10px] font-black tracking-widest uppercase text-emerald-300/80 border-t border-white/5">IE & PND Menu</div>
                            <a href="{{ route('portal.recruitment.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-bullhorn w-5 text-emerald-400 text-sm"></i>
                                <span>Kelola Recruitment</span>
                            </a>
                            <a href="{{ route('portal.ie.roles.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-user-tag w-5 text-sky-400 text-sm"></i>
                                <span>IE: Manajemen Jabatan</span>
                            </a>
                            <a href="{{ route('portal.ie.pemutihan.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-calendar-times w-5 text-sky-400 text-sm"></i>
                                <span>IE: Pemutihan Duty</span>
                            </a>
                            <a href="{{ route('portal.resignation.manage.ie') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-hand-holding-usd w-5 text-orange-400 text-sm"></i>
                                <span>IE: Denda Resign</span>
                            </a>
                            @endif

                            @if(auth()->user()->isInterviewer())
                            <div class="px-2 pt-2 text-[10px] font-black tracking-widest uppercase text-indigo-300/80 border-t border-white/5">Interviewer</div>
                            <a href="{{ route('portal.interview.index') }}"
                                class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                                <i class="fas fa-user-tie w-5 text-indigo-400 text-sm"></i>
                                <span>Interviewer: Calon Medis</span>
                            </a>
                            @else
                            <a href="{{ route('portal.interview.index') }}"
                                class="flex items-center gap-3 text-slate-300 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-xs font-medium transition-colors">
                                <i class="fas fa-user-plus w-5 text-indigo-400 text-xs"></i>
                                <span>Ajukan Jadi Interviewer</span>
                            </a>
                            @endif
                        </div>
                    @endif
                @endauth

                <!-- Section: Layanan Publik & Pasien (Visible to both guest & auth) -->
                <div class="space-y-1 pt-2 border-t border-white/10">
                    <div class="px-2 pt-1 text-[10px] font-black tracking-widest uppercase text-sky-300/70">Layanan Pasien & Warga</div>
                    
                    <a href="{{ route('public.index') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-home w-5 text-sky-400 text-sm"></i>
                        <span>Beranda Utama</span>
                    </a>

                    <a href="{{ route('public.form', ['type' => 'janji_temu', 'poli' => 'Poli Umum']) }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-calendar-check w-5 text-cyan-400 text-sm"></i>
                        <span>Janji Temu Dokter</span>
                    </a>

                    <a href="{{ route('public.cek-kesehatan') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-file-medical w-5 text-emerald-400 text-sm"></i>
                        <span>Surat Keterangan Sehat</span>
                    </a>

                    <a href="{{ route('public.doctor-schedule') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-calendar-alt w-5 text-blue-400 text-sm"></i>
                        <span>Jadwal Praktek Dokter</span>
                    </a>

                    <a href="{{ route('public.operasi-plastik') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-user-nurse w-5 text-rose-400 text-sm"></i>
                        <span>Pendaftaran Operasi Plastik</span>
                    </a>

                    <a href="{{ route('public.surat-psikolog') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-brain w-5 text-purple-400 text-sm"></i>
                        <span>Surat & Tes Psikologi</span>
                    </a>

                    <a href="{{ route('public.pendaftaran-karakter') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-heartbeat w-5 text-red-400 text-sm"></i>
                        <span>Pendaftaran Karakter Kill</span>
                    </a>

                    <a href="{{ route('public.tes-buta-warna') }}"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-eye w-5 text-amber-400 text-sm"></i>
                        <span>Tes Buta Warna Online (WHO)</span>
                    </a>

                    <a href="{{ route('public.index') }}#keluhan-warga"
                        class="flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-bullhorn w-5 text-orange-400 text-sm"></i>
                        <span>Form Keluhan & Pengaduan</span>
                    </a>

                    @guest
                        <button onclick="openRecruitmentModal()"
                            class="w-full flex items-center gap-3 text-slate-200 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl text-sm font-medium transition-colors text-left">
                            <i class="fas fa-user-plus w-5 text-emerald-400 text-sm"></i>
                            <span>Recruitment Calon Medis</span>
                        </button>
                    @endguest
                </div>

                @auth
                    <!-- Logout Button -->
                    <div class="pt-3 pb-8 border-t border-white/10">
                        <form method="POST" action="{{ route('staff.logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-red-500/25 hover:bg-red-500/40 text-red-200 hover:text-white text-sm font-bold border border-red-500/40 shadow-lg transition-all">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Keluar dari Akun (Logout)</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <!-- Session Warning Modal -->
    <div id="sessionWarningModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-gray-900">Session Akan Berakhir</h3>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        Session Anda akan berakhir dalam <span id="sessionCountdown"
                            class="font-bold text-red-600">5</span> menit.
                        Silakan refresh halaman atau lakukan aktivitas untuk memperpanjang session.
                    </p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button id="refreshSessionBtn"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh Session
                    </button>
                    <button id="closeSessionWarningBtn"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-default-theme mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <div class="flex justify-center items-center gap-2.5 mb-4">
                    <!-- Logo Alta & Roxwood Hospital -->
                    <div class="flex items-center gap-1.5 bg-white px-2.5 py-1.5 rounded-xl elegant-shadow">
                        <img src="{{ asset('images/motionlife-logo.png') }}" alt="Alta Hospital" class="h-6 w-6 object-contain">
                        <div class="h-4 w-px bg-slate-300"></div>
                        <img src="{{ asset('images/logo rhv2.png') }}" alt="Roxwood Hospital" class="h-6 w-6 object-contain">
                    </div>
                    <span class="text-lg font-semibold text-white">iMe ROLEPLAY</span>
                </div>
                <p class="text-gray-300 text-sm mb-4">Layanan medis profesional untuk komunitas role-playing</p>
                <div class="flex justify-center space-x-6 text-gray-400 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span>Terpercaya</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>24/7</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user-md mr-2"></i>
                        <span>Profesional</span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-white border-opacity-10">
                    <p class="text-gray-400 text-xs">&copy; 2026 Portal Medis iMe. Semua hak dilindungi develop
                        by tepe-dev.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Image Error Handler - Prevents 404 errors for missing profile images -->
    <script>
        (function () {
            'use strict';

            // Default fallback image for profile images
            const defaultProfileImage = '{{ asset("profile.jpg") }}';

            // Function to handle image error
            function handleImageError(img) {
                try {
                    if (!img || img.tagName !== 'IMG') {
                        return;
                    }

                    const src = img.getAttribute('src') || img.src || '';

                    // Only handle profile images (storage/profile-images or uploads/profile-images)
                    if (src && (src.includes('storage/profile-images') || src.includes('uploads/profile-images'))) {
                        // Check if image is already the default to prevent infinite loop
                        if (!src.includes('profile.jpg') && !src.includes('profile-image')) {
                            // Prevent infinite loop
                            img.onerror = null;
                            img.src = defaultProfileImage;
                        }
                    }
                } catch (e) {
                    // Silently fail if there's an error
                    console.warn('Error handling image fallback:', e);
                }
            }

            // Handle image errors when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    initImageErrorHandler();
                });
            } else {
                initImageErrorHandler();
            }

            function initImageErrorHandler() {
                try {
                    // Handle images that are already in the DOM
                    const profileImages = document.querySelectorAll('img');
                    profileImages.forEach(function (img) {
                        // Only add handler if src contains profile-images
                        const src = img.getAttribute('src') || img.src || '';
                        if (src.includes('profile-images') || src.includes('storage/') || src.includes('uploads/')) {
                            img.addEventListener('error', function () {
                                handleImageError(this);
                            }, { once: true }); // Use once to prevent multiple handlers

                            // Also set onerror as fallback
                            img.onerror = function () {
                                handleImageError(this);
                            };
                        }
                    });

                    // Handle dynamically loaded images using MutationObserver
                    if (typeof MutationObserver !== 'undefined') {
                        const observer = new MutationObserver(function (mutations) {
                            mutations.forEach(function (mutation) {
                                mutation.addedNodes.forEach(function (node) {
                                    if (node.nodeType === 1) { // Element node
                                        if (node.tagName === 'IMG') {
                                            const src = node.getAttribute('src') || node.src || '';
                                            if (src.includes('profile-images') || src.includes('storage/') || src.includes('uploads/')) {
                                                node.addEventListener('error', function () {
                                                    handleImageError(this);
                                                }, { once: true });
                                                node.onerror = function () {
                                                    handleImageError(this);
                                                };
                                            }
                                        } else {
                                            // Check for images inside the added node
                                            const images = node.querySelectorAll && node.querySelectorAll('img');
                                            if (images) {
                                                images.forEach(function (img) {
                                                    const src = img.getAttribute('src') || img.src || '';
                                                    if (src.includes('profile-images') || src.includes('storage/') || src.includes('uploads/')) {
                                                        img.addEventListener('error', function () {
                                                            handleImageError(this);
                                                        }, { once: true });
                                                        img.onerror = function () {
                                                            handleImageError(this);
                                                        };
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });
                            });
                        });

                        observer.observe(document.body, {
                            childList: true,
                            subtree: true
                        });
                    }
                } catch (e) {
                    console.warn('Error initializing image error handler:', e);
                }
            }
        })();
    </script>

    <!-- Chart.js Local -->
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = mobileMenu.classList.contains('hidden');
                    if (isHidden) {
                        mobileMenu.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    } else {
                        mobileMenu.classList.add('hidden');
                        document.body.style.overflow = '';
                    }

                    const icon = mobileMenuButton.querySelector('i');
                    if (icon) {
                        if (mobileMenu.classList.contains('hidden')) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        } else {
                            icon.classList.remove('fa-bars');
                            icon.classList.add('fa-times');
                        }
                    }
                });

                // Auto-close menu when clicking on any link inside
                mobileMenu.querySelectorAll('a, button:not(.pwa-install-trigger)').forEach(el => {
                    el.addEventListener('click', function () {
                        mobileMenu.classList.add('hidden');
                        document.body.style.overflow = '';
                        const icon = mobileMenuButton.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        }
                    });
                });
            }
        });

        // CSRF Token Auto-Refresh untuk mencegah 419 Page Expired
        let csrfTokenRefreshInterval;

        function refreshCsrfToken() {
            return fetch('/csrf-token', {
                method: 'GET',
                credentials: 'same-origin', // Include cookies for same-origin requests
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.csrf_token) {
                        // Update meta tag
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            metaTag.setAttribute('content', data.csrf_token);
                        }

                        // Update all CSRF token inputs
                        const csrfInputs = document.querySelectorAll('input[name="_token"]');
                        csrfInputs.forEach(input => {
                            input.value = data.csrf_token;
                        });

                        console.log('CSRF token refreshed successfully');
                    }
                })
                .catch(error => {
                    console.warn('Failed to refresh CSRF token:', error);
                });
        }

        // Refresh CSRF token setiap 60 menit (3600000 ms)
        function startCsrfTokenRefresh() {
            // Refresh token setiap 60 menit
            csrfTokenRefreshInterval = setInterval(refreshCsrfToken, 60 * 60 * 1000);

            // Juga refresh saat user kembali ke tab (visibility change)
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    refreshCsrfToken();
                }
            });
        }

        // Start CSRF token refresh
        startCsrfTokenRefresh();

        // Session Warning System
        let sessionWarningShown = false;
        let sessionWarningTimer;

        function showSessionWarning() {
            if (sessionWarningShown) return;

            sessionWarningShown = true;
            const modal = document.getElementById('sessionWarningModal');
            const countdown = document.getElementById('sessionCountdown');

            if (!modal || !countdown) return;

            modal.classList.remove('hidden');

            // Countdown timer
            let timeLeft = 5;
            countdown.textContent = timeLeft;

            const countdownInterval = setInterval(() => {
                timeLeft--;
                countdown.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    modal.classList.add('hidden');
                    // Auto refresh page
                    window.location.reload();
                }
            }, 60000); // Update every minute

            // Button handlers
            const refreshSessionBtn = document.getElementById('refreshSessionBtn');
            if (refreshSessionBtn) {
                refreshSessionBtn.onclick = function () {
                    clearInterval(countdownInterval);
                    refreshCsrfToken();
                    modal.classList.add('hidden');
                    sessionWarningShown = false;
                };
            }

            const closeSessionWarningBtn = document.getElementById('closeSessionWarningBtn');
            if (closeSessionWarningBtn) {
                closeSessionWarningBtn.onclick = function () {
                    clearInterval(countdownInterval);
                    modal.classList.add('hidden');
                    sessionWarningShown = false;
                };
            }
        }

        function startSessionWarning() {
            // Show warning 5 minutes before session expires (715 minutes for 12 hour session)
            const warningTime = 715 * 60 * 1000; // 715 minutes in milliseconds

            sessionWarningTimer = setTimeout(showSessionWarning, warningTime);
        }

        // Start session warning timer
        startSessionWarning();

        // Reset session warning on user activity
        ['click', 'keypress', 'scroll', 'mousemove'].forEach(event => {
            document.addEventListener(event, function () {
                if (sessionWarningTimer) {
                    clearTimeout(sessionWarningTimer);
                    startSessionWarning();
                }
            }, true);
        });

        // Handle form submission dengan retry mechanism untuk 419 errors
        document.addEventListener('submit', function (e) {
            const form = e.target;

            // Skip forms that have their own JavaScript handlers
            const formsWithHandlers = [
                'dashboardClockInForm',
            ];

            if (formsWithHandlers.includes(form.id)) {
                // Let the form's own handler take care of it
                return;
            }

            // Skip forms that should submit normally (public forms with error handling)
            // These forms need to redirect normally to show flash messages
            if (form.id === 'medicalForm' ||
                (form.action && form.action.includes('/form/submit')) ||
                (form.action && form.action.includes('/staff/logout')) ||
                (form.action && form.action.includes('clock-out'))) {
                // Let form submit normally - flash messages will be shown on redirect
                return;
            }

            if (form.method.toLowerCase() === 'post') {
                e.preventDefault();

                // Get action from data-action attribute first (more reliable), fallback to action attribute
                let formAction = form.getAttribute('data-action') || form.getAttribute('action');

                // Validate form action - must not contain Blade syntax
                if (!formAction || formAction.includes('{' + '{') || formAction.includes('route(') || formAction.trim() === '') {
                    console.error('Invalid form action detected:', formAction);
                    alert('Terjadi kesalahan: Form action tidak valid. Silakan refresh halaman.');
                    return;
                }

                const originalSubmit = function () {
                    // Remove event listener to prevent infinite loop
                    form.removeEventListener('submit', originalSubmit);
                    form.submit();
                };

                // Try to submit the form
                fetch(formAction, {
                    method: 'POST',
                    credentials: 'same-origin', // Include cookies for same-origin requests
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    redirect: 'follow'
                })
                    .then(response => {
                        if (response.status === 419) {
                            // CSRF token expired, refresh and retry
                            console.log('CSRF token expired, refreshing...');
                            return refreshCsrfToken().then(() => {
                                // Retry submission
                                return fetch(formAction, {
                                    method: 'POST',
                                    body: new FormData(form),
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    redirect: 'follow'
                                });
                            });
                        }
                        return response;
                    })
                    .then(response => {
                        // Check if response is JSON (for AJAX requests)
                        const contentType = response.headers.get('content-type');
                        const isJson = contentType?.includes('application/json');

                        if (isJson) {
                            // Read JSON regardless of status code
                            return response.json().then(data => {
                                console.log('JSON Response:', data);
                                if (response.ok && data.success) {
                                    // Success response
                                    if (data.redirect_url) {
                                        window.location.href = data.redirect_url;
                                    } else if (data.message) {
                                        alert(data.message);
                                        if (data.redirect_url) {
                                            window.location.href = data.redirect_url;
                                        }
                                    }
                                } else {
                                    // Error response (status 422, 400, etc)
                                    let errorMessage = data.message || 'Terjadi kesalahan saat mengirim form.';
                                    if (data.errors) {
                                        const errorList = Object.values(data.errors).flat().join('\n');
                                        if (errorList) {
                                            errorMessage = errorMessage + '\n\n' + errorList;
                                        }
                                    }
                                    console.log('Showing error alert:', errorMessage);
                                    alert(errorMessage);
                                    // Don't redirect on error, let user fix the form
                                    return;
                                }
                                return data;
                            }).catch(err => {
                                console.error('Error parsing JSON:', err);
                                alert('Terjadi kesalahan saat memproses response dari server.');
                            });
                        } else {
                            // Regular form submission (HTML response)
                            if (response.ok) {
                                if (response.redirected) {
                                    // Follow redirect - flash message will be shown on redirected page
                                    window.location.href = response.url;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                // Non-JSON error response - try to read as text
                                return response.text().then(text => {
                                    console.error('Form submission failed:', text);
                                    alert('Terjadi kesalahan saat mengirim form. Status: ' + response.status);
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Form submission error:', error);
                        // Fallback to regular form submission
                        originalSubmit();
                    });
            }
        });

        // Tooltip functionality dengan JavaScript murni

        // Add fade-in-up animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in-up {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-fade-in-up {
                animation: fade-in-up 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);

        // Recruitment Modal Functions
        function openRecruitmentModal() {
            document.getElementById('recruitmentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRecruitmentModal() {
            document.getElementById('recruitmentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        const recruitmentModal = document.getElementById('recruitmentModal');
        if (recruitmentModal) {
            recruitmentModal.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeRecruitmentModal();
                }
            });
        }

    </script>

    <!-- Recruitment Modal -->
    <div id="recruitmentModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full animate-fade-in-up">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-6 rounded-t-3xl relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white rounded-full -translate-y-10 translate-x-10">
                    </div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-white rounded-full translate-y-8 -translate-x-8">
                    </div>
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-plus text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Recruitment</h2>
                            <p class="text-blue-100 text-sm">MOTIONLIFE EMS</p>
                        </div>
                    </div>
                    <button onclick="closeRecruitmentModal()"
                        class="w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-lg">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fab fa-discord text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Informasi Pendaftaran</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Semua informasi mengenai pendaftaran dan persyaratan dapat dilihat secara lengkap melalui
                        saluran Discord kami.
                    </p>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6">
                        <div class="flex flex-col items-center justify-center gap-2 text-indigo-700">
                            <i class="fas fa-external-link-alt mb-1"></i>
                            <span class="text-sm font-medium text-center">Kunjungi channel #recruitment di
                                Discord:</span>
                            <a href="https://discord.com/channels/1357345255728480356/1370432256342233098"
                                target="_blank"
                                class="text-indigo-600 font-bold hover:text-indigo-800 hover:underline text-sm break-all">
                                Klik Disini Untuk Membuka Discord
                            </a>
                        </div>
                    </div>
                    <button onclick="closeRecruitmentModal()"
                        class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-check mr-2"></i>Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Alert/Confirmation Modal -->
    <div id="customModal" class="modal-overlay" style="display: none;">
        <div class="modal-backdrop"></div>
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-icon-wrapper">
                    <div class="modal-icon" id="modalIcon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <h3 class="modal-title" id="modalTitle">Notification</h3>
                    <p class="modal-message" id="modalMessage"></p>
                </div>
                <div class="modal-actions" id="modalActions">
                    <button class="modal-btn modal-btn-secondary" id="modalCancelBtn">Batal</button>
                    <button class="modal-btn modal-btn-primary" id="modalConfirmBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 999999999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: modalFadeIn 0.2s ease-out;
        }

        .modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            animation: backdropFadeIn 0.25s ease-out;
        }

        .modal-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            animation: modalSlideIn 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .modal-content {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 1.25rem;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.2),
                0 8px 24px rgba(0, 0, 0, 0.12),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            text-align: center;
        }

        .modal-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .modal-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: iconBounceIn 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .modal-icon i {
            font-size: 2rem;
        }

        /* Icon Types */
        .modal-icon.icon-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #0284c7;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }

        .modal-icon.icon-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        }

        .modal-icon.icon-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        .modal-icon.icon-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .modal-body {
            margin-bottom: 2rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .modal-message {
            font-size: 0.9375rem;
            color: #6b7280;
            line-height: 1.6;
            font-weight: 450;
        }

        .modal-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .modal-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 100px;
        }

        .modal-btn:active {
            transform: scale(0.96);
        }

        .modal-btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
        }

        .modal-btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .modal-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .modal-btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .modal-btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .modal-btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .modal-btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        }

        .modal-btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .modal-btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
        }

        /* Animations */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes backdropFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes iconBounceIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Mobile responsive */
        @media (max-width: 640px) {
            .modal-content {
                padding: 1.5rem;
            }

            .modal-icon {
                width: 3.5rem;
                height: 3.5rem;
            }

            .modal-icon i {
                font-size: 1.75rem;
            }

            .modal-title {
                font-size: 1.25rem;
            }

            .modal-actions {
                flex-direction: column;
            }

            .modal-btn {
                width: 100%;
            }
        }
    </style>

    <script>
        // Custom Alert Function
        window.showAlert = function (message, type = 'info', title = null) {
            return new Promise((resolve) => {
                const modal = document.getElementById('customModal');
                const icon = document.getElementById('modalIcon');
                const titleEl = document.getElementById('modalTitle');
                const messageEl = document.getElementById('modalMessage');
                const actions = document.getElementById('modalActions');
                const confirmBtn = document.getElementById('modalConfirmBtn');

                // Set icon
                icon.className = 'modal-icon icon-' + type;
                const iconClass = {
                    'info': 'fa-info-circle',
                    'warning': 'fa-exclamation-triangle',
                    'error': 'fa-times-circle',
                    'success': 'fa-check-circle'
                }[type] || 'fa-info-circle';
                icon.querySelector('i').className = 'fas ' + iconClass;

                // Set title
                titleEl.textContent = title || {
                    'info': 'Informasi',
                    'warning': 'Perhatian',
                    'error': 'Error',
                    'success': 'Berhasil'
                }[type] || 'Notification';

                // Set message
                messageEl.textContent = message;

                // Show only OK button
                actions.innerHTML = '<button class="modal-btn modal-btn-primary" id="modalOkBtn">OK</button>';

                const okBtn = document.getElementById('modalOkBtn');
                okBtn.onclick = () => {
                    modal.style.display = 'none';
                    resolve(true);
                };

                // Show modal
                modal.style.display = 'flex';

                // ESC key handler
                const escHandler = (e) => {
                    if (e.key === 'Escape') {
                        modal.style.display = 'none';
                        document.removeEventListener('keydown', escHandler);
                        resolve(true);
                    }
                };
                document.addEventListener('keydown', escHandler);
            });
        };

        // Custom Confirm Function
        window.showConfirm = function (message, options = {}) {
            return new Promise((resolve) => {
                const modal = document.getElementById('customModal');
                const icon = document.getElementById('modalIcon');
                const titleEl = document.getElementById('modalTitle');
                const messageEl = document.getElementById('modalMessage');
                const actions = document.getElementById('modalActions');

                const {
                    type = 'warning',
                    title = 'Konfirmasi',
                    confirmText = 'Ya',
                    cancelText = 'Batal',
                    confirmClass = 'modal-btn-danger'
                } = options;

                // Set icon
                icon.className = 'modal-icon icon-' + type;
                const iconClass = {
                    'info': 'fa-question-circle',
                    'warning': 'fa-exclamation-triangle',
                    'error': 'fa-times-circle',
                    'success': 'fa-check-circle'
                }[type] || 'fa-question-circle';
                icon.querySelector('i').className = 'fas ' + iconClass;

                // Set content
                titleEl.textContent = title;
                messageEl.textContent = message;

                // Show both buttons
                actions.innerHTML = `
                    <button class="modal-btn modal-btn-secondary" id="modalCancelBtn">${cancelText}</button>
                    <button class="modal-btn ${confirmClass}" id="modalConfirmBtn">${confirmText}</button>
                `;

                const cancelBtn = document.getElementById('modalCancelBtn');
                const confirmBtn = document.getElementById('modalConfirmBtn');

                cancelBtn.onclick = () => {
                    modal.style.display = 'none';
                    resolve(false);
                };

                confirmBtn.onclick = () => {
                    modal.style.display = 'none';
                    resolve(true);
                };

                // Show modal
                modal.style.display = 'flex';

                // ESC key handler
                const escHandler = (e) => {
                    if (e.key === 'Escape') {
                        modal.style.display = 'none';
                        document.removeEventListener('keydown', escHandler);
                        resolve(false);
                    }
                };
                document.addEventListener('keydown', escHandler);

                // Backdrop click handler
                modal.querySelector('.modal-backdrop').onclick = () => {
                    modal.style.display = 'none';
                    resolve(false);
                };
            });
        };

        // Custom Confirm Function wrapper for SWEETALERT-like API 
        window.confirmAction = async function (options = {}) {
            const result = await window.showConfirm(options.text || '', {
                type: options.icon || 'warning',
                title: options.title || 'Konfirmasi',
                confirmText: options.confirmText || 'Ya',
                cancelText: options.cancelText || 'Batal',
                confirmClass: options.confirmClass || 'modal-btn-danger'
            });
            return { isConfirmed: result };
        };

        // Custom Toast Function
        window.showToast = function (type, title, message) {
            const container = document.getElementById('notificationContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[1000] w-full max-w-sm animate-fade-in-right pointer-events-auto`;
            
            const bgColor = {
                'success': 'bg-emerald-600',
                'error': 'bg-rose-600',
                'info': 'bg-sky-600',
                'warning': 'bg-amber-600'
            }[type] || 'bg-slate-800';

            const iconClass = {
                'success': 'fa-check-circle',
                'error': 'fa-times-circle',
                'info': 'fa-info-circle',
                'warning': 'fa-exclamation-triangle'
            }[type] || 'fa-info-circle';

            toast.innerHTML = `
                <div class="${bgColor} text-white p-4 rounded-xl shadow-2xl flex items-start gap-3 border-l-4 border-white/30 relative overflow-hidden">
                    <div class="flex-shrink-0 mt-0.5"><i class="fas ${iconClass} text-xl"></i></div>
                    <div class="flex-1">
                        <h4 class="font-bold text-sm">${title}</h4>
                        <p class="text-xs opacity-90 mt-1 leading-relaxed">${message}</p>
                    </div>
                    <button class="opacity-70 hover:opacity-100 transition-opacity"><i class="fas fa-times"></i></button>
                    <div class="absolute bottom-0 left-0 h-1 bg-white/20 w-full animate-shrink"></div>
                </div>
            `;

            container.appendChild(toast);

            const closeBtn = toast.querySelector('button');
            const dismiss = () => {
                toast.style.transition = 'all 0.4s ease-in';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => toast.remove(), 400);
            };

            closeBtn.onclick = dismiss;
            setTimeout(dismiss, 5000);
        };

        // Backward compatibility - override native alert
        window.alert = function (message) {
            showAlert(message, 'info');
        };

        // DON'T override confirm() - it won't work with onsubmit="return confirm()"
        // Instead, intercept form submissions with confirm()

        document.addEventListener('DOMContentLoaded', function () {
            // Intercept all form submissions
            document.addEventListener('submit', async function (e) {
                const form = e.target;
                const onsubmit = form.getAttribute('onsubmit');

                // Check if form has onsubmit with confirm()
                if (onsubmit && onsubmit.includes('confirm(')) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Extract message from confirm('message')
                    const match = onsubmit.match(/confirm\s*\(\s*['"`]([\s\S]+?)['"`]\s*\)/);
                    if (match) {
                        const message = match[1]
                            .replace(/\\n/g, '\n')
                            .replace(/\\'/g, "'")
                            .replace(/\\"/g, '"');

                        // Show custom confirm modal
                        const result = await showConfirm(message, {
                            type: 'warning',
                            title: 'Konfirmasi',
                            confirmText: 'Ya',
                            cancelText: 'Batal',
                            confirmClass: 'modal-btn-danger'
                        });

                        if (result) {
                            // User confirmed - remove onsubmit and submit
                            form.removeAttribute('onsubmit');
                            form.submit();
                        }
                        // If cancelled, do nothing (form won't submit)
                    }
                }
            }, true); // Use capture phase to intercept early
        });
    </script>

    {{-- Floating Wrapped Button (left bottom corner) --}}
    @auth
        <a href="{{ route('wrapped.show', ['year' => now()->year]) }}" class="fixed bottom-6 left-6 z-[100] group"
            title="Lihat Wrapped {{ now()->year }}">
            <div class="relative">
                {{-- Glow effect --}}
                <div
                    class="absolute inset-0 bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 rounded-2xl blur-xl opacity-60 group-hover:opacity-100 animate-pulse transition-opacity duration-300">
                </div>

                {{-- Button --}}
                <div
                    class="relative w-14 h-14 bg-gradient-to-br from-purple-600 via-pink-600 to-orange-600 rounded-2xl shadow-2xl flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-12 border-2 border-white/20">
                    {{-- Sparkle animation overlay --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/40 to-white/0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>

                    {{-- Icon: Calendar with Chart (Year Recap) --}}
                    <svg class="w-7 h-7 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- Calendar base -->
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <line x1="3" y1="9" x2="21" y2="9" stroke-width="2" stroke-linecap="round" />
                        <line x1="9" y1="2" x2="9" y2="6" stroke-width="2" stroke-linecap="round" />
                        <line x1="15" y1="2" x2="15" y2="6" stroke-width="2" stroke-linecap="round" />
                        <!-- Chart bars inside -->
                        <line x1="7" y1="18" x2="7" y2="14" stroke-width="1.5" stroke-linecap="round" />
                        <line x1="12" y1="18" x2="12" y2="12" stroke-width="1.5" stroke-linecap="round" />
                        <line x1="17" y1="18" x2="17" y2="15" stroke-width="1.5" stroke-linecap="round" />
                    </svg>

                    {{-- Badge notification dot (optional, if you want to show "new" indicator) --}}
                    <div
                        class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white animate-ping">
                    </div>
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></div>
                </div>

                {{-- Tooltip --}}
                <div
                    class="absolute bottom-full left-0 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                    <div
                        class="bg-gray-900 text-white text-xs font-medium px-3 py-2 rounded-lg whitespace-nowrap shadow-xl">
                        Wrapped {{ now()->year }} 🎉
                        <div class="absolute top-full left-4 w-2 h-2 bg-gray-900 transform rotate-45 -mt-1"></div>
                    </div>
                </div>
            </div>
        </a>
    @endauth

    @auth
        @livewire('chat-widget')
    @endauth
    {{-- Global Flash Message & Error Handler --}}
    @if(session('error') || session('success') || $errors->any())
        <div class="fixed top-5 right-5 z-[99999] w-full max-w-md animate-fade-in-left pointer-events-auto"
            id="global-toast-layout">
            @if(session('error') || $errors->any())
                <div
                    class="bg-red-600 border-l-4 border-white text-white p-4 rounded shadow-2xl flex items-start gap-3 relative">
                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-2xl"></i></div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg">Perhatian!</h3>
                        <p class="text-sm opacity-95">
                            @if(session('error'))
                                {!! session('error') !!}
                            @else
                                Terdapat {{ $errors->count() }} kesalahan pada isian formulir. Mohon periksa kembali.
                            @endif
                        </p>
                    </div>
                    <button onclick="document.getElementById('global-toast-layout').remove()"
                        class="ml-4 opacity-80 hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <!-- Progress bar -->
                    <div class="absolute bottom-0 left-0 h-1 bg-white/30 animate-shrink w-full"></div>
                </div>
            @elseif(session('success'))
                <div
                    class="bg-green-600 border-l-4 border-white text-white p-4 rounded shadow-2xl flex items-start gap-3 relative">
                    <div class="flex-shrink-0"><i class="fas fa-check-circle text-2xl"></i></div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg">Berhasil!</h3>
                        <p class="text-sm opacity-95">{!! session('success') !!}</p>
                    </div>
                    <button onclick="document.getElementById('global-toast-layout').remove()"
                        class="ml-4 opacity-80 hover:opacity-100 transition-opacity">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Global Toast Initialized');
                const toast = document.getElementById('global-toast-layout');
                if (toast) {
                    console.log('Toast element found, ensuring visibility');
                    toast.style.display = 'block';
                    // Auto dismiss after 10 seconds
                    setTimeout(() => {
                        if (toast) {
                            toast.style.transition = 'opacity 0.5s ease-out';
                            toast.style.opacity = '0';
                            setTimeout(() => toast.remove(), 500);
                        }
                    }, 10000);
                }

                // Backup Alert
                setTimeout(() => {
                    @if(session('error'))
                        alert(@json(session('error')));
                    @elseif($errors->any())
                        alert('Terdapat kesalahan input. Silakan periksa formulir.');
                    @endif
                                                            }, 1000);
            });
        </script>
        <style>
            @keyframes shrink {
                from {
                    width: 100%;
                }

                to {
                    width: 0%;
                }
            }

            .animate-shrink {
                animation: shrink 10s linear forwards;
            }
        </style>
    @endif

    @livewireScripts
    @livewireScriptConfig
    @stack('scripts')

    {{-- Global Heartbeat: Update last_seen_at setiap 30 detik selama user aktif di website --}}
    @auth
    <script>
    (function () {
        var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

        function sendHeartbeat() {
            fetch('/ping-online', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }).catch(function () {});
        }

        // Kirim heartbeat pertama segera
        sendHeartbeat();

        // Ulangi setiap 30 detik
        setInterval(sendHeartbeat, 30000);

        // Refresh CSRF token setiap 4 menit agar sesi tidak expired
        setInterval(function () {
            fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' }).catch(function () {});
        }, 240000);
    })();
    </script>
    @endauth

    <!-- PWA Installation Guide Modal -->
    <div id="pwaInstallGuideModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-[9999] hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-sky-500/40 rounded-2xl p-6 max-w-lg w-full text-white shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-sky-500/20 pb-3 mb-4">
                <h3 class="text-lg font-bold text-sky-400 flex items-center gap-2">
                    <i class="fas fa-desktop text-xl"></i> Install iMe Portal Medis
                </h3>
                <button type="button" onclick="document.getElementById('pwaInstallGuideModal').classList.remove('flex'); document.getElementById('pwaInstallGuideModal').classList.add('hidden');" class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>
            <ol class="space-y-3 text-sm text-slate-300 mb-6">
                <li class="flex items-start gap-2">
                    <span class="bg-sky-500 text-slate-950 font-bold rounded-full w-5 h-5 flex items-center justify-center text-xs shrink-0 mt-0.5">1</span>
                    <span>Klik ikon <strong>[⋮] (Titik Tiga)</strong> atau <strong>[⊕]</strong> di sudut kanan atas browser Google Chrome / Microsoft Edge Anda.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="bg-sky-500 text-slate-950 font-bold rounded-full w-5 h-5 flex items-center justify-center text-xs shrink-0 mt-0.5">2</span>
                    <span>Pilih menu <strong>"Simpan dan Bagikan"</strong> ➔ <strong>"Buat Pintasan..."</strong> (atau <em>"Install iMe Portal Medis"</em>).</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="bg-sky-500 text-slate-950 font-bold rounded-full w-5 h-5 flex items-center justify-center text-xs shrink-0 mt-0.5">3</span>
                    <span>Centang <strong>"Buka sebagai jendela"</strong> lalu klik <strong>Install</strong>.</span>
                </li>
            </ol>
            <div class="flex justify-end">
                <button type="button" onclick="document.getElementById('pwaInstallGuideModal').classList.remove('flex'); document.getElementById('pwaInstallGuideModal').classList.add('hidden');" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-lg text-sm shadow-md transition-all">Paham, Mengerti</button>
            </div>
        </div>
    </div>

    <!-- PWA Service Worker & Install Script -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}?v={{ time() }}")
                    .then(reg => console.log('PWA ServiceWorker registered:', reg.scope))
                    .catch(err => console.error('PWA ServiceWorker error:', err));
            });
        }

        let deferredPwaPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPwaPrompt = e;
            const pwaTriggers = document.querySelectorAll('.pwa-install-trigger');
            pwaTriggers.forEach(btn => btn.style.display = 'inline-flex');
        });

        function triggerPwaInstall() {
            if (deferredPwaPrompt) {
                deferredPwaPrompt.prompt();
                deferredPwaPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA installed successfully');
                    }
                    deferredPwaPrompt = null;
                });
            } else {
                const modal = document.getElementById('pwaInstallGuideModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }
        }

        function checkPwaInstalled() {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                                 window.navigator.standalone === true;
            if (isStandalone) {
                const pwaTriggers = document.querySelectorAll('.pwa-install-trigger');
                pwaTriggers.forEach(btn => btn.style.display = 'none');
            }
        }

        window.addEventListener('DOMContentLoaded', checkPwaInstalled);
        window.addEventListener('appinstalled', () => {
            const pwaTriggers = document.querySelectorAll('.pwa-install-trigger');
            pwaTriggers.forEach(btn => btn.style.display = 'none');
        });
    </script>

@auth
@php
    $aiSettings = \App\Models\AiSetting::getSettings();
    $aiChatEnabled = (bool) $aiSettings->enabled;
    $aiInitialQuotas = \App\Http\Controllers\Staff\AiChatController::getModelQuotas(Auth::id());
    $aiCurrentModel = $aiSettings->model ?? 'gemini-3.5-flash';
    if (!isset($aiInitialQuotas[$aiCurrentModel])) {
        $aiCurrentModel = array_key_first($aiInitialQuotas) ?? 'gemini-3.5-flash';
    }
@endphp
@if($aiChatEnabled)
{{-- =====================================================
     GEMINI AI CHAT ASSISTANT — Fullscreen Modern Workspace
====================================================== --}}
<style>
    /* Trigger Floating Button - Web Theme: Sky & Cyan */
    #ai-chat-btn {
        position: fixed;
        bottom: 92px; /* Positioned right above Live Chat button (bottom: 24px) to prevent overlap */
        right: 24px;
        z-index: 99990;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #06b6d4 100%);
        border: 2px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(14, 165, 233, 0.45), 0 2px 8px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: aiPulse 3s ease-in-out infinite;
    }
    #ai-chat-btn:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 30px rgba(14, 165, 233, 0.65), 0 4px 12px rgba(0,0,0,0.3);
    }
    @keyframes aiPulse {
        0%, 100% { box-shadow: 0 4px 20px rgba(14, 165, 233, 0.45), 0 2px 8px rgba(0,0,0,0.2); }
        50%       { box-shadow: 0 4px 30px rgba(14, 165, 233, 0.75), 0 2px 8px rgba(0,0,0,0.2); }
    }
    @media (max-width: 640px) {
        #ai-chat-btn {
            bottom: 88px;
            right: 20px;
            width: 48px;
            height: 48px;
        }
    }

    /* Fullscreen Modal Overlay */
    #ai-full-modal {
        position: fixed;
        inset: 0;
        z-index: 999999;
        background: rgba(6, 13, 26, 0.85);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    #ai-full-modal.visible {
        display: flex;
        opacity: 1;
    }

    /* Workspace Shell - Slate & Ocean Navy */
    .ai-workspace {
        width: 100%;
        max-width: 1320px;
        height: 92vh;
        max-height: 940px;
        background: #081120;
        border: 1px solid rgba(14, 165, 233, 0.35);
        border-radius: 24px;
        box-shadow: 0 25px 80px -10px rgba(0,0,0,0.85), 0 0 50px rgba(14, 165, 233, 0.18);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: scale(0.97);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #ai-full-modal.visible .ai-workspace {
        transform: scale(1);
    }

    /* Top Navigation Bar - Matches Navbar Theme (#0c4a6e to #075985) */
    .ai-topbar {
        background: linear-gradient(90deg, #0c4a6e 0%, #075985 60%, #091c33 100%);
        border-bottom: 1px solid rgba(14, 165, 233, 0.3);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        position: relative;
        z-index: 100; /* Stays above .ai-body */
    }
    .ai-topbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ai-topbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ai-brand-badge {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ai-brand-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7, #06b6d4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
    }

    /* Interactive Model Selector Styles */
    .ai-model-picker-wrap {
        position: relative;
        z-index: 110; /* Ensures dropdown is highest in stacking context */
    }
    .ai-model-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 12px;
        background: rgba(12, 74, 110, 0.55);
        border: 1px solid rgba(56, 189, 248, 0.45);
        color: #f0f9ff;
        font-size: 11.5px;
        font-weight: 600;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .ai-model-pill-btn:hover {
        background: rgba(12, 74, 110, 0.95);
        border-color: #38bdf8;
        box-shadow: 0 0 14px rgba(14, 165, 233, 0.45);
        color: white;
    }
    .ai-model-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        box-shadow: 0 0 6px currentColor;
    }
    .ai-quota-mini-tag {
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 999px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .ai-model-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        width: 390px;
        max-width: min(390px, calc(100vw - 32px));
        background: #081324;
        border: 1px solid rgba(14, 165, 233, 0.45);
        border-radius: 18px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.92), 0 0 35px rgba(14, 165, 233, 0.25);
        z-index: 9999; /* Above all body elements */
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        animation: aiDropdownIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ai-model-dropdown.hidden {
        display: none;
    }
    @keyframes aiDropdownIn {
        from { opacity: 0; transform: translateY(-8px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .ai-model-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 4px;
    }
    .ai-model-dropdown-title {
        color: #f1f5f9;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ai-model-cards-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 420px;
        overflow-y: auto;
        padding-right: 2px;
    }
    .ai-model-cards-list::-webkit-scrollbar { width: 4px; }
    .ai-model-cards-list::-webkit-scrollbar-thumb { background: rgba(14, 165, 233, 0.35); border-radius: 4px; }
    .ai-model-card {
        padding: 10px 12px;
        background: #0d1a2d;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
    }
    .ai-model-card:hover {
        background: #12243d;
        border-color: rgba(14, 165, 233, 0.5);
        transform: translateY(-1px);
    }
    .ai-model-card.active {
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.25), rgba(12, 74, 110, 0.35));
        border-color: #38bdf8;
        box-shadow: 0 0 16px rgba(14, 165, 233, 0.25);
    }
    .ai-model-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .ai-model-name-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ai-model-check-circle {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .ai-model-card.active .ai-model-check-circle {
        background: #0284c7;
        border-color: #38bdf8;
    }
    .ai-model-name {
        color: #f8fafc;
        font-size: 12.5px;
        font-weight: 700;
    }
    .ai-model-badge {
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 6px;
        background: rgba(14, 165, 233, 0.2);
        color: #38bdf8;
        border: 1px solid rgba(14, 165, 233, 0.35);
        font-weight: 600;
    }
    .ai-model-status-pill {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .status-emerald {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }
    .status-amber {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .status-red {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }
    .ai-model-desc {
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.35;
    }
    .ai-model-quota-bar-track {
        width: 100%;
        height: 6px;
        background: #060d18;
        border-radius: 999px;
        overflow: hidden;
        position: relative;
    }
    .ai-model-quota-bar-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }
    .ai-model-quota-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 10px;
        color: #64748b;
    }
    .ai-model-dropdown-footer {
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,0.06);
        font-size: 10.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ai-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ai-icon-btn:hover {
        background: rgba(255,255,255,0.15);
        color: white;
    }
    .ai-newchat-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        border: 1px solid rgba(56, 189, 248, 0.35);
        color: white;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 10px rgba(14, 165, 233, 0.3);
    }
    .ai-newchat-btn:hover {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        transform: translateY(-1px);
    }
    .ai-close-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ai-close-btn:hover {
        background: rgba(239, 68, 68, 0.85);
        color: white;
    }

    /* Workspace Body: Sidebar + Main Area */
    .ai-body {
        flex: 1;
        display: flex;
        overflow: hidden;
        position: relative;
        z-index: 1; /* Below .ai-topbar */
    }

    /* Left Sidebar: Riwayat Chat */
    .ai-sidebar {
        width: 310px;
        background: #08111e;
        border-right: 1px solid rgba(14, 165, 233, 0.18);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s;
    }
    .ai-sidebar.collapsed {
        width: 0;
        overflow: hidden;
        border-right: none;
    }
    .ai-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .ai-sidebar-search {
        position: relative;
        margin-top: 10px;
    }
    .ai-sidebar-search input {
        width: 100%;
        background: #0d1a2d;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 8px 12px 8px 34px;
        color: #e2e8f0;
        font-size: 12px;
        outline: none;
        transition: border-color 0.2s;
    }
    .ai-sidebar-search input:focus {
        border-color: #0ea5e9;
    }
    .ai-sidebar-search svg {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 14px;
        color: #64748b;
    }
    .ai-history-list {
        flex: 1;
        overflow-y: auto;
        padding: 12px 8px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .ai-history-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(255,255,255,0.02);
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    .ai-history-item:hover {
        background: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.25);
    }
    .ai-history-item.active {
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.25), rgba(12, 74, 110, 0.35));
        border-color: rgba(14, 165, 233, 0.5);
    }
    .ai-history-title {
        color: #e2e8f0;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 210px;
    }
    .ai-history-time {
        color: #64748b;
        font-size: 10px;
        margin-top: 2px;
    }
    .ai-item-del-btn {
        opacity: 0;
        background: none;
        border: none;
        color: #94a3b8;
        padding: 4px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ai-history-item:hover .ai-item-del-btn {
        opacity: 1;
    }
    .ai-item-del-btn:hover {
        color: #f87171;
        background: rgba(239, 68, 68, 0.15);
    }
    .ai-sidebar-footer {
        padding: 12px 16px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .ai-clear-all-btn {
        width: 100%;
        padding: 7px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .ai-clear-all-btn:hover {
        background: rgba(239, 68, 68, 0.25);
        color: white;
    }

    /* Main Chat Column */
    .ai-main-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #070e1a;
    }

    /* Messages Scroll Area */
    #ai-messages {
        flex: 1;
        overflow-y: auto;
        padding: 24px 32px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        scrollbar-width: thin;
        scrollbar-color: rgba(14, 165, 233, 0.3) transparent;
    }
    #ai-messages::-webkit-scrollbar { width: 6px; }
    #ai-messages::-webkit-scrollbar-thumb { background: rgba(14, 165, 233, 0.3); border-radius: 4px; }

    /* Welcome Hero & Suggestions */
    .ai-welcome-hero {
        max-width: 760px;
        margin: auto;
        text-align: center;
        padding: 30px 16px;
    }
    .ai-hero-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: linear-gradient(135deg, #0284c7, #06b6d4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.35);
        margin-bottom: 16px;
    }
    .ai-hero-title {
        color: white;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .ai-hero-desc {
        color: #94a3b8;
        font-size: 13px;
        line-height: 1.6;
        max-width: 580px;
        margin: 0 auto 24px;
    }
    .ai-suggestion-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 12px;
        text-align: left;
    }
    .ai-suggestion-card {
        padding: 14px 16px;
        background: #0d1a2d;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ai-suggestion-card:hover {
        background: #11233e;
        border-color: rgba(14, 165, 233, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .ai-sug-head {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #38bdf8;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 4px;
    }
    .ai-sug-sub {
        color: #64748b;
        font-size: 11px;
        line-height: 1.4;
    }

    /* Message Bubbles */
    .ai-bubble-row {
        display: flex;
        gap: 12px;
        max-width: 88%;
        animation: bubbleFadeIn 0.2s ease-out;
    }
    @keyframes bubbleFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ai-bubble-row.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }
    .ai-bubble-row.assistant {
        align-self: flex-start;
    }
    .ai-bubble-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .ai-bubble-avatar.user {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: white;
    }
    .ai-bubble-avatar.assistant {
        background: linear-gradient(135deg, #0284c7, #06b6d4);
        color: white;
    }
    .ai-bubble-content {
        display: flex;
        flex-direction: column;
    }
    .ai-bubble {
        padding: 14px 18px;
        border-radius: 18px;
        font-size: 13.5px;
        line-height: 1.65;
        word-break: break-word;
        position: relative;
    }
    .ai-bubble-row.user .ai-bubble {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: white;
        border-top-right-radius: 4px;
    }
    .ai-bubble-row.assistant .ai-bubble {
        background: #0e1a2d;
        color: #e2e8f0;
        border: 1px solid rgba(14, 165, 233, 0.25);
        border-top-left-radius: 4px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }
    .ai-bubble-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 6px;
        padding: 0 4px;
    }
    .ai-bubble-time {
        font-size: 10px;
        color: #64748b;
    }
    .ai-copy-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .ai-copy-btn:hover {
        color: white;
        background: rgba(255,255,255,0.1);
    }

    /* Markdown styling */
    .ai-bubble strong { color: #7dd3fc; font-weight: 700; }
    .ai-bubble code {
        background: #050b14;
        padding: 2px 6px;
        border-radius: 5px;
        font-size: 12px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: #34d399;
        border: 1px solid rgba(255,255,255,0.06);
    }
    .ai-bubble pre {
        background: #050b14;
        padding: 12px 14px;
        border-radius: 8px;
        overflow-x: auto;
        margin: 8px 0;
        border: 1px solid rgba(255,255,255,0.08);
    }
    .ai-bubble pre code { background: none; border: none; padding: 0; }
    .ai-bubble ul, .ai-bubble ol { margin: 8px 0 8px 20px; }
    .ai-bubble li { margin: 4px 0; }
    .ai-bubble hr { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 12px 0; }
    .ai-md-h1 { color: #f0f9ff; font-size: 16px; font-weight: 800; margin: 14px 0 8px; padding-bottom: 4px; border-bottom: 1px solid rgba(14, 165, 233, 0.25); }
    .ai-md-h2 { color: #bae6fd; font-size: 14.5px; font-weight: 700; margin: 12px 0 6px; }
    .ai-md-h3 { color: #7dd3fc; font-size: 13.5px; font-weight: 700; margin: 10px 0 4px; }
    .ai-md-quote { border-left: 3px solid #0ea5e9; padding: 6px 12px; margin: 8px 0; color: #cbd5e1; font-style: italic; background: rgba(14, 165, 233, 0.08); border-radius: 0 8px 8px 0; }
    .ai-md-bullet { display: flex; align-items: flex-start; gap: 8px; margin: 4px 0; line-height: 1.6; }
    .ai-bullet-dot { color: #0ea5e9; font-weight: 800; flex-shrink: 0; }
    .ai-md-num { display: flex; align-items: flex-start; gap: 8px; margin: 4px 0; line-height: 1.6; }
    .ai-num-badge { color: #38bdf8; font-weight: 700; flex-shrink: 0; font-size: 12px; }

    /* Typing dots */
    .ai-typing-box {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 12px 16px;
        background: #0e1a2d;
        border-radius: 14px;
        border: 1px solid rgba(14, 165, 233, 0.25);
        width: fit-content;
    }
    .ai-typing-box span {
        width: 7px;
        height: 7px;
        background: #0ea5e9;
        border-radius: 50%;
        animation: aiTypeBounce 1.2s infinite ease-in-out;
    }
    .ai-typing-box span:nth-child(2) { animation-delay: 0.2s; }
    .ai-typing-box span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes aiTypeBounce {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.4; }
        40%            { transform: scale(1.3); opacity: 1; }
    }

    /* Input Dock */
    .ai-input-dock {
        padding: 16px 24px;
        background: #08111e;
        border-top: 1px solid rgba(14, 165, 233, 0.18);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .ai-input-card {
        background: #0d1a2d;
        border: 1px solid rgba(14, 165, 233, 0.3);
        border-radius: 16px;
        padding: 10px 14px;
        display: flex;
        align-items: flex-end;
        gap: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .ai-input-card:focus-within {
        border-color: #0ea5e9;
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.25);
    }
    #ai-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: #f1f5f9;
        font-size: 13.5px;
        line-height: 1.5;
        resize: none;
        max-height: 140px;
        min-height: 38px;
        font-family: inherit;
    }
    #ai-input::placeholder { color: #64748b; }
    .ai-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .ai-send-btn:hover:not(:disabled) {
        transform: scale(1.05);
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
    }
    .ai-send-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    .ai-input-hint {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: #475569;
        padding: 0 4px;
    }

    /* Mobile Adaptations */
    @media (max-width: 768px) {
        #ai-full-modal { padding: 0; }
        .ai-workspace { height: 100vh; max-height: 100vh; border-radius: 0; border: none; }
        .ai-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 10;
            box-shadow: 10px 0 30px rgba(0,0,0,0.8);
        }
        .ai-sidebar.collapsed {
            transform: translateX(-100%);
            width: 280px;
        }
        #ai-messages { padding: 16px; }
        .ai-input-dock { padding: 12px 16px; }
    }
</style>

{{-- Floating Quick Trigger Button --}}
<button id="ai-chat-btn" onclick="toggleFullAiModal()" title="Buka Gemini AI Assistant">
    <svg id="ai-btn-icon" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
    </svg>
</button>

{{-- Fullscreen AI Assistant Modal --}}
<div id="ai-full-modal" role="dialog" aria-modal="true" aria-label="Gemini AI Assistant Workspace">
    <div class="ai-workspace">
        {{-- Top Navigation Bar --}}
        <header class="ai-topbar">
            <div class="ai-topbar-left">
                <button type="button" class="ai-icon-btn" onclick="toggleAiSidebar()" title="Buka/Tutup Riwayat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <div class="ai-brand-badge">
                    <div class="ai-brand-avatar">✨</div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                            <h3 class="text-white text-sm font-bold tracking-wide">Gemini AI</h3>
                            
                            {{-- Interactive Model Picker Dropdown --}}
                            <div class="ai-model-picker-wrap" id="ai-model-picker-container">
                                <button type="button" class="ai-model-pill-btn" onclick="toggleAiModelPicker(event)" id="ai-current-model-btn" title="Klik untuk memilih model AI & lihat sisa kuota">
                                    <span class="ai-model-dot" id="ai-model-dot" style="background:#10b981;"></span>
                                    <span id="ai-current-model-name">{{ $aiCurrentModel }}</span>
                                    <span class="ai-quota-mini-tag status-emerald" id="ai-current-quota-tag">Masih Banyak</span>
                                    <svg class="w-3 h-3 ml-0.5 opacity-70 transition-transform duration-200" id="ai-model-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                
                                {{-- Dropdown Modal / Popup List --}}
                                <div id="ai-model-dropdown" class="ai-model-dropdown hidden" onclick="event.stopPropagation()">
                                    <div class="ai-model-dropdown-header">
                                        <div class="ai-model-dropdown-title">
                                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span>Pilih Model AI</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">Status Kuota Live</span>
                                    </div>
                                    <div id="ai-model-cards-list" class="ai-model-cards-list">
                                        {{-- Rendered dynamically by renderModelPicker() --}}
                                    </div>
                                    <div class="ai-model-dropdown-footer">
                                        <svg class="w-3.5 h-3.5 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Tiap model memiliki kuota terpisah per jam. Jika satu menipis, pilih model lain!</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Deep Thinking Badge --}}
                            <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-1 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30" title="Mode Penalaran Mendalam (Deep Thinking) Aktif untuk Analisis Akurat">
                                <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                <span>Deep Thinking</span>
                            </span>
                        </div>
                        <p class="text-slate-400 text-xs truncate max-w-[280px]" id="ai-active-topic">Asisten Medis IMEROLEPLAY</p>
                    </div>
                </div>
            </div>

            <div class="ai-topbar-right">
                <button type="button" class="ai-newchat-btn" onclick="startNewAiChat()" title="Mulai Sesi Chat Baru">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Chat Baru</span>
                </button>
                <div class="hidden sm:inline-flex items-center gap-1.5 text-xs text-slate-300 px-3 py-1.5 bg-white/5 border border-white/10 rounded-xl" id="ai-topbar-quota-box" title="Sisa kuota model aktif per jam">
                    <span id="ai-topbar-model-short" class="text-slate-400">Sisa:</span>
                    <strong id="ai-remaining-badge" class="text-emerald-400 font-bold">25</strong>
                    <span id="ai-remaining-max" class="text-slate-400">/25</span>
                    <span class="text-[10px] text-slate-500">sesi/jam</span>
                </div>
                <button type="button" class="ai-close-btn" onclick="closeFullAiModal()" title="Tutup Modal (ESC)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </header>

        {{-- Body: Left Sidebar (History) + Main Chat --}}
        <div class="ai-body">
            {{-- Left Sidebar --}}
            <aside class="ai-sidebar" id="ai-sidebar">
                <div class="ai-sidebar-header">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-sky-300 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Riwayat Pencarian
                        </span>
                        <span class="text-[10px] px-2 py-0.5 bg-sky-500/20 text-sky-300 font-semibold rounded-full" id="ai-history-count">0</span>
                    </div>
                    <div class="ai-sidebar-search">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        <input type="text" id="ai-search-query" placeholder="Cari riwayat pertanyaan..." oninput="filterAiHistory()">
                    </div>
                </div>

                {{-- History List --}}
                <div class="ai-history-list" id="ai-history-items">
                    {{-- Dynamically populated --}}
                </div>

                {{-- Sidebar Footer --}}
                <div class="ai-sidebar-footer">
                    <button type="button" class="ai-clear-all-btn" onclick="clearAllAiSessions()">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        <span>Hapus Semua Riwayat</span>
                    </button>
                </div>
            </aside>

            {{-- Main Chat Area --}}
            <main class="ai-main-chat">
                <div id="ai-messages">
                    {{-- Default Welcome Hero / Conversation Messages --}}
                </div>

                {{-- Input Dock --}}
                <div class="ai-input-dock">
                    <div class="ai-input-card">
                        <textarea
                            id="ai-input"
                            placeholder="Tanyakan SOP operasi, kode medis, triage, formulir, atau hal lainnya... (Enter untuk kirim)"
                            rows="1"
                            maxlength="2000"
                        ></textarea>
                        <button type="button" id="ai-send-btn" class="ai-send-btn" onclick="sendAiMessage()" title="Kirim">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                            </svg>
                        </button>
                    </div>
                    <div class="ai-input-hint">
                        <span>💡 Tekan <strong>Enter</strong> untuk kirim, <strong>Shift+Enter</strong> baris baru. Tekan <strong>ESC</strong> untuk menutup.</span>
                        <span id="ai-char-count">0/2000</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
    // ====== AI Chat Workspace & Session History ======
    const AI_USER_ID      = '{{ Auth::id() ?? "guest" }}';
    const AI_STORAGE_KEY  = 'ime_ai_sessions_user_' + AI_USER_ID;
    const AI_CHAT_ROUTE   = '{{ route("staff.ai-chat") }}';
    const AI_MODELS_ROUTE = '{{ route("staff.ai-chat.models") }}';
    const AI_CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const AI_USER_NAME    = '{{ Auth::user()->name ?? "Staf" }}';

    let aiModalOpen       = false;
    let aiLoading         = false;
    let aiSessions        = [];
    let aiActiveSessionId = null;

    // AI Models State & Selection
    let aiModelsData      = @json($aiInitialQuotas);
    let aiSelectedModel   = localStorage.getItem('ime_ai_selected_model') || '{{ $aiCurrentModel }}';

    // Verify aiSelectedModel exists in available configs
    if (!aiModelsData[aiSelectedModel]) {
        aiSelectedModel = Object.keys(aiModelsData)[0] || 'gemini-3.5-flash';
        localStorage.setItem('ime_ai_selected_model', aiSelectedModel);
    }

    // Toggle and Close Model Picker
    function toggleAiModelPicker(e) {
        if (e) e.stopPropagation();
        const dropdown = document.getElementById('ai-model-dropdown');
        const chevron = document.getElementById('ai-model-chevron');
        if (!dropdown) return;

        const isHidden = dropdown.classList.contains('hidden');
        if (isHidden) {
            dropdown.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            renderModelPicker();
        } else {
            dropdown.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }

    function closeAiModelPicker() {
        const dropdown = document.getElementById('ai-model-dropdown');
        const chevron = document.getElementById('ai-model-chevron');
        if (dropdown) dropdown.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    // Select Active Model
    function selectAiModel(key) {
        if (!aiModelsData[key]) return;
        aiSelectedModel = key;
        localStorage.setItem('ime_ai_selected_model', key);
        renderModelPicker();
        closeAiModelPicker();
    }

    // Render Model Picker dropdown and update topbar indicator
    function renderModelPicker() {
        const list = document.getElementById('ai-model-cards-list');
        const currentModel = aiModelsData[aiSelectedModel] || Object.values(aiModelsData)[0];

        // Update Topbar Button display
        if (currentModel) {
            const nameEl     = document.getElementById('ai-current-model-name');
            const dotEl      = document.getElementById('ai-model-dot');
            const tagEl      = document.getElementById('ai-current-quota-tag');
            const remBadge   = document.getElementById('ai-remaining-badge');
            const maxBadge   = document.getElementById('ai-remaining-max');
            const shortLabel = document.getElementById('ai-topbar-model-short');

            if (nameEl) nameEl.textContent = currentModel.name;
            if (dotEl) {
                dotEl.style.backgroundColor = currentModel.dot_color;
                dotEl.style.color = currentModel.dot_color;
            }
            if (tagEl) {
                tagEl.textContent = currentModel.status + ` (${currentModel.remaining})`;
                tagEl.className = 'ai-quota-mini-tag status-' + currentModel.status_color;
            }
            if (remBadge) {
                remBadge.textContent = currentModel.remaining;
                remBadge.className = currentModel.status_color === 'emerald'
                    ? 'text-emerald-400 font-bold'
                    : (currentModel.status_color === 'amber' ? 'text-amber-400 font-bold' : 'text-red-400 font-bold');
            }
            if (maxBadge) maxBadge.textContent = '/' + currentModel.limit;
            if (shortLabel) shortLabel.textContent = currentModel.badge + ':';
        }

        if (!list) return;

        // Render card for each model
        list.innerHTML = Object.values(aiModelsData).map(m => {
            const isActive   = m.key === aiSelectedModel;
            const fillColor  = m.dot_color;
            const statusIcon = m.status_color === 'emerald' ? '🟢' : (m.status_color === 'amber' ? '🟡' : '🔴');

            return `
                <div class="ai-model-card ${isActive ? 'active' : ''}" onclick="selectAiModel('${m.key}')" title="Pilih model ${m.name}">
                    <div class="ai-model-card-top">
                        <div class="ai-model-name-wrap">
                            <div class="ai-model-check-circle">
                                ${isActive ? '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : ''}
                            </div>
                            <span class="ai-model-name">${m.name}</span>
                            <span class="ai-model-badge">${m.badge}</span>
                        </div>
                        <span class="ai-model-status-pill status-${m.status_color}">
                            ${statusIcon} ${m.status}
                        </span>
                    </div>
                    <div class="ai-model-desc">${m.desc}</div>
                    <div class="ai-model-quota-bar-track">
                        <div class="ai-model-quota-bar-fill" style="width: ${m.percent}%; background-color: ${fillColor};"></div>
                    </div>
                    <div class="ai-model-quota-meta">
                        <span>Sisa: <strong style="color:${fillColor}; font-weight:700;">${m.remaining}</strong>/${m.limit} sesi/jam</span>
                        <span>${m.percent}% tersedia</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Fetch live quota status from server
    async function fetchLiveAiModels() {
        try {
            const res = await fetch(AI_MODELS_ROUTE, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && data.models) {
                aiModelsData = data.models;
                renderModelPicker();
            }
        } catch (e) {
            console.warn('[AI] Failed to fetch live models:', e);
        }
    }

    // Load saved sessions from localStorage
    function loadAiSessions() {
        try {
            const raw = localStorage.getItem(AI_STORAGE_KEY);
            aiSessions = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(aiSessions)) aiSessions = [];
        } catch (e) {
            aiSessions = [];
        }
    }

    function saveAiSessions() {
        try {
            localStorage.setItem(AI_STORAGE_KEY, JSON.stringify(aiSessions));
        } catch (e) {
            console.error('[AI] Failed to save sessions:', e);
        }
    }

    // Modal Visibility
    function openFullAiModal() {
        aiModalOpen = true;
        const modal = document.getElementById('ai-full-modal');
        modal.classList.add('visible');
        renderHistoryList();
        renderModelPicker();
        fetchLiveAiModels();

        if (!aiActiveSessionId && aiSessions.length > 0) {
            loadSession(aiSessions[0].id);
        } else if (!aiActiveSessionId) {
            renderWelcomeScreen();
        }

        setTimeout(() => document.getElementById('ai-input')?.focus(), 200);
    }

    function closeFullAiModal() {
        aiModalOpen = false;
        closeAiModelPicker();
        const modal = document.getElementById('ai-full-modal');
        modal.classList.remove('visible');
    }

    function toggleFullAiModal() {
        if (aiModalOpen) {
            closeFullAiModal();
        } else {
            openFullAiModal();
        }
    }

    function toggleAiSidebar() {
        const sidebar = document.getElementById('ai-sidebar');
        sidebar.classList.toggle('collapsed');
    }

    // Start fresh chat
    function startNewAiChat() {
        aiActiveSessionId = null;
        document.getElementById('ai-active-topic').textContent = 'Percakapan Baru';
        renderWelcomeScreen();
        renderHistoryList();
        document.getElementById('ai-input')?.focus();
    }

    // Render Welcome Hero Screen
    function renderWelcomeScreen() {
        const msgs = document.getElementById('ai-messages');
        msgs.innerHTML = `
            <div class="ai-welcome-hero">
                <div class="ai-hero-icon">🏥</div>
                <h2 class="ai-hero-title">Halo, ${AI_USER_NAME}!</h2>
                <p class="ai-hero-desc">Saya asisten resmi MEDIC-IMEROLEPLAY. Tanyakan SOP tindakan medis, triage gawat darurat, penulisan rekam medis, obat-obatan, alur rujukan, atau hal lainnya sesuai kebutuhan Anda.</p>
                <div class="ai-suggestion-grid">
                    <div class="ai-suggestion-card" onclick="sendQuickPrompt('Bagaimana SOP dan tahapan operasi bedah (surgery) medis roleplay di Alta Hospital?')">
                        <div class="ai-sug-head">📋 SOP Tindakan Operasi</div>
                        <div class="ai-sug-sub">Langkah persiapan anestesi, asepsis, insisi, hingga pasca-operasi</div>
                    </div>
                    <div class="ai-suggestion-card" onclick="sendQuickPrompt('Apa langkah penanganan dan triage awal pasien kecelakaan lalu lintas (KLL)?')">
                        <div class="ai-sug-head">🚨 Triage Pasien KLL</div>
                        <div class="ai-sug-sub">Primary survey ABCDE, imobilisasi leher, dan resusitasi cairan</div>
                    </div>
                    <div class="ai-suggestion-card" onclick="sendQuickPrompt('Berikan contoh penulisan rekam medis operasi yang rapi dengan format diagnosa & tindakan.')">
                        <div class="ai-sug-head">📝 Format Rekam Medis</div>
                        <div class="ai-sug-sub">Contoh form diagnosa, laporan operasi, dan tindakan DPJP</div>
                    </div>
                    <div class="ai-suggestion-card" onclick="sendQuickPrompt('Bagaimana panduan penggunaan perintah /me dan /do yang realistis saat memeriksa pasien?')">
                        <div class="ai-sug-head">🎭 Panduan /me & /do Medis</div>
                        <div class="ai-sug-sub">Contoh roleplay pemasangan infus, nebulizer, dan defibrilator</div>
                    </div>
                </div>
            </div>
        `;
    }

    function sendQuickPrompt(text) {
        const input = document.getElementById('ai-input');
        if (input) {
            input.value = text;
            sendAiMessage();
        }
    }

    // Render Session History in Sidebar
    function renderHistoryList(filterText = '') {
        const container = document.getElementById('ai-history-items');
        const countBadge = document.getElementById('ai-history-count');
        if (!container) return;

        let filtered = aiSessions;
        if (filterText.trim()) {
            const q = filterText.toLowerCase();
            filtered = aiSessions.filter(s => (s.title || '').toLowerCase().includes(q));
        }

        if (countBadge) countBadge.textContent = aiSessions.length;

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 px-4 text-slate-500 text-xs">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    ${filterText ? 'Tidak ada riwayat yang cocok.' : 'Belum ada riwayat chat.<br>Mulai ajukan pertanyaan!'}
                </div>
            `;
            return;
        }

        container.innerHTML = filtered.map(s => {
            const isActive = s.id === aiActiveSessionId;
            const dateStr = formatSessionDate(s.createdAt);
            const titleSafe = escapeHtml(s.title || 'Percakapan');
            return `
                <div class="ai-history-item ${isActive ? 'active' : ''}" onclick="loadSession('${s.id}')" title="${titleSafe}">
                    <div style="flex:1; min-width:0;">
                        <div class="ai-history-title">${titleSafe}</div>
                        <div class="ai-history-time">${dateStr} • ${s.messages ? s.messages.length : 0} pesan</div>
                    </div>
                    <button type="button" class="ai-item-del-btn" onclick="deleteAiSession('${s.id}', event)" title="Hapus riwayat ini">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    function filterAiHistory() {
        const val = document.getElementById('ai-search-query')?.value ?? '';
        renderHistoryList(val);
    }

    function loadSession(sessionId) {
        const session = aiSessions.find(s => s.id === sessionId);
        if (!session) return;

        aiActiveSessionId = sessionId;
        document.getElementById('ai-active-topic').textContent = session.title || 'Percakapan';

        const msgs = document.getElementById('ai-messages');
        msgs.innerHTML = '';

        if (session.messages && session.messages.length > 0) {
            session.messages.forEach(m => {
                appendBubbleToDom(m.text, m.role, m.time);
            });
        } else {
            renderWelcomeScreen();
        }

        renderHistoryList();
        scrollToBottom();
    }

    function deleteAiSession(sessionId, event) {
        if (event) event.stopPropagation();
        aiSessions = aiSessions.filter(s => s.id !== sessionId);
        saveAiSessions();

        if (aiActiveSessionId === sessionId) {
            startNewAiChat();
        } else {
            renderHistoryList();
        }
    }

    function clearAllAiSessions() {
        if (!confirm('Apakah Anda yakin ingin menghapus seluruh riwayat pencarian & percakapan AI?')) return;
        aiSessions = [];
        saveAiSessions();
        startNewAiChat();
    }

    function formatSessionDate(isoStr) {
        if (!isoStr) return '';
        try {
            const d = new Date(isoStr);
            const now = new Date();
            const diffHours = (now - d) / (1000 * 60 * 60);
            if (diffHours < 24 && now.getDate() === d.getDate()) {
                return 'Hari ini, ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
            return d.toLocaleDateString([], {day: 'numeric', month: 'short'}) + ', ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        } catch (e) {
            return '';
        }
    }

    function formatCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatMarkdown(text) {
        let html = escapeHtml(text);
        
        // Code blocks ```...```
        html = html.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>');
        html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
        
        // Inline code `...`
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        
        // Bold & Italic
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Headings (#, ##, ###)
        html = html.replace(/^### (.+)$/gm, '<h4 class="ai-md-h3">$1</h4>');
        html = html.replace(/^## (.+)$/gm, '<h3 class="ai-md-h2">$1</h3>');
        html = html.replace(/^# (.+)$/gm, '<h2 class="ai-md-h1">$1</h2>');
        
        // Blockquotes (> ...)
        html = html.replace(/^>\s+(.+)$/gm, '<div class="ai-md-quote">$1</div>');
        
        // Numbered lists (1. 2. 3.)
        html = html.replace(/^(\d+)\.\s+(.+)$/gm, '<div class="ai-md-num"><span class="ai-num-badge">$1.</span><div>$2</div></div>');
        
        // Bullet lists (- or * or •)
        html = html.replace(/^[\-\*•]\s+(.+)$/gm, '<div class="ai-md-bullet"><span class="ai-bullet-dot">•</span><div>$1</div></div>');
        
        // Line breaks
        html = html.replace(/\n/g, '<br>');
        
        return html;
    }

    function appendBubbleToDom(text, role, timeStr) {
        // Remove welcome hero if present
        document.querySelector('.ai-welcome-hero')?.remove();

        const msgs = document.getElementById('ai-messages');
        const row  = document.createElement('div');
        row.className = 'ai-bubble-row ' + role;

        const time = timeStr || formatCurrentTime();
        const avatar = role === 'user' ? '👤' : '✨';

        let innerContent = '';
        if (role === 'assistant') {
            innerContent = `
                <div class="ai-bubble-avatar ${role}">${avatar}</div>
                <div class="ai-bubble-content" style="max-width:calc(100% - 46px);">
                    <div class="ai-bubble" data-raw="${escapeHtml(text)}">
                        ${formatMarkdown(text)}
                    </div>
                    <div class="ai-bubble-footer">
                        <span class="ai-bubble-time">${time}</span>
                        <button type="button" class="ai-copy-btn" onclick="copyAiResponse(this)">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Salin</span>
                        </button>
                    </div>
                </div>
            `;
        } else {
            innerContent = `
                <div class="ai-bubble-avatar ${role}">${avatar}</div>
                <div class="ai-bubble-content">
                    <div class="ai-bubble">${escapeHtml(text)}</div>
                    <div class="ai-bubble-footer" style="justify-content:flex-end;">
                        <span class="ai-bubble-time">${time}</span>
                    </div>
                </div>
            `;
        }

        row.innerHTML = innerContent;
        msgs.appendChild(row);
        scrollToBottom();
        return row;
    }

    function copyAiResponse(btn) {
        const bubble = btn.closest('.ai-bubble-content')?.querySelector('.ai-bubble');
        const raw = bubble?.getAttribute('data-raw') || bubble?.innerText || '';
        navigator.clipboard.writeText(raw).then(() => {
            const span = btn.querySelector('span');
            if (span) span.textContent = 'Tersalin!';
            setTimeout(() => { if (span) span.textContent = 'Salin'; }, 2000);
        });
    }

    function showTypingIndicator() {
        document.querySelector('.ai-welcome-hero')?.remove();
        const msgs = document.getElementById('ai-messages');
        const row = document.createElement('div');
        row.className = 'ai-bubble-row assistant';
        row.id = 'ai-typing-indicator';
        row.innerHTML = `
            <div class="ai-bubble-avatar assistant">🧠</div>
            <div class="ai-typing-box" style="display:inline-flex; align-items:center; gap:8px;">
                <span></span><span></span><span></span>
                <span style="font-size:11px; color:#38bdf8; font-weight:600; margin-left:4px;">Menalar & menganalisis informasi...</span>
            </div>
        `;
        msgs.appendChild(row);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        document.getElementById('ai-typing-indicator')?.remove();
    }

    function scrollToBottom() {
        const msgs = document.getElementById('ai-messages');
        if (msgs) msgs.scrollTop = msgs.scrollHeight;
    }

    // Send AI message handler
    async function sendAiMessage() {
        if (aiLoading) return;

        const input = document.getElementById('ai-input');
        const text  = (input?.value || '').trim();
        if (!text) return;

        // Clear input
        input.value = '';
        input.style.height = 'auto';
        document.getElementById('ai-char-count').textContent = '0/2000';

        // Check or create active session
        let currentSession = aiSessions.find(s => s.id === aiActiveSessionId);
        if (!currentSession) {
            const cleanTitle = text.length > 35 ? text.substring(0, 35) + '...' : text;
            currentSession = {
                id: 'session_' + Date.now(),
                title: cleanTitle,
                createdAt: new Date().toISOString(),
                messages: []
            };
            aiSessions.unshift(currentSession);
            aiActiveSessionId = currentSession.id;
            document.getElementById('ai-active-topic').textContent = cleanTitle;
        }

        const userTime = formatCurrentTime();
        // Append user message
        appendBubbleToDom(text, 'user', userTime);
        currentSession.messages.push({ role: 'user', text: text, time: userTime });
        saveAiSessions();
        renderHistoryList();

        // Build history payload for Gemini API
        const apiHistory = [];
        (currentSession.messages || []).slice(-10).forEach(m => {
            if (m.role === 'user' || m.role === 'assistant') {
                const cleanText = (m.text || '').trim();
                // Exclude empty text or system error notices from AI context
                if (cleanText && !cleanText.startsWith('⚠️')) {
                    apiHistory.push({
                        role: m.role === 'user' ? 'user' : 'model',
                        text: cleanText
                    });
                }
            }
        });
        // Remove the last message from history array since it's the current user message
        apiHistory.pop();

        aiLoading = true;
        document.getElementById('ai-send-btn').disabled = true;
        showTypingIndicator();

        try {
            const res = await fetch(AI_CHAT_ROUTE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': AI_CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: text,
                    history: apiHistory,
                    model: aiSelectedModel,
                }),
            });

            hideTypingIndicator();
            const data = await res.json();
            const aiTime = formatCurrentTime();

            // Refresh quota models data from response if present
            if (data.models) {
                aiModelsData = data.models;
                renderModelPicker();
            }

            if (data.success && data.message) {
                appendBubbleToDom(data.message, 'assistant', aiTime);
                currentSession.messages.push({ role: 'assistant', text: data.message, time: aiTime });
                saveAiSessions();
                renderHistoryList();
            } else {
                appendBubbleToDom('⚠️ ' + (data.message || 'Terjadi kesalahan saat memproses pertanyaan.'), 'assistant', aiTime);
                // Roll back user message from session messages so consecutive unanswered turns don't accumulate
                if (currentSession.messages.length > 0 && currentSession.messages[currentSession.messages.length - 1].role === 'user') {
                    currentSession.messages.pop();
                    saveAiSessions();
                    renderHistoryList();
                }
                if (res.status === 429) {
                    // Open model picker so user can immediately choose another model
                    setTimeout(() => toggleAiModelPicker(), 500);
                }
            }
        } catch (err) {
            hideTypingIndicator();
            appendBubbleToDom('⚠️ Gagal terhubung ke server. Periksa koneksi internet Anda.', 'assistant', formatCurrentTime());
            if (currentSession.messages.length > 0 && currentSession.messages[currentSession.messages.length - 1].role === 'user') {
                currentSession.messages.pop();
                saveAiSessions();
                renderHistoryList();
            }
        } finally {
            aiLoading = false;
            document.getElementById('ai-send-btn').disabled = false;
            document.getElementById('ai-input')?.focus();
        }
    }

    // Keyboard and input listeners
    document.addEventListener('DOMContentLoaded', function () {
        loadAiSessions();
        renderModelPicker();

        const input = document.getElementById('ai-input');
        if (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendAiMessage();
                }
            });

            input.addEventListener('input', function () {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 140) + 'px';
                const counter = document.getElementById('ai-char-count');
                if (counter) counter.textContent = this.value.length + '/2000';
            });
        }

        // Close on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && aiModalOpen) {
                closeFullAiModal();
            }
        });

        // Close modal when clicking on backdrop outside workspace
        const modal = document.getElementById('ai-full-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeFullAiModal();
                }
            });
        }

        // Close model dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const picker = document.getElementById('ai-model-picker-container');
            if (picker && !picker.contains(e.target)) {
                closeAiModelPicker();
            }
        });
    });
</script>
@endif
@endauth

</body>

</html>