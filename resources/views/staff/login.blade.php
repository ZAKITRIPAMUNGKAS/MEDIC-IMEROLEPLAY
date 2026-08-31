@extends('layouts.app')

@section('title', 'Login Staf - Portal Medis iMe')

@section('content')
    <div
        class="min-h-[calc(100dvh-4rem)] flex flex-col justify-center bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 py-4 sm:py-10 px-3 sm:px-6 lg:px-8 relative">
        <!-- Background Effects -->
        <div class="absolute inset-0 bg-black/20 pointer-events-none"></div>
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);">
        </div>

        <div class="relative z-10 max-w-md w-full mx-auto my-auto space-y-4 sm:space-y-6">
            <div class="text-center">
                <div
                    class="mx-auto h-12 w-12 sm:h-16 sm:w-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-2.5 sm:mb-4 shadow-xl animate-float">
                    <i class="fas fa-user-md text-white text-xl sm:text-2xl"></i>
                </div>
                <h2
                    class="text-xl sm:text-3xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-1 sm:mb-2">
                    Login Staf Medis
                </h2>
                <p class="text-sky-200 text-xs sm:text-sm">
                    Akses area privat untuk tim medis profesional
                </p>
            </div>

            <div class="p-4 sm:p-7 rounded-2xl sm:rounded-3xl" style="
                background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.90));
                box-shadow: 0 20px 50px rgba(2, 6, 23, 0.25), inset 0 1px 0 rgba(255,255,255,.8);
                border: 1px solid rgba(2, 132, 199, .15);
                backdrop-filter: blur(12px);
            ">
                <form class="space-y-3.5 sm:space-y-5" method="POST" action="{{ route('staff.login.post') }}" id="loginForm">
                    @csrf

                    <div class="space-y-3 sm:space-y-4">
                        <div>
                            <label for="email" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="w-full px-3.5 py-2 sm:py-2.5 rounded-xl text-sm sm:text-base font-semibold text-slate-900 placeholder-slate-400 bg-white border border-slate-200 shadow-sm focus:outline-none focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition-all duration-300 @error('email') border-red-500 ring-red-300 @enderror"
                                placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-xs sm:text-sm font-bold text-slate-800 mb-1">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" autocomplete="current-password"
                                    required
                                    class="w-full px-3.5 py-2 sm:py-2.5 pr-11 rounded-xl text-sm sm:text-base font-semibold text-slate-900 placeholder-slate-400 bg-white border border-slate-200 shadow-sm focus:outline-none focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition-all duration-300 @error('password') border-red-500 ring-red-300 @enderror"
                                    placeholder="Masukkan password Anda">
                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors duration-200"
                                    aria-label="Toggle password visibility">
                                    <i class="fas fa-eye text-xs sm:text-sm" id="passwordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-sky-600 focus:ring-sky-500 border-slate-300 rounded" checked>
                            <label for="remember" class="ml-1.5 sm:ml-2 block text-slate-700 font-medium">
                                Ingat saya
                            </label>
                        </div>

                        <div>
                            <a href="#" class="font-semibold text-sky-600 hover:text-sky-500 transition-colors">
                                Lupa password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" id="loginButton"
                            class="w-full flex justify-center items-center py-2.5 sm:py-3 px-4 text-sm sm:text-base font-bold rounded-xl text-white shadow-lg shadow-sky-500/25 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 focus:outline-none focus:ring-4 focus:ring-sky-300 transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <span id="buttonText">Masuk ke Dashboard</span>
                        </button>
                    </div>

                    <div class="text-center space-y-2 pt-2 border-t border-slate-200/80">
                        <div>
                            <a href="{{ route('staff.register') }}"
                                class="inline-flex items-center justify-center w-full px-4 py-2 sm:py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-emerald-500 to-teal-600 shadow-md shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all duration-300">
                                <i class="fas fa-user-plus mr-1.5"></i>Daftar Staf Baru
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('public.index') }}"
                                class="inline-flex items-center text-xs font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                                <i class="fas fa-arrow-left mr-1.5"></i>Kembali ke Beranda Utama
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