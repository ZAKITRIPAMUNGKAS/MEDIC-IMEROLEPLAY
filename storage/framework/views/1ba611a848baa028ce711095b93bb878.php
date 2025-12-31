<?php $__env->startSection('title', 'Laporan Absensi - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative max-w-7xl w-full mx-auto text-white">
            <!-- Header Section -->
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-4 sm:p-6 md:p-8 mb-6 sm:mb-8"
                style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Laporan Absensi</h1>
                        <p class="text-sky-200 text-base sm:text-lg">Lihat dan download rekapan absensi staf</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="text-right">
                            <p class="text-gray-300 text-sm">Total Data</p>
                            <p class="text-xl sm:text-2xl font-bold text-white"><?php echo e($attendances->count()); ?></p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasPermission('manage_attendance_advanced')): ?>
                        <button onclick="openManualEntryModal()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg mr-2">
                            <i class="fas fa-plus mr-2"></i><span class="hidden xs:inline">Tambah Manual</span><span
                                class="xs:hidden">Tambah</span>
                        </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <a href="<?php echo e(route('admin.attendance-reports.index', array_merge(request()->query(), ['export' => 'csv']))); ?>"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-download mr-2"></i><span class="hidden xs:inline">Download CSV</span><span
                                class="xs:hidden">Download</span>
                        </a>
                    </div>
                </div>

                <!-- Filter Section -->
                <form method="GET" action="<?php echo e(route('admin.attendance-reports.index')); ?>"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal Mulai</label>
                        <input type="date" name="date_from" value="<?php echo e($filters['date_from'] ?? ''); ?>"
                            class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm shadow-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal Selesai</label>
                        <input type="date" name="date_to" value="<?php echo e($filters['date_to'] ?? ''); ?>"
                            class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm shadow-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Cari Staf</label>
                        <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Nama staf..."
                            class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm shadow-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="clock_in_only"
                            class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm shadow-xl">
                            <option value="" class="bg-slate-800 text-slate-100">Semua</option>
                            <option value="1" <?php if(request('clock_in_only') == '1'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">
                                Hanya yang Masih Clock In</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Hospital</label>
                        <select name="hospital"
                            class="w-full bg-white/30 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm shadow-xl">
                            <option value="" class="bg-slate-800 text-slate-100">Semua Hospital</option>
                            <option value="alta" <?php if(request('hospital') == 'alta'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">
                                Alta Hospital</option>
                            <option value="roxwood" <?php if(request('hospital') == 'roxwood'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">
                                Roxwood Hospital</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1 flex gap-2">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 text-sm">
                            <i class="fas fa-search mr-2"></i><span class="hidden xs:inline">Filter</span>
                        </button>
                        <a href="<?php echo e(route('admin.attendance-reports.index')); ?>"
                            class="inline-flex items-center justify-center px-4 py-3 bg-white/30 text-white rounded-lg border-2 border-sky-400/40 hover:bg-white/40 transition-all duration-300 shadow-xl">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-day text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Total Hari Kerja</p>
                            <p class="text-2xl font-bold text-white"><?php echo e($summary['total_days']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Total Jam Kerja</p>
                            <p class="text-2xl font-bold text-white">
                                <?php echo e(\App\Helpers\TimeHelper::formatDuration($summary['total_hours'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Rata-rata/Hari</p>
                            <p class="text-2xl font-bold text-white">
                                <?php echo e(\App\Helpers\TimeHelper::formatDuration($summary['average_hours'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl p-6"
                    style="background-color: rgba(7, 89, 133, 0.9);">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-users text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-300 text-sm">Staf Aktif</p>
                            <p class="text-2xl font-bold text-white"><?php echo e($summary['user_stats']->count()); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="backdrop-blur-xl border-2 border-sky-400/60 rounded-2xl shadow-2xl mb-8"
                style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="border-b border-sky-400/20">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('daily')" id="daily-tab"
                            class="tab-button active py-4 px-1 border-b-2 border-sky-400 font-medium text-sm text-sky-400">
                            <i class="fas fa-calendar-day mr-2"></i>Laporan Harian
                        </button>
                        <button onclick="showTab('weekly')" id="weekly-tab"
                            class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-300 hover:text-white hover:border-gray-300">
                            <i class="fas fa-calendar-week mr-2"></i>Laporan Mingguan
                        </button>
                        <button onclick="showTab('monthly')" id="monthly-tab"
                            class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-300 hover:text-white hover:border-gray-300">
                            <i class="fas fa-calendar-alt mr-2"></i>Laporan Bulanan
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Daily Report Tab -->
                    <div id="daily-content" class="tab-content">
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                                        <i class="fas fa-calendar-day mr-2 text-sky-400"></i>
                                        Laporan Absensi Harian
                                    </h3>
                                    <p class="text-gray-300 text-sm">Detail absensi per hari untuk periode
                                        <?php echo e($filters['date_from']); ?> - <?php echo e($filters['date_to']); ?></p>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <a href="<?php echo e(route('admin.attendance-reports.index', array_merge(request()->query(), ['export' => 'csv', 'period' => 'daily']))); ?>"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg text-sm">
                                        <i class="fas fa-download mr-2"></i>Export Harian
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->count() > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white/5 rounded-lg overflow-hidden">
                                    <thead class="bg-black/30">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Tanggal</th>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Staf</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Clock In</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Clock Out</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Durasi Terjadwal</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Waktu Tersisa/Berjalan</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Total Jam</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Status</th>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasPermission('manage_attendance_advanced')): ?>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Catatan</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Aksi</th>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="table-row-hover transition-all duration-200">
                                                <td class="px-4 py-4">
                                                    <div class="text-white font-medium">
                                                        <?php echo e($attendance->work_date->format('d/m/Y')); ?></div>
                                                    <div class="text-gray-400 text-sm"><?php echo e($attendance->work_date->format('l')); ?>

                                                    </div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                                            <i class="fas fa-user text-white text-sm"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-white font-semibold">
                                                                <?php echo e($attendance->user->name ?? 'User #' . $attendance->user_id); ?></p>
                                                            <p class="text-sky-300 text-xs"><?php echo e($attendance->user->role->display_name ?? '-'); ?></p>
                                                            <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-xs font-medium <?php echo e($attendance->session_type === 'meeting' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-green-500/20 text-green-300 border border-green-500/30'); ?>">
                                                                <i class="fas <?php echo e($attendance->session_type === 'meeting' ? 'fa-users' : 'fa-briefcase'); ?> mr-1 text-[10px]"></i>
                                                                <?php echo e($attendance->session_type === 'meeting' ? 'Meeting' : 'Kerja'); ?>

                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->clock_in): ?>
                                                        <div class="text-white font-medium">
                                                            <?php echo e($attendance->clock_in->setTimezone('Asia/Jakarta')->format('H:i')); ?>

                                                        </div>
                                                        <div class="text-gray-400 text-sm">WIB</div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400">-</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->clock_out): ?>
                                                        <div class="text-white font-medium">
                                                            <?php echo e($attendance->clock_out->setTimezone('Asia/Jakarta')->format('H:i')); ?>

                                                        </div>
                                                        <div class="text-gray-400 text-sm">WIB</div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400">-</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->scheduled_duty_minutes): ?>
                                                        <div class="text-white font-medium"><?php echo e($attendance->scheduled_duty_minutes); ?>

                                                            menit</div>
                                                        <div class="text-gray-400 text-xs">Terjadwal</div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 text-sm">-</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->scheduled_duty_minutes && $attendance->is_active): ?>
                                                        <?php
                                                            $remainingTime = $attendance->getRemainingTime();
                                                        ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($remainingTime !== null && $remainingTime > 0): ?>
                                                            <div class="text-yellow-300 font-medium"
                                                                id="remaining-time-<?php echo e($attendance->id); ?>"
                                                                data-end-time="<?php echo e($attendance->scheduled_end_time ? $attendance->scheduled_end_time->toISOString() : ''); ?>">
                                                                <?php echo e(\App\Helpers\TimeHelper::formatDuration($remainingTime)); ?>

                                                            </div>
                                                            <div class="text-yellow-400 text-xs">Tersisa</div>
                                                        <?php else: ?>
                                                            <div class="text-red-300 font-medium">00:00:00</div>
                                                            <div class="text-red-400 text-xs">Habis</div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php elseif($attendance->scheduled_duty_minutes && $attendance->clock_out): ?>
                                                        <?php
                                                            $elapsedTime = $attendance->clock_in->diffInSeconds($attendance->clock_out);
                                                        ?>
                                                        <div class="text-white font-medium">
                                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($elapsedTime)); ?></div>
                                                        <div class="text-gray-400 text-xs">Berjalan</div>
                                                    <?php elseif($attendance->is_active && !$attendance->scheduled_duty_minutes): ?>
                                                        <?php
                                                            $elapsedTime = $attendance->clock_in->diffInSeconds(\Carbon\Carbon::now('Asia/Jakarta'));
                                                        ?>
                                                        <div class="text-white font-medium" id="elapsed-time-<?php echo e($attendance->id); ?>"
                                                            data-clock-in="<?php echo e($attendance->clock_in->toISOString()); ?>">
                                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($elapsedTime)); ?>

                                                        </div>
                                                        <div class="text-gray-400 text-xs">Berjalan</div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 text-sm">-</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->session_duration): ?>
                                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($attendance->session_duration)); ?>

                                                        <?php else: ?>
                                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($attendance->total_hours * 60)); ?>

                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->clock_out): ?>
                                                        <span
                                                            class="status-badge inline-flex items-center px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm font-medium border border-green-500/30">
                                                            <i class="fas fa-check-circle mr-1"></i>Selesai
                                                        </span>
                                                    <?php else: ?>
                                                        <span
                                                            class="status-badge inline-flex items-center px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm font-medium border border-yellow-500/30">
                                                            <i class="fas fa-clock mr-1"></i>Belum Selesai
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasPermission('manage_attendance_advanced')): ?>
                                                <td class="px-4 py-4">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendance->notes): ?>
                                                        <div x-data="{ expanded: false }">
                                                            <div class="text-white text-sm max-w-[200px]" 
                                                                 :class="expanded ? '' : 'line-clamp-2'" 
                                                                 title="<?php echo e($attendance->notes); ?>">
                                                                <?php echo e($attendance->notes); ?>

                                                            </div>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strlen($attendance->notes) > 50): ?>
                                                                <button @click="expanded = !expanded" 
                                                                        class="text-xs text-sky-400 hover:text-sky-300 mt-1 focus:outline-none hover:underline">
                                                                    <span x-text="expanded ? 'Sembunyikan' : 'Selengkapnya'"></span>
                                                                </button>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-500">-</span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$attendance->clock_out): ?>
                                                            <button
                                                                onclick="forceCheckOut(event, <?php echo e($attendance->id); ?>, <?php echo e(json_encode($attendance->user->name ?? 'User #' . $attendance->user_id)); ?>)"
                                                                class="force-checkout-btn inline-flex items-center px-2 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 hover:text-red-200 rounded-lg text-xs font-medium border border-red-500/30 hover:border-red-500/50 transition-all duration-200"
                                                                title="Force Check Out">
                                                                <i class="fas fa-power-off mr-1"></i>Force
                                                            </button>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                        <!-- Edit Button -->
                                                        <button
                                                            onclick="openEditModal(<?php echo e($attendance->id); ?>, '<?php echo e($attendance->clock_in->format('H:i')); ?>', '<?php echo e($attendance->clock_out ? $attendance->clock_out->format('H:i') : ''); ?>', <?php echo e(json_encode($attendance->user->name ?? 'User #' . $attendance->user_id)); ?>, '<?php echo e($attendance->work_date->format('d/m/Y')); ?>')"
                                                            class="inline-flex items-center px-2 py-1.5 bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 hover:text-sky-200 rounded-lg text-xs font-medium border border-sky-500/30 hover:border-sky-500/50 transition-all duration-200"
                                                            title="Edit Jam Kerja">
                                                            <i class="fas fa-edit mr-1"></i>Edit
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <button
                                                            onclick="deleteAttendance(<?php echo e($attendance->id); ?>, <?php echo e(json_encode($attendance->user->name ?? 'User #' . $attendance->user_id)); ?>)"
                                                            class="inline-flex items-center px-2 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 hover:text-red-200 rounded-lg text-xs font-medium border border-red-500/30 hover:border-red-500/50 transition-all duration-200"
                                                            title="Hapus Data">
                                                            <i class="fas fa-trash mr-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Links -->
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->hasPages()): ?>
                                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div class="text-sm text-gray-300">
                                        Menampilkan <?php echo e($attendances->firstItem()); ?> - <?php echo e($attendances->lastItem()); ?> dari
                                        <?php echo e($attendances->total()); ?> data
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->onFirstPage()): ?>
                                            <span class="px-3 py-2 bg-white/10 text-gray-400 rounded-lg cursor-not-allowed">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        <?php else: ?>
                                            <a href="<?php echo e($attendances->previousPageUrl()); ?>"
                                                class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <span class="px-4 py-2 bg-sky-500/30 text-white rounded-lg font-medium">
                                            <?php echo e($attendances->currentPage()); ?> / <?php echo e($attendances->lastPage()); ?>

                                        </span>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->hasMorePages()): ?>
                                            <a href="<?php echo e($attendances->nextPageUrl()); ?>"
                                                class="px-3 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="px-3 py-2 bg-white/10 text-gray-400 rounded-lg cursor-not-allowed">
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-sky-700/40 rounded-full flex items-center justify-center mx-auto mb-4 border border-sky-400/30">
                                    <i class="fas fa-calendar-times text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-2">Tidak Ada Data</h3>
                                <p class="text-sky-200">Tidak ada data absensi untuk periode yang dipilih</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Weekly Report Tab -->
                    <div id="weekly-content" class="tab-content hidden">
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                                        <i class="fas fa-calendar-week mr-2 text-sky-400"></i>
                                        Laporan Absensi Mingguan
                                    </h3>
                                    <p class="text-gray-300 text-sm">Rekap absensi per minggu untuk periode
                                        <?php echo e($filters['date_from']); ?> - <?php echo e($filters['date_to']); ?></p>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <a href="<?php echo e(route('admin.attendance-reports.index', array_merge(request()->query(), ['export' => 'weekly']))); ?>"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg text-sm">
                                        <i class="fas fa-download mr-2"></i>Export Mingguan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                            $weeklyData = $attendances->groupBy(function ($attendance) {
                                return $attendance->work_date->startOfWeek()->format('Y-m-d');
                            });
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($weeklyData->count() > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white/5 rounded-lg overflow-hidden">
                                    <thead class="bg-black/30">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Minggu</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Total Hari</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Total Jam</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Rata-rata/Hari</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Staf Aktif</th>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Per Staf (Total Jam)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $weeklyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekStart => $weekAttendances): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $weekEnd = \Carbon\Carbon::parse($weekStart)->endOfWeek();
                                                $totalHours = $weekAttendances->sum('session_duration');
                                                $totalDays = $weekAttendances->count();
                                                $averageHours = $totalDays > 0 ? $totalHours / $totalDays : 0;
                                                $activeStaff = $weekAttendances->pluck('user_id')->unique()->count();
                                            ?>
                                            <tr class="table-row-hover transition-all duration-200">
                                                <td class="px-4 py-4">
                                                    <div class="text-white font-medium">
                                                        <?php echo e(\Carbon\Carbon::parse($weekStart)->format('d/m')); ?> -
                                                        <?php echo e($weekEnd->format('d/m/Y')); ?>

                                                    </div>
                                                    <div class="text-gray-400 text-sm">Minggu
                                                        ke-<?php echo e(\Carbon\Carbon::parse($weekStart)->weekOfYear); ?></div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg"><?php echo e($totalDays); ?></div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg">
                                                        <?php echo e(\App\Helpers\TimeHelper::formatDuration($totalHours)); ?></div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg">
                                                        <?php echo e(\App\Helpers\TimeHelper::formatDuration($averageHours)); ?></div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg"><?php echo e($activeStaff); ?></div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <?php
                                                        $byUser = $weekAttendances->groupBy('user_id');
                                                    ?>
                                                    <div class="space-y-2">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $byUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $uTotal = $items->sum('session_duration');
                                                                $u = $items->first()->user;
                                                                $uDays = $items->count();
                                                                $uAvg = $uDays > 0 ? $uTotal / $uDays : 0;
                                                            ?>
                                                            <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                                                                <div class="flex items-center justify-between mb-1">
                                                                    <span
                                                                        class="font-semibold text-white text-sm"><?php echo e($u?->name ?? 'User #' . $userId); ?></span>
                                                                    <span class="text-xs text-gray-400"><?php echo e($uDays); ?> hari</span>
                                                                </div>
                                                                <div class="text-xs text-gray-300">
                                                                    Total: <span
                                                                        class="text-white font-medium"><?php echo e(\App\Helpers\TimeHelper::formatDuration($uTotal)); ?></span>
                                                                    | Rata: <span
                                                                        class="text-white font-medium"><?php echo e(\App\Helpers\TimeHelper::formatDuration($uAvg)); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-sky-700/40 rounded-full flex items-center justify-center mx-auto mb-4 border border-sky-400/30">
                                    <i class="fas fa-calendar-times text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-2">Tidak Ada Data</h3>
                                <p class="text-sky-200">Tidak ada data absensi untuk periode yang dipilih</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Monthly Report Tab -->
                    <div id="monthly-content" class="tab-content hidden">
                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-sky-400"></i>
                                        Laporan Absensi Bulanan
                                    </h3>
                                    <p class="text-gray-300 text-sm">Rekap absensi per bulan untuk periode
                                        <?php echo e($filters['date_from']); ?> - <?php echo e($filters['date_to']); ?></p>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <a href="<?php echo e(route('admin.attendance-reports.index', array_merge(request()->query(), ['export' => 'csv', 'period' => 'monthly']))); ?>"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg text-sm">
                                        <i class="fas fa-download mr-2"></i>Export Bulanan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                            $monthlyData = $attendances->groupBy(function ($attendance) {
                                return $attendance->work_date->format('Y-m');
                            });
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($monthlyData->count() > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white/5 rounded-lg overflow-hidden">
                                    <thead class="bg-black/30">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Bulan</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Total Hari</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Total Jam</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Rata-rata/Hari</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Staf Aktif</th>
                                            <th
                                                class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                                Per Staf (Total Jam)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthlyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $monthAttendances): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $totalHours = $monthAttendances->sum('session_duration');
                                                $uniqueDaysCount = $monthAttendances->unique('work_date')->count();
                                                $averageHours = $uniqueDaysCount > 0 ? $totalHours / $uniqueDaysCount : 0;
                                                $activeStaff = $monthAttendances->pluck('user_id')->unique()->count();
                                            ?>
                                            <tr class="table-row-hover transition-all duration-200">
                                                <td class="px-4 py-4">
                                                    <div class="text-white font-medium text-lg">
                                                        <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?>

                                                    </div>
                                                    <div class="text-gray-400 text-sm">
                                                        <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y')); ?>

                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg"><?php echo e($monthAttendances->count()); ?>

                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg">
                                                        <?php echo e(\App\Helpers\TimeHelper::formatDuration($totalHours)); ?></div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg">
                                                        <?php echo e(\App\Helpers\TimeHelper::formatDuration($averageHours)); ?></div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="text-white font-medium text-lg"><?php echo e($activeStaff); ?></div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <?php
                                                        $byUser = $monthAttendances->groupBy('user_id');
                                                    ?>
                                                    <div class="space-y-2">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $byUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $uTotal = $items->sum('session_duration');
                                                                $u = $items->first()->user;
                                                                $uDays = $items->count();
                                                                $uAvg = $uDays > 0 ? $uTotal / $uDays : 0;
                                                            ?>
                                                            <div class="bg-white/5 rounded-lg p-3 border border-white/10">
                                                                <div class="flex items-center justify-between mb-1">
                                                                    <span
                                                                        class="font-semibold text-white text-sm"><?php echo e($u?->name ?? 'User #' . $userId); ?></span>
                                                                    <span class="text-xs text-gray-400"><?php echo e($uDays); ?> hari</span>
                                                                </div>
                                                                <div class="text-xs text-gray-300">
                                                                    Total: <span
                                                                        class="text-white font-medium"><?php echo e(\App\Helpers\TimeHelper::formatDuration($uTotal)); ?></span>
                                                                    | Rata: <span
                                                                        class="text-white font-medium"><?php echo e(\App\Helpers\TimeHelper::formatDuration($uAvg)); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-sky-700/40 rounded-full flex items-center justify-center mx-auto mb-4 border border-sky-400/30">
                                    <i class="fas fa-calendar-times text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-white mb-2">Tidak Ada Data</h3>
                                <p class="text-sky-200">Tidak ada data absensi untuk periode yang dipilih</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($summary['user_stats']->count() > 0): ?>
                <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-sky-400"></i>
                        Statistik per Staf
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white/5 rounded-lg overflow-hidden">
                            <thead class="bg-black/30">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">
                                        Staf</th>
                                    <th
                                        class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                        Total Hari</th>
                                    <th
                                        class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                        Total Jam</th>
                                    <th
                                        class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                        Rata-rata/Hari</th>
                                    <th
                                        class="px-4 py-3 text-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                                        Periode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $summary['user_stats']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userStat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-white/5 transition-colors duration-200">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-white text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-white font-semibold"><?php echo e($userStat['user']->name); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-white font-medium"><?php echo e($userStat['total_days']); ?></td>
                                        <td class="px-4 py-4 text-center text-white font-medium">
                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($userStat['total_hours'])); ?></td>
                                        <td class="px-4 py-4 text-center text-white font-medium">
                                            <?php echo e(\App\Helpers\TimeHelper::formatDuration($userStat['average_hours'])); ?></td>
                                        <td class="px-4 py-4 text-center text-gray-300 text-sm">
                                            <?php echo e(\Carbon\Carbon::parse($userStat['first_attendance'])->format('d/m')); ?> -
                                            <?php echo e(\Carbon\Carbon::parse($userStat['last_attendance'])->format('d/m/Y')); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Manual Entry Modal -->
            <div id="manualEntryModal"
                class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div
                    class="bg-gradient-to-br from-sky-900 to-sky-800 rounded-2xl shadow-2xl max-w-2xl w-full border-2 border-sky-400/60">
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-sky-400/20">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-clock mr-3 text-sky-400"></i>
                                Tambah Jam Kerja Manual
                            </h3>
                            <button onclick="closeManualEntryModal()"
                                class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all">
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form id="manualEntryForm" class="p-6 space-y-4">
                        <?php echo csrf_field(); ?>

                        <!-- Staff Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-user mr-1"></i> Pilih Staff
                            </label>
                            <select name="user_id" id="user_id" required
                                class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                                <option value="" class="bg-slate-800">-- Pilih Staff --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" class="bg-slate-800"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <p class="text-red-400 text-sm mt-1 hidden" id="error-user_id"></p>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-calendar mr-1"></i> Tanggal
                            </label>
                            <input type="date" name="work_date" id="work_date" required
                                class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                            <p class="text-red-400 text-sm mt-1 hidden" id="error-work_date"></p>
                        </div>

                        <!-- Time Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Waktu Mulai
                                </label>
                                <input type="time" name="clock_in_time" id="clock_in_time" required
                                    class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                                    onchange="calculateDuration()">
                                <p class="text-red-400 text-sm mt-1 hidden" id="error-clock_in_time"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Waktu Berakhir
                                </label>
                                <input type="time" name="clock_out_time" id="clock_out_time" required
                                    class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                                    onchange="calculateDuration()">
                                <p class="text-red-400 text-sm mt-1 hidden" id="error-clock_out_time"></p>
                            </div>
                        </div>

                        <!-- Duration Preview -->
                        <div id="durationPreview" class="hidden bg-sky-500/20 border border-sky-400/40 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-300">Durasi:</span>
                                <span class="text-xl font-bold text-white" id="durationText">-</span>
                            </div>
                        </div>

                        <!-- Session Type (Hidden - defaults to work) -->
                        <input type="hidden" name="session_type" id="session_type" value="work">

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fas fa-sticky-note mr-1"></i> Catatan (Opsional)
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                                placeholder="Contoh: Lupa clock in/out"></textarea>
                        </div>

                        <!-- Error Message -->
                        <div id="formError"
                            class="hidden bg-red-500/20 border border-red-400/40 rounded-lg p-4 text-red-300 text-sm"></div>

                        <!-- Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="closeManualEntryModal()"
                                class="flex-1 px-6 py-3 bg-white/10 text-white rounded-lg font-semibold hover:bg-white/20 transition-all duration-300 border-2 border-sky-400/40">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                                <i class="fas fa-save mr-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
        <div class="bg-gradient-to-br from-sky-900 to-sky-800 rounded-2xl shadow-2xl max-w-lg w-full border-2 border-sky-400/60">
            <!-- Modal Header -->
            <div class="p-6 border-b border-sky-400/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-edit mr-3 text-sky-400"></i>
                        Edit Jam Kerja
                    </h3>
                    <button onclick="closeEditModal()" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all">
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>
                <p class="text-sky-200 mt-2" id="editModalInfo">-</p>
            </div>

            <!-- Modal Body -->
            <form id="editForm" class="p-6 space-y-4">
                <input type="hidden" id="editAttendanceId" name="attendance_id">

                <!-- Time Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-sign-in-alt mr-1"></i> Waktu Mulai
                        </label>
                        <input type="time" name="clock_in_time" id="edit_clock_in_time" required
                               class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                               onchange="calculateEditDuration()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-sign-out-alt mr-1"></i> Waktu Berakhir
                        </label>
                        <input type="time" name="clock_out_time" id="edit_clock_out_time" required
                               class="w-full bg-white/20 backdrop-blur-xl text-white border-2 border-sky-400/40 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                               onchange="calculateEditDuration()">
                    </div>
                </div>

                <!-- Duration Preview -->
                <div id="editDurationPreview" class="bg-sky-500/20 border border-sky-400/40 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Durasi Baru:</span>
                        <span class="text-xl font-bold text-white" id="editDurationText">-</span>
                    </div>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-500/20 border border-yellow-400/40 rounded-lg p-4 text-yellow-300 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Perubahan akan tercatat di audit log. Durasi payroll akan dihitung ulang.
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
                            class="flex-1 px-6 py-3 bg-white/10 text-white rounded-lg font-semibold hover:bg-white/20 transition-all duration-300 border-2 border-sky-400/40">
                        Batal
                    </button>
                    <button type="submit" id="editSubmitBtn"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
        <div class="bg-gradient-to-br from-red-900 to-red-800 rounded-2xl shadow-2xl max-w-md w-full border-2 border-red-400/60">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-500/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-3xl text-red-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Hapus Data Absensi?</h3>
                <p class="text-red-200 mb-6" id="deleteModalInfo">Data ini akan dihapus permanen.</p>
                <input type="hidden" id="deleteAttendanceId">

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 px-6 py-3 bg-white/10 text-white rounded-lg font-semibold hover:bg-white/20 transition-all duration-300 border-2 border-red-400/40">
                        Batal
                    </button>
                    <button type="button" onclick="confirmDelete()" id="deleteConfirmBtn"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-32 left-0 right-0 z-[100000] flex flex-col items-center gap-3 pointer-events-none"></div>

    <!-- Toast Template Styles -->
    <style>
    .toast {
        pointer-events: auto;
        min-width: 320px;
        max-width: 450px;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        backdrop-filter: blur(20px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
        animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .toast.hiding {
        animation: toastSlideOut 0.3s ease-in forwards;
    }
    .toast-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95));
        border: 2px solid rgba(52, 211, 153, 0.5);
    }
    .toast-error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95));
        border: 2px solid rgba(248, 113, 113, 0.5);
    }
    .toast-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.95), rgba(217, 119, 6, 0.95));
        border: 2px solid rgba(251, 191, 36, 0.5);
    }
    .toast-info {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.95), rgba(6, 182, 212, 0.95));
        border: 2px solid rgba(56, 189, 248, 0.5);
    }
    .toast-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
    }
    .toast-content {
        flex: 1;
    }
    .toast-title {
        font-weight: 700;
        font-size: 1rem;
        color: white;
        margin-bottom: 0.25rem;
    }
    .toast-message {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.4;
    }
    .toast-close {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .toast-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 0 0 1rem 1rem;
        overflow: hidden;
    }
    .toast-progress-bar {
        height: 100%;
        background: rgba(255, 255, 255, 0.6);
        animation: toastProgress var(--duration) linear forwards;
    }
    @keyframes toastSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes toastSlideOut {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(-20px); opacity: 0; }
    }
    @keyframes toastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
    // Toast Notification System
    function showToast(type, title, message, duration = 5000) {
        const container = document.getElementById('toastContainer');
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.setProperty('--duration', `${duration}ms`);
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${icons[type]} text-white text-lg"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="closeToast(this.parentElement)">
                <i class="fas fa-times text-sm"></i>
            </button>
            <div class="toast-progress">
                <div class="toast-progress-bar"></div>
            </div>
        `;

        container.appendChild(toast);

        // Auto dismiss
        setTimeout(() => closeToast(toast), duration);
    }

    function closeToast(toast) {
        if (!toast || toast.classList.contains('hiding')) return;
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    }

    // Edit Modal Functions
    function openEditModal(attendanceId, clockIn, clockOut, userName, workDate) {
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        document.getElementById('editAttendanceId').value = attendanceId;
        document.getElementById('edit_clock_in_time').value = clockIn;
        document.getElementById('edit_clock_out_time').value = clockOut || '';
        document.getElementById('editModalInfo').textContent = `${userName} - ${workDate}`;
        
        calculateEditDuration();
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function calculateEditDuration() {
        const clockIn = document.getElementById('edit_clock_in_time').value;
        const clockOut = document.getElementById('edit_clock_out_time').value;
        
        if (clockIn && clockOut) {
            const [inHours, inMinutes] = clockIn.split(':').map(Number);
            const [outHours, outMinutes] = clockOut.split(':').map(Number);
            
            let durationMinutes = (outHours * 60 + outMinutes) - (inHours * 60 + inMinutes);
            
            if (durationMinutes < 0) {
                durationMinutes += 24 * 60;
            }
            
            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;
            
            document.getElementById('editDurationText').textContent = `${hours} jam ${minutes} menit`;
        } else {
            document.getElementById('editDurationText').textContent = '-';
        }
    }

    // Edit Form Submission
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('editSubmitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        
        const attendanceId = document.getElementById('editAttendanceId').value;
        const data = {
            clock_in_time: document.getElementById('edit_clock_in_time').value,
            clock_out_time: document.getElementById('edit_clock_out_time').value
        };
        
        try {
            const response = await fetch(`/admin/attendance-reports/${attendanceId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Berhasil!', `${result.message}\nDurasi baru: ${result.data.duration}`);
                closeEditModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            showToast('error', 'Gagal', error.message || 'Terjadi kesalahan saat menyimpan');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Delete Functions
    function deleteAttendance(attendanceId, userName) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('deleteAttendanceId').value = attendanceId;
        document.getElementById('deleteModalInfo').textContent = `Data absensi ${userName} akan dihapus permanen.`;
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    async function confirmDelete() {
        const deleteBtn = document.getElementById('deleteConfirmBtn');
        const originalText = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
        
        const attendanceId = document.getElementById('deleteAttendanceId').value;
        
        try {
            const response = await fetch(`/admin/attendance-reports/${attendanceId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Berhasil!', result.message);
                closeDeleteModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            showToast('error', 'Gagal', error.message || 'Terjadi kesalahan saat menghapus');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalText;
        }
    }

    // Update remaining time for active sessions with timer
    function updateRemainingTimeInReport() {
        const remainingTimeElements = document.querySelectorAll('[id^="remaining-time-"]');

        remainingTimeElements.forEach(element => {
            const endTimeStr = element.getAttribute('data-end-time');
            if (endTimeStr && endTimeStr !== '' && endTimeStr !== 'null') {
                const endTime = new Date(endTimeStr);
                const now = new Date();
                const diffMs = endTime - now;
                const diffSeconds = Math.max(0, Math.floor(diffMs / 1000));

                if (diffSeconds <= 0) {
                    element.textContent = '00:00:00';
                    element.classList.remove('text-yellow-300');
                    element.classList.add('text-red-300');
                } else {
                    const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                    const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                    const seconds = String(diffSeconds % 60).padStart(2, '0');

                    element.textContent = `${hours}:${minutes}:${seconds}`;

                    // Warning styling jika < 10 menit (600 detik)
                    if (diffSeconds < 600) {
                        element.classList.remove('text-yellow-300');
                        element.classList.add('text-red-300');
                    } else {
                        element.classList.remove('text-red-300');
                        element.classList.add('text-yellow-300');
                    }
                }
            }
        });
    }

    // Update elapsed time for active sessions without timer
    function updateElapsedTimeInReport() {
        const elapsedTimeElements = document.querySelectorAll('[id^="elapsed-time-"]');

        elapsedTimeElements.forEach(element => {
            const clockInTimeStr = element.getAttribute('data-clock-in');
            if (clockInTimeStr && clockInTimeStr !== '' && clockInTimeStr !== 'null') {
                const clockIn = new Date(clockInTimeStr);
                const now = new Date();
                const diffMs = now - clockIn;
                const diffSeconds = Math.max(0, Math.floor(diffMs / 1000));

                const hours = String(Math.floor(diffSeconds / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((diffSeconds % 3600) / 60)).padStart(2, '0');
                const seconds = String(diffSeconds % 60).padStart(2, '0');

                element.textContent = `${hours}:${minutes}:${seconds}`;
            }
        });
    }

    // Update time displays every second
    setInterval(function() {
        updateRemainingTimeInReport();
        updateElapsedTimeInReport();
    }, 1000);

    // Initial update
    updateRemainingTimeInReport();
    updateElapsedTimeInReport();

    // Force Check Out function
    async function forceCheckOut(event, attendanceId, userName) {
        event.preventDefault();
        event.stopPropagation();

        if (!confirm(`Apakah Anda yakin ingin melakukan Force Check Out untuk ${userName}?\n\nPerhatian: Tindakan ini akan menutup sesi absensi secara paksa.`)) {
            return;
        }

        const button = event.target.closest('.force-checkout-btn');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i>Memproses...';

        try {
            // Get CSRF token from meta tag or use csrf_token() helper
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>';

            const response = await fetch(`<?php echo e(route('admin.attendance-reports.force-checkout')); ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    attendance_id: attendanceId
                })
            });

            // Check if response is ok
            if (!response.ok) {
                // Try to get error message from response
                let errorMessage = 'Terjadi kesalahan saat melakukan Force Check Out.';
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // If can't parse JSON, use default message
                    errorMessage = `Terjadi kesalahan (Status: ${response.status}). Silakan coba lagi.`;
                }
                throw new Error(errorMessage);
            }

            const data = await response.json();

            if (data.success) {
                // Show success message
                showToast('success', 'Force Check Out Berhasil', 'Absensi ' + userName + ' telah selesai.\nDurasi: ' + data.data.duration);
                // Reload after short delay
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat melakukan Force Check Out.');
            }
        } catch (error) {
            console.error('Force Check Out Error:', error);
            showToast('error', 'Gagal', error.message || 'Terjadi kesalahan saat melakukan Force Check Out.');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.classList.add('hidden'));

        // Remove active class from all tabs
        const tabs = document.querySelectorAll('.tab-button');
        tabs.forEach(tab => {
            tab.classList.remove('active', 'border-sky-400', 'text-sky-400');
            tab.classList.add('border-transparent', 'text-gray-300');
        });

        // Show selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');

        // Add active class to selected tab
        const activeTab = document.getElementById(tabName + '-tab');
        activeTab.classList.add('active', 'border-sky-400', 'text-sky-400');
        activeTab.classList.remove('border-transparent', 'text-gray-300');

        // Save active tab to localStorage
        localStorage.setItem('activeTab', tabName);
    }

    // Load active tab from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
        const activeTab = localStorage.getItem('activeTab') || 'daily';
        showTab(activeTab);
    });

    // Auto-refresh stats every 30 seconds
    setInterval(function() {
        fetch('<?php echo e(route("admin.attendance-reports.stats")); ?>?period=month')
            .then(response => response.json())
            .then(data => {
                // Update stats if needed
                console.log('Stats updated:', data);
            })
            .catch(error => console.log('Error updating stats:', error));
    }, 30000);


    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Manual Entry Modal Functions
    function openManualEntryModal() {
        document.getElementById('manualEntryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('work_date').value = today;
    }

    function closeManualEntryModal() {
        document.getElementById('manualEntryModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('manualEntryForm').reset();
        document.getElementById('durationPreview').classList.add('hidden');
        // Hide all error messages
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('formError').classList.add('hidden');
    }

    // Calculate duration when times change
    function calculateDuration() {
        const clockIn = document.getElementById('clock_in_time').value;
        const clockOut = document.getElementById('clock_out_time').value;

        if (clockIn && clockOut) {
            const [inHours, inMinutes] = clockIn.split(':').map(Number);
            const [outHours, outMinutes] = clockOut.split(':').map(Number);

            let durationMinutes = (outHours * 60 + outMinutes) - (inHours * 60 + inMinutes);

            // Handle cross-day scenario
            if (durationMinutes < 0) {
                durationMinutes += 24 * 60; // Add 24 hours
            }

            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;

            document.getElementById('durationText').textContent = `${hours} jam ${minutes} menit`;
            document.getElementById('durationPreview').classList.remove('hidden');
        } else {
            document.getElementById('durationPreview').classList.add('hidden');
        }
    }

    // Form submission
    document.getElementById('manualEntryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

        // Hide previous errors
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('formError').classList.add('hidden');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('<?php echo e(route("admin.attendance-reports.manual")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', 'Jam Kerja Ditambahkan', `${result.message}\nDurasi: ${result.data.duration}`);
                closeManualEntryModal();
                setTimeout(() => location.reload(), 1500); // Reload after toast shown
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);

            // Handle validation errors
            if (error.response && error.response.status === 422) {
                const errors = await error.response.json();
                if (errors.errors) {
                    Object.keys(errors.errors).forEach(field => {
                        const errorEl = document.getElementById(`error-${field}`);
                        if (errorEl) {
                            errorEl.textContent = errors.errors[field][0];
                            errorEl.classList.remove('hidden');
                        }
                    });
                }
            } else {
                showToast('error', 'Gagal Menyimpan', error.message || 'Terjadi kesalahan saat menyimpan data');
            }

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/attendance-reports/index.blade.php ENDPATH**/ ?>