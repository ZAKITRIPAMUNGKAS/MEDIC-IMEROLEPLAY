@extends('layouts.app')

@section('title', 'Login Staf - Portal Medis')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0"
            style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);">
        </div>

        <div class="relative z-10 max-w-md w-full space-y-8">
            <div class="text-center">
                <div
                    class="mx-auto h-20 w-20 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-2xl animate-float">
                    <i class="fas fa-user-md text-white text-3xl"></i>
                </div>
                <h2
                    class="text-4xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-4">
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
                <form class="space-y-6" method="POST" action="{{ route('staff.login.post') }}" id="loginForm">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-lg font-bold text-slate-900 mb-2">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full px-4 py-3 rounded-2xl text-lg font-semibold text-slate-900 placeholder-slate-500 bg-white/90 border border-slate-200 shadow-[0_10px_30px_rgba(2,6,23,0.06)] focus:outline-none focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition-all duration-300 @error('email') border-red-500 ring-red-300 @enderror"
                                placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-lg font-bold text-slate-900 mb-2">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" autocomplete="current-password"
                                    required
                                    class="w-full px-4 py-3 pr-12 rounded-2xl text-lg font-semibold text-slate-900 placeholder-slate-500 bg-white/90 border border-slate-200 shadow-[0_10px_30px_rgba(2,6,23,0.06)] focus:outline-none focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition-all duration-300 @error('password') border-red-500 ring-red-300 @enderror"
                                    placeholder="Masukkan password Anda">
                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors duration-200"
                                    aria-label="Toggle password visibility">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
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
                        <button type="submit" id="loginButton"
                            class="w-full flex justify-center items-center py-4 px-6 text-lg font-bold rounded-2xl text-white shadow-[0_18px_40px_rgba(14,165,233,0.35)] bg-[linear-gradient(135deg,#0ea5e9,#06b6d4)] hover:shadow-[0_22px_50px_rgba(14,165,233,0.45)] focus:outline-none focus:ring-4 focus:ring-sky-300 transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-3"></i>
                            <span id="buttonText">Masuk ke Dashboard</span>
                        </button>
                    </div>

                    <div class="text-center space-y-3">
                        <div>
                            <a href="{{ route('staff.register') }}"
                                class="inline-flex items-center px-6 py-3 rounded-2xl font-semibold text-white bg-[linear-gradient(135deg,#10b981,#059669)] shadow-[0_16px_36px_rgba(16,185,129,0.30)] hover:shadow-[0_20px_44px_rgba(16,185,129,0.40)] transition-all duration-300">
                                <i class="fas fa-user-plus mr-2"></i>Daftar Staf Baru
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('public.index') }}"
                                class="text-sky-600 hover:text-sky-500 font-medium transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali ke halaman utama
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const loginForm = document.getElementById('loginForm');
                const loginButton = document.getElementById('loginButton');
                const buttonText = document.getElementById('buttonText');
                const togglePasswordBtn = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const passwordIcon = document.getElementById('passwordIcon');

                /**
                 * Toggle password visibility
                 */
                function togglePasswordVisibility() {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    // Update icon
                    passwordIcon.classList.toggle('fa-eye', !isPassword);
                    passwordIcon.classList.toggle('fa-eye-slash', isPassword);
                }

                /**
                 * Disable login button and show processing state
                 */
                function setButtonProcessing() {
                    loginButton.disabled = true;
                    buttonText.textContent = 'Memproses...';
                    loginButton.classList.add('opacity-50', 'cursor-not-allowed');
                }

                // Event Listeners
                togglePasswordBtn.addEventListener('click', togglePasswordVisibility);

                loginForm.addEventListener('submit', function () {
                    setButtonProcessing();
                });
            });
        </script>
    @endpush
@endsection