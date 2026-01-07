<?php $__env->startSection('title', 'Tambah Staf - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
<div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <div class="relative max-w-4xl w-full mx-auto text-white">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Buat Akun Staf Baru</h1>
                    <p class="text-sky-200 text-lg">Isi data staf untuk membuat akun baru dengan akses sistem</p>
                </div>
                <a href="<?php echo e(route('admin.staff.index')); ?>" class="inline-flex items-center px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8">
            <form method="POST" action="<?php echo e(route('admin.staff.store')); ?>" enctype="multipart/form-data" class="space-y-8">
                <?php echo csrf_field(); ?>

                <!-- Personal Information -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-user mr-3 text-sky-400"></i>
                        Informasi Personal
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Nama Lengkap</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="Masukkan nama lengkap" class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                <p class="text-red-300 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                </p> 
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Email</label>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="contoh@email.com" class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                <p class="text-red-300 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                </p> 
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Profile Image Upload -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-camera mr-3 text-purple-400"></i>
                        Foto Profil
                    </h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">Upload Foto Profil (Opsional)</label>
                        <div class="relative">
                            <input type="file" name="profile_image" accept="image/*" class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600">
                            <div class="mt-2 text-sm text-gray-300">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jika tidak diupload, akan menggunakan foto default (profile.jpg)
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                            <p class="text-red-300 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                            </p> 
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Security Information -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-lock mr-3 text-cyan-400"></i>
                        Informasi Keamanan
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300" required>
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                <p class="text-red-300 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                </p> 
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300" required>
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                    <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role and Status -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-user-tag mr-3 text-blue-400"></i>
                        Peran dan Status
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Peran</label>
                            <div class="relative">
                                <select name="role_id" id="role_id" class="w-full bg-white/10 text-white border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 appearance-none pr-12" required>
                                    <option value="" disabled selected class="bg-slate-800 text-slate-100">Pilih Peran Staf</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role->id); ?>" data-display="<?php echo e($role->display_name ?? $role->name); ?>" data-description="<?php echo e($role->description ?? ''); ?>" data-permissions='<?php echo json_encode($role->permissions ?? [], 15, 512) ?>' <?php if(old('role_id') == $role->id): echo 'selected'; endif; ?> class="bg-slate-800 text-slate-100">
                                            <?php echo e($role->display_name ?? $role->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-gray-300">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </div>
                            <p class="text-gray-400 text-xs mt-2">Pilih peran untuk menyesuaikan hak akses staf.</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                <p class="text-red-300 text-sm mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                </p> 
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <!-- Preview Role Detail -->
                            <div id="rolePreview" class="mt-4 hidden bg-white/5 border border-white/10 rounded-xl p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p id="rolePreviewName" class="text-white font-semibold text-lg">&nbsp;</p>
                                        <p id="rolePreviewDesc" class="text-gray-300 text-sm mt-1">&nbsp;</p>
                                    </div>
                                    <div class="px-2 py-1 rounded-lg bg-sky-500/20 border border-sky-400/30 text-sky-200 text-xs font-semibold">
                                        <i class="fas fa-user-shield mr-1"></i>Hak Akses
                                    </div>
                                </div>
                                <div id="rolePreviewPerms" class="mt-3 flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Status Akun</label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only" checked>
                                    <div class="relative">
                                        <div class="w-12 h-6 bg-green-500 rounded-full shadow-inner"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 ease-in-out"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-300">Aktif</span>
                                </label>
                            </div>
                            <p class="text-gray-400 text-xs mt-2">Akun aktif dapat login dan mengakses sistem</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-white/10">
                    <a href="<?php echo e(route('admin.staff.index')); ?>" class="inline-flex items-center justify-center px-6 py-3 bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300 font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                        <i class="fas fa-save mr-2"></i>Simpan Staf
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

// Role preview interaction
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('role_id');
    const box = document.getElementById('rolePreview');
    const nameEl = document.getElementById('rolePreviewName');
    const descEl = document.getElementById('rolePreviewDesc');
    const permsEl = document.getElementById('rolePreviewPerms');

    function renderPreview(opt) {
        if (!opt || !opt.value) { box.classList.add('hidden'); return; }
        const label = opt.getAttribute('data-display') || opt.textContent;
        const desc = opt.getAttribute('data-description') || '—';
        let perms = [];
        try { perms = JSON.parse(opt.getAttribute('data-permissions') || '[]'); } catch (e) { perms = []; }

        nameEl.textContent = label;
        descEl.textContent = desc;
        permsEl.innerHTML = '';
        if (Array.isArray(perms) && perms.length) {
            perms.forEach(p => {
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center px-2 py-1 rounded-md bg-white/10 border border-white/20 text-xs text-gray-200';
                chip.innerHTML = `<i class="fas fa-check mr-1 text-sky-300"></i>${p}`;
                permsEl.appendChild(chip);
            });
        } else {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center px-2 py-1 rounded-md bg-white/10 border border-white/20 text-xs text-gray-200';
            chip.textContent = 'Tidak ada permission khusus';
            permsEl.appendChild(chip);
        }
        box.classList.remove('hidden');
    }

    if (select) {
        select.addEventListener('change', function() {
            const opt = select.options[select.selectedIndex];
            renderPreview(opt);
        });
        // Render from old value
        const init = select.options[select.selectedIndex];
        if (init && init.value) renderPreview(init);
    }
});
</script>
<style>
    /* Improve visibility of native <option> list on dark background */
    #role_id option {
        background-color: #0b2550; /* deep blue to match theme */
        color: #e5e7eb; /* text-slate-200 */
    }
    #role_id option[disabled] {
        color: #94a3b8; /* text-slate-400 for placeholder */
    }
    /* High-contrast focus ring for accessibility */
    #role_id:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.45); /* sky-400 */
        border-color: rgba(56, 189, 248, 0.6);
    }
</style>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\admin\staff\create.blade.php ENDPATH**/ ?>