

<?php $__env->startSection('title', 'Duty Tracking & Ranking'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                <i class="fas fa-trophy mr-3"></i>Duty Tracking & Ranking
            </h1>
            <p class="text-sky-200">Leaderboard dan tracking duty staff berdasarkan total waktu</p>
        </div>

        
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 mb-6 border border-white border-opacity-20">
            <form method="GET" action="<?php echo e(route('admin.duty-tracking.index')); ?>" id="monthForm">
                <div class="mb-4">
                    <label class="block text-sky-200 text-sm font-medium mb-3">
                        <i class="fas fa-calendar-alt mr-2"></i>Pilih Bulan (bisa multiple):
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center bg-white bg-opacity-5 hover:bg-opacity-10 p-3 rounded-lg cursor-pointer transition-all border border-white border-opacity-10">
                                <input type="checkbox" name="months[]" value="<?php echo e($month); ?>" 
                                    <?php echo e(in_array($month, $selectedMonths) ? 'checked' : ''); ?>

                                    class="mr-2 rounded text-sky-500 focus:ring-sky-400">
                                <span class="text-white text-sm"><?php echo e(\Carbon\Carbon::parse($month . '-01')->format('M Y')); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-filter mr-2"></i>Tampilkan
                    </button>
                    <a href="<?php echo e(route('admin.duty-tracking.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                    <button type="button" onclick="selectAll()" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-check-double mr-2"></i>Pilih Semua
                    </button>
                </div>
            </form>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($selectedMonths)): ?>
            <div class="mb-6">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sky-200">Periode:</span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $selectedMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-3 py-1 bg-sky-500 bg-opacity-20 text-sky-300 rounded-full text-sm">
                            <?php echo e(\Carbon\Carbon::parse($month . '-01')->format('F Y')); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Staff</p>
                        <h3 class="text-3xl font-bold text-white mt-1"><?php echo e($stats['total_staff']); ?></h3>
                    </div>
                    <div class="bg-sky-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-users text-2xl text-sky-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Total Duty</p>
                        <h3 class="text-3xl font-bold text-emerald-300 mt-1"><?php echo e(number_format($stats['total_duty_seconds'] / 3600, 1)); ?>h</h3>
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
                        <h3 class="text-3xl font-bold text-purple-300 mt-1"><?php echo e(number_format($stats['total_sessions'])); ?></h3>
                    </div>
                    <div class="bg-purple-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-clipboard-list text-2xl text-purple-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 border border-white border-opacity-20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sky-200 text-sm font-medium">Rata-rata</p>
                        <h3 class="text-3xl font-bold text-yellow-300 mt-1"><?php echo e(number_format(($stats['avg_duty_seconds'] ?? 0) / 3600, 1)); ?>h</h3>
                    </div>
                    <div class="bg-yellow-500 bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-chart-bar text-2xl text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($topApprovers) > 0): ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-award mr-2"></i>Top Approval Pelayanan Surat
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topApprovers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="relative bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-5 border border-white border-opacity-20 flex flex-col items-center text-center overflow-hidden group hover:bg-opacity-20 transition-all duration-300">
                            
                            <div class="absolute top-0 right-0 p-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index == 0): ?>
                                    <div class="bg-gradient-to-bl from-yellow-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-lg">
                                        <i class="fas fa-crown mr-1"></i>#1
                                    </div>
                                <?php elseif($index == 1): ?>
                                    <div class="bg-gradient-to-bl from-gray-300 to-gray-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-lg">
                                        #2
                                    </div>
                                <?php elseif($index == 2): ?>
                                    <div class="bg-gradient-to-bl from-orange-400 to-orange-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-lg">
                                        #3
                                    </div>
                                <?php else: ?>
                                    <div class="bg-white bg-opacity-20 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                                        #<?php echo e($index + 1); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="mt-2 h-20 w-20 rounded-full bg-gradient-to-br from-sky-400 to-purple-500 p-0.5 mb-3 shadow-lg transform group-hover:scale-105 transition-transform duration-300">
                                <div class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center overflow-hidden">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stat->processedBy && $stat->processedBy->profile_image): ?>
                                        <img src="<?php echo e($stat->processedBy->profile_image_url); ?>" alt="<?php echo e($stat->processedBy->name); ?>" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <i class="fas fa-user text-3xl text-sky-400"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <h4 class="text-white font-bold text-lg truncate w-full px-2" title="<?php echo e($stat->processedBy->name ?? 'Unknown'); ?>">
                                <?php echo e($stat->processedBy->name ?? 'Unknown'); ?>

                            </h4>
                            <p class="text-sky-200 text-xs mb-3"><?php echo e($stat->processedBy->role->name ?? 'Rank ' . ($index + 1)); ?></p>

                            <div class="bg-white bg-opacity-10 rounded-lg px-6 py-2 w-full mx-4 border border-white border-opacity-10 group-hover:border-opacity-30 transition-all">
                                <div class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500"><?php echo e(number_format($stat->total)); ?></div>
                                <div class="text-[10px] text-gray-300 uppercase tracking-wider">Total Surat</div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg overflow-hidden border border-white border-opacity-20">
            <div class="px-6 py-4 bg-white bg-opacity-5 border-b border-white border-opacity-10">
                <h3 class="text-lg font-bold text-white">
                    <i class="fas fa-medal mr-2"></i>Leaderboard Duty Time
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white divide-opacity-10">
                    <thead class="bg-white bg-opacity-5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Total Duty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Sessions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Avg/Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-sky-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white divide-opacity-10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rankings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-black hover:bg-opacity-20 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rankings->currentPage() == 1 && $index == 0): ?>
                                            <span class="text-3xl">🥇</span>
                                        <?php elseif($rankings->currentPage() == 1 && $index == 1): ?>
                                            <span class="text-3xl">🥈</span>
                                        <?php elseif($rankings->currentPage() == 1 && $index == 2): ?>
                                            <span class="text-3xl">🥉</span>
                                        <?php else: ?>
                                            <span class="text-xl font-bold text-gray-400">#<?php echo e($rankings->firstItem() + $index); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-sky-500 bg-opacity-20 flex items-center justify-center">
                                                <i class="fas fa-user text-sky-300"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white"><?php echo e($user->name); ?></div>
                                            <div class="text-xs text-sky-300"><?php echo e($user->role->name ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-emerald-300">
                                        <?php echo e(number_format($user->total_duty_seconds / 3600, 1)); ?> jam
                                    </div>
                                    <div class="text-xs text-sky-300">
                                        <?php echo e(gmdate('H:i:s', $user->total_duty_seconds)); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-white"><?php echo e(number_format($user->session_count)); ?> sesi</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-yellow-300"><?php echo e(number_format($user->avg_duty_seconds / 3600, 1)); ?> jam</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="<?php echo e(route('admin.duty-tracking.show', ['user' => $user->id, 'months' => $selectedMonths])); ?>" 
                                        class="text-sky-300 hover:text-sky-200">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Tidak ada data duty untuk periode yang dipilih</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rankings->hasPages()): ?>
                <div class="px-6 py-4 border-t border-white border-opacity-10">
                    <?php echo e($rankings->appends(['months' => $selectedMonths])->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('input[name="months[]"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/duty-tracking/index.blade.php ENDPATH**/ ?>