@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Paramedic - IME Medical Center')

@section('content')
<div class="min-h-screen bg-[#f0ede6] py-6 sm:py-10 px-3 sm:px-6">
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Top Header Card -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-200">
            <div class="bg-[#9c834a] h-3"></div>
            <div class="p-6 sm:p-8 space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logoime.webp') }}" alt="IME Logo" class="w-10 h-10 object-contain">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Formulir Pendaftaran Paramedic IME Medical Center
                        </h1>
                        <p class="text-xs text-[#9c834a] font-bold uppercase tracking-wider">
                            {{ $period->batch_name ?? 'Open Recruitment Alta Hospital' }}
                        </p>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-100">
                    Selamat datang di portal pendaftaran resmi anggota paramedic IME Medical Center. Mohon isi data identitas IC (In Character) dan OOC (Out of Character) Anda dengan cermat, lengkap, dan jujur.
                </p>
                @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-rose-900">
                        <i class="fas fa-exclamation-triangle"></i> Mohon perbaiki data berikut:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-white rounded-xl p-3.5 shadow-sm border border-slate-200 flex items-center justify-between text-xs font-semibold text-slate-600">
            <span id="stepIndicatorText">Halaman 1 dari 3: Informasi IC</span>
            <div class="w-36 sm:w-48 bg-slate-200 rounded-full h-2 overflow-hidden">
                <div id="stepProgressBar" class="bg-[#9c834a] h-full transition-all duration-300" style="width: 33.3%;"></div>
            </div>
        </div>

        <form id="recruitmentForm" action="{{ route('public.recruitment.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- ========================================================
                 STEP 1: INFORMASI IC (Persyaratan Umum & Khusus)
            ======================================================== -->
            <div id="step-1" class="step-section space-y-5">
                <!-- Section Header Card -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-200">
                    <div class="bg-[#9c834a] px-6 py-3.5 flex items-center gap-2.5 text-white">
                        <span class="text-lg">👤</span>
                        <h2 class="text-sm sm:text-base font-bold tracking-wide uppercase">INFORMASI IC</h2>
                    </div>
                    <div class="p-5 sm:p-6 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        Isilah pernyataan dibawah ini sebagai persyaratan umum untuk bisa mendaftar menjadi anggota paramedic IME Medical Center.
                    </div>
                </div>

                <!-- Card 1: Persyaratan Umum -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-4">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Berikut ini adalah persyaratan umum untuk bergabung menjadi bagian dari EMS: <span class="text-rose-500">*</span>
                    </label>
                    <div class="space-y-3 pt-1">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_general_1" class="req-general-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Berusia 17 tahun saat mendaftar (IC & OOC)</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_general_2" class="req-general-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Berdedikasi tinggi, mampu bekerja dalam tekanan dan berkemauan untuk belajar</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_general_3" class="req-general-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Bersedia mengikuti masa training selama 1-3 hari</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_general_4" class="req-general-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Bersedia mengikuti Standar Operasi dan Prosedur yang berlaku selama menjadi anggota EMS</span>
                        </label>
                    </div>
                    <!-- Hidden field to satisfy validation -->
                    <input type="hidden" name="agree_general_req" id="agree_general_req" value="1">
                </div>

                <!-- Card 2: Persyaratan Khusus IC -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-4">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Berikut ini adalah persyaratan khusus (IC) yang harus dimiliki sebelum dilakukan interview: <span class="text-rose-500">*</span>
                    </label>
                    <div class="space-y-3 pt-1">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_special_1" class="req-special-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Memiliki Kartu Identitas Warga IME Medical Center (KTP)</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_special_2" class="req-special-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Memiliki SKB</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_special_3" class="req-special-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Memiliki SIM (bisa menyusul kalau sudah diterima)</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_special_4" class="req-special-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Memiliki Surat Kesehatan</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="req_special_5" class="req-special-check mt-1 w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" required>
                            <span class="text-xs sm:text-sm text-slate-700">Memiliki Surat Psikolog</span>
                        </label>
                    </div>
                    <input type="hidden" name="agree_special_req" id="agree_special_req" value="1">
                </div>

                <!-- Buttons Step 1 -->
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="goToStep(2)" class="px-6 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-[#9c834a] border border-[#9c834a] font-bold text-xs sm:text-sm shadow-sm transition">
                        Berikutnya &rarr;
                    </button>
                </div>
            </div>

            <!-- ========================================================
                 STEP 2: CURRICULUM VITAE IC
            ======================================================== -->
            <div id="step-2" class="step-section space-y-5" style="display: none;">
                <!-- Section Header Card -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-200">
                    <div class="bg-[#9c834a] px-6 py-3.5 flex items-center gap-2.5 text-white">
                        <span class="text-lg">📋</span>
                        <h2 class="text-sm sm:text-base font-bold tracking-wide uppercase">CURRICULUM VITAE IC</h2>
                    </div>
                    <div class="p-5 sm:p-6 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        Isilah identitas dan daftar riwayat hidup anda sesuai format di bawah ini.
                    </div>
                </div>

                <!-- Field 1: Nama Karakter IC -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900">
                        Nama Karakter IC <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="ic_name" value="{{ old('ic_name') }}" placeholder="Jawaban Anda" class="w-full text-xs sm:text-sm border-b-2 border-slate-300 focus:border-[#9c834a] outline-none py-2 transition bg-transparent" required>
                </div>

                <!-- Field 2: CID -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900">
                        CID (Citezen ID) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="cid" value="{{ old('cid') }}" placeholder="Jawaban Anda (Contoh: 1234 atau #1234)" class="w-full text-xs sm:text-sm border-b-2 border-slate-300 focus:border-[#9c834a] outline-none py-2 transition bg-transparent" required>
                </div>

                <!-- Field 3: Jenis Kelamin -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="gender" class="w-full sm:w-64 text-xs sm:text-sm border border-slate-300 rounded-xl px-4 py-2.5 focus:border-[#9c834a] outline-none bg-white transition" required>
                        <option value="">Pilih</option>
                        <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Field 4: Tanggal Lahir IC -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900">
                        Tanggal Lahir (IC) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full sm:w-64 text-xs sm:text-sm border border-slate-300 rounded-xl px-4 py-2 focus:border-[#9c834a] outline-none transition" required>
                </div>

                <!-- Field 5: Pengalaman Menjadi Anggota Medis/EMS -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900">
                        Pengalaman Menjadi Anggota Medis/EMS. <span class="text-rose-500">*</span>
                    </label>
                    <select name="has_medical_exp" class="w-full sm:w-64 text-xs sm:text-sm border border-slate-300 rounded-xl px-4 py-2.5 focus:border-[#9c834a] outline-none bg-white transition" required>
                        <option value="">Pilih</option>
                        <option value="Ada" {{ old('has_medical_exp') === 'Ada' ? 'selected' : '' }}>Ada</option>
                        <option value="Tidak" {{ old('has_medical_exp') === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>

                <!-- Field 6: Penjelasan Pengalaman Medis -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900">
                        Apabila memiliki pengalaman medis, harap dijelaskan secara singkat. Apabila tidak, tuliskan 0. <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="medical_exp_desc" value="{{ old('medical_exp_desc', '0') }}" placeholder="Jawaban Anda" class="w-full text-xs sm:text-sm border-b-2 border-slate-300 focus:border-[#9c834a] outline-none py-2 transition bg-transparent" required>
                </div>

                <!-- Field 7: Alasan Bergabung (Minimal 50 Kata) -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-bold text-slate-900">
                            Mengapa anda ingin bergabung dengan IME Medical Center? <span class="text-[#9c834a] font-semibold">(Jelaskan Minimal 50 Kata)</span> <span class="text-rose-500">*</span>
                        </label>
                        <span id="wordCountBadge" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            0 / 50 kata
                        </span>
                    </div>
                    <textarea id="reason_joining" name="reason_joining" rows="4" placeholder="Jawaban Anda (Jelaskan secara komprehensif motivasi dan visi Anda bergabung...)" class="w-full text-xs sm:text-sm border border-slate-300 rounded-xl p-3 focus:border-[#9c834a] outline-none transition" required>{{ old('reason_joining') }}</textarea>
                    <p id="wordCountWarning" class="text-[11px] text-amber-600 hidden font-medium">
                        ⚠️ Jumlah kata belum mencapai batas minimal 50 kata.
                    </p>
                </div>

                <!-- Field 8: Pengalaman Bermain RP (OOC) -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Pengalaman Bermain RP (OOC) <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-500 leading-normal">
                        (Jika tidak memiliki pengalaman, silakan isi 0 atau -). Jika memiliki pengalaman, jelaskan sudah berapa lama bermain RP dan pernah berperan sebagai apa saja (White Side/Bad Side).
                    </p>
                    <textarea name="rp_experience" rows="3" placeholder="Jawaban Anda" class="w-full text-xs sm:text-sm border border-slate-300 rounded-xl p-3 focus:border-[#9c834a] outline-none transition" required>{{ old('rp_experience') }}</textarea>
                </div>

                <!-- Field 9: Upload Foto KTP IC -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Mohon Lampirkan FOTO KTP IC <span class="text-[#9c834a] font-normal italic">(*jika tidak sesuai dengan nama IC, auto rejected)</span> <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-500">Upload 1 file yang didukung (JPG, PNG, PDF). Maks 10 MB.</p>
                    <input type="file" name="ktp_file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-[#9c834a] file:text-xs file:font-semibold file:bg-white file:text-[#9c834a] hover:file:bg-[#9c834a]/10 cursor-pointer pt-2" required>
                </div>

                <!-- Field 10: Upload Foto SKB -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Mohon Lampirkan FOTO SKB <span class="text-[#9c834a] font-normal italic">(*jika sudah kadaluarsa dan tujuan tidak sesuai, auto rejected)</span> <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-500">Upload 1 file yang didukung (JPG, PNG, PDF). Maks 10 MB.</p>
                    <input type="file" name="skb_file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-[#9c834a] file:text-xs file:font-semibold file:bg-white file:text-[#9c834a] hover:file:bg-[#9c834a]/10 cursor-pointer pt-2" required>
                </div>

                <!-- Field 11: Upload Foto Surat Kesehatan -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Mohon Lampirkan FOTO Surat Kesehatan <span class="text-[#9c834a] font-normal italic">(*jika sudah kadaluarsa dan tujuan tidak sesuai, auto rejected)</span> <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-500">Upload 1 file yang didukung (JPG, PNG, PDF). Maks 10 MB.</p>
                    <input type="file" name="health_cert_file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-[#9c834a] file:text-xs file:font-semibold file:bg-white file:text-[#9c834a] hover:file:bg-[#9c834a]/10 cursor-pointer pt-2" required>
                </div>

                <!-- Field 12: Upload Foto Surat Psikolog (Opsional) -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Mohon Lampirkan FOTO Surat Psikolog <span class="text-slate-500 font-normal italic">(Opsional / Dapat menyusul)</span>
                    </label>
                    <p class="text-[11px] text-slate-500">Upload 1 file yang didukung (JPG, PNG, PDF). Maks 10 MB.</p>
                    <input type="file" name="psychology_cert_file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border file:border-slate-300 file:text-xs file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 cursor-pointer pt-2">
                </div>

                <!-- Buttons Step 2 -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="goToStep(1)" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition">
                        &larr; Kembali
                    </button>
                    <button type="button" onclick="goToStep(3)" class="px-6 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-[#9c834a] border border-[#9c834a] font-bold text-xs sm:text-sm shadow-sm transition">
                        Berikutnya &rarr;
                    </button>
                </div>
            </div>

            <!-- ========================================================
                 STEP 3: INFORMASI OOC
            ======================================================== -->
            <div id="step-3" class="step-section space-y-5" style="display: none;">
                <!-- Section Header Card -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-200">
                    <div class="bg-[#9c834a] px-6 py-3.5 flex items-center gap-2.5 text-white">
                        <span class="text-lg">⚠️</span>
                        <h2 class="text-sm sm:text-base font-bold tracking-wide uppercase">INFORMASI OOC</h2>
                    </div>
                    <div class="p-5 sm:p-6 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        Isilah data dibawah ini dengan benar dan jujur.
                    </div>
                </div>

                <!-- Field 1: Tanggung Jawab Kota Lain -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Apakah ada tanggung jawab di kota lain? Jika ada siap membagi waktu?
                    </label>
                    <input type="text" name="other_city_responsibility" value="{{ old('other_city_responsibility') }}" placeholder="Jawaban Anda" class="w-full text-xs sm:text-sm border-b-2 border-slate-300 focus:border-[#9c834a] outline-none py-2 transition bg-transparent">
                </div>

                <!-- Field 2: Jam Online / Masuk Kota -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-3">
                    <label class="block text-sm font-bold text-slate-900">
                        Jam Online / Masuk Kota <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        @php
                            $hourSlots = ['00:00 - 06:00', '07:00 - 12:00', '13:00 - 18:00', '19:00 - 24:00'];
                        @endphp
                        @foreach($hourSlots as $slot)
                        <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 hover:border-[#9c834a] hover:bg-amber-50/40 cursor-pointer transition select-none text-center gap-2">
                            <input type="checkbox" name="online_hours[]" value="{{ $slot }}" class="w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" {{ is_array(old('online_hours')) && in_array($slot, old('online_hours')) ? 'checked' : '' }}>
                            <span class="text-xs font-semibold text-slate-700">{{ $slot }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Field 3: Hari Online / Masuk Kota -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-3">
                    <label class="block text-sm font-bold text-slate-900">
                        Hari Online / Masuk Kota <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        @php
                            $daySlots = ['Every Day', 'Senin - Jumat', 'Weekend'];
                        @endphp
                        @foreach($daySlots as $dSlot)
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-[#9c834a] hover:bg-amber-50/40 cursor-pointer transition select-none">
                            <input type="checkbox" name="online_days[]" value="{{ $dSlot }}" class="w-4 h-4 rounded text-[#9c834a] focus:ring-[#9c834a] border-slate-300" {{ is_array(old('online_days')) && in_array($dSlot, old('online_days')) ? 'checked' : '' }}>
                            <span class="text-xs font-semibold text-slate-800">{{ $dSlot }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Field 4: Discord Username -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-200 space-y-2">
                    <label class="block text-sm font-bold text-slate-900 leading-snug">
                        Username Discord (Untuk Pemanggilan Wawancara)
                    </label>
                    <input type="text" name="discord_username" value="{{ old('discord_username') }}" placeholder="Contoh: user#1234 atau username_dc" class="w-full text-xs sm:text-sm border-b-2 border-slate-300 focus:border-[#9c834a] outline-none py-2 transition bg-transparent">
                </div>

                <!-- Buttons Step 3 -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="goToStep(2)" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition">
                        &larr; Kembali
                    </button>
                    <button type="submit" id="submitBtn" class="px-8 py-3 rounded-xl bg-[#9c834a] hover:bg-[#85703f] text-white font-black text-xs sm:text-sm shadow-lg shadow-[#9c834a]/30 transition hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Formulir Pendaftaran
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    let currentStep = 1;

    function goToStep(step) {
        // Simple client validation before advancing
        if (step > currentStep) {
            if (currentStep === 1) {
                const reqGen = document.querySelectorAll('.req-general-check:checked').length;
                const reqSpe = document.querySelectorAll('.req-special-check:checked').length;
                if (reqGen < 4 || reqSpe < 5) {
                    alert('Harap setujui seluruh persyaratan umum dan persyaratan khusus sebelum melanjutkan.');
                    return;
                }
            } else if (currentStep === 2) {
                const icName = document.querySelector('input[name="ic_name"]').value.trim();
                const cid = document.querySelector('input[name="cid"]').value.trim();
                const gender = document.querySelector('select[name="gender"]').value;
                const birth = document.querySelector('input[name="birth_date"]').value;
                const reason = document.querySelector('textarea[name="reason_joining"]').value.trim();

                if (!icName || !cid || !gender || !birth || !reason) {
                    alert('Mohon lengkapi seluruh kolom yang bertanda bintang (*) pada bagian Curriculum Vitae.');
                    return;
                }

                // Check 50 words
                const words = reason ? reason.split(/\s+/).filter(w => w.length > 0).length : 0;
                if (words < 50) {
                    alert('Alasan bergabung wajib minimal 50 kata. Saat ini baru ' + words + ' kata.');
                    return;
                }
            }
        }

        document.querySelectorAll('.step-section').forEach(el => el.style.display = 'none');
        document.getElementById('step-' + step).style.display = 'block';

        currentStep = step;
        const progress = Math.round((step / 3) * 100);
        document.getElementById('stepProgressBar').style.width = progress + '%';

        const stepNames = ['Informasi IC', 'Curriculum Vitae IC', 'Informasi OOC'];
        document.getElementById('stepIndicatorText').innerText = `Halaman ${step} dari 3: ${stepNames[step - 1]}`;

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Word counter for reason_joining
    const reasonText = document.getElementById('reason_joining');
    const wordBadge = document.getElementById('wordCountBadge');
    const wordWarning = document.getElementById('wordCountWarning');

    function updateWordCount() {
        if (!reasonText) return;
        const text = reasonText.value.trim();
        const count = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
        wordBadge.innerText = `${count} / 50 kata`;

        if (count >= 50) {
            wordBadge.className = 'text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
            wordWarning.classList.add('hidden');
        } else {
            wordBadge.className = 'text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800';
            if (count > 0) {
                wordWarning.classList.remove('hidden');
            } else {
                wordWarning.classList.add('hidden');
            }
        }
    }

    if (reasonText) {
        reasonText.addEventListener('input', updateWordCount);
        updateWordCount();
    }
</script>
@endsection
