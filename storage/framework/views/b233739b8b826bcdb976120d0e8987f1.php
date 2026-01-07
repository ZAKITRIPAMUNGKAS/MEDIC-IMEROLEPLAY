<?php $__env->startSection('title', 'Atur Gaji'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-7xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Atur Gaji</h1>
                    <p class="text-sky-200 text-base sm:text-lg">Kelola gaji per jam untuk setiap role</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total Setting</p>
                        <p class="text-xl sm:text-2xl font-bold text-white"><?php echo e($settings->count()); ?></p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="openBulkCreateModal()" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-plus mr-2"></i><span class="hidden xs:inline">Buat untuk Semua Role</span><span class="xs:hidden">Buat Semua</span>
                        </button>
                        <a href="<?php echo e(route('admin.salary-settings.create')); ?>" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-plus mr-2"></i><span class="hidden xs:inline">Tambah Setting</span><span class="xs:hidden">Tambah</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cog text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Total Setting</p>
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($settings->count()); ?></p>
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
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Aktif</p>
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($settings->where('is_active', true)->count()); ?></p>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl elegant-shadow-lg p-4 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pause-circle text-white text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-300 text-xs sm:text-sm font-medium">Nonaktif</p>
                        <p class="text-white text-lg sm:text-xl font-bold"><?php echo e($settings->where('is_active', false)->count()); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Table -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Role</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Gaji per Minggu</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="table-row-hover transition-all duration-200">
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                            <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-sky-400 to-blue-500 flex items-center justify-center">
                                                <span class="text-white text-xs sm:text-sm font-medium"><?php echo e(substr($setting->role_name, 0, 2)); ?></span>
                                            </div>
                                        </div>
                                        <div class="ml-3 sm:ml-4">
                                            <div class="text-sm sm:text-base font-medium text-white"><?php echo e($setting->role_name); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm sm:text-base font-semibold text-green-400"><?php echo e($setting->formatted_weekly_salary); ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm text-gray-300"><?php echo e($setting->description ?: '-'); ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->is_active): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-pause-circle mr-1"></i>Nonaktif
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="<?php echo e(route('admin.salary-settings.edit', $setting)); ?>" 
                                           class="text-sky-400 hover:text-sky-300 transition-colors duration-200"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button onclick="toggleStatus(<?php echo e($setting->id); ?>)" 
                                                class="<?php echo e($setting->is_active ? 'text-yellow-400 hover:text-yellow-300' : 'text-green-400 hover:text-green-300'); ?> transition-colors duration-200"
                                                title="<?php echo e($setting->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                            <i class="fas <?php echo e($setting->is_active ? 'fa-pause' : 'fa-play'); ?>"></i>
                                        </button>
                                        
                                        <button onclick="deleteSetting(<?php echo e($setting->id); ?>)" 
                                                class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-gray-400">
                                    <i class="fas fa-cog text-4xl mb-4"></i>
                                    <p class="text-lg">Belum Ada Setting Gaji</p>
                                    <p class="text-sm">Mulai dengan membuat setting gaji untuk role yang ada</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Create Modal -->
<div id="bulkCreateModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="bulkModalContent">
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Buat Setting untuk Semua Role</h3>
                        <p class="text-sm text-gray-500 mt-1">Buat setting gaji dengan nilai default untuk semua role</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="<?php echo e(route('admin.salary-settings.bulk-create')); ?>" id="bulkCreateForm">
                <?php echo csrf_field(); ?>
                <div class="px-8 py-6 space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Gaji Per Minggu (Default)</label>
                        <div class="relative">
                            <input type="number" name="default_weekly_salary" required step="0.01" min="0"
                                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-700 font-medium"
                                   placeholder="Masukkan gaji per minggu">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-gray-400 font-medium">$</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Gaji Lembur Per Jam (Opsional)</label>
                        <div class="relative">
                            <input type="number" name="default_overtime_rate" step="0.01" min="0"
                                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-700 font-medium"
                                   placeholder="Masukkan gaji lembur per jam">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-gray-400 font-medium">$</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 bg-gray-50 rounded-b-3xl">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeBulkCreateModal()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-semibold">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-magic mr-2"></i>Buat Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openBulkCreateModal() {
    const modal = document.getElementById('bulkCreateModal');
    const modalContent = document.getElementById('bulkModalContent');
    modal.classList.remove('hidden');
    
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 10);
}

function closeBulkCreateModal() {
    const modal = document.getElementById('bulkCreateModal');
    const modalContent = document.getElementById('bulkModalContent');
    
    modalContent.style.transform = 'scale(0.95)';
    modalContent.style.opacity = '0';
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function toggleStatus(settingId) {
    if (confirm('Apakah Anda yakin ingin mengubah status setting ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/salary-settings/${settingId}/toggle-status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSetting(settingId) {
    if (confirm('Apakah Anda yakin ingin menghapus setting ini? Tindakan ini tidak dapat dibatalkan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/salary-settings/${settingId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('bulkCreateModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeBulkCreateModal();
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/salary-settings/index.blade.php ENDPATH**/ ?>