

<?php $__env->startSection('title', 'Manajemen Gaji - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Manajemen Gaji</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Kelola dan pantau pembayaran gaji staf</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total Gaji</p>
                        <p class="text-xl sm:text-2xl font-bold text-white"><?php echo e($summary['total_payrolls'] ?? 0); ?></p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Generate Payroll Button -->
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($canGenerateManually) && !$canGenerateManually): ?>
                            <button disabled
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed opacity-60"
                                    title="Generate gaji sudah dilakukan secara otomatis pada hari Minggu jam 23:59. Tidak dapat melakukan generate manual setelah auto-generate.">
                                <i class="fas fa-lock mr-2"></i><span class="hidden xs:inline">Generate Gaji (Otomatis)</span><span class="xs:hidden">Generate (Otomatis)</span>
                            </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($lastWeekPayrollExists) && $lastWeekPayrollExists): ?>
                                <div class="flex items-center px-3 py-2 bg-green-500/20 text-green-300 rounded-lg text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>Auto-generated (Minggu 23:59)</span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <button onclick="openGenerateModal()" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                                <i class="fas fa-plus mr-2"></i><span class="hidden xs:inline">Generate Gaji</span><span class="xs:hidden">Generate</span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <a href="<?php echo e(route('admin.payroll.export', request()->query())); ?>" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-download mr-2"></i><span class="hidden xs:inline">Export CSV</span><span class="xs:hidden">Export</span>
                        </a>
                        <button onclick="removeDuplicates()" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg"
                                title="Hapus data gaji duplikat, simpan yang terbaru">
                            <i class="fas fa-trash-alt mr-2"></i><span class="hidden xs:inline">Hapus Duplikat</span><span class="xs:hidden">Duplikat</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Week Navigation -->
            <div class="mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Navigasi Minggu</h3>
                        <p class="text-sm text-gray-300">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($filters['week'])): ?>
                                Menampilkan minggu terpilih
                            <?php else: ?>
                                Menampilkan minggu ini (<?php echo e(\Carbon\Carbon::now()->startOfWeek()->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::now()->endOfWeek()->format('d M Y')); ?>)
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($filters['week'])): ?>
                            <a href="<?php echo e(route('admin.payroll.index', array_merge(request()->query(), ['week' => 'all']))); ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium bg-green-500/20 text-green-300 hover:bg-green-500/30 transition-all duration-200">
                                <i class="fas fa-calendar-alt mr-1"></i>Lihat Semua Minggu
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('admin.payroll.index', array_diff_key(request()->query(), ['week' => '']))); ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 transition-all duration-200">
                                <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <?php if(!empty($filters['week']) && $filters['week'] !== 'all'): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableWeeks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('admin.payroll.index', array_merge(request()->query(), ['week' => $week['date'] ?? '']))); ?>" 
                                   class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo e(($filters['week'] ?? '') == ($week['date'] ?? '') ? 'bg-sky-500 text-white shadow-lg' : 'bg-white/10 text-gray-300 hover:bg-white/20'); ?>">
                                    <?php echo e($week['short_label'] ?? 'Unknown'); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php elseif(!empty($filters['week']) && $filters['week'] === 'all'): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableWeeks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('admin.payroll.index', array_merge(request()->query(), ['week' => $week['date'] ?? '']))); ?>" 
                                   class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-white/10 text-gray-300 hover:bg-white/20">
                                    <?php echo e($week['short_label'] ?? 'Unknown'); ?>

                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <form method="GET" action="<?php echo e(route('admin.payroll.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php if(($filters['status'] ?? '') == 'pending'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Pending</option>
                        <option value="paid" <?php if(($filters['status'] ?? '') == 'paid'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Dibayar</option>
                        <option value="cancelled" <?php if(($filters['status'] ?? '') == 'cancelled'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Rumah Sakit</label>
                    <select name="hospital" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Rumah Sakit</option>
                        <option value="alta" <?php if(($filters['hospital'] ?? '') == 'alta'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Alta</option>
                        <option value="roxwood" <?php if(($filters['hospital'] ?? '') == 'roxwood'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Roxwood</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nama Staf</label>
                    <input type="text" name="staff_name" value="<?php echo e($filters['staff_name'] ?? ''); ?>" 
                           placeholder="Cari nama staf..."
                           class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm placeholder-gray-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Dibayar Oleh</label>
                    <select name="paid_by" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $paidByUsers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php if(($filters['paid_by'] ?? '') == $user->id): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">
                                <?php echo e($user->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Minggu</label>
                    <select name="week" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Minggu Ini</option>
                        <option value="all" <?php if(($filters['week'] ?? '') == 'all'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Semua Minggu</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableWeeks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($week['date'] ?? ''); ?>" <?php if(($filters['week'] ?? '') == ($week['date'] ?? '')): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">
                                <?php echo e($week['label'] ?? 'Unknown'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white rounded-lg px-4 py-3 font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Total Gaji</p>
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($summary['total_payrolls'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Pending</p>
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($summary['pending_payrolls'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Dibayar</p>
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($summary['paid_payrolls'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Total Dibayar</p>
                        <p class="text-white text-lg sm:text-xl font-bold">$ <?php echo e(number_format($summary['total_amount'] ?? 0, 0, '.', ',')); ?></p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Payroll Table - Grouped by Week -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($payrolls) && $payrolls->count() > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekStart => $weekPayrolls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden mb-6">
                    <!-- Week Header -->
                    <div class="bg-gradient-to-r from-sky-500/20 to-blue-500/20 px-6 py-4 border-b border-white/10">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    <i class="fas fa-calendar-week mr-2"></i>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($weekStart !== 'unknown' && !empty($weekStart)): ?>
                                        <?php
                                            try {
                                                $weekDate = \Carbon\Carbon::parse($weekStart);
                                                $weekStartFormatted = $weekDate->format('d M Y');
                                                $weekEndFormatted = $weekDate->copy()->endOfWeek()->format('d M Y');
                                            } catch (\Exception $e) {
                                                $weekStartFormatted = $weekStart;
                                                $weekEndFormatted = '';
                                            }
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($weekEndFormatted)): ?>
                                            Minggu <?php echo e($weekStartFormatted); ?> - <?php echo e($weekEndFormatted); ?>

                                        <?php else: ?>
                                            Minggu <?php echo e($weekStartFormatted); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        Minggu Tidak Diketahui
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </h3>
                                <p class="text-sm text-gray-300"><?php echo e($weekPayrolls->count() ?? 0); ?> gaji • Total: Rp <?php echo e(number_format($weekPayrolls->sum('calculated_salary') ?? 0, 0, ',', '.')); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($weekStart !== 'unknown' && $weekStart === now()->startOfWeek()->format('Y-m-d')): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        <i class="fas fa-star mr-1"></i>Minggu Ini
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <div class="text-sm text-gray-300">Pending</div>
                                    <div class="text-lg font-bold text-yellow-400"><?php echo e($weekPayrolls->where('status', 'pending')->count()); ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-300">Dibayar</div>
                                    <div class="text-lg font-bold text-green-400"><?php echo e($weekPayrolls->where('status', 'paid')->count()); ?></div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($weekPayrolls->where('status', 'pending')->count() > 0): ?>
                                    <button onclick="regenerateWeek('<?php echo e($weekStart); ?>')" 
                                            class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg text-sm font-semibold transition-all duration-300 shadow-lg"
                                            title="Regenerate semua gaji pending minggu ini">
                                        <i class="fas fa-sync-alt mr-1"></i>Regenerate Minggu
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Table for this week -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Staf</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Periode</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Total Jam</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Gaji</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Dibayar</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $weekPayrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $userName = $payroll->user->name ?? 'N/A';
                                        $userEmail = $payroll->user->email ?? 'N/A';
                                        $userInitials = substr($userName, 0, 2);
                                    ?>
                                    <tr class="table-row-hover transition-all duration-200">
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 flex items-center justify-center">
                                                        <span class="text-white text-xs sm:text-sm font-medium"><?php echo e($userInitials); ?></span>
                                                    </div>
                                                </div>
                                                <div class="ml-3 sm:ml-4">
                                                    <div class="text-sm sm:text-base font-medium text-white"><?php echo e($userName); ?></div>
                                                    <div class="text-xs sm:text-sm text-gray-300"><?php echo e($userEmail); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="text-sm sm:text-base text-white"><?php echo e($payroll->period_description ?? '-'); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="text-sm sm:text-base text-white"><?php echo e($payroll->formatted_hours ?? '00:00:00'); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="text-sm sm:text-base font-semibold text-green-400"><?php echo e($payroll->formatted_salary ?? '$ 0'); ?></div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payroll->status === 'paid'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Dibayar
                                                </span>
                                            <?php elseif($payroll->status === 'pending'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i>Dibatalkan
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payroll->paid_at): ?>
                                                <?php
                                                    try {
                                                        $paidAtFormatted = $payroll->paid_at->format('d M Y');
                                                    } catch (\Exception $e) {
                                                        $paidAtFormatted = $payroll->paid_at ?? '-';
                                                    }
                                                ?>
                                                <div class="text-sm text-white"><?php echo e($paidAtFormatted); ?></div>
                                                <div class="text-xs text-gray-300">oleh <?php echo e($payroll->paidBy->name ?? 'Admin'); ?></div>
                                            <?php else: ?>
                                                <div class="text-sm text-gray-400">-</div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="flex space-x-2">
                                                <a href="<?php echo e(route('admin.payroll.show', $payroll)); ?>" 
                                                   class="text-sky-400 hover:text-sky-300 transition-colors duration-200">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payroll->status === 'pending'): ?>
                                                    <button onclick="regeneratePayroll(<?php echo e($payroll->id); ?>)" 
                                                            class="text-purple-400 hover:text-purple-300 transition-colors duration-200"
                                                            title="Regenerate - Hitung ulang dengan formula terbaru">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button onclick="markAsPaid(<?php echo e($payroll->id); ?>)" 
                                                            class="text-green-400 hover:text-green-300 transition-colors duration-200">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button onclick="cancelPayroll(<?php echo e($payroll->id); ?>)" 
                                                            class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                                            title="Tolak">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <button onclick="deletePayroll(<?php echo e($payroll->id); ?>)" 
                                                            class="text-orange-400 hover:text-orange-300 transition-colors duration-200"
                                                            title="Hapus Data Duplikat">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-8 text-center">
                <i class="fas fa-inbox text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-white mb-2">
                    <?php if(!empty($filters['week']) && $filters['week'] === 'all'): ?>
                        Tidak ada data gaji untuk semua minggu
                    <?php elseif(!empty($filters['week'])): ?>
                        Tidak ada data gaji untuk minggu terpilih
                    <?php else: ?>
                        Tidak ada data gaji untuk minggu ini
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h3>
                <p class="text-gray-300 mb-6">
                    <?php if(!empty($filters['week']) && $filters['week'] === 'all'): ?>
                        Belum ada gaji yang di-generate untuk periode apapun
                    <?php elseif(!empty($filters['week'])): ?>
                        Belum ada gaji yang di-generate untuk minggu ini
                    <?php else: ?>
                        Belum ada gaji yang di-generate untuk minggu ini
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($filters['week'])): ?>
                        <a href="<?php echo e(route('admin.payroll.index', array_merge(request()->query(), ['week' => 'all']))); ?>" 
                           class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-calendar-alt mr-2"></i>Lihat Semua Minggu
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<!-- Mark as Paid Modal -->
<div id="markPaidModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tandai sebagai Dibayar</h3>
            <form id="markPaidForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                                  placeholder="Tambahkan catatan untuk pembayaran ini..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeMarkPaidModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-300">
                        Tandai Dibayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Payroll Modal -->
<div id="generateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl max-w-lg w-full mx-4 border border-white/20">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-gray-200/50 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-3xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Generate Gaji</h3>
                        <p class="text-sm text-gray-600">Buat gaji untuk periode tertentu</p>
                    </div>
                </div>
                <button onclick="closeGenerateModal()" 
                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-all duration-200 group">
                    <i class="fas fa-times text-gray-500 group-hover:text-gray-700"></i>
                </button>
            </div>
        </div>
        
        <!-- Form -->
        <form id="generateForm" method="POST" action="<?php echo e(route('admin.payroll.generate')); ?>" class="p-8">
            <?php echo csrf_field(); ?>
            
            <div class="space-y-6">
                <!-- Period Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Periode Mulai
                        </label>
                        <input type="date" name="period_start" required
                               value="<?php echo e(\Carbon\Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d')); ?>"
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-calendar-check text-blue-500 mr-2"></i>Periode Akhir
                        </label>
                        <input type="date" name="period_end" required
                               value="<?php echo e(\Carbon\Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d')); ?>"
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white">
                    </div>
                </div>
                
                
                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span class="text-sm text-blue-700">Generate gaji untuk semua staf aktif (diurutkan berdasarkan nama)</span>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200/50">
                <button type="button" onclick="closeGenerateModal()" 
                        class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-all duration-200 hover:bg-gray-100 rounded-xl">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>Generate Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<script>

function markAsPaid(payrollId) {
    document.getElementById('markPaidForm').action = `/admin/payroll/${payrollId}/mark-paid`;
    document.getElementById('markPaidModal').classList.remove('hidden');
}

function closeMarkPaidModal() {
    document.getElementById('markPaidModal').classList.add('hidden');
}

function cancelPayroll(payrollId) {
    if (confirm('Apakah Anda yakin ingin membatalkan gaji ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payroll/${payrollId}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function deletePayroll(payrollId) {
    if (confirm('Apakah Anda yakin ingin menghapus data gaji ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payroll/${payrollId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(methodInput);
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function removeDuplicates() {
    if (confirm('Apakah Anda yakin ingin menghapus data gaji duplikat?\n\nScript ini akan:\n- Mencari data gaji dengan user, periode mulai, dan periode akhir yang sama\n- Menyimpan data yang terbaru (berdasarkan created_at)\n- Menghapus data duplikat yang lebih lama\n\nData yang sudah dibayar tidak akan dihapus kecuali menggunakan force mode.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/payroll/remove-duplicates';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

// Close modal when clicking outside
const generateModal = document.getElementById('generateModal');
if (generateModal) {
    generateModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeGenerateModal();
        }
    });
}

function regeneratePayroll(payrollId) {
    if (confirm('Apakah Anda yakin ingin regenerate gaji ini?\n\nGaji akan dihitung ulang menggunakan formula terbaru berdasarkan data attendance saat ini.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payroll/${payrollId}/regenerate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function regenerateWeek(weekStart) {
    if (confirm('Apakah Anda yakin ingin regenerate SEMUA gaji pending untuk minggu ini?\n\nSemua gaji pending akan dihitung ulang menggunakan formula terbaru.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/payroll/regenerate-week';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const weekInput = document.createElement('input');
        weekInput.type = 'hidden';
        weekInput.name = 'week_start';
        weekInput.value = weekStart;
        
        form.appendChild(csrfToken);
        form.appendChild(weekInput);
        document.body.appendChild(form);
        form.submit();
    }
}


</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/payroll/index.blade.php ENDPATH**/ ?>