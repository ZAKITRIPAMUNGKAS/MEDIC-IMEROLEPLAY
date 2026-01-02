<?php $__env->startSection('title', 'Edit Staf - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700"></div>
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>

        <div class="relative max-w-4xl w-full mx-auto text-white">
            <!-- Header Section -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Edit Data Staf</h1>
                        <p class="text-sky-200 text-lg">Perbarui informasi dan pengaturan akun staf</p>
                    </div>
                    <a href="<?php echo e(route('admin.staff.index')); ?>"
                        class="inline-flex items-center px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-all duration-300 text-sm font-medium backdrop-blur-sm border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>

                <!-- User Info Card -->
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-lg"><?php echo e($user->name); ?></p>
                            <p class="text-gray-300 text-sm"><?php echo e($user->email); ?></p>
                            <p class="text-gray-400 text-xs">Terakhir diperbarui:
                                <?php echo e(optional($user->updated_at)->format('d M Y, H:i') ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8">
                <form method="POST" action="<?php echo e(route('admin.staff.update', $user)); ?>" enctype="multipart/form-data"
                    class="space-y-8">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <!-- Personal Information -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-user mr-3 text-sky-400"></i>
                            Informasi Personal
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Nama Lengkap</label>
                                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                                    required>
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
                                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                    placeholder="contoh@email.com"
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                                    required>
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

                    <!-- Security Information -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-lock mr-3 text-cyan-400"></i>
                            Informasi Keamanan
                        </h2>

                        <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-yellow-400 mr-3"></i>
                                <p class="text-yellow-200 text-sm">Kosongkan password jika tidak ingin mengubahnya</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Password Baru (opsional)</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                                        class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                                    <button type="button" onclick="togglePassword('password')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
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
                                <label class="block text-sm font-medium text-gray-300 mb-3">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        placeholder="Ulangi password baru"
                                        class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-10 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                        <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image Upload -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-camera mr-3 text-purple-400"></i>
                            Foto Profil
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Foto Profil Saat Ini</label>
                                <div class="flex items-center space-x-4">
                                    <img src="<?php echo e($user->profile_image_url); ?>" alt="<?php echo e($user->name); ?>"
                                        onerror="this.onerror=null;this.src='<?php echo e(asset('profile.jpg')); ?>';"
                                        class="w-20 h-20 rounded-full border-4 border-sky-400 object-cover">
                                    <div>
                                        <p class="text-white text-sm font-medium"><?php echo e($user->name); ?></p>
                                        <p class="text-gray-300 text-xs">
                                            <?php echo e($user->role->display_name ?? $user->role->name ?? 'Staff'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Upload Foto Baru
                                    (Opsional)</label>
                                <div class="relative">
                                    <input type="file" name="profile_image" accept="image/*"
                                        class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600">
                                    <div class="mt-2 text-sm text-gray-300">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Kosongkan jika tidak ingin mengubah foto
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
                                <select name="role_id"
                                    class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 appearance-none"
                                    required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role->id); ?>" <?php if(old('role_id', $user->role_id) == $role->id): echo 'selected'; endif; ?>
                                            class="bg-slate-800 text-slate-100">
                                            <?php echo e($role->display_name ?? $role->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
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
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Status Akun</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only"
                                            <?php if(old('is_active', $user->is_active)): echo 'checked'; endif; ?>>
                                        <div class="relative">
                                            <div
                                                class="w-12 h-6 <?php echo e(old('is_active', $user->is_active) ? 'bg-green-500' : 'bg-gray-500'); ?> rounded-full shadow-inner">
                                            </div>
                                            <div
                                                class="absolute top-1 <?php echo e(old('is_active', $user->is_active) ? 'left-7' : 'left-1'); ?> w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 ease-in-out">
                                            </div>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-300">Aktif</span>
                                    </label>
                                </div>
                                <p class="text-gray-400 text-xs mt-2">Akun aktif dapat login dan mengakses sistem</p>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Permissions -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-key mr-3 text-green-400"></i>
                            Akses Khusus
                        </h2>

                        <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                                <p class="text-blue-200 text-sm">Berikan akses khusus untuk fitur tertentu kepada staf ini
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Live Chat Permission -->
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="custom_permissions[]" value="access_live_chat"
                                        class="w-5 h-5 text-cyan-500 bg-white/10 border-white/20 rounded focus:ring-2 focus:ring-cyan-400"
                                        <?php if(in_array('access_live_chat', old('custom_permissions', $user->custom_permissions ?? []))): echo 'checked'; endif; ?>>
                                    <div class="ml-3">
                                        <span class="text-white font-medium flex items-center">
                                            <i class="fas fa-comments text-cyan-400 mr-2"></i>
                                            Live Chat
                                        </span>
                                        <p class="text-gray-400 text-xs mt-1">Akses untuk melihat dan mengelola live chat
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <!-- Feedback Permission -->
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="custom_permissions[]" value="access_feedback"
                                        class="w-5 h-5 text-purple-500 bg-white/10 border-white/20 rounded focus:ring-2 focus:ring-purple-400"
                                        <?php if(in_array('access_feedback', old('custom_permissions', $user->custom_permissions ?? []))): echo 'checked'; endif; ?>>
                                    <div class="ml-3">
                                        <span class="text-white font-medium flex items-center">
                                            <i class="fas fa-paper-plane text-purple-400 mr-2"></i>
                                            Feedback
                                        </span>
                                        <p class="text-gray-400 text-xs mt-1">Akses untuk melihat dan mengelola feedback</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-white/10">
                        <a href="<?php echo e(route('admin.staff.index')); ?>"
                            class="inline-flex items-center justify-center px-6 py-3 bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300 font-medium">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
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

        // Toggle switch functionality
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('is_active');
            const toggleDiv = toggle.nextElementSibling;
            const toggleCircle = toggleDiv.querySelector('div:last-child');
            const toggleBg = toggleDiv.querySelector('div:first-child');

            toggle.addEventListener('change', function () {
                if (this.checked) {
                    toggleBg.classList.remove('bg-gray-500');
                    toggleBg.classList.add('bg-green-500');
                    toggleCircle.classList.remove('left-1');
                    toggleCircle.classList.add('left-7');
                } else {
                    toggleBg.classList.remove('bg-green-500');
                    toggleBg.classList.add('bg-gray-500');
                    toggleCircle.classList.remove('left-7');
                    toggleCircle.classList.add('left-1');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\admin\staff\edit.blade.php ENDPATH**/ ?>