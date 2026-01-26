

<?php $__env->startSection('title', 'Duty History - ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-6">
            <a href="<?php echo e(route('admin.duty-tracking.index', ['months' => $selectedMonths])); ?>" class="text-sky-300 hover:text-sky-200">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Leaderboard
            </a>
        </div>

        
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 mb-6 border border-white border-opacity-20">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-16 w-16 rounded-full bg-sky-500 bg-opacity-20 flex items-center justify-center mr-4">
                        <i class="fas fa-user text-3xl text-sky-300"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white"><?php echo e($user->name); ?></h1>
                        <p class="text-sky-200"><?php echo e($user->role->name ?? 'N/A'); ?> • <?php echo e($user->staff_id); ?></p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-4 mb-6 border border-white border-opacity-20">
            <form method="GET" action="<?php echo e(route('admin.duty-tracking.show', $user)); ?>" class="flex items-center gap-4 flex-wrap">
                <span class="text-sky-200 text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-2"></i>Filter Bulan:
                </span>
                <div class="flex flex-wrap gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center bg-white bg-opacity-5 hover:bg-opacity-10 px-3 py-1 rounded-lg cursor-pointer transition-all text-sm">
                            <input type="checkbox" name="months[]" value="<?php echo e($month); ?>" 
                                <?php echo e(in_array($month, $selectedMonths) ? 'checked' : ''); ?>

                                class="mr-2 rounded text-sky-500 focus:ring-sky-400">
                            <span class="text-white"><?php echo e(\Carbon\Carbon::parse($month . '-01')->format('M Y')); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm transition-all">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Duty Time</p>
                        <h3 class="text-2xl font-bold text-emerald-300 mt-1"><?php echo e(number_format($stats['total_duty_seconds'] / 3600, 1)); ?> jam</h3>
                        <p class="text-xs text-sky-300 mt-1"><?php echo e(gmdate('H:i:s', $stats['total_duty_seconds'])); ?></p>
                    </div>
                    <div class="bg-emerald-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-clock text-2xl text-emerald-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Sessions</p>
                        <h3 class="text-2xl font-bold text-purple-300 mt-1"><?php echo e(number_format($stats['session_count'])); ?></h3>
                        <p class="text-xs text-sky-300 mt-1">duty sessions</p>
                    </div>
                    <div class="bg-purple-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-clipboard-list text-2xl text-purple-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Avg per Session</p>
                        <h3 class="text-2xl font-bold text-yellow-300 mt-1"><?php echo e(number_format(($stats['avg_duty_seconds'] ?? 0) / 3600, 1)); ?> jam</h3>
                        <p class="text-xs text-sky-300 mt-1"><?php echo e(gmdate('H:i:s', $stats['avg_duty_seconds'] ?? 0)); ?></p>
                    </div>
                    <div class="bg-yellow-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-chart-bar text-2xl text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg overflow-hidden border border-white border-opacity-20">
            <div class="px-6 py-4 bg-white bg-opacity-5 border-b border-white border-opacity-10">
                <h3 class="text-lg font-bold text-white">
                    <i class="fas fa-history mr-2"></i>Riwayat Duty (<?php echo e($attendances->total()); ?> sessions)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white divide-opacity-10">
                    <thead class="bg-white bg-opacity-5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Location</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white divide-opacity-10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-black hover:bg-opacity-20 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">
                                        <?php echo e(\Carbon\Carbon::parse($attendance->clock_in)->format('d M Y')); ?>

                                    </div>
                                    <div class="text-xs text-sky-300">
                                        <?php echo e(\Carbon\Carbon::parse($attendance->clock_in)->format('l')); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-emerald-300">
                                        <i class="fas fa-sign-in-alt mr-1"></i>
                                        <?php echo e(\Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s')); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-red-300">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        <?php echo e(\Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s')); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-yellow-300">
                                        <?php echo e(number_format($attendance->session_duration / 3600, 1)); ?> jam
                                    </div>
                                    <div class="text-xs text-sky-300">
                                        <?php echo e(gmdate('H:i:s', $attendance->session_duration)); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-400"><?php echo e($attendance->location ?? '-'); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Tidak ada riwayat duty untuk periode yang dipilih</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                    <tfoot class="bg-white bg-opacity-5">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right">
                                <span class="text-lg font-bold text-white">TOTAL:</span>
                            </td>
                            <td colspan="2" class="px-6 py-4">
                                <div class="text-lg font-bold text-emerald-300">
                                    <?php echo e(number_format($stats['total_duty_seconds'] / 3600, 1)); ?> jam
                                </div>
                                <div class="text-sm text-sky-300">
                                    <?php echo e(gmdate('H:i:s', $stats['total_duty_seconds'])); ?> (<?php echo e($attendances->total()); ?> sessions)
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attendances->hasPages()): ?>
                <div class="px-6 py-4 border-t border-white border-opacity-10">
                    <?php echo e($attendances->appends(['months' => $selectedMonths])->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/duty-tracking/show.blade.php ENDPATH**/ ?>