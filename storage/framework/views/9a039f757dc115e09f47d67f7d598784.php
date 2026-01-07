<?php $__env->startSection('title', 'Edit Atur Gaji'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-select {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.12);
        color: #ffffff;
        appearance: none;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }

    .form-select:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        transform: translateY(-1px);
        background: rgba(255,255,255,0.18);
        color: #ffffff !important;
    }

    .form-select option {
        background-color: #0f172a !important;
        color: #ffffff !important;
    }

    .form-select option:checked {
        background-color: #0ea5e9 !important;
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

    .form-select:focus + .select-arrow {
        color: #0ea5e9;
        transform: translateY(-50%) rotate(180deg);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"></div>
    <div class="absolute inset-0 bg-black bg-opacity-30"></div>

    <div class="relative max-w-4xl w-full mx-auto">
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-4 sm:p-6 md:p-8 lg:p-12">
            <div class="text-center mb-6 sm:mb-8 md:mb-10">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">Edit Atur Gaji</h1>
                <p class="text-blue-100 text-sm sm:text-base font-medium">Ubah atur gaji untuk role <?php echo e($salarySetting->role_name); ?></p>
            </div>
            <form method="POST" action="<?php echo e(route('admin.salary-settings.update', $salarySetting)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="border-b border-white/10 pb-6 mb-8">
                    <h3 class="text-xl font-semibold text-white mb-6">Informasi Setting Gaji</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Role Name -->
                        <div>
                            <label for="role_name" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Nama Role <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <select id="role_name" name="role_name" required
                                        class="form-select <?php $__errorArgs = ['role_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Pilih Role</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role->name); ?>" 
                                                <?php echo e(old('role_name', $salarySetting->role_name) == $role->name ? 'selected' : ''); ?>>
                                            <?php echo e($role->display_name ?: $role->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <div class="select-arrow">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['role_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Weekly Salary -->
                        <div>
                            <label for="weekly_salary" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Gaji Per Minggu <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="weekly_salary" name="weekly_salary" required step="0.01" min="0"
                                       class="form-input <?php $__errorArgs = ['weekly_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="Masukkan gaji per minggu"
                                       value="<?php echo e(old('weekly_salary', $salarySetting->weekly_salary)); ?>">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <span class="text-gray-300 font-medium">$</span>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['weekly_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>


                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-white mb-2 font-bold text-lg">
                                Deskripsi (Opsional)
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="form-input <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      placeholder="Tambahkan deskripsi untuk role ini"><?php echo e(old('description', $salarySetting->description)); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="form-error"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Is Active -->
                        <div class="sm:col-span-2 flex items-center space-x-3">
                            <input type="checkbox" name="is_active" value="1" id="is_active" 
                                   class="w-4 h-4 text-sky-600 border-white/20 rounded focus:ring-sky-500 bg-white/10"
                                   <?php echo e(old('is_active', $salarySetting->is_active) ? 'checked' : ''); ?>>
                            <label for="is_active" class="text-white font-medium">Aktifkan setting ini</label>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="<?php echo e(route('admin.salary-settings.index')); ?>" 
                       class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-semibold text-sm sm:text-base">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i>Update Setting
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views/admin/salary-settings/edit.blade.php ENDPATH**/ ?>