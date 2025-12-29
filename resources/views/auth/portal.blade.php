<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Staf Medis - Login & Register</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS (CDN for prototype, ideally compiled in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            /* Prevent scrolling during animation */
        }

        /* Container for the double slider */
        .container-custom {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background-color: #fff;
            overflow: hidden;
        }

        /* Forms Container */
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

        form.sign-in-form {
            z-index: 2;
        }

        form.sign-up-form {
            z-index: 1;
            opacity: 0;
        }

        /* Input Styles */
        .input-field {
            max-width: 380px;
            width: 100%;
            background-color: #f0f4f8;
            margin: 10px 0;
            height: 55px;
            border-radius: 55px;
            display: grid;
            grid-template-columns: 15% 85%;
            padding: 0 0.4rem;
            position: relative;
        }

        .input-field i {
            text-align: center;
            line-height: 55px;
            color: #acacac;
            transition: 0.5s;
            font-size: 1.1rem;
        }

        .input-field input,
        .input-field select {
            background: none;
            outline: none;
            border: none;
            line-height: 1;
            font-weight: 500;
            font-size: 1.1rem;
            color: #333;
        }

        .input-field input::placeholder {
            color: #aaa;
            font-weight: 500;
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

        .panel h3 {
            font-weight: 600;
            line-height: 1;
            font-size: 1.5rem;
        }

        .panel p {
            font-size: 0.95rem;
            padding: 0.7rem 0;
        }

        /* Animation Logic */
        .btn.transparent {
            margin: 0;
            background: none;
            border: 2px solid #fff;
            width: 130px;
            height: 41px;
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 49px;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn.transparent:hover {
            background: #fff;
            color: #1e3a8a;
            /* blue-900 */
        }

        .right-panel .image,
        .right-panel .content {
            transform: translateX(800px);
        }

        /* ANIMATION MODE */
        .container-custom.sign-up-mode:before {
            transform: translate(100%, -50%);
            right: 52%;
        }

        .container-custom.sign-up-mode .left-panel .image,
        .container-custom.sign-up-mode .left-panel .content {
            transform: translateX(-800px);
        }

        .container-custom.sign-up-mode .signin-signup {
            left: 25%;
        }

        .container-custom.sign-up-mode form.sign-up-form {
            opacity: 1;
            z-index: 2;
        }

        .container-custom.sign-up-mode form.sign-in-form {
            opacity: 0;
            z-index: 1;
        }

        .container-custom.sign-up-mode .right-panel .image,
        .container-custom.sign-up-mode .right-panel .content {
            transform: translateX(0%);
        }

        .container-custom.sign-up-mode .left-panel {
            pointer-events: none;
        }

        .container-custom.sign-up-mode .right-panel {
            pointer-events: all;
        }

        /* Solid Color Background Blob / Slider */
        .container-custom:before {
            content: "";
            position: absolute;
            height: 2000px;
            width: 2000px;
            top: -10%;
            right: 48%;
            transform: translateY(-50%);
            background-image: linear-gradient(-45deg, #1e3a8a 0%, #0ea5e9 100%);
            transition: 1.8s ease-in-out;
            border-radius: 50%;
            z-index: 6;
        }

        /* Responsiveness */
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
            }

            .signin-signup,
            .container-custom.sign-up-mode .signin-signup {
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

            .image {
                width: 200px;
                transition: transform 0.9s ease-in-out;
                transition-delay: 0.6s;
            }

            .panel .content {
                padding-right: 15%;
                transition: transform 0.9s ease-in-out;
                transition-delay: 0.8s;
            }

            .panel h3 {
                font-size: 1.2rem;
            }

            .panel p {
                font-size: 0.7rem;
                padding: 0.5rem 0;
            }

            .btn.transparent {
                width: 110px;
                height: 35px;
                font-size: 0.7rem;
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

            .container-custom.sign-up-mode .left-panel .image,
            .container-custom.sign-up-mode .left-panel .content {
                transform: translateY(-300px);
            }

            .container-custom.sign-up-mode .right-panel .image,
            .container-custom.sign-up-mode .right-panel .content {
                transform: translateY(0px);
            }

            .right-panel .image,
            .right-panel .content {
                transform: translateY(300px);
            }

            .signin-signup {
                top: 5%;
                transform: translate(-50%, 0);
            }
        }

        @media (max-width: 570px) {
            form {
                padding: 0 1.5rem;
            }

            .image {
                display: none;
            }

            .panel .content {
                padding: 0.5rem 1rem;
            }

            .container-custom {
                padding: 1.5rem;
            }

            .container-custom:before {
                bottom: 72%;
                left: 50%;
            }

            .container-custom.sign-up-mode:before {
                bottom: 28%;
                left: 50%;
            }
        }
    </style>
</head>

<body>
    <div class="container-custom">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- LOGIN FORM -->
                <form action="{{ route('staff.login.post') }}" method="POST" class="sign-in-form">
                    @csrf
                    <h2 class="text-3xl font-bold text-slate-800 mb-2">Login Staf Medis</h2>
                    <p class="text-slate-500 mb-6 font-medium text-sm text-center">Akses area privat untuk medis
                        profesional</p>

                    <div class="input-field shadow-sm">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email" required />
                    </div>
                    <div class="input-field shadow-sm">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required />
                    </div>

                    <div class="w-full max-w-[380px] flex justify-between items-center mt-2 mb-6 text-sm">
                        <label class="flex items-center text-slate-600 gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="accent-blue-600 rounded"> Ingat saya
                        </label>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">Lupa
                            password?</a>
                    </div>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full uppercase text-sm tracking-wider shadow-lg hover:scale-105 transition-all duration-300 w-full max-w-[380px]">
                        Masuk ke Dashboard
                    </button>

                    <p class="text-slate-600 mt-6 text-sm">
                        Belum punya akun? <a href="#" id="sign-up-link"
                            class="text-blue-600 font-bold hover:underline">Daftar disini</a>
                    </p>
                </form>

                <!-- REGISTER FORM -->
                <form action="{{ route('staff.register.post') }}" method="POST" class="sign-up-form">
                    @csrf
                    <h2 class="text-3xl font-bold text-slate-800 mb-2">Pendaftaran Staf</h2>
                    <p class="text-slate-500 mb-6 font-medium text-sm text-center">Buat akun untuk anggota tim medis
                        baru</p>

                    <div class="input-field shadow-sm">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" placeholder="Nama Lengkap" required />
                    </div>
                    <div class="input-field shadow-sm">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required />
                    </div>
                    <div class="input-field shadow-sm">
                        <i class="fas fa-id-badge"></i>
                        <select name="role_id" required class="w-full bg-transparent outline-none">
                            <option value="" disabled selected>Pilih Peran Staf</option>
                            @if(isset($roles))
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-field shadow-sm">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required />
                    </div>
                    <div class="input-field shadow-sm">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                            required />
                    </div>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full uppercase text-sm tracking-wider shadow-lg hover:scale-105 transition-all duration-300 w-full max-w-[380px] mt-4">
                        Daftarkan Staf
                    </button>

                    <p class="text-slate-600 mt-6 text-sm">
                        Sudah punya akun? <a href="#" id="sign-in-link"
                            class="text-blue-600 font-bold hover:underline">Login disini</a>
                    </p>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <!-- LEFT PANEL (Shows when Login is active, displays info for Register) -->
            <!-- Wait, logic: When 'sign-up-mode' is NOT active, we see Left Panel Content on the Left? No. 
                 Standard Mode (Login Form Visible on Left): 
                 - Left Panel (Solid Blue Overlay? No, form is white).
                 - Right Panel (Solid Blue Overlay? Yes).
            -->

            <div class="panel left-panel">
                <div class="content">
                    <h3 class="mb-4">Bergabung dengan Tim Medis Profesional</h3>
                    <p>
                        Dapatkan akses ke fitur kolaborasi tim, jenjang karir yang jelas, dan teknologi medis modern.
                    </p>

                    <!-- Feature List -->
                    <div
                        class="flex flex-col gap-3 text-left w-full max-w-xs mx-auto mt-6 bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-500/30 flex items-center justify-center text-white">
                                <i class="fas fa-users"></i></div>
                            <span class="text-sm font-medium">Kolaborasi Tim</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-500/30 flex items-center justify-center text-white">
                                <i class="fas fa-chart-line"></i></div>
                            <span class="text-sm font-medium">Jenjang Karir</span>
                        </div>
                    </div>

                    <button class="btn transparent mt-8" id="sign-in-btn">
                        Kembali ke Login
                    </button>

                    <div class="mt-8 text-xs text-blue-200">
                        *Pastikan Anda memiliki kredensial resmi.
                    </div>
                </div>
                <!-- Optional Image -->
                <div class="image w-[500px] hidden md:flex items-center justify-center">
                    <i class="fas fa-user-md text-[15rem] text-white opacity-20"></i>
                </div>
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3 class="mb-4">Portal Staf Medis Profesional</h3>
                    <p>
                        Kelola aktivitas medis Anda dengan efisien. Keamanan data terjamin.
                    </p>

                    <!-- Feature List for Login Info -->
                    <div class="flex flex-col gap-3 text-left w-full max-w-xs mx-auto mt-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white"><i
                                    class="fas fa-calendar-check"></i></div>
                            <span class="text-sm font-medium">Manajemen Absensi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white"><i
                                    class="fas fa-file-medical"></i></div>
                            <span class="text-sm font-medium">Formulir Medis</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white"><i
                                    class="fas fa-money-bill-wave"></i></div>
                            <span class="text-sm font-medium">Sistem Penggajian</span>
                        </div>
                    </div>

                    <!-- Small Card -->
                    <div
                        class="mt-8 bg-black/20 p-4 rounded-lg text-left max-w-xs mx-auto border border-white/10 backdrop-blur-md">
                        <h4 class="font-bold text-sm mb-2 text-blue-200"><i class="fas fa-shield-alt mr-2"></i>Keamanan
                            Data</h4>
                        <ul class="text-xs space-y-1 text-white/80 list-disc list-inside">
                            <li>Login terenkripsi SSL</li>
                            <li>Data sesuai standar GDPR</li>
                            <li>Sistem backup otomatis</li>
                        </ul>
                    </div>

                    <button class="btn transparent mt-8" id="sign-up-btn">
                        Daftar Akun Baru
                    </button>
                </div>
                <!-- Optional Image -->
                <div class="image w-[500px] hidden md:flex items-center justify-center">
                    <i class="fas fa-hospital text-[15rem] text-white opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector(".container-custom");
        const sign_in_link = document.querySelector("#sign-in-link");
        const sign_up_link = document.querySelector("#sign-up-link");

        // Main Toggle Logic
        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });

        // Link Triggers (for mobile or alternate access)
        sign_up_link.addEventListener("click", (e) => {
            e.preventDefault();
            container.classList.add("sign-up-mode");
        });

        sign_in_link.addEventListener("click", (e) => {
            e.preventDefault();
            container.classList.remove("sign-up-mode");
        });
    </script>
</body>

</html>