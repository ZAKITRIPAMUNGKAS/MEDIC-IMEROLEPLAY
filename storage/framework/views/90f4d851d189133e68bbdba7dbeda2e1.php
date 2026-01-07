

<?php $__env->startSection('title', 'Profil Staf - Portal Medis'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0"
            style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);">
        </div>

        <div class="relative z-10 px-4 py-8 sm:px-6 lg:px-8 text-white">
            <div class="mb-8 text-center">
                <div class="flex justify-center mb-6">
                    <div
                        class="h-16 w-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-user-cog text-white text-2xl"></i>
                    </div>
                </div>
                <h1
                    class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-2">
                    Pengaturan Profil</h1>
                <p class="text-sky-200">Kelola nama dan password akun Anda</p>
            </div>

            <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl"
                style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="p-6 sm:p-8">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                        <div class="mb-4 bg-red-500/40 border-2 border-red-400/70 text-red-100 px-4 py-3 rounded-xl shadow-lg">
                            <ul class="list-disc list-inside text-sm">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <div
                            class="mb-4 bg-green-500/40 border-2 border-green-400/70 text-green-100 px-4 py-3 rounded-xl shadow-lg">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <form method="POST" action="<?php echo e(route('staff.profile.update')); ?>" enctype="multipart/form-data"
                        class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Nama</label>
                                <input type="text" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    required />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Email</label>
                                <input type="email" value="<?php echo e(auth()->user()->email); ?>"
                                    class="w-full px-4 py-3 rounded-xl bg-white/25 text-white border-2 border-white/40 opacity-80 shadow-lg"
                                    disabled />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Rumah Sakit</label>
                                <select name="hospital"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    required>
                                    <option value="alta" <?php echo e((auth()->user()->hospital ?? 'alta') == 'alta' ? 'selected' : ''); ?>>Alta Hospital (EMS)</option>
                                    <option value="roxwood" <?php echo e((auth()->user()->hospital ?? '') == 'roxwood' ? 'selected' : ''); ?>>Roxwood Hospital</option>
                                </select>
                                <p class="text-sky-200 text-xs mt-2">
                                    <i class="fas fa-hospital mr-1"></i>
                                    Pilih rumah sakit tempat Anda bekerja
                                </p>
                            </div>
                        </div>

                        <!-- Profile Image Section -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="<?php echo e(auth()->user()->name); ?>"
                                    onerror="this.onerror=null;this.src='<?php echo e(asset('profile.jpg')); ?>';"
                                    class="w-20 h-20 rounded-full border-4 border-sky-400 object-cover">
                                <div>
                                    <p class="text-white text-lg font-semibold"><?php echo e(auth()->user()->name); ?></p>
                                    <p class="text-sky-200 text-sm">
                                        <?php echo e(auth()->user()->role->display_name ?? auth()->user()->role->name ?? 'Staff'); ?>

                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Upload Foto Profil Baru
                                    (Opsional)</label>
                                <input type="file" name="profile_image" accept="image/*"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-500 file:text-white hover:file:bg-sky-600" />
                                <p class="text-sky-200 text-xs mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Kosongkan jika tidak ingin mengubah foto. Format yang didukung: JPG, PNG, GIF (maksimal
                                    2MB)
                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-300 text-sm mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Password Saat Ini</label>
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="Isi jika ingin ganti password" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Password Baru</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="Minimal 8 karakter" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Konfirmasi Password
                                    Baru</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="bg-gradient-to-r from-sky-500 to-cyan-500 text-white px-6 sm:px-8 py-3 rounded-xl font-bold hover:from-sky-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 shadow-lg inline-flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email Settings Card -->
            <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl mt-8"
                style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-12 w-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Pengaturan Email</h2>
                            <p class="text-sky-200 text-sm">Ubah alamat email akun Anda</p>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
                        <div
                            class="mb-4 bg-blue-500/40 border-2 border-blue-400/70 text-blue-100 px-4 py-3 rounded-xl shadow-lg">
                            <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <form method="POST" action="<?php echo e(route('staff.profile.update-email')); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label class="block text-sm font-semibold text-sky-200 mb-2">Email Saat Ini</label>
                            <input type="email" value="<?php echo e(auth()->user()->email); ?>"
                                class="w-full px-4 py-3 rounded-xl bg-white/25 text-white border-2 border-white/40 opacity-80 shadow-lg"
                                disabled />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-sky-200 mb-2">
                                Password Saat Ini <span class="text-red-400">*</span>
                            </label>
                            <input type="password" name="current_password"
                                class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                placeholder="Masukkan password untuk verifikasi" required />
                            <p class="text-sky-200/70 text-xs mt-1">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Diperlukan untuk keamanan
                            </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-300 text-sm mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">
                                    Email Baru <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="new_email" value="<?php echo e(old('new_email')); ?>"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="email@example.com" required />
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['new_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-300 text-sm mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">
                                    Konfirmasi Email Baru <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="new_email_confirmation"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="Ketik ulang email baru" required />
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['new_email_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-300 text-sm mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 sm:px-8 py-3 rounded-xl font-bold hover:from-cyan-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg inline-flex items-center">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Perbarui Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\staff\profile.blade.php ENDPATH**/ ?>