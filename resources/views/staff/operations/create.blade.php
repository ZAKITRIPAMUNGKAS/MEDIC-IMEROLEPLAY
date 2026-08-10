@extends('layouts.app')

@section('title', 'Tambah Rekam Operasi Medis Lengkap - Portal Medis')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Dark Theme sesuai website */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        background: rgba(255,255,255,0.08) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
        border-radius: 0.75rem !important;
        min-height: 46px !important;
        padding: 4px 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: white !important;
        line-height: 36px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: rgba(56, 189, 248, 0.7) !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(to right, #0ea5e9, #06b6d4) !important;
        border: none !important;
        border-radius: 9999px !important;
        color: white !important;
        font-size: 13px !important;
        padding: 4px 12px 4px 10px !important;
        font-weight: 600 !important;
        margin-top: 4px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255,255,255,0.8) !important;
        margin-right: 6px !important;
        border: none !important;
        background: transparent !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: white !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        color: white !important;
        margin-top: 6px !important;
        font-family: inherit !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field::placeholder {
        color: rgba(186, 230, 253, 0.7) !important;
    }
    .select2-dropdown {
        background: #0c4a6e !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
        border-radius: 0.75rem !important;
        color: white !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
        z-index: 9999 !important;
    }
    .select2-container--default .select2-results__option {
        color: #bae6fd !important;
        padding: 10px 14px !important;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: linear-gradient(to right, #0ea5e9, #06b6d4) !important;
        color: white !important;
    }

    /* Custom form inputs */
    .form-input-dark {
        background: rgba(255,255,255,0.08) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
        color: white !important;
        border-radius: 0.75rem !important;
        padding: 10px 14px !important;
        width: 100%;
        transition: all 0.2s;
    }
    .form-input-dark:focus {
        outline: none;
        border-color: rgba(56, 189, 248, 0.7) !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15) !important;
        background: rgba(255,255,255,0.12) !important;
    }
    .form-input-dark::placeholder {
        color: rgba(186, 230, 253, 0.5);
    }
    .form-input-dark option {
        background: #0c4a6e;
        color: white;
    }

    /* Drag & Drop Zone */
    .dropzone-active {
        border-color: #38bdf8 !important;
        background-color: rgba(56, 189, 248, 0.15) !important;
    }

    /* Form Section Header styling */
    .section-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #ffffff;
        padding-bottom: 0.75rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
    }
    .section-badge {
        width: 1.75rem;
        height: 1.75rem;
        background: linear-gradient(135deg, #0ea5e9, #06b6d4);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 0.875rem;
        font-weight: 800;
        color: white;
    }
    .sub-section-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #38bdf8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('staff.operations.index') }}"
                   class="inline-flex items-center text-sky-300 hover:text-white text-sm font-medium mb-2 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Rekam Operasi
                </a>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 rounded-2xl shadow-lg">
                        <i class="fas fa-notes-medical text-white text-2xl"></i>
                    </span>
                    Tambah Rekam Operasi Medis Lengkap
                </h1>
                <p class="text-sky-200 text-sm mt-1">Formulir Laporan Operasi, Anestesi, dan Rekam Medis Terintegrasi</p>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-500 bg-opacity-20 border border-red-500 text-red-200 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-400"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form action="{{ route('staff.operations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- SECTION 1: KLASIFIKASI OPERASI & WAKTU --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">1</span>
                    Klasifikasi Operasi & Waktu
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-stethoscope mr-1.5 text-sky-400"></i>Jenis Operasi <span class="text-red-400">*</span>
                        </label>
                        <select name="jenis_operasi" class="form-input-dark" required>
                            <option value="">Pilih Jenis Operasi...</option>
                            <option value="Operasi Minor" {{ old('jenis_operasi') == 'Operasi Minor' ? 'selected' : '' }}>⚡ Operasi Minor (+30 poin)</option>
                            <option value="Operasi Mayor" {{ old('jenis_operasi') == 'Operasi Mayor' ? 'selected' : '' }}>🔴 Operasi Mayor (+60 poin)</option>
                            <option value="Emergency" {{ old('jenis_operasi') == 'Emergency' ? 'selected' : '' }}>🚨 Emergency (+25 poin)</option>
                            <option value="Konsultasi Spesialisasi" {{ old('jenis_operasi') == 'Konsultasi Spesialisasi' ? 'selected' : '' }}>🩺 Konsultasi Spesialisasi (+40 poin)</option>
                            <option value="Lainnya" {{ old('jenis_operasi') == 'Lainnya' ? 'selected' : '' }}>📋 Lainnya (+15 poin)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-calendar-alt mr-1.5 text-sky-400"></i>Tanggal & Waktu Operasi <span class="text-red-400">*</span>
                        </label>
                        <input type="datetime-local" name="tanggal_waktu" class="form-input-dark"
                               value="{{ old('tanggal_waktu', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-map-marker-alt mr-1.5 text-sky-400"></i>Lokasi Operasi / Ruang OK <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="lokasi" class="form-input-dark" value="{{ old('lokasi') }}"
                               placeholder="Contoh: Ruang Operasi OK-1" required>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: IDENTITAS PASIEN --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">2</span>
                    Identitas Pasien
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-user mr-1.5 text-sky-400"></i>Nama Pasien <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="nama_pasien" class="form-input-dark" value="{{ old('nama_pasien') }}"
                               placeholder="Nama lengkap pasien" required>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-birthday-cake mr-1.5 text-sky-400"></i>Tanggal Lahir (DOB)
                        </label>
                        <input type="date" name="medical_details[pasien][dob]" class="form-input-dark"
                               value="{{ old('medical_details.pasien.dob') }}">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-venus-mars mr-1.5 text-sky-400"></i>Jenis Kelamin
                        </label>
                        <select name="medical_details[pasien][jenis_kelamin]" class="form-input-dark">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-tint mr-1.5 text-red-400"></i>Golongan Darah
                        </label>
                        <select name="medical_details[pasien][gol_darah]" class="form-input-dark">
                            <option value="">-- Pilih Gol. Darah --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                            <option value="A+">A+</option>
                            <option value="B+">B+</option>
                            <option value="AB+">AB+</option>
                            <option value="O+">O+</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-phone mr-1.5 text-sky-400"></i>No HP / Telepon
                        </label>
                        <input type="text" name="medical_details[pasien][no_hp]" class="form-input-dark"
                               placeholder="Contoh: 08123456789">
                    </div>

                    <div class="md:col-span-2 lg:col-span-1">
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-id-card mr-1.5 text-sky-400"></i>Citizen ID / KTP Pasien
                        </label>
                        <input type="text" name="medical_details[pasien][citizen_id]" class="form-input-dark"
                               placeholder="Contoh: 100234">
                    </div>
                </div>
            </div>

            {{-- SECTION 3: ANGGOTA MEDIS & TIM OPERASI --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">3</span>
                    Anggota Medis & Tim Operasi
                </div>

                {{-- Select All Members --}}
                <div class="mb-5">
                    <label class="block text-sky-200 text-sm font-semibold mb-2">
                        <i class="fas fa-users mr-1.5 text-sky-400"></i>Pilih Anggota Tim Bertugas <span class="text-red-400" id="members-asterisk">*</span>
                    </label>
                    <select name="members[]" id="members-select" multiple="multiple" required class="w-full"></select>
                    <div class="mt-2 text-xs text-sky-300 flex items-center gap-1.5">
                        <i class="fas fa-info-circle text-sky-400"></i>
                        <span>Pilih seluruh staf yang terlibat dalam operasi ini. Poin duty tracking otomatis terdistribusi.</span>
                    </div>
                </div>

                {{-- Role Per Anggota (DPJP, Anestesi, dll) --}}
                <div id="dpjp-section" style="display:none;" class="space-y-4 pt-4 border-t border-white/10">
                    <div class="sub-section-title">
                        <i class="fas fa-user-md"></i> Penugasan Peran Khusus Tim Medis
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- DPJP --}}
                        <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
                            <label class="block text-emerald-300 text-xs font-bold uppercase mb-1.5">
                                <i class="fas fa-star mr-1"></i> DPJP (Penanggung Jawab) <span class="text-red-400">*</span>
                            </label>
                            <select name="dpjp_id" id="dpjp-select" class="form-input-dark text-sm"></select>
                            <span class="text-[11px] text-emerald-300 mt-1 block">+20 Poin Tambahan DPJP</span>
                        </div>

                        {{-- First Responder --}}
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl">
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">First Responder</label>
                            <select name="medical_details[tim][first_responder]" class="member-role-dropdown form-input-dark text-sm"></select>
                        </div>

                        {{-- Anestesi --}}
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl">
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Petugas Anestesi</label>
                            <select name="medical_details[tim][anestesi]" class="member-role-dropdown form-input-dark text-sm"></select>
                        </div>

                        {{-- Radiologi --}}
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl">
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Petugas Radiologi</label>
                            <select name="medical_details[tim][radiologi]" class="member-role-dropdown form-input-dark text-sm"></select>
                        </div>

                        {{-- Asisten 1 --}}
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl">
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Asisten 1</label>
                            <select name="medical_details[tim][asisten_1]" class="member-role-dropdown form-input-dark text-sm"></select>
                        </div>

                        {{-- Asisten 2 --}}
                        <div class="p-3 bg-white/5 border border-white/10 rounded-xl">
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Asisten 2</label>
                            <select name="medical_details[tim][asisten_2]" class="member-role-dropdown form-input-dark text-sm"></select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: ANAMNESIS & RIWAYAT KESEHATAN --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">4</span>
                    Anamnesis & Riwayat Kesehatan
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-comment-medical mr-1.5 text-sky-400"></i>Anamnesis Utama <span class="text-red-400">*</span>
                        </label>
                        <textarea name="diagnosa" rows="3" class="form-input-dark" placeholder="Keluhan utama dan keluhan penyerta pasien..." required>{{ old('diagnosa') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Penyakit Dahulu</label>
                            <textarea name="medical_details[anamnesis][riwayat_penyakit_dahulu]" rows="2" class="form-input-dark text-sm" placeholder="Hipertensi, Diabetes, Asthma, dll..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Penyakit Keluarga</label>
                            <textarea name="medical_details[anamnesis][riwayat_penyakit_keluarga]" rows="2" class="form-input-dark text-sm" placeholder="Riwayat penyakit keturunan keluarga..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Alergi</label>
                            <textarea name="medical_details[anamnesis][riwayat_alergi]" rows="2" class="form-input-dark text-sm" placeholder="Alergi obat (Penicillin/dll), makanan, dll..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Pengobatan</label>
                            <textarea name="medical_details[anamnesis][riwayat_pengobatan]" rows="2" class="form-input-dark text-sm" placeholder="Obat-obatan yang rutin dikonsumsi saat ini..."></textarea>
                        </div>
                    </div>

                    {{-- OBSTETRI SECTION --}}
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <div class="sub-section-title">
                            <i class="fas fa-baby"></i> Data Obstetri / Kebidanan
                            <span class="text-[10px] font-normal text-sky-300 ml-2 normal-case tracking-normal">(Isi jika pasien adalah ibu hamil/bersalin)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">HPHT (Hari Pertama Haid Terakhir)</label>
                                <input type="date" name="medical_details[obstetri][hpht]" class="form-input-dark text-sm">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Usia Kehamilan</label>
                                <input type="text" name="medical_details[obstetri][usia_kehamilan]" class="form-input-dark text-sm" placeholder="Contoh: 38 minggu 2 hari">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Status G/P/A (Gravida/Para/Abortus)</label>
                                <input type="text" name="medical_details[obstetri][gpa]" class="form-input-dark text-sm" placeholder="Contoh: G2P1A0">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">DJJ (Denyut Jantung Janin)</label>
                                <input type="text" name="medical_details[obstetri][djj]" class="form-input-dark text-sm" placeholder="Contoh: 140 dpm, Reguler">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Presentasi Janin</label>
                                <select name="medical_details[obstetri][presentasi]" class="form-input-dark text-sm">
                                    <option value="">-- Pilih --</option>
                                    <option value="Presentasi Kepala (Cephalic)">Presentasi Kepala (Cephalic)</option>
                                    <option value="Presentasi Bokong (Breech)">Presentasi Bokong (Breech)</option>
                                    <option value="Presentasi Lintang (Transverse)">Presentasi Lintang (Transverse)</option>
                                    <option value="Presentasi Muka">Presentasi Muka</option>
                                    <option value="Belum Dapat Ditentukan">Belum Dapat Ditentukan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">TFU (Tinggi Fundus Uteri)</label>
                                <input type="text" name="medical_details[obstetri][tfu]" class="form-input-dark text-sm" placeholder="Contoh: 32 cm">
                            </div>
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Catatan Obstetri Tambahan</label>
                                <textarea name="medical_details[obstetri][catatan_obstetri]" rows="2" class="form-input-dark text-sm" placeholder="His, pembukaan serviks, penurunan kepala, ketuban, dll..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- SECTION 5: PEMERIKSAAN FISIK & TANDA-TANDA VITAL (TTV) --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">5</span>
                    Pemeriksaan Fisik & TTV
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Keadaan Umum</label>
                        <select name="medical_details[ttv][keadaan_umum]" class="form-input-dark text-sm">
                            <option value="Tampak Sakit Ringan">Tampak Sakit Ringan</option>
                            <option value="Tampak Sakit Sedang">Tampak Sakit Sedang</option>
                            <option value="Tampak Sakit Berat">Tampak Sakit Berat</option>
                            <option value="Baik / Stabil">Baik / Stabil</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Kesadaran (GCS)</label>
                        <input type="text" name="medical_details[ttv][gcs]" class="form-input-dark text-sm" placeholder="E4 V5 M6 (GCS 15)">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Tekanan Darah</label>
                        <input type="text" name="medical_details[ttv][tekanan_darah]" class="form-input-dark text-sm" placeholder="120/80 mmHg">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Nadi</label>
                        <input type="text" name="medical_details[ttv][nadi]" class="form-input-dark text-sm" placeholder="80 x/menit, Teratur">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Respirasi (RR)</label>
                        <input type="text" name="medical_details[ttv][respirasi]" class="form-input-dark text-sm" placeholder="20 x/menit">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Suhu Body (°C)</label>
                        <input type="text" name="medical_details[ttv][suhu]" class="form-input-dark text-sm" placeholder="36.5 °C">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Saturasi O2 (%)</label>
                        <input type="text" name="medical_details[ttv][saturasi]" class="form-input-dark text-sm" placeholder="98 %">
                    </div>
                </div>
            </div>

            {{-- SECTION 6: TINDAKAN OPERASI & LANGKAH-LANGKAH --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">6</span>
                    Tindakan & Prosedur Operasi
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sky-200 text-sm font-semibold mb-2">Nama Tindakan <span class="text-red-400">*</span></label>
                            <input type="text" name="tindakan_operasi" class="form-input-dark" placeholder="Contoh: Apendektomi Laparoskopi" required>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-sm font-semibold mb-2">Waktu Mulai Tindakan</label>
                            <input type="time" name="medical_details[tindakan][waktu_mulai]" class="form-input-dark">
                        </div>
                        <div>
                            <label class="block text-sky-200 text-sm font-semibold mb-2">Waktu Selesai Tindakan</label>
                            <input type="time" name="medical_details[tindakan][waktu_selesai]" class="form-input-dark">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">Langkah-Langkah Tindakan Operasi</label>
                        <textarea name="medical_details[tindakan][langkah_tindakan]" rows="4" class="form-input-dark" placeholder="- Pasien diposisikan supine&#10;- Desinfeksi lapangan operasi&#10;- Insisi kulit dan eksplorasi..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">Hasil Operasi & Intraperatif</label>
                        <textarea name="hasil_operasi" rows="2" class="form-input-dark" placeholder="Operasi berjalan lancar, perdarahan minimal..."></textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 7: ANSESTESI & TABLE SCORE --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">7</span>
                    Manajemen Anestesi & Table Score
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Jenis Anestesi</label>
                        <select name="medical_details[anestesi][jenis_anestesi]" class="form-input-dark text-sm">
                            <option value="Anestesi Umum (GA)">Anestesi Umum (GA)</option>
                            <option value="Anestesi Lokal">Anestesi Lokal</option>
                            <option value="Anestesi Regional (Spinal/Epidural)">Anestesi Regional (Spinal/Epidural)</option>
                            <option value="Sedasi Konstan">Sedasi Konstan</option>
                        </select>
                    </div>
                </div>

                <div class="sub-section-title"><i class="fas fa-pills"></i> Obat-Obatan Pra & Pasca Operasi</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Pra-Operasi (Induksi, Inhalasi, Analgesik, Relaksan)</label>
                        <textarea name="medical_details[anestesi][pra_operasi]" rows="3" class="form-input-dark text-sm" placeholder="Induksi: Propofol 100mg&#10;Analgesik: Fentanyl 100mcg&#10;Relaksan: Rocuronium 50mg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Pasca-Operasi (Antidote, Anti Mual, Analgesik)</label>
                        <textarea name="medical_details[anestesi][pasca_operasi]" rows="3" class="form-input-dark text-sm" placeholder="Anti mual: Ondansetron 4mg&#10;Analgesik: Ketorolac 30mg&#10;Antidote: Sugammadex / Neostigmin"></textarea>
                    </div>
                </div>

                <div class="sub-section-title"><i class="fas fa-clipboard-check"></i> Status Lokalis & Score Pemulihan Pasca Anestesi</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sky-200 text-[11px] uppercase mb-1">a. Kesadaran Pasca</label>
                        <input type="text" name="medical_details[anestesi][score_kesadaran]" class="form-input-dark text-sm" placeholder="Sadar penuh / GCS 15">
                    </div>
                    <div>
                        <label class="block text-sky-200 text-[11px] uppercase mb-1">b. Respon Mual & Muntah</label>
                        <input type="text" name="medical_details[anestesi][score_mual]" class="form-input-dark text-sm" placeholder="Tidak ada mual">
                    </div>
                    <div>
                        <label class="block text-sky-200 text-[11px] uppercase mb-1">c. Pernapasan Pasca</label>
                        <input type="text" name="medical_details[anestesi][score_pernapasan]" class="form-input-dark text-sm" placeholder="Adekuat, SpO2 99%">
                    </div>
                    <div>
                        <label class="block text-sky-200 text-[11px] uppercase mb-1">d. Aktifitas Motorik</label>
                        <input type="text" name="medical_details[anestesi][score_motorik]" class="form-input-dark text-sm" placeholder="Dapat menggerakkan 4 ekstremitas">
                    </div>
                    <div>
                        <label class="block text-sky-200 text-[11px] uppercase mb-1">e. Tekanan Darah Pasca</label>
                        <input type="text" name="medical_details[anestesi][score_td]" class="form-input-dark text-sm" placeholder="120/75 mmHg (Stabil)">
                    </div>
                    <div>
                        <label class="block text-sky-200 text-[11px] uppercase mb-1">f. Warna Kulit</label>
                        <input type="text" name="medical_details[anestesi][score_warna_kulit]" class="form-input-dark text-sm" placeholder="Kemerahan / Merah muda">
                    </div>
                </div>
            </div>

            {{-- SECTION 8: PENUNJANG, SARAN & FOTO --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">8</span>
                    Pemeriksaan Penunjang & Saran
                </div>

                <div class="space-y-4 mb-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Hasil Laboratorium</label>
                            <textarea name="medical_details[penunjang][lab]" rows="2" class="form-input-dark text-sm" placeholder="Hb: 13.5, AL: 8.500, PLT: 250.000..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Hasil Radiologi / X-Ray</label>
                            <textarea name="medical_details[penunjang][radiologi]" rows="2" class="form-input-dark text-sm" placeholder="Foto Thorax DBN, USG Abdomen..."></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Obat-Obatan Pulang / Rawat Inap</label>
                        <textarea name="medical_details[obat_obatan]" rows="2" class="form-input-dark text-sm" placeholder="1. Amoxicillin 500mg 3x1&#10;2. Paracetamol 500mg 3x1..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Saran dan Anjuran Pasca Operasi</label>
                        <textarea name="medical_details[saran_anjuran]" rows="2" class="form-input-dark text-sm" placeholder="Bedrest 24 jam, kontrol jahitan 7 hari lagi..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Catatan Tambahan</label>
                        <textarea name="catatan" rows="2" class="form-input-dark text-sm" placeholder="Catatan tambahan untuk tim medis...">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                {{-- Foto Dokumentasi --}}
                <div class="pt-3 border-t border-white/10">
                    <label class="block text-sky-200 text-sm font-semibold mb-2">Foto Dokumentasi Operasi</label>
                    <div id="dropzone"
                         class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-sky-400/40 rounded-xl cursor-pointer hover:border-opacity-80 hover:bg-white/5 transition-all">
                        <i class="fas fa-cloud-upload-alt text-3xl text-sky-400 mb-1"></i>
                        <span class="text-sky-200 text-sm font-semibold">Klik atau seret foto ke sini</span>
                        <span class="text-sky-300 text-xs">Format: JPG, PNG, WEBP (Maks 5MB)</span>
                        <input type="file" name="photos[]" id="photo-upload" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                    </div>
                    <div id="photo-preview" class="flex flex-wrap gap-3 mt-4"></div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3 mb-12">
                <a href="{{ route('staff.operations.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-save mr-2"></i> Simpan Rekam Operasi Lengkap
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for members
        $('#members-select').select2({
            placeholder: 'Ketik nama anggota untuk mencari...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("api.members.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        // Helper function to update members required attribute
        function updateMembersRequirement() {
            const jenisOperasi = $('select[name="jenis_operasi"]').val();
            if (jenisOperasi === 'Konsultasi Spesialisasi') {
                $('#members-select').removeAttr('required');
                $('#members-asterisk').hide();
            } else {
                $('#members-select').attr('required', 'required');
                $('#members-asterisk').show();
            }
        }

        // Sync roles dropdown based on selected members
        function updateMemberRolesDropdowns() {
            const selectedData = $('#members-select').select2('data');
            
            // DPJP select
            const dpjpSelect = $('#dpjp-select');
            const valDpjp = dpjpSelect.val();
            let dpjpOptions = '<option value="">-- Pilih DPJP --</option>';

            // Role selects
            const roleSelects = $('.member-role-dropdown');
            
            selectedData.forEach(function(item) {
                const isSelectedDpjp = (item.id == valDpjp) ? ' selected' : '';
                dpjpOptions += `<option value="${item.id}"${isSelectedDpjp}>${item.text}</option>`;
            });

            dpjpSelect.html(dpjpOptions);

            roleSelects.each(function() {
                const el = $(this);
                const val = el.val();
                let opts = '<option value="">-- Pilih --</option>';
                selectedData.forEach(function(item) {
                    const sel = (item.text == val) ? ' selected' : '';
                    opts += `<option value="${item.text}"${sel}>${item.text}</option>`;
                });
                el.html(opts);
            });

            // DPJP hanya muncul untuk Operasi Mayor dan Operasi Minor
            const jenisOperasi = $('select[name="jenis_operasi"]').val();
            const dpjpAllowed = ['Operasi Mayor', 'Operasi Minor'];
            if (selectedData.length > 0 && dpjpAllowed.includes(jenisOperasi)) {
                $('#dpjp-section').slideDown(250);
            } else {
                $('#dpjp-section').slideUp(200);
                $('#dpjp-select').val(''); // reset DPJP jika tidak berlaku
            }
        }

        $('#members-select').on('change', updateMemberRolesDropdowns);

        // Saat jenis operasi diubah, update visibilitas DPJP dan requirement juga
        $('select[name="jenis_operasi"]').on('change', function() {
            updateMembersRequirement();
            updateMemberRolesDropdowns();
        });

        // Trigger initial calculation
        updateMembersRequirement();
        updateMemberRolesDropdowns();

        // Photo Upload & Preview Logic
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('photo-upload');
        const previewContainer = document.getElementById('photo-preview');
        let selectedFiles = [];

        dropzone.addEventListener('click', function(e) {
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dropzone-active');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dropzone-active');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            addFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', function() {
            addFiles(this.files);
        });

        function addFiles(newFiles) {
            if (!newFiles || newFiles.length === 0) return;
            Array.from(newFiles).forEach(file => {
                if (file.type.match('image.*')) {
                    if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        selectedFiles.push(file);
                    }
                }
            });
            updateFileInput();
            renderPreview();
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            renderPreview();
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }

        function renderPreview() {
            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'relative group w-24 h-24 rounded-xl overflow-hidden border-2 border-sky-400/50 shadow-md flex-shrink-0 bg-slate-800';

                const img = document.createElement('img');
                img.className = 'w-full h-full object-cover';

                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Delete Button (x)
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-1 right-1 w-6 h-6 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-xs shadow-lg transition-transform transform hover:scale-110 z-20 cursor-pointer';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.title = 'Hapus foto ini';
                removeBtn.onclick = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    removeFile(index);
                };

                const badge = document.createElement('div');
                badge.className = 'absolute bottom-0 inset-x-0 bg-black/70 text-white text-[9px] px-1 py-0.5 truncate text-center z-10';
                badge.textContent = file.name;

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                wrapper.appendChild(badge);
                previewContainer.appendChild(wrapper);
            });
        }
    });
</script>
@endpush
