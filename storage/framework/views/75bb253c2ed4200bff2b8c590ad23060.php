

<?php $__env->startSection('title', 'Dashboard Staf - Portal Medis'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        /* Custom styles for animations, heatmap, and visual enhancements */
        .heatmap-day {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 3px;
        }

        .heatmap-day:hover {
            transform: scale(1.15);
            z-index: 10;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .heatmap-tooltip {
            pointer-events: none;
        }

        .streak-animation {
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.3);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.3);
            }

            50% {
                opacity: 0.8;
                box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.5);
            }
        }

        .contribution-level-0 {
            background-color: #374151;
            border: 1px solid #4b5563;
        }

        .contribution-level-1 {
            background-color: #059669;
            border: 1px solid #10b981;
        }

        .contribution-level-2 {
            background-color: #10b981;
            border: 1px solid #34d399;
        }

        .contribution-level-3 {
            background-color: #34d399;
            border: 1px solid #6ee7b7;
        }

        .contribution-level-4 {
            background-color: #6ee7b7;
            border: 1px solid #a7f3d0;
        }

        .contribution-level-5 {
            background-color: #a7f3d0;
            border: 1px solid #d1fae5;
        }

        /* Custom scrollbar for better aesthetics */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.3) transparent;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(148, 163, 184, 0.1);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }

        /* Enhanced text visibility and card animations */
        .stat-number,
        .leaderboard-number,
        .heatmap-number {
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.5);
            font-weight: 900;
            letter-spacing: -0.025em;
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.3));
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Animation classes */
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Elegant Animations - Subtle and Smooth */
        .elegant-fade-in {
            animation: elegantFadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes elegantFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Elegant Card Hover */
        .elegant-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .elegant-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        /* Subtle Stagger */
        .elegant-stagger {
            opacity: 0;
            animation: elegantFadeIn 0.6s ease-out forwards;
        }

        .elegant-stagger:nth-child(1) {
            animation-delay: 0.05s;
        }

        .elegant-stagger:nth-child(2) {
            animation-delay: 0.1s;
        }

        .elegant-stagger:nth-child(3) {
            animation-delay: 0.15s;
        }

        .elegant-stagger:nth-child(4) {
            animation-delay: 0.2s;
        }

        .elegant-stagger:nth-child(5) {
            animation-delay: 0.25s;
        }

        .elegant-stagger:nth-child(6) {
            animation-delay: 0.3s;
        }

        /* Duty Timer Specific Styles */
        .remaining-time-display {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.05em;
        }

        /* Placeholder styling for duty timer input - Force white color */
        #scheduled_duty_minutes::placeholder {
            color: white !important;
            opacity: 0.9 !important;
        }

        #scheduled_duty_minutes::-webkit-input-placeholder {
            color: white !important;
            opacity: 0.9 !important;
        }

        #scheduled_duty_minutes::-moz-placeholder {
            color: white !important;
            opacity: 0.9 !important;
        }

        #scheduled_duty_minutes:-ms-input-placeholder {
            color: white !important;
            opacity: 0.9 !important;
        }

        #scheduled_duty_minutes:-moz-placeholder {
            color: white !important;
            opacity: 0.9 !important;
        }

        /* Placeholder styling for duty timer input */
        #scheduled_duty_minutes::placeholder {
            color: white !important;
            opacity: 0.8 !important;
        }

        #scheduled_duty_minutes::-webkit-input-placeholder {
            color: white !important;
            opacity: 0.8 !important;
        }

        #scheduled_duty_minutes::-moz-placeholder {
            color: white !important;
            opacity: 0.8 !important;
        }

        #scheduled_duty_minutes:-ms-input-placeholder {
            color: white !important;
            opacity: 0.8 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elegant Stagger Animation
            function initElegantStagger() {
                const staggerItems = document.querySelectorAll('.elegant-stagger');
                staggerItems.forEach((item, index) => {
                    item.style.animationDelay = (index * 0.05) + 's';
                });
            }

            // Initialize elegant animations
            initElegantStagger();

            // Real-time update for remaining time (duty timer)
            function updateRemainingTime() {
                const remainingTimeElement = document.getElementById('remaining-time');
                if (remainingTimeElement) {
                    const endTimeStr = remainingTimeElement.getAttribute('data-end-time');
                    if (endTimeStr && endTimeStr !== '' && endTimeStr !== 'null') {
                        const endTime = new Date(endTimeStr);

                        // Check if date is valid
                        if (isNaN(endTime.getTime())) {
                            return;
                        }

                        const now = new Date();
                        const diffMs = endTime - now;
                        const diffSeconds = Math.max(0, Math.floor(diffMs / 1000));

                        if (diffSeconds <= 0) {
                            // Time expired - Auto checkout immediately
                            remainingTimeElement.textContent = '00:00:00';
                            remainingTimeElement.classList.remove('bg-white/20', 'bg-yellow-500');
                            remainingTimeElement.classList.add('bg-red-500', 'animate-pulse');

                            // Auto checkout via API call
                            autoCheckoutExpiredSession();
                        } else {
                            const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                            const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                            const seconds = String(diffSeconds % 60).padStart(2, '0');

                            remainingTimeElement.textContent = `${hours}:${minutes}:${seconds}`;

                            // Warning styling jika < 10 menit (600 detik)
                            if (diffSeconds < 600) {
                                remainingTimeElement.classList.remove('bg-white/20');
                                remainingTimeElement.classList.add('bg-yellow-500');
                            } else {
                                remainingTimeElement.classList.remove('bg-yellow-500', 'bg-red-500');
                                remainingTimeElement.classList.add('bg-white/20');
                            }

                            // Remove pulse animation if time > 0
                            remainingTimeElement.classList.remove('animate-pulse');
                        }
                    }
                }
            }

            // Real-time update for active sessions
            function updateActiveSessionDuration() {
                // Update active session in header
                const activeSessionDuration = document.getElementById('active-session-duration');
                if (activeSessionDuration) {
                    const clockInTime = activeSessionDuration.getAttribute('data-clock-in');
                    if (clockInTime && clockInTime !== '' && clockInTime !== 'null') {
                        const clockIn = new Date(clockInTime);

                        // Check if date is valid
                        if (isNaN(clockIn.getTime())) {
                            return;
                        }

                        const now = new Date();
                        const diffMs = now - clockIn;
                        const diffSeconds = Math.floor(diffMs / 1000);

                        // Validate duration - must be positive and reasonable (max 7 days for cross-day sessions)
                        if (diffSeconds >= 0 && diffSeconds <= 86400 * 7) {
                            const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                            const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                            const seconds = String(diffSeconds % 60).padStart(2, '0');

                            const formattedDuration = `${hours}:${minutes}:${seconds}`;
                            activeSessionDuration.textContent = formattedDuration;
                        }
                    }
                }

                // Update previous session duration (from previous day)
                const previousSessionDuration = document.getElementById('previous-session-duration');
                if (previousSessionDuration) {
                    const clockInTime = previousSessionDuration.getAttribute('data-clock-in');
                    if (clockInTime && clockInTime !== '' && clockInTime !== 'null') {
                        const clockIn = new Date(clockInTime);

                        // Check if date is valid
                        if (isNaN(clockIn.getTime())) {
                            return;
                        }

                        const now = new Date();
                        const diffMs = now - clockIn;
                        const diffSeconds = Math.floor(diffMs / 1000);

                        // Validate duration - must be positive and reasonable (max 7 days for cross-day)
                        if (diffSeconds >= 0 && diffSeconds <= 86400 * 7) {
                            const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                            const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                            const seconds = String(diffSeconds % 60).padStart(2, '0');

                            const formattedDuration = `${hours}:${minutes}:${seconds}`;
                            previousSessionDuration.textContent = formattedDuration;
                        }
                    }
                }

                // Update active sessions in list
                const activeSessionElements = document.querySelectorAll('[data-session-active="true"]');

                activeSessionElements.forEach(element => {
                    const clockInTime = element.dataset.clockIn;
                    if (clockInTime && clockInTime !== '' && clockInTime !== 'null') {
                        const clockIn = new Date(clockInTime);

                        // Check if date is valid
                        if (isNaN(clockIn.getTime())) {
                            return;
                        }

                        const now = new Date();
                        const diffMs = now - clockIn;
                        const diffSeconds = Math.floor(diffMs / 1000);

                        // Validate duration - must be positive and reasonable (max 7 days for cross-day sessions)
                        if (diffSeconds < 0 || diffSeconds > 86400 * 7) {
                            const durationElement = element.querySelector('[data-duration]');
                            if (durationElement) {
                                durationElement.textContent = '00:00:00';
                            }
                            return;
                        }

                        const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                        const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                        const seconds = String(diffSeconds % 60).padStart(2, '0');

                        const formattedDuration = `${hours}:${minutes}:${seconds}`;

                        const durationElement = element.querySelector('[data-duration]');
                        if (durationElement) {
                            durationElement.textContent = formattedDuration;
                        }
                    }
                });
            }

            // Check if there's any active session
            if (document.querySelector('[data-session-active="true"]') ||
                document.getElementById('active-session-duration') ||
                document.getElementById('previous-session-duration')) {
                setInterval(updateActiveSessionDuration, 1000);
                updateActiveSessionDuration();
            }

            // Handle Clock In button disabled state based on scheduled_duty_minutes input
            const scheduledDutyMinutesInput = document.getElementById('scheduled_duty_minutes');
            const dashboardClockInBtn = document.getElementById('dashboardClockInBtn');

            if (scheduledDutyMinutesInput && dashboardClockInBtn) {
                // Function to update button state
                function updateClockInButtonState() {
                    const value = scheduledDutyMinutesInput.value.trim();
                    const numValue = parseInt(value);

                    if (value === '' || isNaN(numValue) || numValue < 1 || numValue > 300) {
                        // Disable button
                        dashboardClockInBtn.disabled = true;
                        dashboardClockInBtn.classList.remove('from-green-500', 'to-emerald-500', 'hover:from-green-600', 'hover:to-emerald-600', 'hover:scale-105');
                        dashboardClockInBtn.classList.add('from-gray-400', 'to-gray-500', 'cursor-not-allowed', 'opacity-60');
                        dashboardClockInBtn.title = 'Masukkan durasi terlebih dahulu (1-300 menit / maksimal 5 jam)';
                    } else {
                        // Enable button
                        dashboardClockInBtn.disabled = false;
                        dashboardClockInBtn.classList.remove('from-gray-400', 'to-gray-500', 'cursor-not-allowed', 'opacity-60');
                        dashboardClockInBtn.classList.add('from-green-500', 'to-emerald-500', 'hover:from-green-600', 'hover:to-emerald-600', 'hover:scale-105');
                        dashboardClockInBtn.title = '';
                    }
                }

                // Initial state check
                updateClockInButtonState();

                // Listen to input changes
                scheduledDutyMinutesInput.addEventListener('input', updateClockInButtonState);
                scheduledDutyMinutesInput.addEventListener('change', updateClockInButtonState);
                scheduledDutyMinutesInput.addEventListener('keyup', updateClockInButtonState);
            }

            // Check if there's remaining time timer (duty timer)
            if (document.getElementById('remaining-time')) {
                setInterval(updateRemainingTime, 1000);
                updateRemainingTime();
            }
        });

        function changeYear(year) {
            window.location.href = '<?php echo e(route("staff.dashboard")); ?>?year=' + year;
        }
    </script>

    <div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute inset-0"
            style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.2) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.2) 0%, transparent 50%);">
        </div>

        <div class="relative z-10 px-4 py-8 sm:px-6 lg:px-8 text-white">
            <div class="mb-12 text-center animate-fade-in-up">
                <div class="flex justify-center mb-6">
                    <div
                        class="h-16 w-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl animate-float">
                        <i class="fas fa-tachometer-alt text-white text-2xl"></i>
                    </div>
                </div>
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-4">
                    Dashboard Staf Medis</h1>
                <p class="text-lg sm:text-xl text-sky-200 max-w-3xl mx-auto">Selamat datang kembali, <span
                        class="font-bold text-sky-100"><?php echo e(auth()->user()->name); ?></span>. Kelola operasional medis Anda di
                    sini.</p>
            </div>

            <!-- Warning for Active Session from Previous Day -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($anyActiveSession) && $anyActiveSession && !isset($activeSession)): ?>
                <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-2xl animate-fade-in-up mb-8 border border-red-400/50"
                    style="animation-delay: 0.05s;">
                    <div class="relative p-6 sm:p-8">
                        <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                            <!-- Left side - Icon and Info -->
                            <div class="flex items-center space-x-4 flex-1">
                                <!-- Warning Icon -->
                                <div
                                    class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg border border-white/30 flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                                </div>

                                <!-- Info Text -->
                                <div class="flex-1">
                                    <h4 class="text-xl sm:text-2xl font-bold text-white mb-2">
                                        Sesi Aktif dari Hari Sebelumnya
                                    </h4>
                                    <div class="bg-sky-700/40 backdrop-blur-md rounded-lg p-3 border border-sky-400/50">
                                        <p class="text-white text-sm font-medium mb-1">
                                            Sesi belum di-clock out dari
                                            <span class="bg-yellow-400 text-black px-2 py-1 rounded font-bold text-sm">
                                                <?php echo e(isset($anyActiveSession) && $anyActiveSession ? $anyActiveSession->work_date->format('d/m/Y') : ''); ?>

                                            </span>
                                        </p>
                                        <p class="text-yellow-200 text-sm">
                                            Durasi:
                                            <span id="previous-session-duration"
                                                data-clock-in="<?php echo e(isset($anyActiveSession) && $anyActiveSession ? $anyActiveSession->clock_in->toISOString() : ''); ?>"
                                                class="bg-white/20 text-white px-2 py-1 rounded font-bold">
                                                <?php echo e(isset($anyActiveSession) && $anyActiveSession ? \App\Helpers\TimeHelper::formatDuration($anyActiveSession->calculateTotalHours()) : '0:00:00'); ?>

                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right side - Clock Out Button -->
                            <div class="w-full lg:w-auto">
                                <form method="POST" action="<?php echo e(route('staff.attendance.clock-out')); ?>"
                                    id="dashboardClockOutForm" data-action="<?php echo e(route('staff.attendance.clock-out')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white px-8 py-4 rounded-xl font-bold hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center border border-red-400/50"
                                        id="dashboardClockOutBtn">
                                        <span class="btn-text">
                                            <i class="fas fa-stop mr-3"></i>
                                            <span>Clock Out</span>
                                        </span>
                                        <span class="btn-loading hidden">
                                            <i class="fas fa-spinner fa-spin mr-3"></i>
                                            <span>Memproses...</span>
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Clock In/Out Section - Paling Atas -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!(isset($anyActiveSession) && $anyActiveSession && !isset($activeSession))): ?>
                <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl animate-fade-in-up mb-8"
                    style="animation-delay: 0.05s; background-color: rgba(7, 89, 133, 0.9);">
                    <div class="p-6 sm:p-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($activeSession) && $activeSession): ?>
                            <!-- Active Session -->
                            <div
                                class="bg-gradient-to-br from-yellow-500/50 to-orange-500/50 rounded-2xl p-4 sm:p-6 border-2 border-yellow-400/60 shadow-xl">
                                <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                                    <div class="flex items-center space-x-3 sm:space-x-4">
                                        <div
                                            class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-xl animate-pulse flex-shrink-0">
                                            <i class="fas fa-briefcase text-2xl text-white"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl sm:text-2xl font-bold text-white mb-1 sm:mb-2">Sesi
                                                <?php echo e(isset($activeSession) && $activeSession ? $activeSession->session_number : ''); ?>

                                                Sedang Aktif
                                            </h4>
                                            <p class="text-yellow-200">Dimulai
                                                <?php echo e(isset($activeSession) && $activeSession ? $activeSession->clock_in->diffForHumans() : ''); ?>

                                            </p>
                                            <?php if(isset($activeSession) && $activeSession && $activeSession->scheduled_duty_minutes): ?>
                                                <p class="text-yellow-300 text-sm">
                                                    Waktu Tersisa:
                                                    <span id="remaining-time"
                                                        data-end-time="<?php echo e($activeSession->scheduled_end_time ? $activeSession->scheduled_end_time->toISOString() : ''); ?>"
                                                        class="remaining-time-display bg-white/20 text-white px-2 py-1 rounded font-bold text-base">
                                                        <?php
                                                            $remainingTime = $activeSession->getRemainingTime();
                                                        ?>
                                                        <?php echo e($remainingTime !== null ? \App\Helpers\TimeHelper::formatDuration($remainingTime) : '00:00:00'); ?>

                                                    </span>
                                                    <span
                                                        class="text-xs text-yellow-200 ml-1">(<?php echo e($activeSession->scheduled_duty_minutes); ?>m)</span>
                                                </p>
                                            <?php else: ?>
                                                <p class="text-yellow-300 text-sm">Durasi: <span id="active-session-duration"
                                                        data-clock-in="<?php echo e(isset($activeSession) && $activeSession ? $activeSession->clock_in->toISOString() : ''); ?>"><?php echo e(isset($activeSession) && $activeSession ? \App\Helpers\TimeHelper::formatDuration($activeSession->calculateTotalHours()) : '0:00:00'); ?></span>
                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('staff.attendance.clock-out')); ?>"
                                        id="activeSessionClockOutForm" data-action="<?php echo e(route('staff.attendance.clock-out')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" id="activeSessionClockOutBtn"
                                            class="w-full lg:w-auto bg-gradient-to-r from-red-500 to-orange-500 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-bold hover:from-red-600 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                                            <span class="btn-text">
                                                <i class="fas fa-sign-out-alt mr-3"></i>Clock Out
                                            </span>
                                            <span class="btn-loading hidden">
                                                <i class="fas fa-spinner fa-spin mr-3"></i>Memproses...
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Start New Session -->
                            <div
                                class="bg-gradient-to-br from-sky-500/50 to-cyan-500/50 rounded-2xl p-4 sm:p-6 border-2 border-sky-400/60 shadow-xl">
                                <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                                    <div class="flex items-center space-x-3 sm:space-x-4">
                                        <div
                                            class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0">
                                            <i class="fas fa-plus text-2xl text-white"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl sm:text-2xl font-bold text-white mb-1 sm:mb-2">Mulai Sesi Kerja Baru
                                            </h4>
                                            <p class="text-sky-100 font-medium">Masukkan durasi kerja untuk memulai</p>
                                        </div>
                                    </div>
                                    <form id="dashboardClockInForm" method="POST" action="<?php echo e(route('staff.attendance.clock-in')); ?>"
                                        data-action="<?php echo e(route('staff.attendance.clock-in')); ?>"
                                        class="w-full lg:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                        <?php echo csrf_field(); ?>
                                        <select name="session_type"
                                            class="px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all shadow-xl">
                                            <option value="work" class="text-gray-800">Kerja</option>
                                            <option value="meeting" class="text-gray-800">Meeting</option>
                                        </select>
                                        <input type="number" name="scheduled_duty_minutes" id="scheduled_duty_minutes" min="1"
                                            max="300" required placeholder="Durasi (menit) - Wajib"
                                            class="w-full sm:w-40 px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all shadow-xl"
                                            style="color: white;" title="Masukkan durasi dalam menit (1-300 menit / maksimal 5 jam)"
                                            value="">
                                        <button type="submit" id="dashboardClockInBtn" disabled
                                            class="bg-gradient-to-r from-gray-400 to-gray-500 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-bold transition-all duration-300 shadow-lg flex items-center justify-center cursor-not-allowed opacity-60"
                                            title="Masukkan durasi terlebih dahulu (1-300 menit / maksimal 5 jam)">
                                            <i class="fas fa-sign-in-alt mr-3"></i>
                                            Clock In
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php
                $weeklyHoursFormatted = optional($weeklyStats)->total_hours_formatted ?? '00:00:00';
                $weeklyActiveDays = optional($weeklyStats)->total_days ?? 0;
                $accumulatedEmsHours = $totalEmsHours['formatted'] ?? null;
            ?>

            <div class="flex flex-col gap-8">

                <!-- Total Jam Kerja Minggu Ini & Akumulasi EMS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Jam Kerja Minggu Ini -->
                    <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                        style="background-color: rgba(7, 89, 133, 0.9);">
                        <div class="p-5 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl">
                                        <i class="fas fa-stopwatch text-white text-lg sm:text-xl"></i>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-sm sm:text-base font-semibold text-sky-100 uppercase tracking-wider">
                                            Total Jam Kerja Minggu Ini</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="text-3xl sm:text-4xl font-black text-white stat-number mb-2">
                                <?php echo e($weeklyHoursFormatted); ?>

                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($weeklyActiveDays > 0): ?>
                                <p class="text-xs sm:text-sm text-sky-100 font-medium"><?php echo e($weeklyActiveDays); ?> hari kerja aktif
                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Akumulasi Jam Sebagai EMS -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($accumulatedEmsHours)): ?>
                        <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                            style="background-color: rgba(7, 89, 133, 0.9);">
                            <div class="p-5 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl">
                                            <i class="fas fa-clock text-white text-lg sm:text-xl"></i>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-sm sm:text-base font-semibold text-sky-100 uppercase tracking-wider">
                                                Akumulasi Jam Sebagai EMS</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-3xl sm:text-4xl font-black text-white stat-number mb-2">
                                    <?php echo e($accumulatedEmsHours); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Today's Sessions Card -->
                <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="px-6 sm:px-8 py-6 border-b-2 border-sky-400/50 flex items-center justify-between">
                        <h3 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-history text-sky-400 mr-3"></i>Sesi Hari Ini
                        </h3>
                        <span
                            class="px-3 py-1 bg-sky-500/30 text-sky-200 text-sm font-medium rounded-full border-2 border-sky-400/50 shadow-md">
                            <?php echo e(isset($todaySessions) ? $todaySessions->count() : 0); ?> Sesi
                        </span>
                    </div>
                    <div class="p-6 sm:p-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($todaySessions) && $todaySessions->count() > 0): ?>
                            <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = isset($todaySessions) ? $todaySessions : []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-sky-700/40 rounded-xl p-4 flex items-center justify-between transition-all duration-300 border-2 <?php echo e($session->is_active ? 'border-yellow-400/70' : 'border-sky-400/50'); ?> shadow-lg"
                                        data-session-active="<?php echo e($session->is_active ? 'true' : 'false'); ?>"
                                        data-clock-in="<?php echo e($session->is_active ? $session->clock_in->toISOString() : ''); ?>">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-10 h-10 rounded-lg flex items-center justify-center <?php echo e($session->is_active ? 'bg-yellow-500' : 'bg-green-500'); ?> shadow-md">
                                                <span class="font-bold text-white"><?php echo e($session->session_number); ?></span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-white">
                                                    Sesi <?php echo e($session->session_number); ?> - <span
                                                        class="font-normal"><?php echo e(ucfirst($session->session_type)); ?></span>
                                                </p>
                                                <p class="text-sm text-sky-300">
                                                    <?php echo e($session->clock_in->format('H:i')); ?> -
                                                    <?php echo e($session->clock_out ? $session->clock_out->format('H:i') : '...'); ?>

                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->is_active): ?>
                                                <span class="font-bold text-lg text-yellow-300" data-duration>00:00:00</span>
                                                <p class="text-xs text-yellow-400">Sedang Berlangsung</p>
                                            <?php else: ?>
                                                <span class="font-bold text-lg text-white">
                                                    <?php echo e(\App\Helpers\TimeHelper::formatDuration($session->session_duration ?: $session->clock_in->diffInSeconds($session->clock_out))); ?>

                                                </span>
                                                <p class="text-xs text-green-300">Selesai</p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10 bg-sky-700/40 rounded-xl border-2 border-sky-400/50 shadow-lg">
                                <i class="fas fa-moon text-4xl text-sky-400 mb-4"></i>
                                <p class="text-sky-200">Belum ada sesi yang dimulai hari ini.</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>


                <!-- Leaderboard -->
                <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="px-6 sm:px-8 py-6 border-b-2 border-sky-400/50">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-bold text-white mb-1 sm:mb-2 flex items-center">
                                    <i class="fas fa-trophy text-yellow-400 mr-2 sm:mr-3"></i>
                                    Leaderboard Absensi Mingguan
                                </h3>
                                <p class="text-sky-100 font-medium">Peringkat staf berdasarkan total jam kerja</p>
                            </div>
                            <div
                                class="px-3 py-1 bg-sky-500/40 text-sky-100 text-sm font-medium rounded-full border-2 border-sky-400/70 shadow-lg">
                                <?php echo e($leaderboard->count()); ?> Staf
                            </div>
                        </div>
                    </div>
                    <div class="p-6 sm:p-8 space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leaderboard; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div
                                class="bg-gradient-to-r from-sky-700/50 to-sky-800/40 backdrop-blur-xl rounded-2xl p-4 sm:p-6 hover:from-sky-700/60 hover:to-sky-800/50 transition-all duration-300 card-hover group border-2 border-sky-400/50 hover:border-sky-400/70 shadow-xl <?php echo e($index < 3 ? 'ring-2 ring-yellow-400/70 shadow-2xl' : ''); ?>">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center space-x-3 sm:space-x-4">
                                        <div class="relative">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index < 3): ?>
                                                <div
                                                    class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br <?php echo e($index === 0 ? 'from-yellow-500 to-orange-500' : ($index === 1 ? 'from-gray-400 to-gray-500' : 'from-amber-600 to-amber-700')); ?> text-white rounded-2xl flex items-center justify-center text-lg font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <?php echo e($index + 1); ?>

                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index === 0): ?>
                                                    <div
                                                        class="absolute -top-1 -right-1 w-5 h-5 sm:w-6 sm:h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-crown text-yellow-800 text-xs"></i>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php else: ?>
                                                <div
                                                    class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-sky-500 to-cyan-500 text-white rounded-2xl flex items-center justify-center text-lg font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                    <?php echo e($index + 1); ?>

                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p
                                                    class="font-bold text-base sm:text-lg text-white group-hover:text-sky-200 transition-colors duration-300">
                                                    <?php echo e($staff->name); ?>

                                                </p>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($staff->total_juara_1_count) && $staff->total_juara_1_count > 0): ?>
                                                    <div
                                                        class="flex items-center gap-1 px-2 py-1 bg-yellow-500/20 border border-yellow-400/30 rounded-full">
                                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                        <span
                                                            class="text-xs font-bold text-yellow-300">x<?php echo e($staff->total_juara_1_count); ?></span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index < 3): ?>
                                                    <span
                                                        class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($index === 0 ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-400/30' : ($index === 1 ? 'bg-gray-500/20 text-gray-300 border border-gray-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30')); ?>">
                                                        <?php echo e($index === 0 ? 'Juara 1' : ($index === 1 ? 'Juara 2' : 'Juara 3')); ?>

                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <p class="text-sky-300 font-medium text-sm sm:text-base">
                                                <?php echo e($staff->role->display_name ?? 'Staf'); ?>

                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="font-black text-2xl sm:text-3xl text-sky-200 group-hover:text-white transition-colors duration-300 drop-shadow-lg leaderboard-number">
                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($staff->attendances_sum_session_duration ?: 0)); ?>

                                        </p>
                                        <p class="text-sky-300 font-medium text-sm sm:text-lg">
                                            <?php echo e($staff->unique_work_days ?? $staff->attendances_count); ?> hari kerja
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 bg-white/10 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-line text-sky-400 text-2xl"></i>
                                </div>
                                <p class="text-sky-200 text-lg font-medium">Belum ada data absensi minggu ini.</p>
                                <p class="text-sky-300 text-sm mt-2">Mulai clock in untuk melihat peringkat Anda!</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Statistik Form Mingguan (dipindah ke bawah Leaderboard) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Menunggu Review Card -->
                    <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                        style="background-color: rgba(7, 89, 133, 0.9);">
                        <div class="p-4 sm:p-6 text-center">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                                <i class="fas fa-hourglass-half text-xl sm:text-2xl text-white"></i>
                            </div>
                            <h3 class="text-3xl font-black text-yellow-300 stat-number mb-2"><?php echo e($stats['pending_forms']); ?>

                            </h3>
                            <p class="text-white font-semibold">Menunggu Review</p>
                        </div>
                    </div>

                    <!-- Disetujui Card -->
                    <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                        style="background-color: rgba(7, 89, 133, 0.9);">
                        <div class="p-4 sm:p-6 text-center">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                                <i class="fas fa-check-circle text-xl sm:text-2xl text-white"></i>
                            </div>
                            <h3 class="text-3xl font-black text-green-300 stat-number mb-2"><?php echo e($stats['approved_forms']); ?>

                            </h3>
                            <p class="text-white font-semibold">Disetujui</p>
                        </div>
                    </div>

                    <!-- Ditolak Card -->
                    <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                        style="background-color: rgba(7, 89, 133, 0.9);">
                        <div class="p-4 sm:p-6 text-center">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                                <i class="fas fa-times-circle text-xl sm:text-2xl text-white"></i>
                            </div>
                            <h3 class="text-3xl font-black text-red-300 stat-number mb-2"><?php echo e($stats['rejected_forms']); ?>

                            </h3>
                            <p class="text-white font-semibold">Ditolak</p>
                        </div>
                    </div>

                    <!-- Formulir Hari Ini Card -->
                    <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl elegant-card elegant-stagger"
                        style="background-color: rgba(7, 89, 133, 0.9);">
                        <div class="p-4 sm:p-6 text-center">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                                <i class="fas fa-calendar-day text-xl sm:text-2xl text-white"></i>
                            </div>
                            <h3 class="text-3xl font-black text-sky-300 stat-number mb-2"><?php echo e($stats['total_forms_today']); ?>

                            </h3>
                            <p class="text-white font-semibold">Formulir Hari Ini</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Forms Table -->
                <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl overflow-hidden elegant-card elegant-stagger"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="px-6 sm:px-8 py-6 border-b border-sky-400/30 flex justify-between items-center">
                        <h3 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-file-medical text-sky-400 mr-2 sm:mr-3"></i>Surat dari Form
                        </h3>
                        <a href="<?php echo e(route('staff.forms')); ?>"
                            class="text-sky-300 hover:text-sky-200 text-base sm:text-lg font-semibold transition-colors flex items-center">
                            Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b border-sky-400/30" style="background-color: rgba(14, 165, 233, 0.4);">
                                <tr>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Karakter</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Jenis</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Diproses Oleh</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-400/20">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentForms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-sky-700/40 transition-all duration-300">
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm font-mono text-sky-200 font-bold">
                                            #<?php echo e(str_pad($form->id, 6, '0', STR_PAD_LEFT)); ?></td>
                                        <td class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm font-bold text-white">
                                            <?php echo e($form->character_name); ?>

                                        </td>
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm text-sky-300 font-medium">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $form->form_type))); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->form_type === 'tes_psikologi'): ?>
                                                <?php
                                                    $data = is_array($form->form_data ?? null) ? $form->form_data : [];
                                                    $answers = [];
                                                    $hasAny = false;
                                                    for ($i = 1; $i <= 10; $i++) {
                                                        $key = 'stress' . $i;
                                                        $raw = $data[$key] ?? null;
                                                        if ($raw !== null && $raw !== '') {
                                                            $hasAny = true;
                                                        }
                                                        $val = is_numeric($raw) ? (int) $raw : 0;
                                                        if (in_array($i, [4, 5, 7, 9], true)) {
                                                            $val = 4 - $val;
                                                        }
                                                        $answers[] = $val;
                                                    }
                                                    $total = array_sum($answers);
                                                    $level = $total <= 13 ? 'Rendah' : ($total <= 26 ? 'Sedang' : 'Tinggi');
                                                    $cls = [
                                                        'Rendah' => 'bg-green-500/20 text-green-300 border border-green-500/30',
                                                        'Sedang' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
                                                        'Tinggi' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                                                    ][$level] ?? 'bg-sky-700/40 text-white border border-sky-400/50';
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAny): ?>
                                                    <span
                                                        class="ml-2 px-2 py-0.5 rounded-full text-[11px] font-bold align-middle <?php echo e($cls); ?>">Stres:
                                                        <?php echo e($level); ?> (<?php echo e($total); ?>)</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->status === 'pending'): ?>
                                                <span
                                                    class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-400/30">Menunggu</span>
                                            <?php elseif($form->status === 'approved'): ?>
                                                <span
                                                    class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-green-500/20 text-green-300 border border-green-400/30">Disetujui</span>
                                            <?php else: ?>
                                                <span
                                                    class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-red-500/20 text-red-300 border border-red-400/30">Ditolak</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm text-sky-300 font-medium">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->processedBy && $form->status !== 'pending'): ?>
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-white"><?php echo e($form->processedBy->name); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->processed_at): ?>
                                                        <span
                                                            class="text-xs text-sky-400"><?php echo e($form->processed_at->format('d/m/Y H:i')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-slate-400">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm text-sky-300 font-medium">
                                            <?php echo e($form->created_at->format('d/m/Y H:i')); ?>

                                        </td>
                                        <td class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <a href="<?php echo e(route('staff.forms.show', $form->id)); ?>"
                                                    class="inline-flex items-center justify-center px-3 py-2 sm:px-4 bg-gradient-to-r from-sky-500 to-cyan-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-sky-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                    <i class="fas fa-eye mr-2"></i>Lihat
                                                </a>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->status === 'pending'): ?>
                                                    <button type="button" onclick="approveForm(<?php echo e($form->id); ?>, this)"
                                                        class="w-full inline-flex items-center justify-center px-3 py-2 sm:px-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                        <i class="fas fa-check mr-2"></i>Setujui
                                                    </button>
                                                    <button type="button" onclick="rejectForm(<?php echo e($form->id); ?>, this)"
                                                        class="w-full inline-flex items-center justify-center px-3 py-2 sm:px-4 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                        <i class="fas fa-times mr-2"></i>Tolak
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-sky-300 font-medium">Selesai</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="px-8 py-16 text-center text-sky-300 text-lg font-medium">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-inbox text-4xl mb-4 text-sky-400"></i>
                                                Belum ada formulir yang masuk.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Appointments Table -->
                <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up"
                    style="animation-delay: 0.6s; background-color: rgba(7, 89, 133, 0.9);">
                    <div class="px-6 sm:px-8 py-6 border-b border-sky-400/30 flex justify-between items-center">
                        <h3 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-calendar-check text-green-400 mr-2 sm:mr-3"></i>Janji Temu Terbaru
                        </h3>
                        <a href="<?php echo e(route('staff.forms')); ?>?type=appointment"
                            class="text-green-300 hover:text-green-200 text-base sm:text-lg font-semibold transition-colors flex items-center">
                            Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b border-sky-400/30" style="background-color: rgba(14, 165, 233, 0.4);">
                                <tr>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Karakter</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Poli</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Tanggal & Waktu</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Diproses Oleh</th>
                                    <th
                                        class="px-4 sm:px-8 py-4 text-left text-xs sm:text-sm font-bold text-sky-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-400/20">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $formData = $appointment->form_data ?? [];
                                        $appointmentDate = $formData['appointment_date'] ?? null;
                                        $appointmentTime = $formData['appointment_time'] ?? null;
                                        $formTypeNames = [
                                            'penyakit_dalam' => 'Poli Penyakit Dalam',
                                            'spesialis_anak' => 'Poli Spesialis Anak',
                                            'spesialis_bedah' => 'Poli Spesialis Bedah',
                                            'spesialis_mata' => 'Poli Spesialis Mata',
                                            'spesialis_saraf' => 'Poli Spesialis Saraf',
                                            'spesialis_urologi' => 'Poli Spesialis Urologi',
                                            'spesialis_tht' => 'Poli Spesialis THT',
                                            'spesialis_ortopedi' => 'Poli Spesialis Ortopedi',
                                        ];
                                    ?>
                                    <tr class="hover:bg-sky-700/40 transition-all duration-300">
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm font-mono text-sky-200 font-bold">
                                            #<?php echo e(str_pad($appointment->id, 6, '0', STR_PAD_LEFT)); ?></td>
                                        <td class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm font-bold text-white">
                                            <?php echo e($appointment->character_name); ?>

                                        </td>
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm text-green-300 font-medium">
                                            <?php echo e($formTypeNames[$appointment->form_type] ?? ucfirst(str_replace('_', ' ', $appointment->form_type))); ?>

                                        </td>
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm text-sky-300 font-medium">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointmentDate && $appointmentTime): ?>
                                                <?php echo e(\Carbon\Carbon::parse($appointmentDate)->format('d/m/Y')); ?>

                                                <?php echo e($appointmentTime); ?>

                                            <?php else: ?>
                                                <span class="text-slate-400">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointment->status === 'pending'): ?>
                                                <span
                                                    class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-400/30">Menunggu</span>
                                            <?php elseif($appointment->status === 'approved'): ?>
                                                <span
                                                    class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-green-500/20 text-green-300 border border-green-400/30">Sudah
                                                    Ditemui</span>
                                            <?php else: ?>
                                                <span
                                                    class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-red-500/20 text-red-300 border border-red-400/30">Tolak
                                                    Formulir</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td
                                            class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm text-sky-300 font-medium">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointment->processedBy && $appointment->status !== 'pending'): ?>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-semibold text-white"><?php echo e($appointment->processedBy->name); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointment->processed_at): ?>
                                                        <span
                                                            class="text-xs text-sky-400"><?php echo e($appointment->processed_at->format('d/m/Y H:i')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-slate-400">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-8 py-5 sm:py-6 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <a href="<?php echo e(route('staff.forms.show', $appointment->id)); ?>"
                                                    class="inline-flex items-center justify-center px-3 py-2 sm:px-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                    <i class="fas fa-eye mr-2"></i>Lihat
                                                </a>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointment->status === 'pending'): ?>
                                                    <button type="button" onclick="approveForm(<?php echo e($appointment->id); ?>, this, true)"
                                                        class="w-full inline-flex items-center justify-center px-3 py-2 sm:px-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                        <i class="fas fa-check mr-2"></i>Sudah Ditemui
                                                    </button>
                                                    <button type="button" onclick="rejectForm(<?php echo e($appointment->id); ?>, this, true)"
                                                        class="w-full inline-flex items-center justify-center px-3 py-2 sm:px-4 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs sm:text-sm font-bold rounded-xl hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                                        <i class="fas fa-times mr-2"></i>Tolak Formulir
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-green-300 font-medium">Selesai</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="px-8 py-16 text-center text-sky-300 text-lg font-medium">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-calendar-times text-4xl mb-4 text-green-400"></i>
                                                Belum ada janji temu yang masuk.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-20 left-0 right-0 flex flex-col items-center gap-3 pointer-events-none px-4"
        style="z-index: 9999999999;">
    </div>

    <!-- Toast Template Styles - Modern Premium Design v3 -->
    <style id="toastStyles-v3">
        /* MODERN PREMIUM DESIGN */
        .toast {
            pointer-events: auto;
            min-width: 320px;
            max-width: 400px;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.12),
                0 4px 12px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(0, 0, 0, 0.04);
            animation: toastSlideIn 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            display: flex;
            align-items: center;
            gap: 0.875rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .toast:hover {
            transform: translateY(-2px);
            box-shadow:
                0 12px 48px rgba(0, 0, 0, 0.15),
                0 6px 16px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        /* Animated gradient accent */
        .toast::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--accent-light) 0%, var(--accent-dark) 100%);
            animation: accentPulse 2s ease-in-out infinite;
        }

        .toast.hiding {
            animation: toastSlideOut 0.3s cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        .toast-success {
            --accent-light: #10b981;
            --accent-dark: #059669;
        }

        .toast-error {
            --accent-light: #ef4444;
            --accent-dark: #dc2626;
        }

        .toast-warning {
            --accent-light: #f59e0b;
            --accent-dark: #d97706;
        }

        .toast-info {
            --accent-light: #0ea5e9;
            --accent-dark: #0284c7;
        }

        .toast-icon {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            animation: iconBounce 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        @keyframes iconBounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
            }
        }

        .toast-success .toast-icon {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #059669;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }

        .toast-error .toast-icon {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        }

        .toast-warning .toast-icon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #d97706;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
        }

        .toast-info .toast-icon {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #0284c7;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
        }

        .toast-icon i {
            animation: iconRotate 0.5s ease-out 0.2s;
        }

        @keyframes iconRotate {
            0% {
                transform: rotate(-10deg);
                opacity: 0;
            }

            100% {
                transform: rotate(0deg);
                opacity: 1;
            }
        }

        .toast-content {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.9375rem;
            color: #111827;
            margin-bottom: 0.25rem;
            letter-spacing: -0.01em;
        }

        .toast-message {
            font-size: 0.8125rem;
            color: #6b7280;
            line-height: 1.5;
            font-weight: 450;
        }

        .toast-close {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            border: none;
        }

        .toast-close:hover {
            background: #f3f4f6;
            color: #4b5563;
            transform: scale(1.1) rotate(90deg);
        }

        .toast-close:active {
            transform: scale(0.95) rotate(90deg);
        }

        @keyframes toastSlideIn {
            from {
                transform: translateY(-24px) scale(0.94);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes toastSlideOut {
            from {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            to {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }
        }

        @keyframes toastProgress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Toast Notification System
        function showToast(type, title, message, duration = 5000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.setProperty('--duration', `${duration}ms`);
            console.log('🎨 NEW TOAST DESIGN v2 - White Card Design');
            toast.innerHTML = `
                                    <div class="toast-icon">
                                        <i class="fas ${icons[type]} text-sm"></i>
                                    </div>
                                    <div class="toast-content">
                                        <div class="toast-title">${title}</div>
                                        <div class="toast-message">${message}</div>
                                    </div>
                                    <button class="toast-close" onclick="closeToast(this.parentElement)">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                `;

            container.appendChild(toast);
            setTimeout(() => closeToast(toast), duration);
        }

        // Check for session messages on page load
        document.addEventListener('DOMContentLoaded', function () {
            <?php if(session('success')): ?>
                showToast('success', 'Berhasil!', '<?php echo e(session('success')); ?>');
            <?php endif; ?>
            <?php if(session('error')): ?>
                showToast('error', 'Error!', '<?php echo e(session('error')); ?>');
            <?php endif; ?>
            <?php if(session('warning')): ?>
                showToast('warning', 'Perhatian!', '<?php echo e(session('warning')); ?>');
            <?php endif; ?>
            <?php if(session('info')): ?>
                showToast('info', 'Informasi', '<?php echo e(session('info')); ?>');
            <?php endif; ?>
                        });

        function closeToast(toast) {
            if (!toast || toast.classList.contains('hiding')) return;
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }

        // Approve Form via AJAX
        async function approveForm(formId, button, isAppointment = false) {
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const response = await fetch(`/staff/forms/${formId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const label = isAppointment ? 'Janji temu ditandai sudah ditemui' : 'Formulir berhasil disetujui';
                    showToast('success', 'Berhasil!', label);

                    // Update UI - hide action buttons and show status
                    const td = button.closest('td');
                    if (td) {
                        td.innerHTML = '<span class="text-green-300 font-medium">Selesai</span>';
                    }

                    // Update status badge in the same row
                    const row = button.closest('tr');
                    if (row) {
                        const statusTd = row.querySelectorAll('td')[isAppointment ? 4 : 3];
                        if (statusTd) {
                            statusTd.innerHTML = `<span class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-green-500/20 text-green-300 border border-green-400/30">${isAppointment ? 'Sudah Ditemui' : 'Disetujui'}</span>`;
                        }
                    }
                } else {
                    throw new Error('Gagal memproses permintaan');
                }
            } catch (error) {
                showToast('error', 'Gagal', error.message || 'Terjadi kesalahan');
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        }

        // Reject Form via AJAX
        async function rejectForm(formId, button, isAppointment = false) {
            const originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const response = await fetch(`/staff/forms/${formId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    showToast('warning', 'Ditolak', isAppointment ? 'Janji temu ditolak' : 'Formulir ditolak');

                    // Update UI
                    const td = button.closest('td');
                    if (td) {
                        td.innerHTML = '<span class="text-red-300 font-medium">Selesai</span>';
                    }

                    // Update status badge
                    const row = button.closest('tr');
                    if (row) {
                        const statusTd = row.querySelectorAll('td')[isAppointment ? 4 : 3];
                        if (statusTd) {
                            statusTd.innerHTML = '<span class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-bold rounded-full bg-red-500/20 text-red-300 border border-red-400/30">Ditolak</span>';
                        }
                    }
                } else {
                    throw new Error('Gagal memproses permintaan');
                }
            } catch (error) {
                showToast('error', 'Gagal', error.message || 'Terjadi kesalahan');
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        }

        // Real-time duration update sudah ditangani oleh script di bagian atas halaman

        // Auto checkout function for expired sessions
        function autoCheckoutExpiredSession() {
            // Get clock out form
            const clockOutForm = document.getElementById('activeSessionClockOutForm');

            if (!clockOutForm) {
                console.warn('Clock out form not found, refreshing page...');
                setTimeout(() => location.reload(), 2000);
                return;
            }

            // Create form data
            const formData = new FormData(clockOutForm);
            const formAction = clockOutForm.getAttribute('action') || clockOutForm.getAttribute('data-action');

            if (!formAction) {
                console.error('Form action not found');
                setTimeout(() => location.reload(), 2000);
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value;

            if (!csrfToken) {
                console.error('CSRF token not found');
                setTimeout(() => location.reload(), 2000);
                return;
            }

            // Show notification
            console.log('Waktu habis! Melakukan auto checkout...');

            // Submit clock out
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => {
                    if (response.ok || response.redirected) {
                        console.log('Auto checkout berhasil!');
                        // Reload page to show updated state
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        throw new Error('Auto checkout failed');
                    }
                })
                .catch(error => {
                    console.error('Auto checkout error:', error);
                    // Fallback: reload page after 3 seconds (scheduler might handle it)
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                });
        }

        // Dashboard Clock In & Clock Out Form Handler
        document.addEventListener('DOMContentLoaded', function () {
            // Clock In Form Handler - Simplified
            const dashboardClockInForm = document.getElementById('dashboardClockInForm');
            const dashboardClockInBtn = document.getElementById('dashboardClockInBtn');

            if (dashboardClockInForm && dashboardClockInBtn) {
                dashboardClockInForm.addEventListener('submit', function (e) {
                    // Allow form to submit normally, just update button state
                    dashboardClockInBtn.disabled = true;
                    dashboardClockInBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Memproses...';

                    // Form will submit normally - no need to preventDefault
                    // This ensures CSRF and all form data is handled correctly by Laravel
                });
            }

            // Clock Out Form Handler (for previous day session)
            const dashboardClockOutForm = document.getElementById('dashboardClockOutForm');
            const dashboardClockOutBtn = document.getElementById('dashboardClockOutBtn');

            if (dashboardClockOutForm && dashboardClockOutBtn) {
                dashboardClockOutForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Show loading state
                    const btnText = dashboardClockOutBtn.querySelector('.btn-text');
                    const btnLoading = dashboardClockOutBtn.querySelector('.btn-loading');

                    if (btnText) btnText.classList.add('hidden');
                    if (btnLoading) btnLoading.classList.remove('hidden');
                    dashboardClockOutBtn.disabled = true;

                    // Submit form with retry mechanism
                    if (typeof submitDashboardClockOutForm === 'function') {
                        submitDashboardClockOutForm();
                    } else {
                        dashboardClockOutForm.submit();
                    }
                });
            }

            // Clock Out Form Handler (for active session today)
            const activeSessionClockOutForm = document.getElementById('activeSessionClockOutForm');
            const activeSessionClockOutBtn = document.getElementById('activeSessionClockOutBtn');

            if (activeSessionClockOutForm && activeSessionClockOutBtn) {
                activeSessionClockOutForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Show loading state
                    const btnText = activeSessionClockOutBtn.querySelector('.btn-text');
                    const btnLoading = activeSessionClockOutBtn.querySelector('.btn-loading');

                    if (btnText) btnText.classList.add('hidden');
                    if (btnLoading) btnLoading.classList.remove('hidden');
                    activeSessionClockOutBtn.disabled = true;

                    // Submit form with retry mechanism
                    if (typeof submitActiveSessionClockOutForm === 'function') {
                        submitActiveSessionClockOutForm();
                    } else {
                        activeSessionClockOutForm.submit();
                    }
                });
            }

            function submitDashboardClockOutForm(retryCount = 0) {
                const form = document.getElementById('dashboardClockOutForm');
                if (!form) {
                    console.error('Form dashboardClockOutForm not found');
                    return;
                }

                // Get action from data-action attribute first (more reliable), fallback to action attribute
                let formAction = form.getAttribute('data-action') || form.getAttribute('action');

                // Debug logging
                console.log('[Clock Out - Previous Day] Form action (from action attr):', form.getAttribute('action'));
                console.log('[Clock Out - Previous Day] Form action (from data-action attr):', form.getAttribute('data-action'));
                console.log('[Clock Out - Previous Day] Form action (final):', formAction);

                // Validate form action
                if (!formAction || formAction.includes('{{') || formAction.includes('route(') || formAction.trim() === '') {
                    console.error('[Clock Out - Previous Day] Invalid form action:', formAction);
                    console.error('[Clock Out - Previous Day] Form HTML:', form.outerHTML);
                    alert('Terjadi kesalahan: Form action tidak valid. Silakan refresh halaman.');
                    return;
                }

                const formData = new FormData(form);

                fetch(formAction, {
                    method: 'POST',
                    credentials: 'same-origin', // Include cookies for same-origin requests
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    redirect: 'follow'
                })
                    .then(response => {
                        if (response.status === 419) {
                            // CSRF token expired
                            if (retryCount < 2) {
                                console.log('CSRF token expired, refreshing and retrying...');
                                return refreshCsrfToken().then(() => {
                                    return submitDashboardClockOutForm(retryCount + 1);
                                });
                            } else {
                                throw new Error('CSRF token refresh failed after multiple attempts');
                            }
                        }
                        return response;
                    })
                    .then(response => {
                        if (response.ok) {
                            // Success - reload page to show updated state
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else {
                                window.location.reload();
                            }
                        } else {
                            throw new Error('Clock out failed');
                        }
                    })
                    .catch(error => {
                        console.error('Clock out error:', error);

                        // Reset button state
                        const btn = document.getElementById('dashboardClockOutBtn');
                        if (btn) {
                            const btnText = btn.querySelector('.btn-text');
                            const btnLoading = btn.querySelector('.btn-loading');

                            if (btnText) btnText.classList.remove('hidden');
                            if (btnLoading) btnLoading.classList.add('hidden');
                            btn.disabled = false;
                        }

                        // Show error message
                        alert('Terjadi kesalahan saat clock out. Silakan coba lagi atau refresh halaman.');
                    });
            }

            function submitActiveSessionClockOutForm(retryCount = 0) {
                const form = document.getElementById('activeSessionClockOutForm');
                if (!form) {
                    console.error('Form activeSessionClockOutForm not found');
                    return;
                }

                // Get action from data-action attribute first (more reliable), fallback to action attribute
                let formAction = form.getAttribute('data-action') || form.getAttribute('action');

                // Debug logging
                console.log('[Clock Out - Active Session] Form action (from action attr):', form.getAttribute('action'));
                console.log('[Clock Out - Active Session] Form action (from data-action attr):', form.getAttribute('data-action'));
                console.log('[Clock Out - Active Session] Form action (final):', formAction);

                // Validate form action
                if (!formAction || formAction.includes('{{') || formAction.includes('route(') || formAction.trim() === '') {
                    console.error('[Clock Out - Active Session] Invalid form action:', formAction);
                    console.error('[Clock Out - Active Session] Form HTML:', form.outerHTML);
                    alert('Terjadi kesalahan: Form action tidak valid. Silakan refresh halaman.');
                    return;
                }

                const formData = new FormData(form);

                fetch(formAction, {
                    method: 'POST',
                    credentials: 'same-origin', // Include cookies for same-origin requests
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    redirect: 'follow'
                })
                    .then(response => {
                        if (response.status === 419) {
                            // CSRF token expired
                            if (retryCount < 2) {
                                console.log('CSRF token expired, refreshing and retrying active session clock out...');
                                return refreshCsrfToken().then(() => {
                                    return submitActiveSessionClockOutForm(retryCount + 1);
                                });
                            } else {
                                throw new Error('CSRF token refresh failed after multiple attempts');
                            }
                        }
                        return response;
                    })
                    .then(response => {
                        if (response.ok) {
                            // Success - reload page to show updated state
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else {
                                window.location.reload();
                            }
                        } else {
                            throw new Error('Clock out failed');
                        }
                    })
                    .catch(error => {
                        console.error('Active session clock out error:', error);

                        // Reset button state
                        const btn = document.getElementById('activeSessionClockOutBtn');
                        if (btn) {
                            const btnText = btn.querySelector('.btn-text');
                            const btnLoading = btn.querySelector('.btn-loading');

                            if (btnText) btnText.classList.remove('hidden');
                            if (btnLoading) btnLoading.classList.add('hidden');
                            btn.disabled = false;
                        }

                        // Show error message
                        alert('Terjadi kesalahan saat clock out. Silakan coba lagi atau refresh halaman.');
                    });
            }

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

                            // Update all CSRF token inputs in all forms
                            const csrfInputs = document.querySelectorAll('input[name="_token"]');
                            csrfInputs.forEach(input => {
                                input.value = data.csrf_token;
                            });

                            console.log('CSRF token refreshed successfully');
                        }
                    });
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\staff\dashboard.blade.php ENDPATH**/ ?>