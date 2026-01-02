<?php
    $weeklyHours = $weeklyHours ?? '00:00:00';
    $weeklyDays = $weeklyDays ?? null;
    $accumulatedHours = $accumulatedHours ?? null;
?>

<div class="card bg-white/15 backdrop-blur-sm border border-white/30 rounded-3xl shadow-2xl card-hover w-full animate-fade-in-up" style="animation-delay: 0.08s;">
    <div class="p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl">
                    <i class="fas fa-stopwatch text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-[11px] sm:text-xs uppercase tracking-[0.35em] text-sky-200 mb-1">Total Jam Kerja Minggu Ini</p>
                    <p class="text-3xl sm:text-4xl font-black leading-tight stat-number text-sky-100">
                        <?php echo e($weeklyHours); ?>

                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!is_null($weeklyDays)): ?>
                        <p class="text-xs text-sky-200 mt-1">
                            <?php echo e($weeklyDays); ?> hari kerja aktif
                        </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($accumulatedHours)): ?>
                <div class="flex-1">
                    <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-3 sm:px-5 sm:py-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] sm:text-xs uppercase tracking-[0.3em] text-sky-200 mb-1">Akumulasi Jam Sebagai EMS</p>
                            <p class="text-xl sm:text-2xl font-bold text-white leading-tight">
                                <?php echo e($accumulatedHours); ?>

                            </p>
                        </div>
                        <div class="hidden sm:flex w-11 h-11 rounded-2xl bg-white/15 border border-white/20 items-center justify-center">
                            <i class="fas fa-layer-group text-sky-100"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>

<?php /**PATH D:\website\EMS-IME\public_html\resources\views\components\total-weekly-hours-card.blade.php ENDPATH**/ ?>