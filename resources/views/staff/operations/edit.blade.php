@extends('layouts.app')

@section('title', 'Edit & Lengkapi Rekam Operasi - Portal Medis')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container { width: 100% !important; }
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
    .select2-container--default .select2-dropdown {
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
    .form-input-dark option { background: #0c4a6e; color: white; }

    .section-card {
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #38bdf8;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .section-badge {
        background: #0284c7;
        color: white;
        border-radius: 9999px;
        width: 26px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
    }
    .sub-section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #7dd3fc;
        margin-top: 1rem;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">

        {{-- Header Navigation --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('staff.operations.show', $operation->id) }}"
                   class="inline-flex items-center text-sky-300 hover:text-white text-sm font-medium mb-2 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Rekam Operasi
                </a>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="p-2.5 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl shadow-lg">
                        <i class="fas fa-edit text-white text-2xl"></i>
                    </span>
                    Edit & Lengkapi Rekam Operasi
                </h1>
                <p class="text-sky-200 text-sm mt-1">DPJP dan seluruh anggota tim bertugas yang di-tag dapat mengedit seluruh form & foto rekam medis ini.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-4 py-2 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                    <i class="fas fa-users-cog mr-1.5"></i> Mode Edit Kolaboratif
                </span>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-xl flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-400"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @php $med = $operation->medical_details ?? []; @endphp

        <form action="{{ route('staff.operations.update', $operation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                            <option value="Operasi Minor" {{ old('jenis_operasi', $operation->jenis_operasi) === 'Operasi Minor' ? 'selected' : '' }}>⚡ Operasi Minor (+30 poin)</option>
                            <option value="Operasi Mayor" {{ old('jenis_operasi', $operation->jenis_operasi) === 'Operasi Mayor' ? 'selected' : '' }}>🔴 Operasi Mayor (+60 poin)</option>
                            <option value="Emergency" {{ old('jenis_operasi', $operation->jenis_operasi) === 'Emergency' ? 'selected' : '' }}>🚨 Emergency (+25 poin)</option>
                            <option value="Konsultasi Spesialisasi" {{ old('jenis_operasi', $operation->jenis_operasi) === 'Konsultasi Spesialisasi' ? 'selected' : '' }}>🩺 Konsultasi Spesialisasi (+40 poin)</option>
                            <option value="Lainnya" {{ old('jenis_operasi', $operation->jenis_operasi) === 'Lainnya' ? 'selected' : '' }}>📋 Lainnya (+15 poin)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-calendar-alt mr-1.5 text-sky-400"></i>Tanggal & Waktu Operasi <span class="text-red-400">*</span>
                        </label>
                        <input type="datetime-local" name="tanggal_waktu" class="form-input-dark"
                               value="{{ old('tanggal_waktu', $operation->tanggal_waktu ? $operation->tanggal_waktu->format('Y-m-d\TH:i') : '') }}" required>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-map-marker-alt mr-1.5 text-sky-400"></i>Lokasi Operasi / Ruang OK <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="lokasi" class="form-input-dark" value="{{ old('lokasi', $operation->lokasi) }}"
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
                        <input type="text" name="nama_pasien" class="form-input-dark" value="{{ old('nama_pasien', $operation->nama_pasien) }}"
                               placeholder="Nama lengkap pasien" required>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-birthday-cake mr-1.5 text-sky-400"></i>Tanggal Lahir (DOB)
                        </label>
                        <input type="date" name="medical_details[pasien][dob]" class="form-input-dark"
                               value="{{ old('medical_details.pasien.dob', $med['pasien']['dob'] ?? '') }}">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-venus-mars mr-1.5 text-sky-400"></i>Jenis Kelamin
                        </label>
                        @php $jk = $med['pasien']['jenis_kelamin'] ?? ''; @endphp
                        <select name="medical_details[pasien][jenis_kelamin]" class="form-input-dark">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-Laki" {{ $jk === 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ $jk === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-tint mr-1.5 text-red-400"></i>Golongan Darah
                        </label>
                        @php $gol = $med['pasien']['gol_darah'] ?? ''; @endphp
                        <select name="medical_details[pasien][gol_darah]" class="form-input-dark">
                            <option value="">-- Pilih Gol. Darah --</option>
                            @foreach(['A','B','AB','O','A+','B+','AB+','O+'] as $g)
                                <option value="{{ $g }}" {{ $gol === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-phone mr-1.5 text-sky-400"></i>No HP / Telepon
                        </label>
                        <input type="text" name="medical_details[pasien][no_hp]" class="form-input-dark"
                               value="{{ old('medical_details.pasien.no_hp', $med['pasien']['no_hp'] ?? '') }}"
                               placeholder="Contoh: 08123456789">
                    </div>

                    <div class="md:col-span-2 lg:col-span-1">
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-id-card mr-1.5 text-sky-400"></i>Citizen ID / KTP Pasien
                        </label>
                        <input type="text" name="medical_details[pasien][citizen_id]" class="form-input-dark"
                               value="{{ old('medical_details.pasien.citizen_id', $med['pasien']['citizen_id'] ?? '') }}"
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
                        <span>Anggota yang dipilih memiliki akses penuh untuk mengisi dan melengkapi rekam medis ini.</span>
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
                            <i class="fas fa-comment-medical mr-1.5 text-sky-400"></i>Anamnesis / Keluhan Utama Pasien
                        </label>
                        <textarea name="medical_details[anamnesis][anamnesis_keluhan]" rows="3" class="form-input-dark" placeholder="Keluhan utama pasien, kronologi kejadian, dan keluhan penyerta...">{{ old('medical_details.anamnesis.anamnesis_keluhan', $med['anamnesis']['anamnesis_keluhan'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">
                            <i class="fas fa-diagnoses mr-1.5 text-sky-400"></i>Diagnosa Medis / Diagnosa Utama <span class="text-red-400">*</span>
                        </label>
                        <textarea name="diagnosa" rows="2" class="form-input-dark" placeholder="Diagnosa medis dokter (contoh: Vulnus Laceratum, Laryngeal Web, Fraktur Femur...)" required>{{ old('diagnosa', $operation->diagnosa) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Penyakit Dahulu</label>
                            <textarea name="medical_details[anamnesis][riwayat_penyakit_dahulu]" rows="2" class="form-input-dark text-sm" placeholder="Hipertensi, Diabetes, Asthma, dll...">{{ old('medical_details.anamnesis.riwayat_penyakit_dahulu', $med['anamnesis']['riwayat_penyakit_dahulu'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Penyakit Keluarga</label>
                            <textarea name="medical_details[anamnesis][riwayat_penyakit_keluarga]" rows="2" class="form-input-dark text-sm" placeholder="Riwayat penyakit keturunan keluarga...">{{ old('medical_details.anamnesis.riwayat_penyakit_keluarga', $med['anamnesis']['riwayat_penyakit_keluarga'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Alergi</label>
                            <textarea name="medical_details[anamnesis][riwayat_alergi]" rows="2" class="form-input-dark text-sm" placeholder="Alergi obat (Penicillin/dll), makanan, dll...">{{ old('medical_details.anamnesis.riwayat_alergi', $med['anamnesis']['riwayat_alergi'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Riwayat Pengobatan</label>
                            <textarea name="medical_details[anamnesis][riwayat_pengobatan]" rows="2" class="form-input-dark text-sm" placeholder="Obat-obatan yang rutin dikonsumsi saat ini...">{{ old('medical_details.anamnesis.riwayat_pengobatan', $med['anamnesis']['riwayat_pengobatan'] ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- OBSTETRI SECTION --}}
                    @php $obs = $med['obstetri'] ?? []; @endphp
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <div class="sub-section-title">
                            <i class="fas fa-baby"></i> Data Obstetri / Kebidanan
                            <span class="text-[10px] font-normal text-sky-300 ml-2 normal-case tracking-normal">(Isi jika pasien adalah ibu hamil/bersalin)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">HPHT (Hari Pertama Haid Terakhir)</label>
                                <input type="date" name="medical_details[obstetri][hpht]" class="form-input-dark text-sm"
                                       value="{{ old('medical_details.obstetri.hpht', $obs['hpht'] ?? '') }}">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Usia Kehamilan</label>
                                <input type="text" name="medical_details[obstetri][usia_kehamilan]" class="form-input-dark text-sm" 
                                       value="{{ old('medical_details.obstetri.usia_kehamilan', $obs['usia_kehamilan'] ?? '') }}" placeholder="Contoh: 38 minggu 2 hari">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Status G/P/A (Gravida/Para/Abortus)</label>
                                <input type="text" name="medical_details[obstetri][gpa]" class="form-input-dark text-sm" 
                                       value="{{ old('medical_details.obstetri.gpa', $obs['gpa'] ?? '') }}" placeholder="Contoh: G2P1A0">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">DJJ (Denyut Jantung Janin)</label>
                                <input type="text" name="medical_details[obstetri][djj]" class="form-input-dark text-sm" 
                                       value="{{ old('medical_details.obstetri.djj', $obs['djj'] ?? '') }}" placeholder="Contoh: 140 dpm, Reguler">
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Presentasi Janin</label>
                                @php $pres = $obs['presentasi'] ?? ''; @endphp
                                <select name="medical_details[obstetri][presentasi]" class="form-input-dark text-sm">
                                    <option value="">-- Pilih --</option>
                                    <option value="Presentasi Kepala (Cephalic)" {{ $pres === 'Presentasi Kepala (Cephalic)' ? 'selected' : '' }}>Presentasi Kepala (Cephalic)</option>
                                    <option value="Presentasi Bokong (Breech)" {{ $pres === 'Presentasi Bokong (Breech)' ? 'selected' : '' }}>Presentasi Bokong (Breech)</option>
                                    <option value="Presentasi Lintang (Transverse)" {{ $pres === 'Presentasi Lintang (Transverse)' ? 'selected' : '' }}>Presentasi Lintang (Transverse)</option>
                                    <option value="Presentasi Muka" {{ $pres === 'Presentasi Muka' ? 'selected' : '' }}>Presentasi Muka</option>
                                    <option value="Belum Dapat Ditentukan" {{ $pres === 'Belum Dapat Ditentukan' ? 'selected' : '' }}>Belum Dapat Ditentukan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">TFU (Tinggi Fundus Uteri)</label>
                                <input type="text" name="medical_details[obstetri][tfu]" class="form-input-dark text-sm" 
                                       value="{{ old('medical_details.obstetri.tfu', $obs['tfu'] ?? '') }}" placeholder="Contoh: 32 cm">
                            </div>
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Catatan Obstetri Tambahan</label>
                                <textarea name="medical_details[obstetri][catatan_obstetri]" rows="2" class="form-input-dark text-sm" placeholder="His, pembukaan serviks, penurunan kepala, ketuban, dll...">{{ old('medical_details.obstetri.catatan_obstetri', $obs['catatan_obstetri'] ?? '') }}</textarea>
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
                        @php $ku = $med['ttv']['keadaan_umum'] ?? ''; @endphp
                        <select name="medical_details[ttv][keadaan_umum]" class="form-input-dark text-sm">
                            <option value="Tampak Sakit Ringan" {{ $ku === 'Tampak Sakit Ringan' ? 'selected' : '' }}>Tampak Sakit Ringan</option>
                            <option value="Tampak Sakit Sedang" {{ $ku === 'Tampak Sakit Sedang' ? 'selected' : '' }}>Tampak Sakit Sedang</option>
                            <option value="Tampak Sakit Berat" {{ $ku === 'Tampak Sakit Berat' ? 'selected' : '' }}>Tampak Sakit Berat</option>
                            <option value="Baik / Stabil" {{ $ku === 'Baik / Stabil' ? 'selected' : '' }}>Baik / Stabil</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Kesadaran (GCS)</label>
                        <input type="text" name="medical_details[ttv][gcs]" class="form-input-dark text-sm" 
                               value="{{ old('medical_details.ttv.gcs', $med['ttv']['gcs'] ?? '') }}" placeholder="E4 V5 M6 (GCS 15)">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Tekanan Darah</label>
                        <input type="text" name="medical_details[ttv][tekanan_darah]" class="form-input-dark text-sm" 
                               value="{{ old('medical_details.ttv.tekanan_darah', $med['ttv']['tekanan_darah'] ?? '') }}" placeholder="120/80 mmHg">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Nadi</label>
                        <input type="text" name="medical_details[ttv][nadi]" class="form-input-dark text-sm" 
                               value="{{ old('medical_details.ttv.nadi', $med['ttv']['nadi'] ?? '') }}" placeholder="80 x/menit, Teratur">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Respirasi (RR)</label>
                        <input type="text" name="medical_details[ttv][respirasi]" class="form-input-dark text-sm" 
                               value="{{ old('medical_details.ttv.respirasi', $med['ttv']['respirasi'] ?? '') }}" placeholder="20 x/menit">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Suhu Body (°C)</label>
                        <input type="text" name="medical_details[ttv][suhu]" class="form-input-dark text-sm" 
                               value="{{ old('medical_details.ttv.suhu', $med['ttv']['suhu'] ?? '') }}" placeholder="36.5 °C">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Saturasi O2 (%)</label>
                        <input type="text" name="medical_details[ttv][saturasi]" class="form-input-dark text-sm" 
                               value="{{ old('medical_details.ttv.saturasi', $med['ttv']['saturasi'] ?? '') }}" placeholder="98 %">
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
                            <input type="text" name="tindakan_operasi" class="form-input-dark" 
                                   value="{{ old('tindakan_operasi', $operation->tindakan_operasi) }}" placeholder="Contoh: Apendektomi Laparoskopi" required>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-sm font-semibold mb-2">Waktu Mulai Tindakan</label>
                            <input type="time" name="medical_details[tindakan][waktu_mulai]" 
                                   value="{{ old('medical_details.tindakan.waktu_mulai', $med['tindakan']['waktu_mulai'] ?? '') }}" class="form-input-dark">
                        </div>
                        <div>
                            <label class="block text-sky-200 text-sm font-semibold mb-2">Waktu Selesai Tindakan</label>
                            <input type="time" name="medical_details[tindakan][waktu_selesai]" 
                                   value="{{ old('medical_details.tindakan.waktu_selesai', $med['tindakan']['waktu_selesai'] ?? '') }}" class="form-input-dark">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">Langkah-Langkah Tindakan Operasi</label>
                        <textarea name="medical_details[tindakan][langkah_tindakan]" rows="6" class="form-input-dark" 
                                  placeholder="1. Pasien diposisikan supine&#10;2. Desinfeksi lapangan operasi&#10;3. Insisi kulit dan eksplorasi...">{{ old('medical_details.tindakan.langkah_tindakan', $med['tindakan']['langkah_tindakan'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-sm font-semibold mb-2">Hasil Operasi & Intraperatif</label>
                        <textarea name="hasil_operasi" rows="4" class="form-input-dark" placeholder="Operasi berjalan lancar, perdarahan minimal...">{{ old('hasil_operasi', $operation->hasil_operasi) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 7: ANSESTESI & TABLE SCORE --}}
            <div class="section-card border-indigo-500/30">
                <div class="section-title text-indigo-300">
                    <span class="section-badge bg-indigo-600">7</span>
                    <i class="fas fa-syringe mr-1"></i> Manajemen Anestesi & Table Score
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Jenis Anestesi</label>
                        @php $ja = $med['anestesi']['jenis_anestesi'] ?? $med['anestesi']['jenis'] ?? ''; @endphp
                        <select name="medical_details[anestesi][jenis_anestesi]" class="form-input-dark text-sm">
                            <option value="">-- Pilih --</option>
                            <option value="Anestesi Umum (GA)" {{ $ja === 'Anestesi Umum (GA)' ? 'selected' : '' }}>Anestesi Umum (GA)</option>
                            <option value="Anestesi Lokal" {{ $ja === 'Anestesi Lokal' ? 'selected' : '' }}>Anestesi Lokal</option>
                            <option value="Anestesi Regional (Spinal/Epidural)" {{ $ja === 'Anestesi Regional (Spinal/Epidural)' ? 'selected' : '' }}>Anestesi Regional (Spinal/Epidural)</option>
                            <option value="Sedasi Konstan" {{ $ja === 'Sedasi Konstan' ? 'selected' : '' }}>Sedasi Konstan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Teknik Anestesi</label>
                        <input type="text" name="medical_details[anestesi][teknik]" 
                               value="{{ old('medical_details.anestesi.teknik', $med['anestesi']['teknik'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="Intubasi ETT / LMA / Infiltrasi">
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">ASA Score</label>
                        @php $asa = $med['anestesi']['asa_score'] ?? ''; @endphp
                        <select name="medical_details[anestesi][asa_score]" class="form-input-dark text-sm">
                            <option value="">-- Score ASA --</option>
                            <option value="ASA I (Pasien Sehat)" {{ $asa === 'ASA I (Pasien Sehat)' ? 'selected' : '' }}>ASA I (Pasien Sehat)</option>
                            <option value="ASA II (Penyakit Sistemik Ringan)" {{ $asa === 'ASA II (Penyakit Sistemik Ringan)' ? 'selected' : '' }}>ASA II (Penyakit Sistemik Ringan)</option>
                            <option value="ASA III (Penyakit Sistemik Berat)" {{ $asa === 'ASA III (Penyakit Sistemik Berat)' ? 'selected' : '' }}>ASA III (Penyakit Sistemik Berat)</option>
                            <option value="ASA IV (Penyakit Menjelang Ajal)" {{ $asa === 'ASA IV (Penyakit Menjelang Ajal)' ? 'selected' : '' }}>ASA IV (Penyakit Menjelang Ajal)</option>
                            <option value="ASA E (Emergency)" {{ $asa === 'ASA E (Emergency)' ? 'selected' : '' }}>ASA E (Emergency)</option>
                        </select>
                    </div>
                </div>

                <div class="sub-section-title"><i class="fas fa-pills"></i> Obat-Obatan Pra & Pasca Operasi</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Pra-Operasi (Induksi, Inhalasi, Analgesik, Relaksan)</label>
                        <textarea name="medical_details[anestesi][pra_operasi]" rows="3" class="form-input-dark text-sm" placeholder="Induksi: Propofol 100mg&#10;Analgesik: Fentanyl 100mcg&#10;Relaksan: Rocuronium 50mg">{{ old('medical_details.anestesi.pra_operasi', $med['anestesi']['pra_operasi'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Pasca-Operasi (Antidote, Anti Mual, Analgesik)</label>
                        <textarea name="medical_details[anestesi][pasca_operasi]" rows="3" class="form-input-dark text-sm" placeholder="Anti mual: Ondansetron 4mg&#10;Analgesik: Ketorolac 30mg&#10;Antidote: Sugammadex / Neostigmin">{{ old('medical_details.anestesi.pasca_operasi', $med['anestesi']['pasca_operasi'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="sub-section-title"><i class="fas fa-clipboard-check"></i> Status Lokalis & Score Pemulihan Pasca Anestesi</div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                    <div>
                        <label class="block text-sky-200 uppercase mb-1">a. Kesadaran Pasca</label>
                        <input type="text" name="medical_details[anestesi][score_kesadaran]" 
                               value="{{ old('medical_details.anestesi.score_kesadaran', $med['anestesi']['score_kesadaran'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="Sadar penuh / GCS 15">
                    </div>
                    <div>
                        <label class="block text-sky-200 uppercase mb-1">b. Respon Mual & Muntah</label>
                        <input type="text" name="medical_details[anestesi][score_mual]" 
                               value="{{ old('medical_details.anestesi.score_mual', $med['anestesi']['score_mual'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="Tidak ada mual">
                    </div>
                    <div>
                        <label class="block text-sky-200 uppercase mb-1">c. Pernapasan Pasca</label>
                        <input type="text" name="medical_details[anestesi][score_pernapasan]" 
                               value="{{ old('medical_details.anestesi.score_pernapasan', $med['anestesi']['score_pernapasan'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="Adekuat, SpO2 99%">
                    </div>
                    <div>
                        <label class="block text-sky-200 uppercase mb-1">d. Aktifitas Motorik</label>
                        <input type="text" name="medical_details[anestesi][score_motorik]" 
                               value="{{ old('medical_details.anestesi.score_motorik', $med['anestesi']['score_motorik'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="Dapat menggerakkan 4 ekstremitas">
                    </div>
                    <div>
                        <label class="block text-sky-200 uppercase mb-1">e. Tekanan Darah Pasca</label>
                        <input type="text" name="medical_details[anestesi][score_td]" 
                               value="{{ old('medical_details.anestesi.score_td', $med['anestesi']['score_td'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="120/75 mmHg (Stabil)">
                    </div>
                    <div>
                        <label class="block text-sky-200 uppercase mb-1">f. Warna Kulit</label>
                        <input type="text" name="medical_details[anestesi][score_warna_kulit]" 
                               value="{{ old('medical_details.anestesi.score_warna_kulit', $med['anestesi']['score_warna_kulit'] ?? '') }}"
                               class="form-input-dark text-sm" placeholder="Kemerahan / Merah muda">
                    </div>
                </div>
            </div>

            {{-- SECTION 8: PEMERIKSAAN PENUNJANG, SARAN & CATATAN --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="section-badge">8</span>
                    Pemeriksaan Penunjang & Saran
                </div>

                <div class="space-y-4 mb-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Hasil Laboratorium</label>
                            <textarea name="medical_details[penunjang][lab]" rows="2" class="form-input-dark text-sm" placeholder="Hb: 13.5, AL: 8.500, PLT: 250.000...">{{ old('medical_details.penunjang.lab', $med['penunjang']['lab'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Hasil Radiologi / X-Ray</label>
                            <textarea name="medical_details[penunjang][radiologi]" rows="2" class="form-input-dark text-sm" placeholder="Foto Thorax DBN, USG Abdomen...">{{ old('medical_details.penunjang.radiologi', $med['penunjang']['radiologi'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Obat-Obatan Pulang / Rawat Inap</label>
                        <textarea name="medical_details[obat_obatan]" rows="2" class="form-input-dark text-sm" placeholder="1. Amoxicillin 500mg 3x1&#10;2. Paracetamol 500mg 3x1...">{{ old('medical_details.obat_obatan', $med['obat_obatan'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Saran dan Anjuran Pasca Operasi</label>
                        <textarea name="medical_details[saran_anjuran]" rows="2" class="form-input-dark text-sm" placeholder="Bedrest 24 jam, kontrol jahitan 7 hari lagi...">{{ old('medical_details.saran_anjuran', $med['saran_anjuran'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sky-200 text-xs font-semibold uppercase mb-1.5">Catatan Tambahan</label>
                        <textarea name="catatan" rows="2" class="form-input-dark text-sm" placeholder="Catatan tambahan untuk tim medis...">{{ old('catatan', $operation->catatan) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 9: FOTO DOKUMENTASI OPERASI (EDIT & UPLOAD) --}}
            <div class="section-card border-amber-500/30">
                <div class="section-title text-amber-300">
                    <span class="section-badge bg-amber-600">9</span>
                    <i class="fas fa-camera mr-1"></i> Foto Dokumentasi Operasi
                </div>

                {{-- Existing Photos --}}
                @if($operation->photos && $operation->photos->count() > 0)
                <div class="mb-5">
                    <label class="block text-sky-200 text-xs font-semibold uppercase mb-2">Foto Dokumentasi Saat Ini (Centang untuk menghapus):</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($operation->photos as $photo)
                        <div class="relative group bg-slate-900 border border-white/10 rounded-xl overflow-hidden p-2">
                            <img src="{{ asset($photo->file_path) }}" alt="Foto Operasi" class="w-full h-32 object-cover rounded-lg">
                            <div class="mt-2 flex items-center gap-2">
                                <input type="checkbox" name="delete_photos[]" value="{{ $photo->id }}" id="del_photo_{{ $photo->id }}" class="rounded text-red-500 focus:ring-red-400">
                                <label for="del_photo_{{ $photo->id }}" class="text-xs text-red-300 font-semibold cursor-pointer select-none">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus Foto
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upload New Photos --}}
                <div class="pt-3 border-t border-white/10">
                    <label class="block text-sky-200 text-sm font-semibold mb-2">Tambah Foto Dokumentasi Baru</label>
                    <div id="dropzone"
                         class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-sky-400/40 rounded-xl cursor-pointer hover:border-opacity-80 hover:bg-white/5 transition-all">
                        <i class="fas fa-cloud-upload-alt text-3xl text-sky-400 mb-1"></i>
                        <span class="text-sky-200 text-sm font-semibold">Klik atau seret foto baru ke sini</span>
                        <span class="text-sky-300 text-xs">Format: JPG, PNG, WEBP (Maks 5MB per file)</span>
                        <input type="file" name="photos[]" id="photo-upload" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                    </div>
                    <div id="photo-preview" class="flex flex-wrap gap-3 mt-4"></div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end gap-3 mb-12">
                <a href="{{ route('staff.operations.show', $operation->id) }}"
                   class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-save mr-2"></i> Simpan & Perbarui Rekam Operasi
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
    // Existing data from server
    const initialMembers = @json($operation->members->map(fn($m) => ['id' => $m->id, 'text' => $m->name . ($m->staff_id ? ' ('.$m->staff_id.')' : '')]));
    const initialDpjpId = @json($operation->dpjp_id);
    const initialTim = @json($med['tim'] ?? []);

    // Init Select2 for members
    const $membersSelect = $('#members-select').select2({
        placeholder: "Ketik nama atau ID staf untuk memilih tim bertugas...",
        allowClear: true,
        ajax: {
            url: "{{ route('api.members.search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        }
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

    // Function to update role dropdowns and DPJP section visibility
    function updateMemberRolesDropdowns() {
        const selectedData = $membersSelect.select2('data');
        const count = selectedData.length;

        // DPJP hanya muncul untuk Operasi Mayor dan Operasi Minor
        const jenisOperasi = $('select[name="jenis_operasi"]').val();
        const dpjpAllowed = ['Operasi Mayor', 'Operasi Minor'];
        if (count > 0 && dpjpAllowed.includes(jenisOperasi)) {
            $('#dpjp-section').slideDown(250);
        } else {
            $('#dpjp-section').slideUp(200);
            $('#dpjp-select').val(''); // reset DPJP jika tidak berlaku
        }

        // DPJP Dropdown
        const $dpjpSelect = $('#dpjp-select');
        const currentDpjpVal = $dpjpSelect.val() || initialDpjpId;
        $dpjpSelect.empty();
        $dpjpSelect.append(new Option('-- Pilih DPJP Operasi --', ''));

        selectedData.forEach(function(item) {
            const isSelected = item.id == currentDpjpVal;
            $dpjpSelect.append(new Option(item.text, item.id, false, isSelected));
        });

        // Role Dropdowns
        $('.member-role-dropdown').each(function() {
            const $dropdown = $(this);
            const nameAttr = $dropdown.attr('name');
            const fieldKey = nameAttr ? nameAttr.replace('medical_details[tim][', '').replace(']', '') : '';
            const currentVal = $dropdown.val() || (initialTim[fieldKey] || '');

            $dropdown.empty();
            $dropdown.append(new Option('-- Tidak Ada --', ''));

            selectedData.forEach(function(item) {
                const isSelected = item.text === currentVal || item.id == currentVal;
                $dropdown.append(new Option(item.text, item.text, false, isSelected));
            });
        });
    }

    // Register event handlers
    $membersSelect.on('change', updateMemberRolesDropdowns);
    $('select[name="jenis_operasi"]').on('change', function() {
        updateMembersRequirement();
        updateMemberRolesDropdowns();
    });

    // Populate initial members
    initialMembers.forEach(function(item) {
        const option = new Option(item.text, item.id, true, true);
        $membersSelect.append(option);
    });

    // Trigger initial calculation
    updateMembersRequirement();
    $membersSelect.trigger('change');

    // Dropzone logic for photo upload
    const dropzone = document.getElementById('dropzone');
    const photoInput = document.getElementById('photo-upload');
    const previewContainer = document.getElementById('photo-preview');

    if (dropzone && photoInput) {
        dropzone.addEventListener('click', function(e) {
            if (e.target !== photoInput) {
                photoInput.click();
            }
        });

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('bg-white/10');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('bg-white/10');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('bg-white/10');
            if (e.dataTransfer.files.length) {
                photoInput.files = e.dataTransfer.files;
                renderPreviews(e.dataTransfer.files);
            }
        });

        photoInput.addEventListener('change', () => {
            renderPreviews(photoInput.files);
        });
    }

    function renderPreviews(files) {
        if (!previewContainer) return;
        previewContainer.innerHTML = '';
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'relative w-24 h-24 border border-white/20 rounded-xl overflow-hidden shadow-md';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endpush
