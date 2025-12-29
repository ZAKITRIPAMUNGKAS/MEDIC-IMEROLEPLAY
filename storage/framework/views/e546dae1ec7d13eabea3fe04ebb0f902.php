<?php $__env->startSection('title', 'Daftar Staf Baru - Portal Medis MPK-BA'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);"></div>

    <div class="relative z-10 px-4 py-8 sm:px-6 lg:px-8 text-white">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <div class="h-16 w-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl animate-float">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-4">Daftar Staf Baru</h1>
                <p class="text-xl text-sky-200">Tambahkan anggota tim medis baru ke sistem</p>
            </div>

            <!-- Registration Form -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-8">
                <form method="POST" action="<?php echo e(route('staff.register.post')); ?>" enctype="multipart/form-data" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-lg font-bold text-white mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required
                               class="w-full bg-white text-black placeholder-slate-600 border-3 border-slate-700 rounded-lg px-4 py-3 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold shadow-lg">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-lg font-bold text-white mb-2">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required
                               class="w-full bg-white text-black placeholder-slate-600 border-3 border-slate-700 rounded-lg px-4 py-3 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold shadow-lg">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>


                    <!-- Role Selection -->
                    <div>
                        <label for="role_id" class="block text-lg font-bold text-white mb-3">
                            <i class="fas fa-user-tag mr-2"></i>Pilih Peran Staf
                        </label>
                        <div class="relative">
                            <select id="role_id" name="role_id" required
                                    class="w-full bg-white text-black border-3 border-slate-700 rounded-xl px-4 py-4 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold appearance-none cursor-pointer shadow-lg">
                                <option value="" disabled selected class="bg-slate-800 text-white font-bold">🔽 Pilih peran yang sesuai</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php if(old('role_id') == $role->id): echo 'selected'; endif; ?> class="bg-slate-900 text-white font-bold">
                                        <?php echo e($role->display_name ?? $role->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-sky-300 text-lg"></i>
                            </div>
                        </div>
                        <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-300 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <!-- Role Preview -->
                        <div id="role-preview" class="mt-4 p-4 bg-white/10 rounded-lg border border-white/20 hidden">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-sky-500/20 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-user-md text-sky-400"></i>
                                </div>
                                <div>
                                    <h4 id="role-name" class="text-lg font-semibold text-white mb-1"></h4>
                                    <p id="role-description" class="text-sky-200 text-sm mb-2"></p>
                                    <div id="role-permissions" class="text-sky-300 text-xs"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image Upload -->
                    <div>
                        <label for="profile_image" class="block text-lg font-bold text-white mb-2">
                            <i class="fas fa-camera mr-2"></i>Foto Profil (Opsional)
                        </label>
                        <div class="relative">
                            <input type="file" id="profile_image" name="profile_image" accept="image/*"
                                   class="w-full bg-white text-black border-3 border-slate-700 rounded-lg px-4 py-3 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold shadow-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-500 file:text-white hover:file:bg-sky-600">
                            <div class="mt-2 text-sm text-sky-200">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jika tidak diupload, akan menggunakan foto default
                            </div>
                        </div>
                        <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-sky-200 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                            <button type="button" 
                                    id="togglePassword" 
                                    class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-sky-300 hover:text-sky-100 transition-colors duration-200">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-sky-200 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                            <button type="button" 
                                    id="togglePasswordConfirmation" 
                                    class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-sky-300 hover:text-sky-100 transition-colors duration-200">
                                <i class="fas fa-eye" id="passwordConfirmationIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?php echo e(route('staff.login')); ?>" 
                           class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-white/10 text-white rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300 font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
                        </a>
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-lg">
                            <i class="fas fa-user-plus mr-2"></i>Daftarkan Staf
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="mt-8 glass-effect rounded-xl elegant-shadow-lg p-6">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Informasi Penting</h3>
                        <ul class="text-sky-200 space-y-1 text-sm">
                            <li>• Staf yang didaftarkan akan langsung aktif dan dapat login</li>
                            <li>• Password minimal 8 karakter</li>
                            <li>• Pilih peran yang sesuai: Trainee, Perawat, Co-Ass, Dokter Umum, atau Dokter Spesialis</li>
                            <li>• Role dapat diubah nanti oleh Admin, Eksekutif, atau Manajer</li>
                            <li>• Notifikasi akan dikirim ke Discord saat registrasi berhasil</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.elegant-shadow-lg {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Custom select styling */
select {
    background-image: none;
}

select:focus {
    background-image: none;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const rolePreview = document.getElementById('role-preview');
    const roleName = document.getElementById('role-name');
    const roleDescription = document.getElementById('role-description');
    const rolePermissions = document.getElementById('role-permissions');
    
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const passwordConfirmationIcon = document.getElementById('passwordConfirmationIcon');
    
    // Toggle password visibility
    function togglePasswordVisibility(input, icon) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        // Toggle icon
        if (type === 'password') {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
    
    // Add event listeners
    togglePassword.addEventListener('click', function() {
        togglePasswordVisibility(passwordInput, passwordIcon);
    });
    
    togglePasswordConfirmation.addEventListener('click', function() {
        togglePasswordVisibility(passwordConfirmationInput, passwordConfirmationIcon);
    });

    // Role data diambil langsung dari DB
    const roleData = {
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($role->id); ?>: {
                name: <?php echo json_encode($role->display_name ?? $role->name, 15, 512) ?>,
                description: <?php echo json_encode($role->description ?? 'Peran staf medis profesional', 15, 512) ?>,
                permissions: <?php echo json_encode($role->permissions ?? [], 15, 512) ?>
            },
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    };

    roleSelect.addEventListener('change', function() {
        const selectedRoleId = this.value;
        
        if (selectedRoleId && roleData[selectedRoleId]) {
            const role = roleData[selectedRoleId];
            
            roleName.textContent = role.name;
            roleDescription.textContent = role.description;
            
            // Update permissions (jika kosong, tampilkan info default)
            if (Array.isArray(role.permissions) && role.permissions.length > 0) {
                rolePermissions.innerHTML = role.permissions.map(permission => 
                    `<span class="inline-block bg-sky-500/20 text-sky-300 px-2 py-1 rounded-full text-xs mr-2 mb-1">${permission}</span>`
                ).join('');
            } else {
                rolePermissions.innerHTML = `<span class="text-sky-300 text-xs">Tidak ada hak akses khusus.</span>`;
            }
            
            rolePreview.classList.remove('hidden');
        } else {
            rolePreview.classList.add('hidden');
        }
    });

    // Trigger change event if there's a pre-selected value
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\staff\register.blade.php ENDPATH**/ ?>