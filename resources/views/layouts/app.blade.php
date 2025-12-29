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
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/motionlife-logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Inline Image Error Handler - Must load early to prevent 404 errors -->
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
        /* Cache busting: {{ time() }}
        */ :root {
            --ml-bg-start: #0c4a6e;
            /* sky-900 - biru gelap medis */
            --ml-bg-mid: #075985;
            /* sky-800 */
            --ml-bg-end: #0369a1;
            /* sky-700 */
        }

        /* CSS Variables */
        :root {
            --ml-primary: #0ea5e9;
            /* sky-500 - biru medis */
            --ml-primary-700: #0284c7;
            /* sky-600 */
            --ml-secondary: #059669;
            /* emerald-600 - hijau medis */
            --ml-accent: #06b6d4;
            /* cyan-500 - cyan medis */
            --ml-success: #10b981;
            /* emerald-500 */
            --ml-warning: #f59e0b;
            /* amber-500 */
            --ml-danger: #ef4444;
            /* red-500 */
            --ml-muted: #94a3b8;
            /* slate-400 */
            --ml-surface: rgba(12, 74, 110, 0.9);
            --ml-border: rgba(14, 165, 233, 0.3);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, var(--ml-bg-start) 0%, var(--ml-bg-mid) 50%, var(--ml-bg-end) 100%);
            min-height: 100vh;
        }

        .glass-effect {
            background: var(--ml-surface);
            backdrop-filter: blur(16px);
            border: 1px solid var(--ml-border);
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
        }

        /* Notification Container Position - Adjusted to be below navbar */
        .notification-container {
            position: fixed;
            top: 80px;
            /* Adjusted to be below the navbar (approx 64px + padding) */
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
            width: 100%;
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
                top: 70px;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="glass-effect fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('public.index') }}" class="flex-shrink-0 flex items-center group">
                        <!-- Logo MOTIONLIFE ROLEPLAY -->
                        <div
                            class="h-10 w-10 flex items-center justify-center bg-white rounded-lg elegant-shadow group-hover:shadow-lg transition-all duration-300">
                            <img src="{{ asset('images/motionlife-logo.png') }}" alt="MOTIONLIFE ROLEPLAY"
                                class="h-6 w-6 object-contain">
                        </div>
                        <div class="ml-3">
                            <div
                                class="text-lg font-semibold text-white group-hover:text-gray-200 transition-colors duration-300">
                                MOTIONLIFE</div>
                            <div
                                class="text-xs text-gray-300 -mt-1 group-hover:text-gray-400 transition-colors duration-300">
                                Portal Medis</div>
                        </div>
                    </a>
                </div>

                <!-- Mobile Menu Button (hidden on screens larger than sm) -->
                <div class="sm:hidden flex items-center">
                    <button id="mobile-menu-button"
                        class="text-white p-2 rounded-lg hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-all duration-300">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
                <div class="hidden sm:flex items-center space-x-2 sm:space-x-4" id="desktop-menu">
                    @guest
                        <!-- Show Recruitment button only when not logged in -->
                        <button onclick="openRecruitmentModal()"
                            class="bg-white bg-opacity-10 text-white px-4 py-2 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center mr-2">
                            <i class="fas fa-user-plus mr-2"></i>
                            <span>Recruitment</span>
                        </button>
                    @endguest

                    @auth
                        <!-- User Info -->
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-white text-sm font-medium">Selamat datang,</div>
                                <div class="text-gray-300 text-xs">{{ auth()->user()->name }}</div>
                            </div>
                        </div>

                        <!-- Navigation Menu -->
                        <div class="flex items-center space-x-2">
                            <!-- Primary Menu Items -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('staff.dashboard') }}"
                                    class="bg-white bg-opacity-10 text-white px-4 py-2 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    <span>Dashboard</span>
                                </a>

                                <a href="{{ route('staff.profile') }}"
                                    class="bg-white bg-opacity-10 text-white px-4 py-2 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center">
                                    <i class="fas fa-user-cog mr-2"></i>
                                    <span>Profil</span>
                                </a>

                                @if(auth()->user()->hasPermission('manage_users'))
                                    <a href="{{ route('admin.staff.index') }}"
                                        class="bg-white bg-opacity-10 text-white px-4 py-2 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center">
                                        <i class="fas fa-users-cog mr-2"></i>
                                        <span>Staf</span>
                                    </a>
                                @endif


                                @if(auth()->user()->hasPermission('view_reports'))
                                    <a href="{{ route('admin.attendance-reports.index') }}"
                                        class="bg-white bg-opacity-10 text-white px-4 py-2 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center">
                                        <i class="fas fa-chart-bar mr-2"></i>
                                        <span>Laporan</span>
                                    </a>
                                @endif
                            </div>

                            <!-- Payroll Menu Dropdown -->
                            <div class="relative group">
                                <button
                                    class="bg-white bg-opacity-10 text-white px-4 py-2 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center">
                                    <i class="fas fa-dollar-sign mr-2"></i>
                                    <span>Gaji</span>
                                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-2">
                                        @if(auth()->user()->hasPermission('manage_payroll'))
                                            <a href="{{ route('admin.payroll.index') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-list mr-2"></i>
                                                Daftar Gaji
                                            </a>
                                            <a href="{{ route('admin.salary-settings.index') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-cog mr-2"></i>
                                                Atur Gaji
                                            </a>
                                        @else
                                            <a href="{{ route('staff.payroll.index') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-list mr-2"></i>
                                                Gaji Saya
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Logout Button -->
                            <form method="POST" action="{{ route('staff.logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-red-400 border-opacity-30 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('staff.login') }}"
                            class="bg-white bg-opacity-10 text-white px-4 py-2 sm:px-6 rounded-lg hover:bg-opacity-20 transition-all duration-300 text-xs sm:text-sm font-medium backdrop-blur-sm border border-white border-opacity-20 flex items-center">
                            <i class="fas fa-user-md mr-2"></i>
                            <span>Login Staf</span>
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="sm:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @guest
                    <!-- Show Recruitment button only when not logged in -->
                    <button onclick="openRecruitmentModal()"
                        class="w-full text-left text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-user-plus w-6 mr-2"></i>Recruitment
                    </button>
                @endguest

                @auth
                    <div class="text-white text-sm font-medium px-3 py-2">Selamat datang, {{ auth()->user()->name }}</div>
                    <a href="{{ route('staff.dashboard') }}"
                        class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                            class="fas fa-tachometer-alt w-6 mr-2"></i>Dashboard</a>
                    <a href="{{ route('staff.profile') }}"
                        class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                            class="fas fa-user-cog w-6 mr-2"></i>Profil</a>
                    @if(auth()->user()->hasPermission('manage_users'))
                        <a href="{{ route('admin.staff.index') }}"
                            class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                                class="fas fa-users-cog w-6 mr-2"></i>Staf</a>
                    @endif
                    @if(auth()->user()->hasPermission('view_reports'))
                        <a href="{{ route('admin.attendance-reports.index') }}"
                            class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                                class="fas fa-chart-bar w-6 mr-2"></i>Laporan</a>
                    @endif
                    @if(auth()->user()->hasPermission('manage_payroll'))
                        <a href="{{ route('admin.payroll.index') }}"
                            class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                                class="fas fa-list w-6 mr-2"></i>Daftar Gaji</a>
                        <a href="{{ route('admin.salary-settings.index') }}"
                            class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                                class="fas fa-cog w-6 mr-2"></i>Atur Gaji</a>
                    @else
                        <a href="{{ route('staff.payroll.index') }}"
                            class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium"><i
                                class="fas fa-dollar-sign w-6 mr-2"></i>Gaji Saya</a>
                    @endif
                    <form method="POST" action="{{ route('staff.logout') }}" class="block">
                        @csrf
                        <button type="submit"
                            class="w-full text-left text-gray-300 hover:bg-red-500/20 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                            <i class="fas fa-sign-out-alt w-6 mr-2"></i>Logout
                        </button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('staff.login') }}"
                        class="text-gray-300 hover:bg-white/10 hover:text-white block px-3 py-2 rounded-md text-base font-medium">
                        <i class="fas fa-user-md w-6 mr-2"></i>Login Staf
                    </a>
                @endguest
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
    <footer class="glass-effect mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <div class="flex justify-center items-center mb-4">
                    <!-- Logo MOTIONLIFE ROLEPLAY -->
                    <div class="h-8 w-8 flex items-center justify-center mr-3 bg-white rounded-lg elegant-shadow">
                        <img src="{{ asset('images/motionlife-logo.png') }}" alt="MOTIONLIFE ROLEPLAY"
                            class="h-5 w-5 object-contain">
                    </div>
                    <span class="text-lg font-semibold text-white">MOTIONLIFE ROLEPLAY</span>
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
                    <p class="text-gray-400 text-xs">&copy; 2025 Portal Medis MOTIONLIFE. Semua hak dilindungi develop
                        by tepe-dev.</p>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')

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

            mobileMenuButton.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
            });
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
                'dashboardClockOutForm',
                'activeSessionClockOutForm'
            ];

            if (formsWithHandlers.includes(form.id)) {
                // Let the form's own handler take care of it
                return;
            }

            // Skip forms that should submit normally (public forms with error handling)
            // These forms need to redirect normally to show flash messages
            if (form.id === 'medicalForm' || form.action && form.action.includes('/form/submit')) {
                // Let form submit normally - flash messages will be shown on redirect
                return;
            }

            if (form.method.toLowerCase() === 'post') {
                e.preventDefault();

                // Get action from data-action attribute first (more reliable), fallback to action attribute
                let formAction = form.getAttribute('data-action') || form.getAttribute('action');

                // Validate form action - must not contain Blade syntax
                if (!formAction || formAction.includes('{{') || formAction.includes('route(') || formAction.trim() === '') {
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
</body>

</html>