@extends('layouts.app')

@php
    $formTitles = [
        'operasi'              => 'FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIII',
        'surat_menyurat'       => 'Formulir Pendaftaran Surat Menyurat',
        'visum_hidup'          => 'PENDAFTARAN PELATIHAN VISUM HIDUP',
        'rekam_medis'          => 'PENDAFTARAN PELATIHAN REKAM MEDIS',
        'pemulsaran_jenazah'   => 'PENDAFTARAN PELATIHAN PEMULSARAN JENAZAH',
    ];

    $formDescriptions = [
        'operasi'             => 'Pendaftaran Pelatihan Operasi dibuka pada 5–7 September 2026. Kegiatan pelatihan akan dilaksanakan pada 8 September 2026 oleh Department People & Development bagian MOT (Medical of Trainer) sebagai upaya meningkatkan pengetahuan dan keterampilan peserta terkait prosedur operasi dan keselamatan pasien.',
        'surat_menyurat'      => '(Minimal Co-Ass) Pelatihan tata naskah dinas, penulisan surat resmi, dan korespondensi internal maupun eksternal IME Medical Center.',
        'visum_hidup'         => 'PELATIHAN VISUM HIDUP yang diselenggarakan oleh Medical Science & Laboratory bersama People & Development Department – IME Medical Center. Semua Dokter Umum WAJIB mengikuti dan opsional bagi dokter spesialis, nanti yang sudah ikut pelatihan bakal dapat sertifikat dan yang tidak mengikuti di periode sekarang wajib mengikuti di periode berikutnya bareng dengan dokter baru, Terimakasih',
        'rekam_medis'         => 'Pelatihan Rekam Medis diselenggarakan oleh divisi Medical Science & Laboratory (MSL) IME Medical Center. Pelatihan ini mencakup tata cara pengisian rekam medis, pengarsipan data klinis pasien, dan standar dokumentasi medis sesuai prosedur rumah sakit.',
        'pemulsaran_jenazah'  => 'Pelatihan Pemulsaran Jenazah diselenggarakan oleh Divisi People & Development IME Medical Center. Peserta akan mempelajari prosedur penanganan jenazah secara profesional sesuai dengan standar medis dan etika yang berlaku.',
    ];

    $currentTitle = $formTitles[$normalizedType] ?? 'Formulir Pendaftaran Pelatihan';
    $currentDesc  = $formDescriptions[$normalizedType] ?? '';
@endphp

@section('title', $currentTitle . ' — IME Medical Center')

@section('content')
<div class="min-h-screen pt-20 pb-16" style="background: linear-gradient(135deg, #0b1329 0%, #0c2461 50%, #0b1329 100%);">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs text-white/50 mb-6">
            <a href="{{ route('public.index') }}" class="hover:text-white transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <a href="{{ route('portal.training.index') }}" class="hover:text-white transition-colors">Pendaftaran Pelatihan</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-emerald-400 font-medium truncate">{{ $currentTitle }}</span>
        </div>

        {{-- Alert Info if already has pending submission --}}
        @if($existingPending)
        <div class="mb-6 p-4 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-200 text-sm flex items-start gap-3 shadow-lg shadow-amber-950/20">
            <div class="w-8 h-8 rounded-xl bg-amber-500/20 flex items-center justify-center shrink-0 text-amber-400 mt-0.5">
                <i class="fas fa-info-circle text-base"></i>
            </div>
            <div>
                <div class="font-bold text-amber-300">Anda Memiliki Pendaftaran Yang Sedang Diverifikasi</div>
                <div class="text-xs text-amber-200/80 mt-1">
                    Anda telah mengirim formulir untuk <strong>{{ $existingPending->type_label }}</strong> pada {{ $existingPending->created_at->translatedFormat('d M Y, H:i') }} (Batch: {{ $existingPending->batch }}). Status saat ini: <em>Menunggu Review PND</em>. Mengirim formulir baru akan menambahkan entri baru.
                </div>
            </div>
        </div>
        @endif

        {{-- Form Header Card (Google Forms Style with Alta Aesthetic) --}}
        <div class="rounded-2xl bg-white/[0.05] border border-white/10 shadow-2xl backdrop-blur-md overflow-hidden mb-6">
            {{-- Top Accent Line --}}
            <div class="h-2.5 w-full 
                @if($normalizedType === 'operasi') bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-500
                @elseif($normalizedType === 'surat_menyurat') bg-gradient-to-r from-blue-500 via-indigo-400 to-violet-500
                @elseif($normalizedType === 'rekam_medis') bg-gradient-to-r from-cyan-500 via-sky-400 to-blue-500
                @elseif($normalizedType === 'pemulsaran_jenazah') bg-gradient-to-r from-amber-500 via-orange-400 to-yellow-400
                @else bg-gradient-to-r from-purple-500 via-fuchsia-400 to-rose-500 @endif">
            </div>

            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-2.5 text-xs font-bold uppercase tracking-wider mb-2
                    @if($normalizedType === 'operasi') text-emerald-400
                    @elseif($normalizedType === 'surat_menyurat') text-blue-400
                    @elseif($normalizedType === 'rekam_medis') text-cyan-400
                    @elseif($normalizedType === 'pemulsaran_jenazah') text-amber-400
                    @else text-purple-400 @endif">
                    <i class="fas fa-shield-alt"></i>
                    <span>Official Registration Form • Divisi PND</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight tracking-wide mb-3">
                    {{ $currentTitle }}
                </h1>

                <div class="text-sm text-white/70 leading-relaxed space-y-2 border-t border-white/10 pt-4">
                    <p>{{ $currentDesc }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-white/5 flex flex-wrap items-center justify-between gap-2 text-xs text-white/50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-circle text-white/60"></i>
                        <span>Login sebagai: <strong class="text-white/80">{{ $user->name }}</strong> (CID: {{ $user->staff_id ?? '-' }})</span>
                    </div>
                    <span class="text-rose-400 font-medium">* Menunjukkan pertanyaan yang wajib diisi</span>
                </div>
            </div>
        </div>

        {{-- Main Submission Form --}}
        <form action="{{ route('portal.training.submit', $type) }}" method="POST" class="space-y-6">
            @csrf

            {{-- 1. NAMA IC --}}
            <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 shadow-xl backdrop-blur-sm transition-all focus-within:border-emerald-500/50 focus-within:bg-white/[0.06]">
                <label for="nama_ic" class="block text-sm font-bold text-white mb-1">
                    @if($normalizedType === 'operasi') NAMA IC @elseif($normalizedType === 'surat_menyurat') Nama (IC) @else NAMA (IC) @endif
                    <span class="text-rose-400">*</span>
                </label>
                <p class="text-xs text-white/50 mb-3">Tuliskan nama lengkap karakter In-Character (IC) Anda dengan benar</p>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <input type="text"
                           id="nama_ic"
                           name="nama_ic"
                           value="{{ old('nama_ic', $defaultName) }}"
                           placeholder="Contoh: dr. Billy McCartney"
                           required
                           class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all @error('nama_ic') border-rose-500 @enderror">
                </div>
                @error('nama_ic')
                <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- 2. JENIS KELAMIN --}}
            <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 shadow-xl backdrop-blur-sm transition-all focus-within:border-emerald-500/50 focus-within:bg-white/[0.06]">
                <label class="block text-sm font-bold text-white mb-1">
                    @if($normalizedType === 'operasi') JENIS KELAMIN @else Jenis Kelamin @endif
                    <span class="text-rose-400">*</span>
                </label>
                <p class="text-xs text-white/50 mb-4">Pilih jenis kelamin karakter In-Character Anda</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                        $oldGender = old('gender');
                        $maleVal = ($normalizedType === 'operasi') ? 'Laki-laki' : 'Laki-Laki';
                    @endphp

                    {{-- Option: Laki-laki --}}
                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
                        <input type="radio"
                               name="gender"
                               value="{{ $maleVal }}"
                               {{ strcasecmp($oldGender ?? '', 'laki-laki') === 0 ? 'checked' : '' }}
                               required
                               class="w-4 h-4 text-emerald-500 bg-white/5 border-white/20 focus:ring-emerald-500 focus:ring-offset-0">
                        <div class="flex items-center gap-2 text-sm text-white font-medium">
                            <i class="fas fa-mars text-blue-400"></i>
                            <span>{{ $maleVal }}</span>
                        </div>
                    </label>

                    {{-- Option: Perempuan --}}
                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
                        <input type="radio"
                               name="gender"
                               value="Perempuan"
                               {{ strcasecmp($oldGender ?? '', 'perempuan') === 0 ? 'checked' : '' }}
                               required
                               class="w-4 h-4 text-emerald-500 bg-white/5 border-white/20 focus:ring-emerald-500 focus:ring-offset-0">
                        <div class="flex items-center gap-2 text-sm text-white font-medium">
                            <i class="fas fa-venus text-rose-400"></i>
                            <span>Perempuan</span>
                        </div>
                    </label>
                </div>

                @error('gender')
                <p class="text-rose-400 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- 3. NOMOR TELEPON (IC) - Surat Menyurat, Visum Hidup, Rekam Medis, Pemulsaran Jenazah --}}
            @if(in_array($normalizedType, ['surat_menyurat', 'visum_hidup', 'rekam_medis', 'pemulsaran_jenazah']))
            <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 shadow-xl backdrop-blur-sm transition-all focus-within:border-emerald-500/50 focus-within:bg-white/[0.06]">
                <label for="phone_ic" class="block text-sm font-bold text-white mb-1">
                    Nomor Telepon (IC)
                    <span class="text-rose-400">*</span>
                </label>
                <p class="text-xs text-white/50 mb-3">Nomor kontak handphone In-Character aktif yang dapat dihubungi</p>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                        <i class="fas fa-phone-alt text-sm"></i>
                    </div>
                    <input type="text"
                           id="phone_ic"
                           name="phone_ic"
                           value="{{ old('phone_ic', $defaultPhone) }}"
                           placeholder="Contoh: 555-1234 atau 08123456789"
                           required
                           class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all @error('phone_ic') border-rose-500 @enderror">
                </div>
                @error('phone_ic')
                <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
            </div>
            @endif

            {{-- 4. JABATAN SAAT INI - Surat Menyurat & Visum Hidup --}}
            @if($normalizedType === 'surat_menyurat')
            <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 shadow-xl backdrop-blur-sm transition-all focus-within:border-emerald-500/50 focus-within:bg-white/[0.06]">
                <label class="block text-sm font-bold text-white mb-1">
                    Jabatan Saat Ini
                    <span class="text-rose-400">*</span>
                </label>
                <p class="text-xs text-white/50 mb-4">Pilih jabatan medis Anda saat ini (minimal Co-Ass)</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach(['Co-Ass', 'Dokter', 'Dokter Spesialis'] as $jab)
                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10">
                        <input type="radio"
                               name="jabatan"
                               value="{{ $jab }}"
                               {{ old('jabatan', (str_contains(strtolower($defaultRole), strtolower($jab)) ? $jab : '')) === $jab ? 'checked' : '' }}
                               required
                               class="w-4 h-4 text-blue-500 bg-white/5 border-white/20 focus:ring-blue-500 focus:ring-offset-0">
                        <span class="text-sm text-white font-medium">{{ $jab }}</span>
                    </label>
                    @endforeach
                </div>

                @error('jabatan')
                <p class="text-rose-400 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
            </div>
            @elseif($normalizedType === 'visum_hidup')
            <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 shadow-xl backdrop-blur-sm transition-all focus-within:border-emerald-500/50 focus-within:bg-white/[0.06]">
                <label class="block text-sm font-bold text-white mb-1">
                    Jabatan Saat Ini
                    <span class="text-rose-400">*</span>
                </label>
                <p class="text-xs text-white/50 mb-4">Wajib untuk Dokter Umum dan opsional bagi Dokter Spesialis</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach(['Dokter Umum', 'Dokter Spesialis'] as $jab)
                    <label class="relative flex items-center gap-3 p-3.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                        <input type="radio"
                               name="jabatan"
                               value="{{ $jab }}"
                               {{ old('jabatan', (str_contains(strtolower($defaultRole), 'spesialis') ? 'Dokter Spesialis' : 'Dokter Umum')) === $jab ? 'checked' : '' }}
                               required
                               class="w-4 h-4 text-purple-500 bg-white/5 border-white/20 focus:ring-purple-500 focus:ring-offset-0">
                        <span class="text-sm text-white font-medium">{{ $jab }}</span>
                    </label>
                    @endforeach
                </div>

                @error('jabatan')
                <p class="text-rose-400 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
            </div>
            @endif

            {{-- 5. BATCH --}}
            <div class="rounded-2xl bg-white/[0.04] border border-white/10 p-6 shadow-xl backdrop-blur-sm transition-all focus-within:border-emerald-500/50 focus-within:bg-white/[0.06]">
                <label for="batch" class="block text-sm font-bold text-white mb-1">
                    BATCH
                    <span class="text-rose-400">*</span>
                </label>
                <p class="text-xs text-white/50 mb-3">Pilih gelombang / batch pelatihan yang ingin Anda ikuti</p>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </div>
                    <select id="batch"
                            name="batch"
                            required
                            class="w-full pl-10 pr-10 py-3 bg-white/5 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-transparent transition-all appearance-none cursor-pointer @error('batch') border-rose-500 @enderror">
                        <option value="" class="bg-slate-900 text-white/50">-- Pilih Batch Pelatihan --</option>
                        @foreach($batches as $b)
                        <option value="{{ $b }}" {{ old('batch') === $b ? 'selected' : '' }} class="bg-slate-900 text-white">
                            {{ $b }}
                        </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-white/40">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>

                @error('batch')
                <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Submit & Cancel Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                <a href="{{ route('portal.training.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white/80 hover:text-white text-sm font-semibold transition-all flex items-center justify-center gap-2 border border-white/10">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Beranda Pelatihan</span>
                </a>

                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm tracking-wide text-white shadow-xl transition-all flex items-center justify-center gap-2.5
                    @if($normalizedType === 'operasi') bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-600/30
                    @elseif($normalizedType === 'surat_menyurat') bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-blue-600/30
                    @elseif($normalizedType === 'rekam_medis') bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-500 hover:to-sky-500 shadow-cyan-600/30
                    @elseif($normalizedType === 'pemulsaran_jenazah') bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 shadow-amber-600/30
                    @else bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 shadow-purple-600/30 @endif">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Formulir Pendaftaran</span>
                </button>
            </div>

            {{-- Footer Notes --}}
            <div class="text-center text-[11px] text-white/40 pt-4">
                Formulir ini dikelola oleh <strong>Divisi People & Development (PND)</strong> IME Medical Center. Data yang Anda kirimkan akan diverifikasi oleh tim panitia.
            </div>
        </form>

    </div>
</div>
@endsection
