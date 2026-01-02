<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Staf Medis - Login & Register</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <!-- Font Awesome (Local) -->
    <link rel="stylesheet" href="<?php echo e(asset('css/font-awesome.min.css')); ?>">
    <!-- Tailwind CSS (Local) -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            /* Important for the sliding effect */
        }

        /* Input Styles */
        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #334155;
            /* Fallback styles if Tailwind fails */
            padding-left: 2.75rem;
            /* pl-11 (11 * 0.25rem = 2.75rem) */
            padding-right: 1rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            border-radius: 0.75rem;
            /* rounded-xl */
            width: 100%;
            /* Slate-700 for better readability on white/light bg of the form side */
            transition: all 0.3s ease;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(14, 165, 233, 0.5);
            outline: none;
            box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
        }

        .form-input::placeholder {
            color: #94a3b8;
            /* Slate-400 */
        }

        /* Container & Animation Logic */
        /* Tailwind Fallbacks */
        .text-slate-600 {
            color: #475569;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .bg-slate-100 {
            background-color: #f1f5f9;
        }

        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .hidden {
            display: none;
        }

        .appearance-none {
            -webkit-appearance: none;
            appearance: none;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .container-custom {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
        }

        .forms-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .signin-signup {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 75%;
            /* Initial Position: Right Side (Login) */
            width: 50%;
            transition: 1s 0.7s ease-in-out;
            display: grid;
            grid-template-columns: 1fr;
            z-index: 5;
        }

        form {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0rem 5rem;
            transition: all 0.2s 0.7s;
            overflow: hidden;
            grid-column: 1 / 2;
            grid-row: 1 / 2;
        }

        /* Form Visibility Logic */
        form.sign-in-form {
            z-index: 2;
        }

        form.sign-up-form {
            z-index: 1;
            opacity: 0;
        }

        /* Panels (The colored side) */
        .panels-container {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        .panel {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            text-align: center;
            z-index: 6;
        }

        .left-panel {
            pointer-events: all;
            padding: 3rem 17% 2rem 12%;
        }

        .right-panel {
            pointer-events: none;
            padding: 3rem 12% 2rem 17%;
        }

        .panel .content {
            color: #fff;
            transition: transform 0.9s ease-in-out;
            transition-delay: 0.6s;
        }

        /* The Moving Background Blob */
        .container-custom:before {
            content: "";
            position: absolute;
            height: 3500px;
            /* Increased from 2000px */
            width: 3500px;
            /* Increased from 2000px */
            top: -10%;
            right: 45%;
            /* Adjusted for larger size */
            transform: translateY(-50%);
            /* Sky/Cyan Gradient matching branding */
            background-image: linear-gradient(-45deg, #0c4a6e 0%, #0284c7 100%);
            transition: 1.8s ease-in-out;
            border-radius: 50%;
            z-index: 6;
        }

        /* Register Mode (Sign Up Mode) Styles */
        .container-custom.sign-up-mode:before {
            transform: translate(100%, -50%);
            right: 52%;
        }

        .container-custom.sign-up-mode .left-panel .content {
            transform: translateX(-800px);
        }

        .container-custom.sign-up-mode .signin-signup {
            left: 25%;
            /* Moves to Left Size */
        }

        .container-custom.sign-up-mode form.sign-up-form {
            opacity: 1;
            z-index: 2;
        }

        .container-custom.sign-up-mode form.sign-in-form {
            opacity: 0;
            z-index: 1;
        }

        .container-custom.sign-up-mode .right-panel .content {
            transform: translateX(0%);
        }

        .right-panel .content {
            transform: translateX(800px);
        }

        .container-custom.sign-up-mode .left-panel {
            pointer-events: none;
        }

        .container-custom.sign-up-mode .right-panel {
            pointer-events: all;
        }

        /* Mobile Responsiveness */
        @media (max-width: 870px) {
            .container-custom {
                min-height: 800px;
                height: 100vh;
            }

            .signin-signup {
                width: 100%;
                top: 95%;
                transform: translate(-50%, -100%);
                transition: 1s 0.8s ease-in-out;
                left: 50%;
            }

            .panels-container {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr 2fr 1fr;
            }

            .panel {
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                padding: 2.5rem 8%;
                grid-column: 1 / 2;
            }

            .right-panel {
                grid-row: 3 / 4;
            }

            .left-panel {
                grid-row: 1 / 2;
            }

            .panel .content {
                padding-right: 0;
                transition: transform 0.9s ease-in-out;
                transition-delay: 0.8s;
            }

            .container-custom:before {
                width: 1500px;
                height: 1500px;
                transform: translateX(-50%);
                left: 30%;
                bottom: 68%;
                right: initial;
                top: initial;
                transition: 2s ease-in-out;
            }

            .container-custom.sign-up-mode:before {
                transform: translate(-50%, 100%);
                bottom: 32%;
                right: initial;
            }

            .container-custom.sign-up-mode .left-panel .content {
                transform: translateY(-300px);
            }

            .container-custom.sign-up-mode .right-panel .content {
                transform: translateY(0px);
            }

            .right-panel .content {
                transform: translateY(300px);
            }

            .container-custom.sign-up-mode .signin-signup {
                top: 5%;
                transform: translate(-50%, 0);
            }
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">

    <div class="container-custom <?php echo e((isset($mode) && $mode === 'register') ? 'sign-up-mode' : ''); ?>">
        <!-- Forms Container -->
        <div class="forms-container">
            <div class="signin-signup">

                <!-- LOGIN FORM -->
                <form action="<?php echo e(route('staff.login.post')); ?>" method="POST"
                    class="sign-in-form w-full max-w-md mx-auto">
                    <?php echo csrf_field(); ?>
                    <div class="text-center mb-8">
                        <img src="/images/motionlife-logo.png" alt="Logo" class="h-16 mx-auto mb-4 drop-shadow-md">
                        <h2 class="text-3xl font-bold text-slate-800 mb-2">Welcome Back</h2>
                        <p class="text-slate-500 text-sm">Masuk ke portal EMS iMe</p>
                    </div>

                    <div class="w-full space-y-4">
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="text" name="email"
                                class="form-input w-full pl-11 pr-4 py-4 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white"
                                placeholder="Email Address" required>
                        </div>

                        <div class="relative group password-group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" id="login_password"
                                class="form-input w-full pl-11 pr-12 py-4 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white"
                                placeholder="Password" required>
                            <button type="button" onclick="togglePassword('login_password', this)"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-sky-500 transition-colors focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="w-full flex justify-between items-center mt-4 mb-8 text-xs font-medium">
                        <label
                            class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-sky-600 transition-colors">
                            <input type="checkbox" name="remember" class="accent-sky-600 rounded"> Ingat saya
                        </label>
                        <a href="#" class="text-sky-600 hover:text-sky-800">Lupa Password?</a>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-sky-500/30 transform hover:-translate-y-0.5 transition-all duration-300">
                        LOGIN
                    </button>

                    <!-- Mobile only switch (hidden on desktop generally, but useful fallback) -->
                    <p class="mt-8 text-sm text-slate-500 md:hidden">
                        Belum punya akun? <a href="#" id="mobile-sign-up-btn" class="text-sky-600 font-bold">Daftar</a>
                    </p>
                </form>

                <!-- REGISTER FORM -->
                <form action="<?php echo e(route('staff.register.post')); ?>" method="POST"
                    class="sign-up-form w-full max-w-md mx-auto">
                    <?php echo csrf_field(); ?>
                    <div class="text-center mb-6">
                        <h2 class="text-3xl font-bold text-slate-800 mb-2">Create Account</h2>
                        <p class="text-slate-500 text-sm">Bergabung dengan tim medis kami</p>
                    </div>

                    <div class="w-full space-y-3">
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" name="name"
                                class="form-input w-full pl-11 pr-4 py-3 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white"
                                placeholder="Nama Lengkap" required>
                        </div>

                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="email"
                                class="form-input w-full pl-11 pr-4 py-3 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white"
                                placeholder="Email Address" required>
                        </div>

                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <select name="role_id" required
                                class="form-input w-full pl-11 pr-4 py-3 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white appearance-none cursor-pointer text-slate-600">
                                <option value="" disabled selected>Pilih Peran Staf</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($roles)): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role->id); ?>">
                                            <?php echo e(ucfirst($role->display_name ?? str_replace('_', ' ', (string) $role->name))); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        <div class="relative group password-group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" id="reg_password"
                                class="form-input w-full pl-11 pr-12 py-3 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white"
                                placeholder="Password" required>
                            <button type="button" onclick="togglePassword('reg_password', this)"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-sky-500 transition-colors focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="relative group password-group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-sky-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="reg_password_confirmation"
                                class="form-input w-full pl-11 pr-12 py-3 rounded-xl text-sm bg-slate-100 border-transparent focus:bg-white"
                                placeholder="Confirm Password" required>
                            <button type="button" onclick="togglePassword('reg_password_confirmation', this)"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-sky-500 transition-colors focus:outline-none">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-500 hover:to-sky-500 text-white font-bold rounded-xl shadow-lg hover:shadow-cyan-500/30 transform hover:-translate-y-0.5 transition-all duration-300 mt-6">
                        REGISTER
                    </button>

                    <p class="mt-6 text-sm text-slate-500 md:hidden">
                        Sudah punya akun? <a href="#" id="mobile-sign-in-btn" class="text-sky-600 font-bold">Login</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Panels Container -->
        <div class="panels-container">

            <!-- LEFT PANEL: Visible when in Login Mode (Right side blob covers Right Panel, Form is on Right) 
                 Wait, logically:
                 Standard Mode: Form is on Right. Blob is on Left. 
                 So Left Panel should be visible on the Left side.
            -->
            <div class="panel left-panel">
                <div class="content">
                    <h3 class="text-4xl font-bold mb-4">Belum Punya Akun?</h3>
                    <p class="text-sky-100 text-lg mb-8 max-w-sm mx-auto">
                        Bergabunglah dengan tim medis profesional iMe. Akses dashboard lengkap, manajemen gaji,
                        dan jadwal dinas.
                    </p>
                    <button
                        class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-sky-700 transition-all duration-300"
                        id="sign-up-btn">
                        DAFTAR SEKARANG
                    </button>

                    <div class="mt-12 grid grid-cols-2 gap-4 max-w-md mx-auto text-left">
                        <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                            <i class="fas fa-user-md text-2xl mb-2 text-sky-300"></i>
                            <div class="text-sm font-bold">Karir Jelas</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                            <i class="fas fa-calendar-check text-2xl mb-2 text-sky-300"></i>
                            <div class="text-sm font-bold">Jadwal Fleksibel</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                            <i class="fas fa-heart text-2xl mb-2 text-sky-300"></i>
                            <div class="text-sm font-bold">Banyak Teman</div>
                            <div class="text-[10px] text-sky-200">(Besa Jadi Pasangan)</div>
                        </div>
                        <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                            <i class="fas fa-money-bill-wave text-2xl mb-2 text-sky-300"></i>
                            <div class="text-sm font-bold">Gaji Pasti</div>
                            <div class="text-[10px] text-sky-200">(Yang Penting Happy Duty)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: Visible when in Register Mode (Form moves to Left, Blob moves to Right) -->
            <div class="panel right-panel">
                <div class="content">
                    <h3 class="text-4xl font-bold mb-4">Sudah Bergabung?</h3>
                    <p class="text-sky-100 text-lg mb-8 max-w-sm mx-auto">
                        Silakan login untuk mengakses dashboard personal Anda dan mulai beraktivitas.
                    </p>
                    <button
                        class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-full hover:bg-white hover:text-sky-700 transition-all duration-300"
                        id="sign-in-btn">
                        LOGIN STAFF
                    </button>

                    <div class="mt-12 flex flex-col gap-4 max-w-xs mx-auto text-left">
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-check-circle text-sky-300"></i>
                            <span class="text-sm font-medium">Akses Dashboard</span>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-check-circle text-sky-300"></i>
                            <span class="text-sm font-medium">Laporan Harian</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const mobile_sign_in_btn = document.querySelector("#mobile-sign-in-btn");
        const mobile_sign_up_btn = document.querySelector("#mobile-sign-up-btn");
        const container = document.querySelector(".container-custom");

        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });

        // Mobile fallback listeners
        if (mobile_sign_up_btn) {
            mobile_sign_up_btn.addEventListener("click", (e) => {
                e.preventDefault();
                container.classList.add("sign-up-mode");
            });
        }

        if (mobile_sign_in_btn) {
            mobile_sign_in_btn.addEventListener("click", (e) => {
                e.preventDefault();
                container.classList.remove("sign-up-mode");
            });
        }

        // Toggle Password Function (Vanilla JS replacement for Alpine)
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html><?php /**PATH D:\website\EMS-IME\public_html\resources\views/auth/portal.blade.php ENDPATH**/ ?>