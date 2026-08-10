@extends('layouts.app')

@section('title', 'Portal Medis iMe Roleplay - Layanan Medis untuk Komunitas RP')

@section('meta_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi. Konsultasi Medis. Pemeriksaan umum dan diagnosis.')

@section('meta_keywords', 'ime roleplay, portal medis ime roleplay, ime roleplay medical, motion ime roleplay, gta roleplay, motionlife roleplay, motion ime, portal medis, motion medical center, layanan medis, EMS, medical center roleplay, roleplay medical services')

@section('og_title', 'Portal Medis iMe Roleplay - Layanan Medis untuk Komunitas RP')

@section('og_description', 'Portal Medis iMe Roleplay - Menyediakan perawatan komprehensif untuk semua kebutuhan kesehatan Anda dengan standar profesional tertinggi. Konsultasi Medis. Pemeriksaan umum dan diagnosis.')

@section('content')
    <!-- Pop-up Informasi Kenaikan Regulasi -->
    <div id="regulationModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-2 sm:p-4"
        style="display: none;">
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[95vh] overflow-hidden animate-fade-in-up flex flex-col">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-red-500 via-red-600 to-pink-600 text-white p-6 sm:p-8 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full translate-y-12 -translate-x-12">
                    </div>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 sm:space-x-6">
                        <div
                            class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-lg animate-pulse flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-2xl sm:text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-3xl font-black mb-1 sm:mb-2">UPDATE REGULASI</h2>
                            <div class="flex flex-wrap items-center mt-2 sm:mt-3 gap-2 sm:space-x-4">
                                <i class="fas fa-calendar-alt text-xs sm:text-sm"></i>
                                <span class="text-xs sm:text-sm font-semibold">07 Januari 2026</span>
                            </div>
                            <div class="flex items-center space-x-2 bg-white/20 px-3 py-1 rounded-full">
                                <i class="fas fa-clock text-xs sm:text-sm"></i>
                                <span class="text-xs sm:text-sm font-semibold">Efektif Segera</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="closeRegulationModal()"
                    class="absolute top-0 right-0 mt-4 mr-4 sm:relative sm:mt-0 sm:mr-0 w-10 h-10 sm:w-12 sm:h-12 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl sm:rounded-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-lg flex-shrink-0">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Content -->
            <div
                class="flex-1 p-6 sm:p-8 space-y-6 sm:space-y-8 bg-gradient-to-b from-gray-50 to-white overflow-y-auto custom-scrollbar">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-cash-register text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Cash Payment</h4>
                        <p class="text-sm opacity-90">Pembayaran Tunai</p>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-file-invoice text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Invoice Payment</h4>
                        <p class="text-sm opacity-90">Pembayaran Tagihan</p>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-2xl text-center">
                        <i class="fas fa-chart-line text-2xl mb-2"></i>
                        <h4 class="font-bold text-lg">Price Update</h4>
                        <p class="text-sm opacity-90">Update Harga</p>
                    </div>
                </div>

                <!-- REGULASI PENGOBATAN (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-blue-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            REGULASI PENGOBATAN (CASH)
                        </h3>
                        <p class="text-blue-100 mt-2">Layanan pengobatan dengan pembayaran tunai</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div
                                    class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-hospital text-sky-500 text-xl"></i>
                                        <span class="font-semibold text-gray-800">TREATMENT RS</span>
                                    </div>
                                    <span class="text-2xl font-bold text-sky-500">$200</span>
                                </div>
                                <div
                                    class="flex items-center justify-between p-4 bg-cyan-50 rounded-xl border border-cyan-200">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-ambulance text-cyan-600 text-xl"></i>
                                        <span class="font-semibold text-gray-800">TREATMENT LUAR RS</span>
                                    </div>
                                    <span class="text-2xl font-bold text-cyan-600">$220</span>
                                </div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika terdapat luka dan diresepkan obat atau
                                            bandage tambahan, akan ditambah <span class="font-bold">$20</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KONSULTASI KESEHATAN (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-emerald-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-user-md text-xl"></i>
                            </div>
                            REGULASI KONSULTASI KESEHATAN (CASH)
                        </h3>
                        <p class="text-emerald-100 mt-2">Layanan konsultasi kesehatan dengan dokter umum dan spesialis</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-gradient-to-br from-emerald-50 to-teal-50 p-6 rounded-xl border border-emerald-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-stethoscope text-emerald-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">DOKTER UMUM</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-emerald-600">$1,300</span>
                                        <span class="text-lg text-emerald-500">- $1,800</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Per-Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 p-6 rounded-xl border border-teal-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-md text-teal-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">DOKTER SPESIALIS</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-teal-600">$1,900</span>
                                        <span class="text-lg text-teal-500">- $3,500</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Per-Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-3">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika diresepkan pemeriksaan tambahan <span
                                                class="font-bold">$630</span></p>
                                        <p class="text-sm text-yellow-700 mt-1">Jika diresepkan obat tambahan akan ditambah
                                            <span class="font-bold">$130</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KONSULTASI DAN PENGOBATAN KESEHATAN GIGI UMUM DAN ANAK (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-cyan-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-tooth text-xl"></i>
                            </div>
                            REGULASI KONSULTASI DAN PENGOBATAN KESEHATAN GIGI UMUM DAN ANAK (CASH)
                        </h3>
                        <p class="text-cyan-100 mt-2">Layanan konsultasi dan pengobatan kesehatan gigi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-xl border border-cyan-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-graduate text-cyan-600 text-xl"></i>
                                    <span class="text-xl font-bold text-cyan-600">TBA</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">CO-ASS</h4>
                                <p class="text-xs text-gray-600 mt-1">Co-assistant</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-tooth text-sky-500 text-xl"></i>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-sky-500">$1,300</span>
                                        <span class="text-sm text-blue-500">- $1,900</span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">DOKTER GIGI UMUM</h4>
                                <p class="text-xs text-gray-600 mt-1">Per Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border border-indigo-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-md text-indigo-600 text-xl"></i>
                                    <div class="text-right">
                                        <span class="text-xl font-bold text-indigo-600">$1,900</span>
                                        <span class="text-sm text-indigo-500">- $3,800</span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">DOKTER SPESIALIS GIGI & BEDAH MULUT</h4>
                                <p class="text-xs text-gray-600 mt-1">Per Konsultasi (TERGANTUNG KESULITAN)</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika diresepkan pemeriksaan tambahan <span
                                                class="font-bold">$650</span></p>
                                        <p class="text-sm text-yellow-700 mt-1">Jika diresepkan obat tambahan akan ditambah
                                            <span class="font-bold">$130</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI PENANGANAN PINGSAN (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-green-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-ambulance text-xl"></i>
                            </div>
                            REGULASI PENANGANAN PINGSAN (CASH)
                        </h3>
                        <p class="text-green-100 mt-2">Layanan penanganan pingsan berdasarkan area lokasi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-city text-green-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-green-600">$380</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">AREA KOTA</h4>
                                <p class="text-xs text-gray-600 mt-1">Lokasi perkotaan</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-emerald-50 to-teal-50 p-4 rounded-xl border border-emerald-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-umbrella-beach text-emerald-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-emerald-600">$320</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">SANDY SHORES</h4>
                                <p class="text-xs text-gray-600 mt-1">Area pantai</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-teal-50 to-cyan-50 p-4 rounded-xl border border-teal-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-mountain text-teal-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-teal-600">$550</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">GUNUNG - LAUT</h4>
                                <p class="text-xs text-gray-600 mt-1">Area terpencil</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-xl border border-cyan-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-crosshairs text-cyan-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-cyan-600">$570</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">AREA HUNTING</h4>
                                <p class="text-xs text-gray-600 mt-1">Zona berburu</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-bomb text-sky-500 text-xl"></i>
                                    <span class="text-2xl font-bold text-sky-500">$500</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">ZONA PERANG / PRA-SITUASI</h4>
                                <p class="text-xs text-gray-600 mt-1">Area konflik</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-xl border border-indigo-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-fist-raised text-indigo-600 text-xl"></i>
                                    <span class="text-2xl font-bold text-indigo-600">$570</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">ZONA ADU MEKANIK / UFC</h4>
                                <p class="text-xs text-gray-600 mt-1">Area pertarungan</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-1"></i>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800 mb-2">Catatan Penting</h4>
                                        <p class="text-sm text-yellow-700">Jika terdapat luka dan diresepkan obat atau
                                            bandage tambahan, akan ditambah <span class="font-bold">$20</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI PEMBUATAN SURAT-SURAT (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-purple-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-file-alt text-xl"></i>
                            </div>
                            REGULASI PEMBUATAN SURAT-SURAT (CASH)
                        </h3>
                        <p class="text-purple-100 mt-2">Layanan pembuatan dokumen medis resmi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-gradient-to-br from-purple-50 to-indigo-50 p-6 rounded-xl border border-purple-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-certificate text-purple-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">SURAT KETERANGAN KESEHATAN</h4>
                                    </div>
                                    <span class="text-3xl font-bold text-purple-600">$2,000</span>
                                </div>
                                <p class="text-sm text-gray-600">Dokumen resmi untuk keperluan administrasi kesehatan</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-brain text-indigo-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">SURAT KETERANGAN PSIKOLOGI</h4>
                                    </div>
                                    <span class="text-3xl font-bold text-indigo-600">$3,000</span>
                                </div>
                                <p class="text-sm text-gray-600">Dokumen psikologis untuk keperluan khusus</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KONSULTASI (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-indigo-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-user-md text-xl"></i>
                            </div>
                            REGULASI KONSULTASI (CASH)
                        </h3>
                        <p class="text-indigo-100 mt-2">Layanan konsultasi kesehatan (Wajib Cash)</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-blue-50 p-6 rounded-xl border border-indigo-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-nurse text-indigo-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI DOKTER UMUM</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-indigo-600">$2,500</span>
                                        <span class="text-lg text-indigo-500">- $10,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Pemeriksaan dan konsultasi umum</p>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-user-graduate text-sky-500 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI SPESIALIS</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-sky-500">$5,000</span>
                                        <span class="text-lg text-blue-500">- $10,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Konsultasi dengan dokter spesialis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI OPERASI (CASH) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-orange-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-cut text-xl"></i>
                            </div>
                            REGULASI OPERASI (CASH)
                        </h3>
                        <p class="text-orange-100 mt-2">Layanan operasi (Wajib Cash)</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 rounded-xl border border-orange-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-scalpel text-orange-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">OPERASI BESAR</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-orange-600">$15,000</span>
                                        <span class="text-lg text-orange-500">- $20,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Prosedur operasi kompleks dan rumit</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <p class="text-xs text-yellow-800 font-semibold">TERGANTUNG KESULITAN</p>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 p-6 rounded-xl border border-red-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-tools text-red-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">OPERASI KECIL</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-red-600">$5,000</span>
                                        <span class="text-lg text-red-500">- $10,000</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">Prosedur operasi sederhana dan ringan</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <p class="text-xs text-yellow-800 font-semibold">TERGANTUNG KESULITAN</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI FARMASI (INVOICE) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-teal-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-pills text-xl"></i>
                            </div>
                            REGULASI FARMASI (INVOICE)
                        </h3>
                        <p class="text-teal-100 mt-2">Layanan obat-obatan dan peralatan medis</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-teal-50 to-cyan-50 p-4 rounded-xl border border-teal-200 text-center">
                                <i class="fas fa-band-aid text-teal-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">BANDAGE</h4>
                                <div class="text-2xl font-bold text-teal-600 mb-1">$20</div>
                                <p class="text-xs text-gray-600">MAX 25 PCS</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-xl border border-cyan-200 text-center">
                                <i class="fas fa-first-aid text-cyan-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">IFAKS</h4>
                                <div class="text-2xl font-bold text-cyan-600 mb-1">$70</div>
                                <p class="text-xs text-gray-600">MAX 6 PCS</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 text-center">
                                <i class="fas fa-pills text-sky-500 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">PAINKILLER</h4>
                                <div class="text-2xl font-bold text-sky-500 mb-1">$70</div>
                                <p class="text-xs text-gray-600">MAX 6 PCS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI KEMATIAN (INVOICE) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-gray-500 to-slate-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-cross text-xl"></i>
                            </div>
                            REGULASI KEMATIAN (INVOICE)
                        </h3>
                        <p class="text-gray-100 mt-2">Layanan pemakaman dan kremasi</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gradient-to-br from-gray-50 to-slate-50 p-6 rounded-xl border border-gray-200">
                                <div class="text-center mb-4">
                                    <i class="fas fa-archway text-gray-600 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET A. PENGUBURAN</h4>
                                    <div class="text-3xl font-bold text-gray-600">$25,000</div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 font-semibold">CASH</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Pembersihan Jenazah (Mandi)</li>
                                    <li>Pengantaran ke Tempat Pemakaman menggunakan IME Van Ambulance</li>
                                    <li>Gratis Kamar Mayat 2 Hari International (untuk Melayat)</li>
                                    <li>Penggalian Liang Kubur + Penguburan Jenazah</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-slate-50 to-gray-50 p-6 rounded-xl border border-slate-200">
                                <div class="text-center mb-4">
                                    <i class="fas fa-fire text-slate-600 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET B. KREMASI</h4>
                                    <div class="text-3xl font-bold text-slate-600">$18,800</div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 font-semibold">CASH</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Pembersihan Jenazah (Mandi)</li>
                                    <li>Proses Kremasi Oven Modern 12 - 24 Jam International</li>
                                    <li>IME Medical Center Gucci Abu Exclusive, dibuat oleh pengrajin terbaik dan dilapisi
                                        dengan emas</li>
                                </ul>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-sky-50 p-6 rounded-xl border border-blue-200">
                                <div class="text-center mb-4">
                                    <i class="fas fa-users text-sky-500 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-gray-800 mb-2">PAKET C. KELUARGA</h4>
                                    <div class="text-3xl font-bold text-sky-500">$7,500</div>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 font-semibold">CASH</p>
                                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                    <li>Jenazah diambil oleh pihak keluarga</li>
                                    <li>Administrasi surat kematian</li>
                                    <li>Sertifikat kematian resmi</li>
                                    <li>Tidak termasuk layanan pemakaman/kremasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI RAWAT INAP (15 MENIT / DAY) (INVOICE) -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-pink-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-bed text-xl"></i>
                            </div>
                            REGULASI RAWAT INAP (15 MENIT / DAY)
                        </h3>
                        <p class="text-pink-100 mt-2">Layanan rawat inap dengan sistem tagihan harian</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-pink-50 to-rose-50 p-6 rounded-xl border border-pink-200 text-center">
                                <i class="fas fa-crown text-pink-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">VIP ROOM</h4>
                                <div class="text-3xl font-bold text-pink-600 mb-1">$650</div>
                                <p class="text-sm text-gray-600">/ DAY</p>
                                <p class="text-xs text-pink-700 mt-2">Kamar mewah dengan fasilitas lengkap</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-rose-50 to-pink-50 p-6 rounded-xl border border-rose-200 text-center">
                                <i class="fas fa-star text-rose-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">KELAS 1</h4>
                                <div class="text-3xl font-bold text-rose-600 mb-1">$400</div>
                                <p class="text-sm text-gray-600">/ DAY</p>
                                <p class="text-xs text-rose-700 mt-2">Kamar standar dengan fasilitas baik</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-pink-50 to-purple-50 p-6 rounded-xl border border-pink-200 text-center">
                                <i class="fas fa-bed text-purple-600 text-3xl mb-3"></i>
                                <h4 class="font-bold text-gray-800 mb-2">KELAS 2</h4>
                                <div class="text-3xl font-bold text-purple-600 mb-1">$200</div>
                                <p class="text-sm text-gray-600">/ DAY</p>
                                <p class="text-xs text-purple-700 mt-2">Kamar standar dengan fasilitas dasar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REGULASI VISUM DAN OTOPSI MAYAT -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-yellow-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-search text-xl"></i>
                            </div>
                            REGULASI VISUM DAN OTOPSI MAYAT
                        </h3>
                        <p class="text-yellow-100 mt-2">Layanan forensik dan pemeriksaan medis khusus</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                class="bg-gradient-to-br from-yellow-50 to-amber-50 p-4 rounded-xl border border-yellow-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-check text-yellow-600 text-xl"></i>
                                    <span class="text-xl font-bold text-yellow-600">$25,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Pada Orang Hidup</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan pada orang hidup</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-amber-50 to-orange-50 p-4 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-user-times text-amber-600 text-xl"></i>
                                    <span class="text-xl font-bold text-amber-600">$38,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Post Mortem</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan setelah kematian</p>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-4 rounded-xl border border-orange-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-microscope text-orange-600 text-xl"></i>
                                    <span class="text-xl font-bold text-orange-600">$63,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Post Mortem dengan Autopsi
                                </h4>
                                <p class="text-xs text-gray-600">Pemeriksaan lengkap dengan autopsi</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl border border-red-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-brain text-red-600 text-xl"></i>
                                    <span class="text-xl font-bold text-red-600">$32,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Visum et Repertum Psikiatri</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan psikiatris</p>
                            </div>
                            <div class="bg-gradient-to-br from-pink-50 to-purple-50 p-4 rounded-xl border border-pink-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-flask text-pink-600 text-xl"></i>
                                    <span class="text-xl font-bold text-pink-600">$38,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Laboratorium Forensik</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan laboratorium forensik</p>
                            </div>
                            <div
                                class="bg-gradient-to-br from-purple-50 to-indigo-50 p-4 rounded-xl border border-purple-200">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fas fa-vial text-purple-600 text-xl"></i>
                                    <span class="text-xl font-bold text-purple-600">$13,000</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-sm">Laboratorium Klinis</h4>
                                <p class="text-xs text-gray-600">Pemeriksaan laboratorium klinis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OPERASI PLASTIK -->
                <div
                    class="bg-white rounded-3xl shadow-lg border border-fuchsia-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                    <div class="bg-gradient-to-r from-fuchsia-500 to-pink-500 text-white p-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-magic text-xl"></i>
                            </div>
                            OPERASI PLASTIK
                        </h3>
                        <p class="text-fuchsia-100 mt-2">Layanan bedah estetika (Cash + Billing)</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div
                                class="bg-gradient-to-br from-fuchsia-50 to-pink-50 p-6 rounded-xl border border-fuchsia-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-comments text-fuchsia-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">KONSULTASI BEDAH PLASTIK</h4>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-3xl font-bold text-fuchsia-600">Gratis</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Konsultasi awal dengan dokter spesialis</p>
                            </div>
                            <div class="bg-gradient-to-br from-pink-50 to-rose-50 p-6 rounded-xl border border-pink-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-scalpel text-pink-600 text-2xl"></i>
                                        <h4 class="font-bold text-gray-800">BIAYA OPERASI</h4>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <div class="flex items-center">
                                            <span
                                                class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded mr-2">CASH</span>
                                            <span class="text-3xl font-bold text-pink-600">$10,000</span>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <span
                                                class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded mr-2">BILLING</span>
                                            <span class="text-xl font-bold text-pink-500">$3,000</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">Total Biaya: $13,000 (Cash + Invoice)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-4 sm:p-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Informasi Penting</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Regulasi ini berlaku efektif mulai 02 Januari 2026
                            </p>
                        </div>
                    </div>
                    <div class="flex w-full sm:w-auto space-x-3">
                        <button onclick="closeRegulationModal()"
                            class="w-full sm:w-auto bg-gradient-to-r from-gray-500 to-slate-500 text-white px-6 py-3 rounded-xl font-bold hover:from-gray-600 hover:to-slate-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-times mr-2"></i>Tutup
                        </button>
                        <button onclick="closeRegulationModal()"
                            class="bg-gradient-to-r from-sky-500 to-cyan-500 text-white px-8 py-3 rounded-xl font-bold hover:from-sky-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-check mr-2"></i>Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ================================================================
         HERO &mdash; Innovation Clinic style
    ================================================================ -->
    <section class="lp-hero">
        <div class="lp-hero__inner">

            <!-- LEFT: copy -->
            <div class="lp-hero__copy">
                <p class="lp-hero__eyebrow">Los Santos Medical Services</p>
                <h1 class="lp-hero__title">iMe<br><span>Medical</span></h1>
                <p class="lp-hero__sub">
                    We combine innovative technologies with a human approach
                    to make every patient feel confident and calm.
                </p>
                <div class="lp-hero__actions">
                    <a href="#layanan" class="lp-btn lp-btn--white">Lihat Layanan</a>
                    <a href="{{ route('public.doctor-schedule') }}" class="lp-btn lp-btn--outline">Jadwal Dokter</a>
                </div>
                <!-- stats -->
                <div class="lp-hero__stats">
                    <div class="lp-stat">
                        <strong>{{ number_format($stats['total_forms']) }}+</strong>
                        <span>Pasien</span>
                    </div>
                    <div class="lp-stat-divider"></div>
                    <div class="lp-stat">
                        <strong>{{ $stats['total_staff'] }}+</strong>
                        <span>Tenaga Medis</span>
                    </div>
                    <div class="lp-stat-divider"></div>
                    <div class="lp-stat">
                        <strong>100%</strong>
                        <span>Digital</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: doctor image + floating chips -->
            <div class="lp-hero__visual">
                <div class="lp-hero__img-wrap">
                    <img
                        src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=900&q=85"
                        alt="Doctor iMe Medical"
                        class="lp-hero__doctor"
                    >
                    <!-- floating chips -->
                    <div class="lp-chip lp-chip--1">Reliability</div>
                    <div class="lp-chip lp-chip--2">Experience</div>
                    <div class="lp-chip lp-chip--3">Professional</div>

                    <!-- mini card bottom-right -->
                    <div class="lp-hero__mini-card">
                        <div class="lp-hero__mini-card-img">
                            <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=400&q=80" alt="Hospital">
                        </div>
                        <div>
                            <div class="lp-hero__mini-card-title">Alta Hospital</div>
                            <div class="lp-hero__mini-card-sub">Alta Street, Los Santos</div>
                        </div>
                    </div>
                </div>

                <!-- right-side text block -->
                <div class="lp-hero__right-text">
                    <p class="lp-hero__right-label">With Advanced Technologies</p>
                    <p class="lp-hero__right-desc">
                        Sistem digital terpadu, diagnostik modern &mdash; semua dalam
                        satu portal khusus untuk komunitas RP.
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- ================================================================
         ABOUT &mdash; centered statement
    ================================================================ -->
    <section class="lp-about">
        <div class="lp-container">
            <p class="lp-about__text">
                We combine innovative
                <i class="fas fa-plus-circle text-sky-500"></i>
                technologies with a human approach to make every patient
                <i class="fas fa-user-md text-sky-500"></i>
                feel <em>confident and calm.</em>
            </p>
            <p class="lp-about__sub">
                iMe adalah platform yang menjadi space of trust, modern medicine and care, based
                on many years of experience and love for people.
            </p>
            <a href="#tentang" class="lp-btn lp-btn--dark">More about us &rarr;</a>
        </div>
    </section>

    <!-- ================================================================
         SERVICES
    ================================================================ -->
    <section id="layanan" class="lp-services">
        <div class="lp-container">

            <div class="lp-services__header">
                <div>
                    <h2 class="lp-section-title">Our medical<br>services <span class="lp-tag">iMe</span></h2>
                </div>
                <p class="lp-services__desc">
                    We provide a full range of medical services &mdash;
                    from consultation to diagnosis and treatment.
                    <a href="{{ route('public.doctor-schedule') }}" class="lp-link">See all services &rarr;</a>
                </p>
            </div>

            <div class="lp-services__grid">

                <!-- 01 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">01</div>
                    <div class="lp-service-card__icon"><i class="fas fa-stethoscope text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Konsultasi Medis</h3>
                    <p class="lp-service-card__desc">Pemeriksaan umum, diagnosis, dan layanan kesehatan standar.</p>
                    <a href="{{ route('public.cek-kesehatan') }}" class="lp-service-card__link">Surat Kesehatan</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- Featured center card (blue) -->
                <article class="lp-service-card lp-service-card--featured">
                    <div class="lp-service-card__logo">iMe<br><span>Medical</span></div>
                    <img
                        src="https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?auto=format&fit=crop&w=600&q=80"
                        alt="Medical"
                        class="lp-service-card__bg-img"
                    >
                </article>

                <!-- 02 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">02</div>
                    <div class="lp-service-card__icon"><i class="fas fa-user-nurse text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Operasi Plastik</h3>
                    <p class="lp-service-card__desc">Prosedur bedah estetika oleh tenaga profesional terlatih.</p>
                    <a href="{{ route('public.operasi-plastik') }}" class="lp-service-card__link">Daftar Oplas</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- 03 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">03</div>
                    <div class="lp-service-card__icon"><i class="fas fa-brain text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Psikologi</h3>
                    <p class="lp-service-card__desc">Dukungan kesehatan mental untuk karakter Anda.</p>
                    <a href="{{ route('public.surat-psikolog') }}" class="lp-service-card__link">Formulir Psikologi</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- 04 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">04</div>
                    <div class="lp-service-card__icon"><i class="fas fa-heartbeat text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Karakter Kill</h3>
                    <p class="lp-service-card__desc">Layanan medis kritis untuk kebutuhan storyline roleplay.</p>
                    <a href="{{ route('public.pendaftaran-karakter') }}" class="lp-service-card__link">Daftar Sekarang</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- 05 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">05</div>
                    <div class="lp-service-card__icon"><i class="fas fa-file-medical text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Surat Medis</h3>
                    <p class="lp-service-card__desc">Surat sehat, bebas narkoba, dan keterangan khusus.</p>
                    <a href="{{ route('public.cek-kesehatan') }}" class="lp-service-card__link">Buat Surat</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

            </div>
        </div>
    </section>

    <!-- ================================================================
         WHY CHOOSE US
    ================================================================ -->
    <section class="lp-why">
        <div class="lp-container">
            <div class="lp-why__inner">

                <!-- left: image + chips -->
                <div class="lp-why__visual">
                    <div class="lp-why__img-wrap">
                        <img
                            src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&w=900&q=85"
                            alt="Medical team"
                            class="lp-why__img"
                        >
                        <div class="lp-why__chip lp-why__chip--1">
                            <span class="lp-why__chip-dot"></span>
                            Experienced Doctors
                        </div>
                        <div class="lp-why__chip lp-why__chip--2">
                            <span class="lp-why__chip-dot"></span>
                            Modern Equipment
                        </div>
                        <div class="lp-why__chip lp-why__chip--3">
                            <span class="lp-why__chip-dot"></span>
                            Certified Clinic
                        </div>
                    </div>
                </div>

                <!-- right: copy + stats -->
                <div class="lp-why__copy">
                    <p class="lp-eyebrow">Why choose us</p>
                    <h2 class="lp-why__title">Why<br>choose us</h2>
                    <p class="lp-why__sub">
                        Kami adalah platform yang telah dipercaya komunitas roleplay
                        Los Santos dengan standar medis yang konsisten dan profesional.
                    </p>
                    <div class="lp-why__stats">
                        <div class="lp-why__stat">
                            <strong>10+</strong>
                            <span>Years of experience</span>
                        </div>
                        <div class="lp-why__stat">
                            <strong>15</strong>
                            <span>Areas of medicine</span>
                        </div>
                        <div class="lp-why__stat">
                            <strong>95%</strong>
                            <span>Satisfied patients</span>
                        </div>
                        <div class="lp-why__stat">
                            <strong>98%</strong>
                            <span>Diagnostics</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================================================================
         SCHEDULE + CTA
    ================================================================ -->
    <section id="jadwal" class="lp-schedule">
        <div class="lp-container">
            <div class="lp-schedule__inner">

                <!-- schedule table -->
                <div class="lp-schedule__table">
                    <h2 class="lp-section-title" style="margin-bottom:24px;">Jam Operasional</h2>
                    <div class="lp-schedule__notice">
                        <i class="fas fa-exclamation-triangle text-amber-500 mr-1"></i> <strong>Info:</strong> Pelayanan sesuai ketersediaan tenaga medis (On Duty).
                    </div>
                    <div class="lp-schedule__row">
                        <div class="lp-schedule__info">
                            <div class="lp-schedule__icon"><i class="fas fa-user-nurse"></i></div>
                            <div>
                                <div class="lp-schedule__name">Operasi Plastik</div>
                                <div class="lp-schedule__desc">Shift 1: 13:00&ndash;16:00 &middot; Shift 2: 20:00&ndash;22:00</div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-schedule__row">
                        <div class="lp-schedule__info">
                            <div class="lp-schedule__icon"><i class="fas fa-file-medical"></i></div>
                            <div>
                                <div class="lp-schedule__name">Surat-Suratan Medis</div>
                                <div class="lp-schedule__desc">Shift 1: 13:00&ndash;17:00 &middot; Shift 2: 19:00&ndash;22:00</div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-schedule__row">
                        <div class="lp-schedule__info">
                            <div class="lp-schedule__icon lp-schedule__icon--green"><i class="fas fa-pills"></i></div>
                            <div>
                                <div class="lp-schedule__name">Layanan Farmasi</div>
                                <div class="lp-schedule__desc">Pengambilan & pengobatan medis</div>
                            </div>
                        </div>
                        <div class="lp-badge-green">
                            <span class="lp-badge-dot"></span>
                            BUKA 24 JAM
                        </div>
                    </div>
                </div>

                <!-- CTA card -->
                <div class="lp-cta-card">
                    <p class="lp-eyebrow lp-eyebrow--light">Ready when you are</p>
                    <h3>Butuh layanan medis sekarang?</h3>
                    <p>Akses layanan iMe dengan mudah dan dapatkan bantuan dari tenaga medis kami kapan saja.</p>
                    <a href="#layanan" class="lp-btn lp-btn--white" style="margin-top:24px;display:inline-block;">Lihat Layanan</a>
                    <button onclick="showRegulationModal()" class="lp-btn lp-btn--outline-light" style="margin-top:12px;">
                        Regulasi Pengobatan
<div class="lp-container">

            <div class="lp-services__header">
                <div>
                    <h2 class="lp-section-title">Our medical<br>services <span class="lp-tag">iMe</span></h2>
                </div>
                <p class="lp-services__desc">
                    We provide a full range of medical services &mdash;
                    from consultation to diagnosis and treatment.
                    <a href="{{ route('public.doctor-schedule') }}" class="lp-link">See all services &rarr;</a>
                </p>
            </div>

            <div class="lp-services__grid">

                <!-- 01 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">01</div>
                    <div class="lp-service-card__icon"><i class="fas fa-stethoscope text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Konsultasi Medis</h3>
                    <p class="lp-service-card__desc">Pemeriksaan umum, diagnosis, dan layanan kesehatan standar.</p>
                    <a href="{{ route('public.cek-kesehatan') }}" class="lp-service-card__link">Surat Kesehatan</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- Featured center card (blue) -->
                <article class="lp-service-card lp-service-card--featured">
                    <div class="lp-service-card__logo">iMe<br><span>Medical</span></div>
                    <img
                        src="https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?auto=format&fit=crop&w=600&q=80"
                        alt="Medical"
                        class="lp-service-card__bg-img"
                    >
                </article>

                <!-- 02 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">02</div>
                    <div class="lp-service-card__icon"><i class="fas fa-user-nurse text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Operasi Plastik</h3>
                    <p class="lp-service-card__desc">Prosedur bedah estetika oleh tenaga profesional terlatih.</p>
                    <a href="{{ route('public.operasi-plastik') }}" class="lp-service-card__link">Daftar Oplas</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- 03 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">03</div>
                    <div class="lp-service-card__icon"><i class="fas fa-brain text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Psikologi</h3>
                    <p class="lp-service-card__desc">Dukungan kesehatan mental untuk karakter Anda.</p>
                    <a href="{{ route('public.surat-psikolog') }}" class="lp-service-card__link">Formulir Psikologi</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- 04 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">04</div>
                    <div class="lp-service-card__icon"><i class="fas fa-heartbeat text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Karakter Kill</h3>
                    <p class="lp-service-card__desc">Layanan medis kritis untuk kebutuhan storyline roleplay.</p>
                    <a href="{{ route('public.pendaftaran-karakter') }}" class="lp-service-card__link">Daftar Sekarang</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

                <!-- 05 -->
                <article class="lp-service-card">
                    <div class="lp-service-card__num">05</div>
                    <div class="lp-service-card__icon"><i class="fas fa-file-medical text-sky-500"></i></div>
                    <h3 class="lp-service-card__title">Surat Medis</h3>
                    <p class="lp-service-card__desc">Surat sehat, bebas narkoba, dan keterangan khusus.</p>
                    <a href="{{ route('public.cek-kesehatan') }}" class="lp-service-card__link">Buat Surat</a>
                    <span class="lp-service-card__tba">TBA</span>
                </article>

            </div>
        </div>
    </section>

    <!-- ================================================================
         WHY CHOOSE US
    ================================================================ -->
    <section class="lp-why">
        <div class="lp-container">
            <div class="lp-why__inner">

                <!-- left: image + chips -->
                <div class="lp-why__visual">
                    <div class="lp-why__img-wrap">
                        <img
                            src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&w=900&q=85"
                            alt="Medical team"
                            class="lp-why__img"
                        >
                        <div class="lp-why__chip lp-why__chip--1">
                            <span class="lp-why__chip-dot"></span>
                            Experienced Doctors
                        </div>
                        <div class="lp-why__chip lp-why__chip--2">
                            <span class="lp-why__chip-dot"></span>
                            Modern Equipment
                        </div>
                        <div class="lp-why__chip lp-why__chip--3">
                            <span class="lp-why__chip-dot"></span>
                            Certified Clinic
                        </div>
                    </div>
                </div>

                <!-- right: copy + stats -->
                <div class="lp-why__copy">
                    <p class="lp-eyebrow">Why choose us</p>
                    <h2 class="lp-why__title">Why<br>choose us</h2>
                    <p class="lp-why__sub">
                        Kami adalah platform yang telah dipercaya komunitas roleplay
                        Los Santos dengan standar medis yang konsisten dan profesional.
                    </p>
                    <div class="lp-why__stats">
                        <div class="lp-why__stat">
                            <strong>10+</strong>
                            <span>Years of experience</span>
                        </div>
                        <div class="lp-why__stat">
                            <strong>15</strong>
                            <span>Areas of medicine</span>
                        </div>
                        <div class="lp-why__stat">
                            <strong>95%</strong>
                            <span>Satisfied patients</span>
                        </div>
                        <div class="lp-why__stat">
                            <strong>98%</strong>
                            <span>Diagnostics</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================================================================
         SCHEDULE + CTA
    ================================================================ -->
    <section id="jadwal" class="lp-schedule">
        <div class="lp-container">
            <div class="lp-schedule__inner">

                <!-- schedule table -->
                <div class="lp-schedule__table">
                    <h2 class="lp-section-title" style="margin-bottom:24px;">Jam Operasional</h2>
                    <div class="lp-schedule__notice">
                        <i class="fas fa-exclamation-triangle text-amber-500 mr-1"></i> <strong>Info:</strong> Pelayanan sesuai ketersediaan tenaga medis (On Duty).
                    </div>
                    <div class="lp-schedule__row">
                        <div class="lp-schedule__info">
                            <div class="lp-schedule__icon"><i class="fas fa-user-nurse"></i></div>
                            <div>
                                <div class="lp-schedule__name">Operasi Plastik</div>
                                <div class="lp-schedule__desc">Shift 1: 13:00&ndash;16:00 &middot; Shift 2: 20:00&ndash;22:00</div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-schedule__row">
                        <div class="lp-schedule__info">
                            <div class="lp-schedule__icon"><i class="fas fa-file-medical"></i></div>
                            <div>
                                <div class="lp-schedule__name">Surat-Suratan Medis</div>
                                <div class="lp-schedule__desc">Shift 1: 13:00&ndash;17:00 &middot; Shift 2: 19:00&ndash;22:00</div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-schedule__row">
                        <div class="lp-schedule__info">
                            <div class="lp-schedule__icon lp-schedule__icon--green"><i class="fas fa-pills"></i></div>
                            <div>
                                <div class="lp-schedule__name">Layanan Farmasi</div>
                                <div class="lp-schedule__desc">Pengambilan & pengobatan medis</div>
                            </div>
                        </div>
                        <div class="lp-badge-green">
                            <span class="lp-badge-dot"></span>
                            BUKA 24 JAM
                        </div>
                    </div>
                </div>

                <!-- CTA card -->
                <div class="lp-cta-card">
                    <p class="lp-eyebrow lp-eyebrow--light">Ready when you are</p>
                    <h3>Butuh layanan medis sekarang?</h3>
                    <p>Akses layanan iMe dengan mudah dan dapatkan bantuan dari tenaga medis kami kapan saja.</p>
                    <a href="#layanan" class="lp-btn lp-btn--white" style="margin-top:24px;display:inline-block;">Lihat Layanan</a>
                    <button onclick="showRegulationModal()" class="lp-btn lp-btn--outline-light" style="margin-top:12px;">
                        Regulasi Pengobatan
                    </button>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&display=swap" rel="stylesheet">
<style>
/* ================================================
   TOKENS
================================================ */
:root {
  --lp-blue:   #0ea5e9; /* Sky 500 - Biru Medis Navbar */
  --lp-blue2:  #0284c7; /* Sky 600 */
  --lp-navy:   #0c4a6e; /* Sky 900 - Background Navbar */
  --lp-dark:   #075985; /* Sky 800 */
  --lp-ink:    #0f172a;
  --lp-muted:  #64748b;
  --lp-light:  #f0f9ff; /* Sky 50 */
  --lp-border: #e0f2fe; /* Sky 100 */
  --lp-white:  #ffffff;
  --lp-radius: 16px;
  font-family: 'Inter', sans-serif;
}

/* ================================================
   MODAL FIXES
================================================ */
.custom-scrollbar::-webkit-scrollbar { width:6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background:#3b82f6; border-radius:99px; }
@keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.animate-fade-in-up { animation:fadeInUp .4s ease; }

/* ================================================
   SHARED
================================================ */
.lp-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 32px;
}
@media(max-width:640px){ .lp-container{padding:0 16px;} }

.lp-eyebrow {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: var(--lp-blue);
}
.lp-eyebrow--light { color: rgba(255,255,255,.6); }

.lp-section-title {
  font-family: 'DM Sans', sans-serif;
  font-size: clamp(28px,4vw,44px);
  font-weight: 700;
  line-height: 1.1;
  letter-spacing: -.03em;
  color: var(--lp-ink);
}

.lp-tag {
  display: inline-block;
  background: var(--lp-blue);
  color: white;
  font-size: 12px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 6px;
  vertical-align: middle;
  margin-left: 6px;
}

.lp-link {
  color: var(--lp-blue);
  font-weight: 600;
  font-size: 13px;
  text-decoration: none;
}
.lp-link:hover { text-decoration: underline; }

/* Buttons */
.lp-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 11px 22px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: all .2s;
  cursor: pointer;
  border: none;
}
.lp-btn--white  { background: white; color: var(--lp-blue); }
.lp-btn--white:hover { background: #f0f9ff; }
.lp-btn--dark   { background: var(--lp-ink); color: white; }
.lp-btn--dark:hover { background: #1e293b; }
.lp-btn--outline { background: rgba(255,255,255,.15); color: white; border: 1px solid rgba(255,255,255,.4); }
.lp-btn--outline:hover { background: rgba(255,255,255,.25); }
.lp-btn--outline-light { background: transparent; color: white; border: 1px solid rgba(255,255,255,.4); width:100%; justify-content:center; }
.lp-btn--outline-light:hover { background: rgba(255,255,255,.1); }

/* ================================================
   HERO
================================================ */
.lp-hero {
  background: linear-gradient(180deg, var(--lp-navy) 0%, var(--lp-dark) 100%);
  padding-top: 40px;
  overflow: hidden;
  position: relative;
}
.lp-hero::before {
  content: '';
  position: absolute;
  top: -100px; right: -100px;
  width: 500px; height: 500px;
  background: rgba(255,255,255,.05);
  border-radius: 50%;
}

.lp-hero__inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 64px 32px 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: flex-end;
  min-height: 580px;
}
@media(max-width:900px){
  .lp-hero__inner { grid-template-columns:1fr; min-height:auto; padding-bottom:40px; }
  .lp-hero__visual { display:none; }
}

/* copy */
.lp-hero__copy { color: white; padding-bottom: 48px; }
.lp-hero__eyebrow {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: rgba(255,255,255,.7);
  margin-bottom: 16px;
}
.lp-hero__title {
  font-family: 'DM Sans', sans-serif;
  font-size: clamp(48px,8vw,88px);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -.04em;
  color: white;
  margin-bottom: 20px;
}
.lp-hero__title span { color: rgba(255,255,255,.6); font-weight: 400; }
.lp-hero__sub {
  font-size: 14px;
  line-height: 1.7;
  color: rgba(255,255,255,.7);
  max-width: 380px;
  margin-bottom: 28px;
}
.lp-hero__actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 36px; }
.lp-hero__stats {
  display: flex;
  align-items: center;
  gap: 0;
  background: rgba(255,255,255,.1);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,.2);
  border-radius: 14px;
  padding: 16px 24px;
  width: fit-content;
}
.lp-stat { text-align: center; padding: 0 20px; }
.lp-stat strong {
  display: block;
  font-family: 'DM Sans', sans-serif;
  font-size: 26px;
  font-weight: 700;
  color: white;
  letter-spacing: -.03em;
}
.lp-stat span { font-size: 11px; color: rgba(255,255,255,.6); font-weight: 600; }
.lp-stat-divider { width: 1px; height: 40px; background: rgba(255,255,255,.2); }

/* visual right side */
.lp-hero__visual {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 16px;
  align-items: flex-end;
}
.lp-hero__img-wrap {
  position: relative;
  border-radius: 24px 24px 0 0;
  overflow: hidden;
  height: 440px;
  background: rgba(255,255,255,.1);
}
.lp-hero__doctor {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
  mix-blend-mode: luminosity;
  opacity: .9;
}

/* floating chips */
.lp-chip {
  position: absolute;
  background: white;
  color: var(--lp-ink);
  font-size: 11px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 50px;
  box-shadow: 0 4px 20px rgba(0,0,0,.15);
}
.lp-chip--1 { top: 24px; left: 16px; animation: chipFloat 3s ease-in-out infinite; }
.lp-chip--2 { top: 24px; right: 16px; animation: chipFloat 3.5s ease-in-out infinite .5s; }
.lp-chip--3 { bottom: 80px; left: 16px; animation: chipFloat 4s ease-in-out infinite 1s; }
@keyframes chipFloat {
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-6px)}
}

/* mini card bottom */
.lp-hero__mini-card {
  position: absolute;
  bottom: 16px; right: 16px;
  background: white;
  border-radius: 12px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.lp-hero__mini-card-img {
  width: 40px; height: 40px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}
.lp-hero__mini-card-img img { width:100%;height:100%;object-fit:cover; }
.lp-hero__mini-card-title { font-size: 12px; font-weight: 700; color: var(--lp-ink); }
.lp-hero__mini-card-sub { font-size: 10px; color: var(--lp-muted); }

/* right text block */
.lp-hero__right-text {
  width: 140px;
  padding-bottom: 24px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.lp-hero__right-label {
  font-size: 13px;
  font-weight: 700;
  color: white;
  line-height: 1.3;
}
.lp-hero__right-desc {
  font-size: 11px;
  line-height: 1.6;
  color: rgba(255,255,255,.6);
}

/* ================================================
   ABOUT
================================================ */
.lp-about {
  background: white;
  padding: 80px 0;
  text-align: center;
}
.lp-about__text {
  font-family: 'DM Sans', sans-serif;
  font-size: clamp(20px,3.5vw,36px);
  font-weight: 700;
  line-height: 1.3;
  letter-spacing: -.02em;
  color: var(--lp-ink);
  max-width: 780px;
  margin: 0 auto 16px;
}
.lp-about__text em { font-style: italic; color: var(--lp-blue); }
.lp-icon-inline { font-size: 1em; vertical-align: middle; }
.lp-about__sub {
  font-size: 13px;
  line-height: 1.7;
  color: var(--lp-muted);
  max-width: 520px;
  margin: 0 auto 28px;
}

/* ================================================
   SERVICES
================================================ */
.lp-services { background: var(--lp-light); padding: 80px 0; }
.lp-services__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 32px;
  margin-bottom: 40px;
  flex-wrap: wrap;
}
.lp-services__desc {
  font-size: 13px;
  line-height: 1.7;
  color: var(--lp-muted);
  max-width: 260px;
  margin-top: 8px;
}
.lp-services__grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}
@media(max-width:900px){ .lp-services__grid{grid-template-columns:repeat(2,1fr);} }
@media(max-width:540px){ .lp-services__grid{grid-template-columns:1fr;} }

/* service card */
.lp-service-card {
  background: white;
  border: 1px solid var(--lp-border);
  border-radius: var(--lp-radius);
  padding: 28px 24px;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: box-shadow .2s, transform .2s;
  min-height: 200px;
}
.lp-service-card:hover {
  box-shadow: 0 8px 32px rgba(37,99,235,.12);
  transform: translateY(-2px);
}
.lp-service-card__num {
  font-size: 11px;
  font-weight: 700;
  color: var(--lp-muted);
  letter-spacing: .08em;
}
.lp-service-card__icon { font-size: 22px; margin: 4px 0; }
.lp-service-card__title {
  font-size: 15px;
  font-weight: 700;
  color: var(--lp-ink);
  letter-spacing: -.02em;
}
.lp-service-card__desc {
  font-size: 12px;
  line-height: 1.6;
  color: var(--lp-muted);
  flex: 1;
}
.lp-service-card__link {
  font-size: 12px;
  font-weight: 700;
  color: var(--lp-blue);
  text-decoration: none;
}
.lp-service-card__link:hover { text-decoration: underline; }
.lp-service-card__tba {
  position: absolute;
  top: 20px; right: 20px;
  font-size: 10px;
  font-weight: 700;
  color: var(--lp-muted);
  background: var(--lp-light);
  padding: 2px 8px;
  border-radius: 50px;
  border: 1px solid var(--lp-border);
}

/* featured blue card */
.lp-service-card--featured {
  background: var(--lp-blue);
  border-color: var(--lp-blue);
  overflow: hidden;
  padding: 0;
  min-height: 220px;
}
.lp-service-card--featured:hover { transform:translateY(-2px); }
.lp-service-card__logo {
  position: absolute;
  top: 20px; left: 20px;
  font-family: 'DM Sans', sans-serif;
  font-size: 18px;
  font-weight: 700;
  color: white;
  line-height: 1.1;
  z-index: 2;
}
.lp-service-card__logo span { font-weight: 400; opacity: .7; }
.lp-service-card__bg-img {
  position: absolute;
  inset: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  opacity: .35;
}

/* ================================================
   WHY CHOOSE US
================================================ */
.lp-why {
  background: var(--lp-navy);
  padding: 80px 0;
}
.lp-why__inner {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 64px;
  align-items: center;
}
@media(max-width:900px){
  .lp-why__inner{grid-template-columns:1fr;}
  .lp-why__visual{order:2;}
}

/* visual */
.lp-why__img-wrap {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  height: 420px;
}
.lp-why__img {
  width: 100%; height: 100%;
  object-fit: cover;
  object-position: top center;
}
.lp-why__chip {
  position: absolute;
  background: white;
  color: var(--lp-ink);
  font-size: 11px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 50px;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
  display: flex;
  align-items: center;
  gap: 6px;
}
.lp-why__chip--1 { bottom: 100px; left: 16px; }
.lp-why__chip--2 { bottom: 60px;  left: 16px; }
.lp-why__chip--3 { bottom: 20px;  left: 16px; }
.lp-why__chip-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--lp-blue);
}

/* copy */
.lp-why__copy { color: white; }
.lp-why__title {
  font-family: 'DM Sans', sans-serif;
  font-size: clamp(36px,5vw,64px);
  font-weight: 700;
  line-height: .95;
  letter-spacing: -.04em;
  margin: 12px 0 16px;
  color: white;
}
.lp-why__sub {
  font-size: 13px;
  line-height: 1.7;
  color: rgba(255,255,255,.55);
  margin-bottom: 40px;
  max-width: 360px;
}
.lp-why__stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}
.lp-why__stat strong {
  display: block;
  font-family: 'DM Sans', sans-serif;
  font-size: 36px;
  font-weight: 700;
  letter-spacing: -.04em;
  color: white;
}
.lp-why__stat span {
  font-size: 12px;
  color: rgba(255,255,255,.5);
  font-weight: 500;
}

/* ================================================
   SCHEDULE + CTA
================================================ */
.lp-schedule { background: white; padding: 80px 0; }
.lp-schedule__inner {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 48px;
  align-items: start;
}
@media(max-width:900px){ .lp-schedule__inner{grid-template-columns:1fr;} }

.lp-schedule__notice {
  background: #fefce8;
  border: 1px solid #fef08a;
  border-radius: 10px;
  padding: 10px 16px;
  font-size: 12px;
  color: #713f12;
  margin-bottom: 16px;
}
.lp-schedule__row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 0;
  border-bottom: 1px solid var(--lp-border);
  gap: 12px;
  flex-wrap: wrap;
}
.lp-schedule__info { display: flex; align-items: center; gap: 12px; }
.lp-schedule__icon {
  width: 36px; height: 36px;
  background: #dbeafe;
  border-radius: 10px;
  display: grid;
  place-items: center;
  font-size: 16px;
  flex-shrink: 0;
}
.lp-schedule__icon--green { background: #dcfce7; }
.lp-schedule__name { font-size: 13px; font-weight: 700; color: var(--lp-ink); }
.lp-schedule__desc { font-size: 11px; color: var(--lp-muted); margin-top: 2px; }
.lp-badge-green {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 10px;
  font-weight: 700;
  color: #16a34a;
  background: #dcfce7;
  padding: 4px 10px;
  border-radius: 50px;
  letter-spacing: .06em;
}
.lp-badge-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #16a34a;
  animation: badgePulse 1.5s ease-in-out infinite;
}
@keyframes badgePulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)}}

/* CTA card */
.lp-cta-card {
  background: linear-gradient(135deg, var(--lp-navy) 0%, var(--lp-dark) 100%);
  border-radius: 20px;
  padding: 36px 28px;
  color: white;
  display: flex;
  flex-direction: column;
}
.lp-cta-card h3 {
  font-family: 'DM Sans', sans-serif;
  font-size: 22px;
  font-weight: 700;
  line-height: 1.2;
  margin: 8px 0 12px;
}
.lp-cta-card p {
  font-size: 13px;
  line-height: 1.7;
  color: rgba(255,255,255,.7);
}
</style>
@endpush

@push('scripts')
<script>
  function showRegulationModal(){
    var m=document.getElementById('regulationModal');
    if(m){m.style.display='flex';document.body.style.overflow='hidden';}
  }
  function closeRegulationModal(){
    var m=document.getElementById('regulationModal');
    if(m){m.style.display='none';document.body.style.overflow='auto';}
  }
  document.addEventListener('DOMContentLoaded',function(){
    var m=document.getElementById('regulationModal');
    if(m){
      m.addEventListener('click',function(e){if(e.target===m)closeRegulationModal();});
      document.addEventListener('keydown',function(e){if(e.key==='Escape')closeRegulationModal();});
    }
  });
</script>
@endpush
