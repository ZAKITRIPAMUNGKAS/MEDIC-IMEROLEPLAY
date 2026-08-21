@extends('layouts.app')

@section('title', 'Profil Staf - Portal Medis')

@section('content')
@php
    $operations = $operations ?? collect([]);
    $managerEvaluations = $managerEvaluations ?? collect([]);
    $evaluationsAvg = $evaluationsAvg ?? 0;
    $evaluationsCount = $evaluationsCount ?? 0;
    $canSeeAll = $canSeeAll ?? false;
@endphp
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
                    @if ($errors->any())
                        <div class="mb-4 bg-red-500/40 border-2 border-red-400/70 text-red-100 px-4 py-3 rounded-xl shadow-lg">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('success'))
                        <div
                            class="mb-4 bg-green-500/40 border-2 border-green-400/70 text-green-100 px-4 py-3 rounded-xl shadow-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Nama</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    required />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Email</label>
                                <input type="email" value="{{ auth()->user()->email }}"
                                    class="w-full px-4 py-3 rounded-xl bg-white/25 text-white border-2 border-white/40 opacity-80 shadow-lg"
                                    disabled />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Citizen ID (FiveM) <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="citizen_id"
                                    value="{{ old('citizen_id', auth()->user()->citizen_id) }}"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="Contoh: JDN12345" required />
                                <p class="text-sky-200 text-xs mt-2">
                                    <i class="fas fa-id-card mr-1"></i>
                                    Wajib diisi untuk integrasi absensi kota
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">Rumah Sakit</label>
                                <select name="hospital"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    required>
                                    <option value="alta" {{ (auth()->user()->hospital ?? 'alta') == 'alta' ? 'selected' : '' }}>Alta Hospital (EMS)</option>
                                    <option value="roxwood" {{ (auth()->user()->hospital ?? '') == 'roxwood' ? 'selected' : '' }}>Roxwood Hospital</option>
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
                                <img src="{{ auth()->user()->profile_image_url }}" alt="{{ auth()->user()->name }}"
                                    onerror="this.onerror=null;this.src='{{ asset('profile.jpg') }}';"
                                    class="w-20 h-20 rounded-full border-4 border-sky-400 object-cover">
                                <div>
                                    <p class="text-white text-lg font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="text-sky-200 text-sm">
                                        {{ auth()->user()->role->display_name ?? auth()->user()->role->name ?? 'Staff' }}
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
                                @error('profile_image')
                                    <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                                @enderror
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

                    @if (session('info'))
                        <div
                            class="mb-4 bg-blue-500/40 border-2 border-blue-400/70 text-blue-100 px-4 py-3 rounded-xl shadow-lg">
                            {{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.profile.update-email') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-sky-200 mb-2">Email Saat Ini</label>
                            <input type="email" value="{{ auth()->user()->email }}"
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
                            @error('current_password')
                                <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">
                                    Email Baru <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="new_email" value="{{ old('new_email') }}"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="email@example.com" required />
                                @error('new_email')
                                    <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-sky-200 mb-2">
                                    Konfirmasi Email Baru <span class="text-red-400">*</span>
                                </label>
                                <input type="email" name="new_email_confirmation"
                                    class="w-full px-4 py-3 rounded-xl bg-white/30 text-white border-2 border-white/50 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-xl"
                                    placeholder="Ketik ulang email baru" required />
                                @error('new_email_confirmation')
                                    <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                                @enderror
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

            <!-- Riwayat Operasi Card -->
            <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl mt-8"
                style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-procedures text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Riwayat Operasi</h2>
                            <p class="text-sky-200 text-sm">Daftar operasi yang pernah Anda tangani</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-sky-200 border-b border-sky-500/30">
                                    <th class="py-3 px-4 font-semibold">Tanggal</th>
                                    <th class="py-3 px-4 font-semibold">Jenis Operasi</th>
                                    <th class="py-3 px-4 font-semibold">Lokasi</th>
                                    <th class="py-3 px-4 font-semibold">Nama Pasien</th>
                                    <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($operations as $op)
                                    <tr class="border-b border-sky-500/10 hover:bg-sky-500/10 transition">
                                        <td class="py-3 px-4 text-white">{{ $op->tanggal_waktu->format('d M Y') }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $op->jenis_operasi == 'Operasi Mayor' ? 'bg-red-500/20 text-red-300 border border-red-500/30' : ($op->jenis_operasi == 'Operasi Minor' ? 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30') }}">
                                                {{ $op->jenis_operasi }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-300">{{ $op->lokasi }}</td>
                                        <td class="py-3 px-4 text-white font-medium">{{ $op->nama_pasien }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('staff.operations.show', $op->id) }}" class="text-sky-300 hover:text-sky-100 text-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-sky-200">
                                            Belum ada riwayat operasi yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Hasil Ulasan & Evaluasi Anonim Staf Card -->
            <div class="card backdrop-blur-xl border-2 border-sky-400/60 rounded-3xl shadow-2xl mt-8"
                style="background-color: rgba(7, 89, 133, 0.9);">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-sky-500/30 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-star text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Hasil Ulasan & Evaluasi Staf</h2>
                                <p class="text-sky-200 text-sm">Daftar ulasan evaluasi anonim dari staf medis untuk Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 px-4 py-2 rounded-2xl border border-white/20">
                            <div class="text-right">
                                <span class="text-xs text-sky-200 block font-medium">Rating Rata-rata</span>
                                <span class="text-lg font-black text-amber-300">
                                    {{ $evaluationsAvg > 0 ? number_format($evaluationsAvg, 1) : '0.0' }} / 5.0 ⭐
                                </span>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-500/20 text-amber-300 text-xs font-bold rounded-lg border border-amber-400/30">
                                {{ $evaluationsCount }} Ulasan
                            </span>
                        </div>
                    </div>

                    @if(count($managerEvaluations) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($managerEvaluations as $review)
                                <div class="bg-black/20 border border-white/10 rounded-2xl p-5 space-y-3">
                                    <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-cyan-600 text-white flex items-center justify-center text-xs shadow font-bold shrink-0 border border-white/20">
                                                <i class="fas fa-user-secret"></i>
                                            </div>
                                            <div>
                                                <span class="text-xs font-extrabold text-amber-300 block flex items-center gap-1.5">
                                                    🎭 Staf Medis (Anonim)
                                                    @if(isset($canSeeAll) && $canSeeAll)
                                                        <span class="px-1.5 py-0.5 rounded bg-amber-500/20 border border-amber-400/40 text-amber-200 text-[9px] font-bold">
                                                            <i class="fas fa-eye mr-0.5"></i> Admin View
                                                        </span>
                                                    @endif
                                                </span>

                                                @if(isset($canSeeAll) && $canSeeAll && $review->evaluator)
                                                    <div class="my-1 p-2 rounded-lg bg-amber-500/10 border border-amber-400/30 text-[11px] text-amber-200 space-y-0.5">
                                                        <div class="font-bold text-white flex items-center gap-1">
                                                            <i class="fas fa-id-card text-amber-400"></i> Nama Penilai: {{ $review->evaluator->name }}
                                                        </div>
                                                        <div class="text-[10px] text-amber-200 opacity-90">
                                                            Jabatan: {{ $review->evaluator->role?->name ?? '-' }} &middot; ID: {{ $review->evaluator->staff_id ?? '-' }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <span class="text-[10px] text-slate-300 font-medium bg-white/10 px-2 py-1 rounded-md">
                                            {{ $review->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-1 text-amber-400">
                                            @for($s = 1; $s <= 5; $s++)
                                                @if($s <= $review->rating)
                                                    <i class="fas fa-star text-sm"></i>
                                                @else
                                                    <i class="far fa-star text-slate-600 text-sm"></i>
                                                @endif
                                            @endfor
                                            <span class="ml-1 text-white font-bold">{{ number_format($review->rating, 1) }}</span>
                                        </div>

                                        <span class="px-2.5 py-0.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 font-semibold text-[10px]">
                                            {{ $review->kategori }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-sky-100 leading-relaxed bg-white/5 p-3.5 rounded-xl border border-white/5">
                                        "{!! nl2br(e($review->komentar)) !!}"
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-sky-200 border border-dashed border-white/20 rounded-2xl">
                            <i class="fas fa-comment-slash text-3xl mb-2 block text-sky-300"></i>
                            <p class="text-sm font-medium">Belum ada ulasan evaluasi anonim yang diberikan untuk Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection