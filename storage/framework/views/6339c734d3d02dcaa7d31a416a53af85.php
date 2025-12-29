

<?php $__env->startSection('title', 'Detail Gaji - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-4xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Detail Gaji</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Informasi lengkap gaji staf</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('admin.payroll.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <?php if($payroll->status === 'pending'): ?>
                        <button onclick="markAsPaid(<?php echo e($payroll->id); ?>)" 
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg font-semibold transition-all duration-300">
                            <i class="fas fa-check mr-2"></i>Tandai Dibayar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Payroll Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
            <!-- Staff Information -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Informasi Staf</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 flex items-center justify-center">
                            <span class="text-white text-lg font-medium"><?php echo e(substr($payroll->user->name, 0, 2)); ?></span>
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-medium text-white"><?php echo e($payroll->user->name); ?></div>
                            <div class="text-sm text-gray-300"><?php echo e($payroll->user->email); ?></div>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-300">Role</p>
                                <p class="text-white font-medium"><?php echo e($payroll->user->role->display_name ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300">Status Akun</p>
                                <p class="text-white font-medium">
                                    <?php if($payroll->user->is_active): ?>
                                        <span class="text-green-400">Aktif</span>
                                    <?php else: ?>
                                        <span class="text-red-400">Tidak Aktif</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Status -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Status Gaji</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <?php if($payroll->status === 'paid'): ?>
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>Dibayar
                            </div>
                        <?php elseif($payroll->status === 'pending'): ?>
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-2"></i>Pending
                            </div>
                        <?php else: ?>
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i>Dibatalkan
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($payroll->paid_at): ?>
                        <div class="border-t border-white/10 pt-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-300">Dibayar Pada</p>
                                    <p class="text-white font-medium"><?php echo e($payroll->paid_at->format('d M Y H:i')); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-300">Dibayar Oleh</p>
                                    <p class="text-white font-medium"><?php echo e($payroll->paidBy->name ?? 'Admin'); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Payroll Details -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
            <h3 class="text-lg sm:text-xl font-semibold text-white mb-6">Detail Gaji</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Periode</p>
                    <p class="text-lg font-semibold text-white"><?php echo e($payroll->period_description); ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Total Jam</p>
                    <p class="text-lg font-semibold text-white"><?php echo e($payroll->formatted_hours); ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Gaji Pokok per Minggu</p>
                    <p class="text-lg font-semibold text-white"><?php echo e($payroll->formatted_base_salary); ?></p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-300 mb-2">Gaji Dihitung</p>
                    <p class="text-xl font-bold text-green-400"><?php echo e($payroll->formatted_salary); ?></p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <?php if($payroll->notes): ?>
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Catatan</h3>
                <div class="bg-white/5 rounded-lg p-4">
                    <p class="text-white"><?php echo e($payroll->notes); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Notifications -->
        <?php if($payroll->notifications->count() > 0): ?>
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-white mb-4">Riwayat Notifikasi</h3>
                <div class="space-y-3">
                    <?php $__currentLoopData = $payroll->notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                        <?php if($notification->sent_at): ?>
                                            <?php echo e($notification->sent_at->format('d M Y H:i')); ?>

                                        <?php else: ?>
                                            Belum dikirim
                                        <?php endif; ?>
                                    </p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        <?php if($notification->status === 'sent'): ?> bg-green-100 text-green-800
                                        <?php elseif($notification->status === 'failed'): ?> bg-red-100 text-red-800
                                        <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($notification->status)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
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

<script>
function markAsPaid(payrollId) {
    document.getElementById('markPaidForm').action = `/admin/payroll/${payrollId}/mark-paid`;
    document.getElementById('markPaidModal').classList.remove('hidden');
}

function closeMarkPaidModal() {
    document.getElementById('markPaidModal').classList.add('hidden');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\admin\payroll\show.blade.php ENDPATH**/ ?>