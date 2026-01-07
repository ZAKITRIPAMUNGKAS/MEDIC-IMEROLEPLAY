@extends('layouts.app')

@section('title', 'Edit Staf - Portal Medis MPK-BA')

@section('content')
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
                    <a href="{{ route('admin.staff.index') }}"
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
                            <p class="text-white font-semibold text-lg">{{ $user->name }}</p>
                            <p class="text-gray-300 text-sm">{{ $user->email }}</p>
                            <p class="text-gray-400 text-xs">Terakhir diperbarui:
                                {{ optional($user->updated_at)->format('d M Y, H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="glass-effect rounded-2xl elegant-shadow-lg p-6 md:p-8">
                <form method="POST" action="{{ route('admin.staff.update', $user) }}" enctype="multipart/form-data"
                    class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                            <i class="fas fa-user mr-3 text-sky-400"></i>
                            Informasi Personal
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                                    required>
                                @error('name')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    placeholder="contoh@email.com"
                                    class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300"
                                    required>
                                @error('email')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Rumah Sakit</label>
                                <select name="hospital"
                                    class="w-full bg-white/10 text-white border border-white/20 rounded-lg px-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300 appearance-none"
                                    required>
                                    <option value="alta" @selected(old('hospital', $user->hospital ?? 'alta') == 'alta')
                                        class="bg-slate-800 text-slate-100">Alta Hospital (EMS)</option>
                                    <option value="roxwood" @selected(old('hospital', $user->hospital) == 'roxwood')
                                        class="bg-slate-800 text-slate-100">Roxwood Hospital</option>
                                </select>
                                @error('hospital')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
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
                                @error('password')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
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
                                    <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}"
                                        onerror="this.onerror=null;this.src='{{ asset('profile.jpg') }}';"
                                        class="w-20 h-20 rounded-full border-4 border-sky-400 object-cover">
                                    <div>
                                        <p class="text-white text-sm font-medium">{{ $user->name }}</p>
                                        <p class="text-gray-300 text-xs">
                                            {{ $user->role->display_name ?? $user->role->name ?? 'Staff' }}
                                        </p>
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
                                @error('profile_image')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
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
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)
                                            class="bg-slate-800 text-slate-100">
                                            {{ $role->display_name ?? $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">
                                    <i class="fas fa-money-bill-wave mr-2"></i>Gaji Per Minggu (Custom)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">$</span>
                                    <input type="number" name="custom_salary" id="custom_salary"
                                        value="{{ old('custom_salary', $user->custom_salary) }}"
                                        placeholder="Otomatis dari role" step="0.01" min="0"
                                        class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition-all duration-300">
                                </div>
                                <div class="mt-2 space-y-1">
                                    <p class="text-gray-400 text-xs flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Default gaji role: <span id="role-salary"
                                            class="font-semibold text-green-400 ml-1">Loading...</span>
                                    </p>
                                    <p class="text-gray-400 text-xs">
                                        Kosongkan untuk menggunakan default, atau isi untuk custom salary
                                    </p>
                                </div>
                                @error('custom_salary')
                                    <p class="text-red-300 text-sm mt-2 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Status Akun</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only"
                                            @checked(old('is_active', $user->is_active))>
                                        <div class="relative">
                                            <div
                                                class="w-12 h-6 {{ old('is_active', $user->is_active) ? 'bg-green-500' : 'bg-gray-500' }} rounded-full shadow-inner">
                                            </div>
                                            <div
                                                class="absolute top-1 {{ old('is_active', $user->is_active) ? 'left-7' : 'left-1' }} w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 ease-in-out">
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
                                        @checked(in_array('access_live_chat', old('custom_permissions', $user->custom_permissions ?? [])))>
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
                                        @checked(in_array('access_feedback', old('custom_permissions', $user->custom_permissions ?? [])))>
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
                        <a href="{{ route('admin.staff.index') }}"
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

        // Fetch and display role salary when role changes
        const roleSelect = document.querySelector('select[name="role_id"]');
        const salaryDisplay = document.getElementById('role-salary');
        const customSalaryInput = document.getElementById('custom_salary');

        // Role salary mapping (fetched from backend)
        const roleSalaries = @json($roles->mapWithKeys(function ($role) {
            $salary = \App\Models\SalarySetting::where('role_name', $role->name)->first();
            return [$role->id => $salary ? $salary->weekly_salary : 0];
        }));

        function updateRoleSalary() {
            const selectedRoleId = roleSelect.value;
            const salary = roleSalaries[selectedRoleId] || 0;
            salaryDisplay.textContent = '$' + Number(salary).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Set placeholder to show default
            if (!customSalaryInput.value) {
                customSalaryInput.placeholder = `Default: $${Number(salary).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            }
        }

        roleSelect.addEventListener('change', updateRoleSalary);

        // Initial load
        updateRoleSalary();
    </script>
@endsection