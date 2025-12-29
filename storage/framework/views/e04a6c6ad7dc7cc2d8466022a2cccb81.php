

<?php $__env->startSection('title', 'Gaji Saya - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Gaji Saya</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Lihat riwayat dan status gaji Anda</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-300 text-sm">Total Gaji</p>
                    <p class="text-xl sm:text-2xl font-bold text-white"><?php echo e($summary['total_payrolls']); ?></p>
                </div>
            </div>

            <!-- Filter Section -->
            <form method="GET" action="<?php echo e(route('staff.payroll.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 appearance-none text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php if($filters['status'] == 'pending'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Pending</option>
                        <option value="paid" <?php if($filters['status'] == 'paid'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Dibayar</option>
                        <option value="cancelled" <?php if($filters['status'] == 'cancelled'): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Periode Mulai</label>
                    <input type="date" name="period_start" value="<?php echo e($filters['period_start']); ?>" 
                           class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Periode Akhir</label>
                    <input type="date" name="period_end" value="<?php echo e($filters['period_end']); ?>" 
                           class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 text-sm">
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
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($summary['total_payrolls']); ?></p>
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
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($summary['pending_payrolls']); ?></p>
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
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($summary['paid_payrolls']); ?></p>
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
                        <p class="text-white text-lg sm:text-xl font-bold">$ <?php echo e(number_format($summary['total_amount'], 0, '.', ',')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <?php if($recentNotifications->count() > 0): ?>
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Notifikasi Terbaru</h3>
                <div class="space-y-3">
                    <?php $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white/5 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-white font-medium">
                                        <?php if($notification->notification_type === 'salary_paid'): ?>
                                            <i class="fas fa-check-circle text-green-400 mr-2"></i>Gaji Dibayar
                                        <?php elseif($notification->notification_type === 'salary_pending'): ?>
                                            <i class="fas fa-clock text-yellow-400 mr-2"></i>Gaji Pending
                                        <?php else: ?>
                                            <i class="fas fa-bell text-blue-400 mr-2"></i>Reminder Gaji
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-gray-300 mt-1"><?php echo e($notification->message); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400">
                                        <?php echo e($notification->sent_at ? $notification->sent_at->format('d M Y H:i') : '-'); ?>

                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Payroll Table -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Periode</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Total Jam</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Gaji</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Dibayar</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php $__empty_1 = true; $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="table-row-hover transition-all duration-200">
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base text-white"><?php echo e($payroll->period_description); ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base text-white"><?php echo e($payroll->formatted_hours); ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base font-semibold text-green-400"><?php echo e($payroll->formatted_salary); ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <?php if($payroll->status === 'paid'): ?>
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
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <?php if($payroll->paid_at): ?>
                                        <div class="text-sm text-white"><?php echo e($payroll->paid_at ? $payroll->paid_at->format('d M Y') : '-'); ?></div>
                                        <div class="text-xs text-gray-300">oleh <?php echo e(optional($payroll->paidBy)->name ?? 'Admin'); ?></div>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-400">-</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <a href="<?php echo e(route('staff.payroll.show', $payroll->id)); ?>" 
                                       class="text-sky-400 hover:text-sky-300 transition-colors duration-200">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p class="text-lg">Tidak ada data gaji</p>
                                    <p class="text-sm">Gaji akan muncul setelah admin generate gaji untuk periode Anda</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($payrolls->hasPages()): ?>
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    <?php echo e($payrolls->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\staff\payroll\index.blade.php ENDPATH**/ ?>