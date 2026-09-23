@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Paramedic - IME Medical Center')

@section('content')
<div class="relative min-h-screen py-8 sm:py-12 px-3 sm:px-6">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-indigo-950/40 to-slate-900 pointer-events-none"></div>
    <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>

    <div class="relative max-w-3xl mx-auto space-y-6 text-white">

        <!-- Top Header Card -->
        <div class="glass-effect rounded-2xl p-6 sm:p-8 border border-white/10 shadow-2xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 p-2 flex items-center justify-center shrink-0 shadow-inner">
                        <img src="{{ asset('images/logoime.webp') }}" alt="IME Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                            Formulir Pendaftaran Paramedic
                        </h1>
                        <p class="text-xs text-sky-400 font-semibold flex items-center gap-1.5 mt-0.5">
                            <i class="fas fa-hospital-alt"></i> IME Medical Center &bull; {{ $period->batch_name ?? 'Open Recruitment Alta Hospital' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 self-start sm:self-center">
                    <a href="{{ route('public.recruitment.status') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/40 hover:bg-sky-500/30 transition shadow-sm">
                        <i class="fas fa-search"></i> Cek Status Pendaftaran
                    </a>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Batch Aktif
                    </span>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed pt-2 border-t border-white/10">
                Selamat datang di portal pendaftaran resmi calon anggota paramedic IME Medical Center. Mohon isi data identitas IC (In Character) dan OOC (Out of Character) Anda dengan cermat, lengkap, dan jujur.
            </p>

            @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs sm:text-sm space-y-1.5 shadow-lg">
                <div class="font-bold flex items-center gap-2 text-rose-200">
                    <i class="fas fa-exclamation-triangle text-rose-400"></i> Mohon perbaiki data berikut sebelum melanjutkan:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-300/90 pl-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Step Progression Wizard Header -->
        <div class="glass-effect rounded-2xl p-4 sm:p-5 border border-white/10 shadow-xl space-y-3">
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div id="wizardTab1" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl transition-all font-semibold text-white bg-indigo-600/40 border border-indigo-500/50 shadow">
                    <span class="w-5 h-5 rounded-full bg-indigo-500 text-white text-[10px] flex items-center justify-center font-bold">1</span>
                    <span class="hidden sm:inline">Persyaratan IC</span>
                </div>
                <div id="wizardTab2" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl transition-all font-medium text-slate-400 bg-white/5 border border-white/5">
                    <span class="w-5 h-5 rounded-full bg-white/10 text-slate-300 text-[10px] flex items-center justify-center font-bold">2</span>
                    <span class="hidden sm:inline">Curriculum Vitae</span>
                </div>
                <div id="wizardTab3" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl transition-all font-medium text-slate-400 bg-white/5 border border-white/5">
                    <span class="w-5 h-5 rounded-full bg-white/10 text-slate-300 text-[10px] flex items-center justify-center font-bold">3</span>
                    <span class="hidden sm:inline">Informasi OOC</span>
                </div>
            </div>
            <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                <div id="stepProgressBar" class="bg-gradient-to-r from-indigo-500 to-sky-400 h-full transition-all duration-300" style="width: 33.3%;"></div>
            </div>
        </div>

        <form id="recruitmentForm" action="{{ route('public.recruitment.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- ========================================================
                 STEP 1: INFORMASI IC (Persyaratan Umum & Khusus)
            ======================================================== -->
            <div id="step-1" class="step-section space-y-5">
                <!-- Section Title Card -->
                <div class="glass-effect rounded-2xl p-5 border border-white/10 shadow-xl flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 text-lg">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-white uppercase tracking-wide">BAGIAN 1: PERSYARATAN IC &amp; UMUM</h2>
                        <p class="text-xs text-slate-300">Setujui seluruh persyaratan umum dan khusus sebelum mengisi data diri Anda.</p>
                    </div>
                </div>

                <!-- Card 1: Persyaratan Umum -->
                <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-xl space-y-4">
                    <label class="block text-sm font-bold text-white leading-snug">
                        Berikut ini adalah persyaratan umum untuk bergabung menjadi bagian dari EMS: <span class="text-rose-400">*</span>
                    </label>
                    <div class="space-y-2.5 pt-1">
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_general_1" class="req-general-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Berusia minimal 17 tahun saat mendaftar (IC &amp; OOC)</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_general_2" class="req-general-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Berdedikasi tinggi, mampu bekerja dalam tekanan dan berkemauan untuk belajar</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_general_3" class="req-general-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Bersedia mengikuti masa training selama 1-3 hari kerja</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_general_4" class="req-general-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Bersedia mengikuti Standar Operasi dan Prosedur (SOP) yang berlaku selama menjadi anggota EMS</span>
                        </label>
                    </div>
                    <input type="hidden" name="agree_general_req" id="agree_general_req" value="1">
                </div>

                <!-- Card 2: Persyaratan Khusus IC -->
                <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-xl space-y-4">
                    <label class="block text-sm font-bold text-white leading-snug">
                        Berikut ini adalah persyaratan khusus (IC) yang harus dimiliki sebelum dilakukan interview: <span class="text-rose-400">*</span>
                    </label>
                    <div class="space-y-2.5 pt-1">
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_special_1" class="req-special-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Memiliki Kartu Identitas Warga IME Medical Center (KTP)</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_special_2" class="req-special-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Memiliki Surat Kelakuan Baik (SKB) aktif</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_special_3" class="req-special-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Memiliki SIM (Surat Izin Mengemudi — bisa menyusul setelah diterima)</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_special_4" class="req-special-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Memiliki Surat Kesehatan / Bebas Narkoba yang masih berlaku</span>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer select-none transition-all has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/15">
                            <input type="checkbox" name="req_special_5" class="req-special-check mt-0.5 w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" required>
                            <span class="text-xs sm:text-sm text-slate-200">Memiliki Surat Psikolog (dapat menyusul)</span>
                        </label>
                    </div>
                    <input type="hidden" name="agree_special_req" id="agree_special_req" value="1">
                </div>

                <!-- Buttons Step 1 -->
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="goToStep(2)" class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-900/40 transition flex items-center gap-2">
                        <span>Lanjut ke Formulir CV</span> <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ========================================================
                 STEP 2: CURRICULUM VITAE IC
            ======================================================== -->
            <div id="step-2" class="step-section space-y-5" style="display: none;">
                <!-- Section Title Card -->
                <div class="glass-effect rounded-2xl p-5 border border-white/10 shadow-xl flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-500/40 flex items-center justify-center text-sky-400 text-lg">
                        <i class="fas fa-id-card-alt"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-white uppercase tracking-wide">BAGIAN 2: CURRICULUM VITAE IC</h2>
                        <p class="text-xs text-slate-300">Isilah identitas karakter IC dan lampirkan dokumen yang dipersyaratkan.</p>
                    </div>
                </div>

                <!-- Grid Identitas Dasar -->
                <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-xl space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Field 1: Nama Karakter IC -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                Nama Karakter IC <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="ic_name" value="{{ old('ic_name') }}" placeholder="Contoh: John Doe"
                                   class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                        </div>

                        <!-- Field 2: CID -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                CID (Citizen ID) <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="cid" value="{{ old('cid') }}" placeholder="Contoh: 1234 atau #1234"
                                   class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Field 3: Jenis Kelamin -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                Jenis Kelamin IC <span class="text-rose-400">*</span>
                            </label>
                            <select name="gender" class="w-full bg-slate-900 text-white border border-white/20 rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Field 4: Tanggal Lahir IC -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                Tanggal Lahir IC <span class="text-rose-400">*</span>
                            </label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                   class="w-full bg-slate-900 text-white border border-white/20 rounded-xl px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                        </div>
                    </div>

                    <!-- Field 5: Pengalaman Menjadi Anggota Medis/EMS -->
                    <div class="pt-2 border-t border-white/10 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                    Pengalaman Medis / EMS <span class="text-rose-400">*</span>
                                </label>
                                <select name="has_medical_exp" class="w-full bg-slate-900 text-white border border-white/20 rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                                    <option value="">Pilih</option>
                                    <option value="Ada" {{ old('has_medical_exp') === 'Ada' ? 'selected' : '' }}>Ada</option>
                                    <option value="Tidak" {{ old('has_medical_exp') === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                    Penjelasan Pengalaman Medis <span class="text-slate-400 font-normal">(Isi 0 jika tidak ada)</span> <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" name="medical_exp_desc" value="{{ old('medical_exp_desc', '0') }}" placeholder="Contoh: Pernah menjadi dokter umum di RS sebelumnya..."
                                       class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                            </div>
                        </div>
                    </div>

                    <!-- Field 7: Alasan Bergabung (Minimal 50 Huruf) -->
                    <div class="pt-2 border-t border-white/10 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <label for="reason_joining" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider">
                                Mengapa Anda ingin bergabung dengan IME Medical Center? <span class="text-rose-400">*</span>
                            </label>
                            <span id="wordCountBadge" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 self-start sm:self-center">
                                0 / 50 huruf
                            </span>
                        </div>
                        <textarea id="reason_joining" name="reason_joining" rows="4"
                                  placeholder="Jelaskan secara komprehensif motivasi, visi, dan alasan Anda ingin menjadi bagian dari tim medis IME Medical Center (Minimal 50 huruf)..."
                                  class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl p-3.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none leading-relaxed" required>{{ old('reason_joining') }}</textarea>
                        <p id="wordCountWarning" class="text-[11px] text-amber-400 hidden flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> Jumlah huruf belum mencapai batas minimal 50 huruf.
                        </p>
                    </div>

                    <!-- Field 8: Pengalaman Bermain RP (OOC) -->
                    <div class="pt-2 border-t border-white/10 space-y-2">
                        <label class="block text-xs font-semibold text-slate-200 uppercase tracking-wider">
                            Pengalaman Bermain RP (OOC) <span class="text-rose-400">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400">
                            (Jika belum memiliki pengalaman, silakan isi 0 atau -). Jika ada, jelaskan berapa lama bermain RP dan faksi/peran apa saja yang pernah Anda mainkan.
                        </p>
                        <textarea name="rp_experience" rows="3" placeholder="Contoh: 1 tahun bermain roleplay, pernah aktif di faksi kepolisian dan mekanik..."
                                  class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl p-3.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none" required>{{ old('rp_experience') }}</textarea>
                    </div>
                </div>

                <!-- Dokumen Lampiran Pendaftaran -->
                <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-xl space-y-5">
                    <h3 class="text-xs uppercase font-bold text-amber-300 tracking-wider flex items-center gap-2 pb-2 border-b border-white/10">
                        <i class="fas fa-file-upload"></i> Unggah Dokumen Lampiran (Format JPG, PNG, atau PDF — Maks 10 MB)
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <!-- Field 9: Foto KTP IC -->
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2">
                            <label class="block font-bold text-white">
                                Foto KTP IC <span class="text-rose-400">*</span>
                            </label>
                            <p class="text-[11px] text-slate-400">Harus sesuai dengan nama IC di atas.</p>
                            <input type="file" name="ktp_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="block w-full text-[11px] text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer pt-1" required>
                        </div>

                        <!-- Field 10: Foto SKB -->
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2">
                            <label class="block font-bold text-white">
                                Foto SKB <span class="text-rose-400">*</span>
                            </label>
                            <p class="text-[11px] text-slate-400">Pastikan SKB masih aktif dan berlaku.</p>
                            <input type="file" name="skb_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="block w-full text-[11px] text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer pt-1" required>
                        </div>

                        <!-- Field 11: Foto Surat Kesehatan -->
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2">
                            <label class="block font-bold text-white">
                                Foto Surat Kesehatan <span class="text-rose-400">*</span>
                            </label>
                            <p class="text-[11px] text-slate-400">Surat keterangan sehat atau bebas narkoba.</p>
                            <input type="file" name="health_cert_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="block w-full text-[11px] text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer pt-1" required>
                        </div>

                        <!-- Field 12: Foto Surat Psikolog (Opsional) -->
                        <div class="p-4 rounded-xl bg-white/5 border border-white/10 space-y-2">
                            <label class="block font-bold text-white">
                                Foto Surat Psikolog <span class="text-slate-400 font-normal italic">(Opsional)</span>
                            </label>
                            <p class="text-[11px] text-slate-400">Dapat menyusul saat sesi wawancara.</p>
                            <input type="file" name="psychology_cert_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="block w-full text-[11px] text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white/10 file:text-slate-200 hover:file:bg-white/20 cursor-pointer pt-1">
                        </div>
                    </div>
                </div>

                <!-- Buttons Step 2 -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="goToStep(1)" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-slate-300 font-semibold text-xs sm:text-sm transition flex items-center gap-2">
                        <i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span>
                    </button>
                    <button type="button" onclick="goToStep(3)" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-900/40 transition flex items-center gap-2">
                        <span>Lanjut ke Data OOC</span> <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ========================================================
                 STEP 3: INFORMASI OOC & JADWAL
            ======================================================== -->
            <div id="step-3" class="step-section space-y-5" style="display: none;">
                <!-- Section Title Card -->
                <div class="glass-effect rounded-2xl p-5 border border-white/10 shadow-xl flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-purple-400 text-lg">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-white uppercase tracking-wide">BAGIAN 3: INFORMASI OOC &amp; WAKTU ONLINE</h2>
                        <p class="text-xs text-slate-300">Isilah data komitmen jam online dan kontak Discord Anda.</p>
                    </div>
                </div>

                <div class="glass-effect rounded-2xl p-6 border border-white/10 shadow-xl space-y-5">
                    <!-- Field 1: Tanggung Jawab Kota Lain -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            Apakah ada tanggung jawab di kota/server lain? Jika ada siap membagi waktu?
                        </label>
                        <input type="text" name="other_city_responsibility" value="{{ old('other_city_responsibility') }}" placeholder="Contoh: Tidak ada / Hanya fokus di kota IME"
                               class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl px-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <!-- Field 2: Jam Online / Masuk Kota -->
                    <div class="pt-2 border-t border-white/10 space-y-2.5">
                        <label class="block text-xs font-semibold text-slate-200 uppercase tracking-wider">
                            Perkiraan Jam Online / Masuk Kota <span class="text-rose-400">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @php
                                $hourSlots = ['00:00 - 06:00', '07:00 - 12:00', '13:00 - 18:00', '19:00 - 24:00'];
                            @endphp
                            @foreach($hourSlots as $slot)
                            <label class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition select-none text-center gap-2 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/20">
                                <input type="checkbox" name="online_hours[]" value="{{ $slot }}" class="w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" {{ is_array(old('online_hours')) && in_array($slot, old('online_hours')) ? 'checked' : '' }}>
                                <span class="text-xs font-semibold text-white font-mono">{{ $slot }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Field 3: Hari Online / Masuk Kota -->
                    <div class="pt-2 border-t border-white/10 space-y-2.5">
                        <label class="block text-xs font-semibold text-slate-200 uppercase tracking-wider">
                            Hari Masuk Kota / Aktif <span class="text-rose-400">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @php
                                $daySlots = ['Every Day', 'Senin - Jumat', 'Weekend'];
                            @endphp
                            @foreach($daySlots as $dSlot)
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 cursor-pointer transition select-none has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-500/20">
                                <input type="checkbox" name="online_days[]" value="{{ $dSlot }}" class="w-4 h-4 rounded text-indigo-500 focus:ring-indigo-400 border-white/20 bg-slate-800" {{ is_array(old('online_days')) && in_array($dSlot, old('online_days')) ? 'checked' : '' }}>
                                <span class="text-xs font-semibold text-white">{{ $dSlot }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Field 4: Discord Username -->
                    <div class="pt-2 border-t border-white/10 space-y-2">
                        <label class="block text-xs font-semibold text-slate-200 uppercase tracking-wider">
                            Username Discord <span class="text-indigo-300 font-normal">(Untuk pemanggilan sesi wawancara)</span>
                        </label>
                        <div class="relative">
                            <i class="fab fa-discord absolute left-3.5 top-1/2 -translate-y-1/2 text-indigo-400 text-sm"></i>
                            <input type="text" name="discord_username" value="{{ old('discord_username') }}" placeholder="Contoh: user_discord atau username#1234"
                                   class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    {{-- Pembuatan Akun Portal --}}
                    <div class="pt-2 border-t border-white/10 space-y-4">
                        <div class="flex items-center gap-2.5 mb-1">
                            <i class="fas fa-key text-amber-400 text-sm"></i>
                            <p class="text-xs font-black text-white uppercase tracking-wider">
                                Akun Portal Staf (Untuk Login Jika Diterima)
                            </p>
                        </div>
                        <div class="rounded-xl bg-amber-500/10 border border-amber-500/30 p-3 text-xs text-amber-200 leading-relaxed">
                            <i class="fas fa-info-circle mr-1 text-amber-400"></i>
                            <strong>Penting:</strong> Email dan password ini akan digunakan untuk login ke Portal Staf <strong>jika Anda diterima</strong>.
                            Simpan baik-baik. Tidak perlu daftar ulang — akun langsung aktif otomatis setelah diterima.
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                    Email <span class="text-rose-400">*</span>
                                    <span class="text-slate-400 font-normal normal-case">(untuk login portal)</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com"
                                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                                           required>
                                </div>
                                @error('email')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                    Password <span class="text-rose-400">*</span>
                                    <span class="text-slate-400 font-normal normal-case">(min. 8 karakter)</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input type="password" name="password" placeholder="Buat password Anda"
                                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                                           required minlength="8">
                                </div>
                                @error('password')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                                    Konfirmasi Password <span class="text-rose-400">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input type="password" name="password_confirmation" placeholder="Ulangi password"
                                           class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/20 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                                           required minlength="8">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Buttons Step 3 --}}
                <div class="flex items-center justify-between pt-2">
                    <button type="button" onclick="goToStep(2)" class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-slate-300 font-semibold text-xs sm:text-sm transition flex items-center gap-2">
                        <i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span>
                    </button>
                    <button type="submit" id="submitBtn" class="px-8 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs sm:text-sm shadow-xl shadow-emerald-900/40 transition hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> <span>Kirim Formulir Pendaftaran</span>
                    </button>
                </div>
            </div>
        </form>


    </div>
</div>

<script>
    let currentStep = 1;

    function goToStep(step) {
        // Validasi saat maju ke langkah berikutnya
        if (step > currentStep) {
            if (currentStep === 1) {
                const reqGen = document.querySelectorAll('.req-general-check:checked').length;
                const reqSpe = document.querySelectorAll('.req-special-check:checked').length;
                if (reqGen < 4 || reqSpe < 5) {
                    alert('Harap centang dan setujui seluruh persyaratan umum (4 poin) dan persyaratan khusus (5 poin) sebelum melanjutkan.');
                    return;
                }
            }
            if (currentStep === 2) {
                const form = document.getElementById('recruitmentForm');
                const icName = form.querySelector('[name="ic_name"]').value.trim();
                const cid = form.querySelector('[name="cid"]').value.trim();
                const gender = form.querySelector('[name="gender"]').value;
                const birthDate = form.querySelector('[name="birth_date"]').value;
                const reasonJoining = form.querySelector('[name="reason_joining"]').value.trim();
                const ktpFile = form.querySelector('[name="ktp_file"]').files.length;
                const skbFile = form.querySelector('[name="skb_file"]').files.length;
                const healthFile = form.querySelector('[name="health_cert_file"]').files.length;

                if (!icName || !cid || !gender || !birthDate) {
                    alert('Mohon lengkapi Nama Karakter IC, CID, Jenis Kelamin, dan Tanggal Lahir.');
                    return;
                }

                const chars = (reasonJoining || '').trim().length;
                if (chars < 50) {
                    alert(`Alasan bergabung wajib minimal 50 huruf. Saat ini baru terisi ${chars} huruf.`);
                    document.getElementById('reason_joining').focus();
                    return;
                }

                if (!ktpFile || !skbFile || !healthFile) {
                    alert('Harap lampirkan berkas wajib: Foto KTP, Foto SKB, dan Foto Surat Kesehatan.');
                    return;
                }
            }
        }

        // Sembunyikan semua step
        document.querySelectorAll('.step-section').forEach(el => el.style.display = 'none');
        document.getElementById(`step-${step}`).style.display = 'block';

        // Update Wizard Header
        currentStep = step;
        const progress = step === 1 ? '33.3%' : (step === 2 ? '66.6%' : '100%');
        document.getElementById('stepProgressBar').style.width = progress;

        for (let i = 1; i <= 3; i++) {
            const tab = document.getElementById(`wizardTab${i}`);
            if (i === step) {
                tab.className = "flex items-center justify-center gap-2 py-2 px-3 rounded-xl transition-all font-semibold text-white bg-indigo-600/40 border border-indigo-500/50 shadow";
            } else if (i < step) {
                tab.className = "flex items-center justify-center gap-2 py-2 px-3 rounded-xl transition-all font-medium text-emerald-300 bg-emerald-500/10 border border-emerald-500/30";
            } else {
                tab.className = "flex items-center justify-center gap-2 py-2 px-3 rounded-xl transition-all font-medium text-slate-400 bg-white/5 border border-white/5";
            }
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function countWords(str) {
        if (!str) return 0;
        return str.trim().split(/\s+/).filter(Boolean).length;
    }

    const reasonEl = document.getElementById('reason_joining');
    const badgeEl = document.getElementById('wordCountBadge');
    const warningEl = document.getElementById('wordCountWarning');

    if (reasonEl) {
        reasonEl.addEventListener('input', function() {
            const chars = this.value.trim().length;
            badgeEl.textContent = `${chars} / 50 huruf`;
            if (chars >= 50) {
                badgeEl.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30";
                warningEl.classList.add('hidden');
            } else {
                badgeEl.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30";
                warningEl.classList.remove('hidden');
            }
        });
    }

    document.getElementById('recruitmentForm').addEventListener('submit', function(e) {
        const hours = document.querySelectorAll('input[name="online_hours[]"]:checked').length;
        const days = document.querySelectorAll('input[name="online_days[]"]:checked').length;
        if (hours === 0 || days === 0) {
            e.preventDefault();
            alert('Pilih minimal satu slot jam online dan satu hari aktif.');
            return false;
        }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim Berkas...';
    });
</script>
@endsection
