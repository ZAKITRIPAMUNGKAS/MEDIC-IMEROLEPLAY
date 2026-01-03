@extends('layouts.app')

@section('title', 'Daftar Staf Baru - Portal Medis MPK-BA')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-sky-900 via-sky-800 to-sky-700 relative overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0"
            style="background-image: radial-gradient(circle at 20% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);">
        </div>

        <div class="relative z-10 px-4 py-8 sm:px-6 lg:px-8 text-white">
            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <div
                            class="h-16 w-16 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl animate-float">
                            <i class="fas fa-user-plus text-white text-2xl"></i>
                        </div>
                    </div>
                    <h1
                        class="text-4xl md:text-5xl font-black bg-gradient-to-r from-sky-300 to-cyan-300 bg-clip-text text-transparent mb-4">
                        Daftar Staf Baru</h1>
                    <p class="text-xl text-sky-200">Tambahkan anggota tim medis baru ke sistem</p>
                </div>

                <!-- Registration Form -->
                <div class="glass-effect rounded-2xl elegant-shadow-lg p-8">
                    <form method="POST" action="{{ route('staff.register.post') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-lg font-bold text-white mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full bg-white text-black placeholder-slate-600 border-3 border-slate-700 rounded-lg px-4 py-3 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold shadow-lg">
                            @error('name')
                                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-lg font-bold text-white mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-white text-black placeholder-slate-600 border-3 border-slate-700 rounded-lg px-4 py-3 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold shadow-lg">
                            @error('email')
                                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role Selection -->
                        <div>
                            <label for="role_id" class="block text-lg font-bold text-white mb-3">
                                <i class="fas fa-user-tag mr-2"></i>Pilih Peran Staf
                            </label>
                            <div class="relative">
                                <select id="role_id" name="role_id" required
                                    class="w-full bg-white text-black border-3 border-slate-700 rounded-xl px-4 py-4 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold appearance-none cursor-pointer shadow-lg">
                                    <option value="" disabled selected class="bg-slate-800 text-white font-bold">🔽 Pilih
                                        peran yang sesuai</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)
                                            class="bg-slate-900 text-white font-bold">
                                            {{ $role->display_name ?? $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="fas fa-chevron-down text-sky-300 text-lg"></i>
                                </div>
                            </div>
                            @error('role_id')
                                <p class="mt-2 text-sm text-red-300 font-medium">{{ $message }}</p>
                            @enderror

                            <!-- Role Preview -->
                            <div id="rolePreview" class="mt-4 p-4 bg-white/10 rounded-lg border border-white/20 hidden">
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 bg-sky-500/20 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fas fa-user-md text-sky-400"></i>
                                    </div>
                                    <div>
                                        <h4 id="roleName" class="text-lg font-semibold text-white mb-1"></h4>
                                        <p id="roleDescription" class="text-sky-200 text-sm mb-2"></p>
                                        <div id="rolePermissions" class="text-sky-300 text-xs"></div>
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
                            @error('profile_image')
                                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hospital Selection -->
                        <div>
                            <label for="hospital" class="block text-lg font-bold text-white mb-3">
                                <i class="fas fa-hospital mr-2"></i>Rumah Sakit
                            </label>
                            <div class="relative">
                                <select id="hospital" name="hospital" required
                                    class="w-full bg-white text-black border-3 border-slate-700 rounded-xl px-4 py-4 focus:ring-4 focus:ring-blue-500 focus:border-blue-700 transition-all duration-300 text-lg font-bold appearance-none cursor-pointer shadow-lg">
                                    <option value="" disabled selected class="bg-slate-800 text-white font-bold">🏥 Pilih
                                        rumah sakit</option>
                                    <option value="alta" @selected(old('hospital') == 'alta')
                                        class="bg-slate-900 text-white font-bold">
                                        Alta Hospital (EMS)
                                    </option>
                                    <option value="roxwood" @selected(old('hospital') == 'roxwood')
                                        class="bg-slate-900 text-white font-bold">
                                        Roxwood Hospital
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="fas fa-chevron-down text-sky-300 text-lg"></i>
                                </div>
                            </div>
                            @error('hospital')
                                <p class="mt-2 text-sm text-red-300 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-lg font-bold text-white mb-2">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sky-300 hover:text-sky-100 transition-colors duration-200"
                                    aria-label="Toggle password visibility">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-lg font-bold text-white mb-2">Konfirmasi
                                Password</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                                <button type="button" id="togglePasswordConfirmation"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sky-300 hover:text-sky-100 transition-colors duration-200"
                                    aria-label="Toggle password confirmation visibility">
                                    <i class="fas fa-eye" id="passwordConfirmationIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('staff.login') }}"
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
                        <div
                            class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-2">Informasi Penting</h3>
                            <ul class="text-sky-200 space-y-1 text-sm">
                                <li>• Staf yang didaftarkan akan langsung aktif dan dapat login</li>
                                <li>• Password minimal 8 karakter</li>
                                <li>• Pilih peran yang sesuai: Trainee, Perawat, Co-Ass, Dokter Umum, atau Dokter Spesialis
                                </li>
                                <li>• Role dapat diubah nanti oleh Admin, Eksekutif, atau Manajer</li>
                                <li>• Notifikasi akan dikirim ke Discord saat registrasi berhasil</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
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

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-10px);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const roleSelect = document.getElementById('role_id');
                const rolePreview = document.getElementById('rolePreview');
                const roleName = document.getElementById('roleName');
                const roleDescription = document.getElementById('roleDescription');
                const rolePermissions = document.getElementById('rolePermissions');

                const togglePasswordBtn = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const passwordIcon = document.getElementById('passwordIcon');

                const togglePasswordConfirmationBtn = document.getElementById('togglePasswordConfirmation');
                const passwordConfirmationInput = document.getElementById('password_confirmation');
                const passwordConfirmationIcon = document.getElementById('passwordConfirmationIcon');

                /**
                 * Toggle password visibility for any input/icon pair
                 * @param {HTMLInputElement} input - The password input element
                 * @param {HTMLElement} icon - The icon element to toggle
                 */
                function togglePasswordVisibility(input, icon) {
                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');

                    // Update icon
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }

                /**
                 * Update role preview display based on selected role
                 */
                function updateRolePreview() {
                    const selectedRoleId = roleSelect.value;

                    if (selectedRoleId && roleData[selectedRoleId]) {
                        const role = roleData[selectedRoleId];

                        roleName.textContent = role.name;
                        roleDescription.textContent = role.description;

                        // Update permissions display
                        if (Array.isArray(role.permissions) && role.permissions.length > 0) {
                            rolePermissions.innerHTML = role.permissions.map(permission =>
                                `<span class="inline-block bg-sky-500/20 text-sky-300 px-2 py-1 rounded-full text-xs mr-2 mb-1">${permission}</span>`
                            ).join('');
                        } else {
                            rolePermissions.innerHTML = '<span class="text-sky-300 text-xs">Tidak ada hak akses khusus.</span>';
                        }

                        rolePreview.classList.remove('hidden');
                    } else {
                        rolePreview.classList.add('hidden');
                    }
                }

                // Role data from server
                const roleData = {
                    @foreach($roles as $role)
                                    {{ $role->id }}: {
                            name: @json($role->display_name ?? $role->name),
                            description: @json($role->description ?? 'Peran staf medis profesional'),
                            permissions: @json($role->permissions ?? [])
                        },
                    @endforeach
                    };

            // Event Listeners
            togglePasswordBtn.addEventListener('click', function () {
                togglePasswordVisibility(passwordInput, passwordIcon);
            });

            togglePasswordConfirmationBtn.addEventListener('click', function () {
                togglePasswordVisibility(passwordConfirmationInput, passwordConfirmationIcon);
            });

            roleSelect.addEventListener('change', updateRolePreview);

            // Initialize role preview if value already selected (for validation errors)
            if (roleSelect.value) {
                updateRolePreview();
            }
                });
        </script>
    @endpush
@endsection