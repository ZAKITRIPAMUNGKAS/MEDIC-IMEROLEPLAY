

<?php $__env->startSection('title', 'Login Staf - Portal Medis'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);"></div>
    
    <div class="relative z-10 max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-2xl animate-float">
                <i class="fas fa-user-md text-white text-3xl"></i>
            </div>
            <h2 class="text-4xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-4">
                Login Staf Medis
            </h2>
            <p class="text-sky-200 text-lg">
                Akses area privat untuk tim medis profesional
            </p>
        </div>
        
        <div class="p-8 rounded-3xl" style="
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.88));
            box-shadow: 0 24px 60px rgba(2, 6, 23, 0.25), inset 0 1px 0 rgba(255,255,255,.8);
            border: 1px solid rgba(2, 132, 199, .12);
            backdrop-filter: blur(10px);
        ">
            <form class="space-y-6" method="POST" action="<?php echo e(route('staff.login.post')); ?>" id="loginForm">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-lg font-bold text-slate-900 mb-2">Email</label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               class="w-full px-4 py-3 rounded-2xl text-lg font-semibold text-slate-900 placeholder-slate-500 bg-white/90 border border-slate-200 shadow-[0_10px_30px_rgba(2,6,23,0.06)] focus:outline-none focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition-all duration-300 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Masukkan email Anda"
                               value="<?php echo e(old('email')); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="password" class="block text-lg font-bold text-slate-900 mb-2">Password</label>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="current-password" 
                                   required 
                                   class="w-full px-4 py-3 pr-12 rounded-2xl text-lg font-semibold text-slate-900 placeholder-slate-500 bg-white/90 border border-slate-200 shadow-[0_10px_30px_rgba(2,6,23,0.06)] focus:outline-none focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition-all duration-300 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Masukkan password Anda">
                            <button type="button" 
                                    id="togglePassword" 
                                    class="password-toggle-btn absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors duration-200">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-slate-300 rounded" checked>
                        <label for="remember" class="ml-2 block text-sm text-slate-700 font-medium">
                            Ingat saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-semibold text-sky-600 hover:text-sky-500 transition-colors">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            id="loginButton"
                            class="w-full flex justify-center items-center py-4 px-6 text-lg font-bold rounded-2xl text-white shadow-[0_18px_40px_rgba(14,165,233,0.35)] bg-[linear-gradient(135deg,#0ea5e9,#06b6d4)] hover:shadow-[0_22px_50px_rgba(14,165,233,0.45)] focus:outline-none focus:ring-4 focus:ring-sky-300 transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-3"></i>
                        <span id="buttonText">Masuk ke Dashboard</span>
                    </button>
                </div>
                
                <div id="loginStatus" class="hidden text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        <span>Login berhasil! Mengarahkan ke dashboard...</span>
                    </div>
                </div>

                <div class="text-center space-y-3">
                    <div>
                        <a href="<?php echo e(route('staff.register')); ?>" class="inline-flex items-center px-6 py-3 rounded-2xl font-semibold text-white bg-[linear-gradient(135deg,#10b981,#059669)] shadow-[0_16px_36px_rgba(16,185,129,0.30)] hover:shadow-[0_20px_44px_rgba(16,185,129,0.40)] transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Daftar Staf Baru
                        </a>
                    </div>
                    <div>
                        <a href="<?php echo e(route('public.index')); ?>" class="text-sky-600 hover:text-sky-500 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke halaman utama
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const buttonText = document.getElementById('buttonText');
    const loginStatus = document.getElementById('loginStatus');
    
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle icon
        if (type === 'password') {
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        } else {
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        }
    });
    
    // Gunakan submit form standar agar redirect Laravel berjalan normal
    form.addEventListener('submit', function(e) {
        // Pastikan CSRF token ada
        const csrfToken = document.querySelector('input[name="_token"]');
        if (!csrfToken) {
            e.preventDefault();
            alert('CSRF token tidak ditemukan. Silakan refresh halaman dan coba lagi.');
            return;
        }
        
        loginButton.disabled = true;
        buttonText.textContent = 'Memproses...';
        loginButton.classList.add('opacity-50', 'cursor-not-allowed');
    });
    
    function resetButton() {
        loginButton.disabled = false;
        buttonText.textContent = 'Masuk ke Dashboard';
        loginButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    function showSuccessStatus() {
        loginStatus.classList.remove('hidden');
        loginButton.classList.add('hidden');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\website\EMS-IME\public_html\resources\views\staff\login.blade.php ENDPATH**/ ?>